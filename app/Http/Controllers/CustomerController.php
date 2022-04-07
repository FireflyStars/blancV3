<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    /**
     * Create Customer
     */

    public function createCustomer(Request $request){
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName'  => 'required',
            'email'     => 'required|email'
        ]);        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 500);
        }

        // add a new record to infoCustomer table
        $info_customer = [
            'CustomerID'    => '',
            'isMaster'      => $request->accountType == 'Main' ? 1 : 0,
            'TypeDelivery'  => $request->typeDelivery,
            'btob'          => $request->customerType == 'B2B' ? 1 : 0,
            'FirstName'     => $request->firstName,
            'LastName'      => $request->lastName,
            'EmailAddress'  => $request->email,
            'Name'          => $request->firstName.", ".$request->lastName,
            'Phone'         => $request->phoneCountryCode.' '.$request->phoneNumber,
            'bycard'        => $request->paymentMethod == 'Credit' ? 1 : 0,
            'discount'      => (intval($request->discountLevel) / 100),
            'credit'        => 0,
            'SignupDate'    => Carbon::now()->format('Y-m-d'),
        ];
        if($request->CustomerID !=''){
            DB::table('infoCustomer')->where('CustomerID', $request->CustomerID)->update($info_customer);
            $CustomerUUID = $request->CustomerID;
        }else{
            $custId = DB::table('infoCustomer')->insertGetId($info_customer);
            $CustomerUUID = DB::table('infoCustomer')->where('id', $custId)->value('CustomerID');
        }
        // set CustomerIdMaster of sub account as Main customer's CustomerID
        if(count($request->linkedAccounts) > 1){
            foreach ($request->linkedAccounts as $account) {
                DB::table('infoCustomer')->where('id', $account['id'])->update(['CustomerIDMaster' => $CustomerUUID]);
            }
        }
        // add a new record to address table
        $address = [
            'CustomerID'    => $CustomerUUID,
            'AddressID'     => '',
            'longitude'     => $request->customerLon,
            'Latitude'      => $request->customerLat,
            'Town'          => $request->city,
            'County'        => $request->county,
            'Country'       => $request->country,
            'postcode'      => $request->postCode,
            'address1'      => $request->deliveryAddress1,
            'address2'      => $request->deliveryAddress2,
            'status'        => 'DELIVERY',
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
        $addressId = DB::table('address')->insertGetId($address);
        $addressUUID = DB::table('address')->where('id', $addressId)->value('AddressID');
        // add a new record to NewAddress table
        $new_address = [
            'CustomerID'    => $CustomerUUID,
            'AddressID'     => $addressUUID,
            'City'          => $request->city,
            'State'         => $request->state,
            'postcode'      => $request->postCode,
            'address1'      => $request->deliveryAddress1,
            'address2'      => $request->deliveryAddress2,
            'created_at'    => now(),
            'updated_at'    => now(),            
            'status'        => 'DONE',
        ];
        DB::table('NewAddress')->insert($new_address);
        // add a new record to NewCustomer table
        $new_customer = [
            'CustomerID'    => $CustomerUUID,
            'AddressID'     => $addressUUID,
            'City'          => $request->city,
            'postcode'      => $request->postCode,
            'address1'      => $request->deliveryAddress1,
            'address2'      => $request->deliveryAddress2,
            'Name'          => $info_customer['Name'],
            'Phone'         => $info_customer['Phone'],
            'EmailAddress'  => $info_customer['EmailAddress'],
            'LastName'      => $info_customer['LastName'],
            'FirstName'     => $info_customer['FirstName'],
            'created_at'    => now(),
            'updated_at'    => now(),            
        ];
        DB::table('NewCustomer')->insert($new_customer);
        // payment 
        if($request->paymentMethod == 'Credit Card'){ // payment is credit card
            $stripe = new \Stripe\StripeClient(env('STRIPE_TEST_SECURITY_KEY'));
            try {
                //create a card object to stripe
                $card = $stripe->paymentMethods->create([
                    'type' => 'card',
                    'card' => [
                        'number'      => $request->cardDetails,
                        'exp_month'   => intval(explode('/', $request->cardExpDate)[0]),
                        'exp_year'    => intval(explode('/', $request->cardExpDate)[1]),
                        'cvc'         => $request->cardCVC,
                    ],
                ]);
                //create a customer object to stripe
                $stripe_customer = $stripe->customers->create([
                    'name'              => $request->cardHolderName,
                    'email'             => $request->email,
                    'payment_method'    => $card->id,
                    'invoice_settings'  => ['default_payment_method' => $card->id],
                    'metadata'          => [
                                                'CustomerID' => $CustomerUUID
                                        ],
                    'address'           => [
                                            'city'          => $request->city,
                                            'state'         => $request->state,
                                            'country'       => $request->country,
                                            'postal_code'   => $request->postCode,
                                            'line1'         => $request->deliveryAddress1,
                                            'line2'         => $request->deliveryAddress2,
                                        ]
                ]);
                //add a new record to cards table
                $credit_card = [
                    'CustomerID'        => $CustomerUUID,
                    'type'              => $card->brand,
                    'cardHolder'        => $request->cardHolderName,
                    'cardNumber'        => substr_replace($number, str_repeat('*', strlen($number) - 6), 3, -3),
                    'dateexpiration'    => $request->cardExpDate,
                    'stripe_customer_id'=> $stripe_customer->id,
                    'stripe_card_id'    => $card->id
                ];
                DB::table('cards')->insert($credit_card);                
                //if addCredit is set, make payment with credit card
                if($request->addCredit != 0){
                    // create a payment intent
                    $payment_intent = $stripe->paymentIntents->create([
                        'amount'            => $request->addCredit*100,
                        'currency'          => 'gbp',
                        'confirm'           => true,
                        "payment_method"    => $card->id,
                        "customer"          => $stripe_customer->id,
                        "capture_method"    => "automatic",
                        'payment_method_types' => ['card'],
                    ]);
                    if($payment_intent->status == 'SUCCESSED')
                        DB::table('infoCustomer')->where('CustomerID', $CustomerUUID)->update(['credit'=> $request->addCredit]);
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }else{ // paymentMethod is BACS, we add extra records to several table.
            $billing_address = [
                'CustomerID'    => $CustomerUUID,
                'AddressID'     => '',
                'longitude'     => $request->customerLon,
                'Latitude'      => $request->customerLat,
                'Town'          => $request->city,
                'County'        => $request->county,
                'Country'       => $request->country,
                'postcode'      => $request->postCode,
                'address1'      => $request->deliveryAddress1,
                'address2'      => $request->deliveryAddress2,
                'status'        => 'BILLING',
                'created_at'    => now(),
                'updated_at'    => now(),                
            ];    
            $billing_address_id = DB::table('address')->insertGetId($billing_address);            

            $contact = [
                'CustomerID'    => $CustomerUUID,
                'address_id'    => $billing_address_id,
                'name'          => $info_customer['Name'],
                'firstname'     => $request->companyRepFirstName,
                'company'       => $request->companyLegalName,
                'email'         => $request->invoiceEmail1,
                'phone'         => $request->companyPhoneCountryCode.' '.$request->companyPhoneNumber,
                'created_at'    => now(),
                'updated_at'    => now(),                
                'type'          => 'BILLING',
            ];   
            DB::table('contacts')->insert($contact);
        }
        
        $customer_preferences = [
            [ 'CustomerID'=> $CustomerUUID, 'Titre'=> 'Shirts premium finish',   'Value'=> $request->premiumFinish ],
            [ 'CustomerID'=> $CustomerUUID, 'Titre'=> 'Shirts folded',           'Value'=> $request->shirtsFolded ],
            [ 'CustomerID'=> $CustomerUUID, 'Titre'=> 'Debobbling',              'Value'=> $request->debobbling ],
            [ 'CustomerID'=> $CustomerUUID, 'Titre'=> 'Minor repairs (up to £8)','Value'=> $request->minorRepairs ],
            [ 'CustomerID'=> $CustomerUUID, 'Titre'=> 'Transactional SMS',       'Value'=> $request->transactionalSMS ],
            [ 'CustomerID'=> $CustomerUUID, 'Titre'=> 'Transactional Email',     'Value'=> $request->transactionalEmail ],
            [ 'CustomerID'=> $CustomerUUID, 'Titre'=> 'Marketing SMS',           'Value'=> $request->marketingSMS ],
            [ 'CustomerID'=> $CustomerUUID, 'Titre'=> 'Marketing Email',         'Value'=> $request->marketingEmail ],
            [ 'CustomerID'=> $CustomerUUID, 'Titre'=> 'Bi-Monthly VAT Invoices', 'Value'=> $request->VATEmail ],
            [ 'CustomerID'=> $CustomerUUID, 'Titre'=> 'Cleaning partner',        'Value'=> $request->cleaningPartner ],
            [ 'CustomerID'=> $CustomerUUID, 'Titre'=> 'No care labels',          'Value'=> $request->noCareLabel ],
            [ 'CustomerID'=> $CustomerUUID, 'Titre'=> 'Tailoring Approval',      'Value'=> $request->tailoringApproval ],
        ];
        DB::table('infoCustomerPreference')->insert($customer_preferences);
        $delivery_preference = [
            'CustomerId'    => $CustomerUUID,
            'TypeDelivery'  => $request->altTypeDelivery,
            'CodeCountry'   => $request->altPhoneCountryCode,
            'PhoneNumber'   => $request->altPhoneNumber,
            'OtherInstruction' => $request->altDriverInstruction,
            'created_at'    => now(),
            'updated_at'    => now(),            
        ];
        DB::table('DeliveryPreference')->insert($delivery_preference);

        return response()->json($CustomerUUID);
    }
    /**
     * this made for temporary to test stripe payment
     */
    public function checkStripe(Request $request){
        $stripe = new \Stripe\StripeClient(env('STRIPE_TEST_SECURITY_KEY'));
        try {
            //code...
            $card = $stripe->paymentMethods->create([
                'type' => 'card',
                'card' => [
                    'number'      => $request->cardDetails,
                    'exp_month'   => explode('/', $request->cardExpDate)[0],
                    'exp_year'    => explode('/', $request->cardExpDate)[1],
                    'cvc'         => $request->cardCVC,
                ],
            ]);
            $stripe_customer = $stripe->customers->create([
                'name'              => $request->cardHolderName,
                'email'             => $request->email,
                'payment_method'    => $card->id,
                'invoice_settings'  => ['default_payment_method' => $card->id],
                'metadata'          => [
                                            'CustomerID' => Str::uuid()
                                    ],
                'address'           => [
                                        'city'          => $request->city,
                                        'state'         => $request->state,
                                        'country'       => $request->country,
                                        'postal_code'   => $request->postCode,
                                        'line1'         => $request->deliveryAddress1,
                                        'line2'         => $request->deliveryAddress2,
                                    ]
            ]);
            if($request->addCredit != ''){
                // payment intent
                $payment_intent = $stripe->paymentIntents->create([
                    'amount'            => intval($request->addCredit)*100,
                    'currency'          => 'gbp',
                    'confirm'           => true,
                    "payment_method"    => $card->id,
                    "customer"          => $stripe_customer->id,
                    "capture_method"    => "automatic",
                    'payment_method_types' => ['card'],
                ]);
                return response()->json($payment_intent);
            }
        } catch (\Throwable $th) {
            return response()->json($th);
        }
    }
    /**
     * create a customer as sub account
     */
    public function createSubAccount(Request $request){
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName'  => 'required',
            'email'     => 'required|email|unique:infoCustomer,EmailAddress'
        ]);        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 500);
        }     
           
        $info_customer = [
            'CustomerID'    => '',
            'CustomerIDMaster'=> $request->mainId,
            'isMaster'      => 0,
            'TypeDelivery'  => $request->typeDelivery,
            'btob'          => $request->customerType == 'B2B' ? 1 : 0,
            'FirstName'     => $request->firstName,
            'LastName'      => $request->lastName,
            'Name'          => $request->firstName.", ".$request->lastName,
            'EmailAddress'  => $request->email,
            'Phone'         => $request->phoneCountryCode.' '.$request->phoneNumber,
            'SignupDate'    => Carbon::now()->format('Y-m-d'),
        ];
        $custId = DB::table('infoCustomer')->insertGetId($info_customer);
        $customerUUID = DB::table('infoCustomer')->where('id', $custId)->value('CustomerID');
        $response = [
            'id'        => $custId,
            'name'      => $info_customer['Name'], 
            'email'     => $info_customer['EmailAddress'], 
            'phone'     => $info_customer['Phone'], 
            'date'      => $info_customer['SignupDate'], 
            'spent'     => 0, 
        ];

        $address = [
            'CustomerID'    => $customerUUID,
            'AddressID'     => '',
            'longitude'     => $request->customerLon,
            'Latitude'      => $request->customerLat,
            'Town'          => $request->city,
            'County'        => $request->county,
            'Country'       => $request->country,
            'postcode'      => $request->postCode,
            'address1'      => $request->deliveryAddress1,
            'address2'      => $request->deliveryAddress2,
            'created_at'    => now(),
            'updated_at'    => now(),
            'status'        => 'DELIVERY',
        ];
        $addressId = DB::table('address')->insertGetId($address);
        $addressUUID = DB::table('address')->where('id', $addressId)->value('AddressID');
        $new_address = [
            'CustomerID'    => $customerUUID,
            'AddressID'     => $addressUUID,
            'City'          => $request->city,
            'State'        => $request->state,
            // 'Country'       => $request->country,
            'postcode'      => $request->postCode,
            'address1'      => $request->deliveryAddress1,
            'address2'      => $request->deliveryAddress2,
            'created_at'    => now(),
            'updated_at'    => now(),
            'status'        => 'DONE',
        ];
        DB::table('NewAddress')->insert($new_address);
        $new_customer = [
            'CustomerID'    => $customerUUID,
            'AddressID'     => $addressUUID,
            'City'          => $request->city,
            'postcode'      => $request->postCode,
            'address1'      => $request->deliveryAddress1,
            'address2'      => $request->deliveryAddress2,
            'Name'          => $info_customer['Name'],
            'Phone'         => $info_customer['Phone'],
            'EmailAddress'  => $info_customer['EmailAddress'],
            'LastName'      => $info_customer['LastName'],
            'FirstName'     => $info_customer['FirstName'],
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
        DB::table('NewCustomer')->insert($new_customer);
        return response()->json($response);
    }
    /**
     * generate CustomerID
     */
    public function generateCustomerID(Request $request){
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName'  => 'required',
            'email'     => 'required|email|unique:infoCustomer,EmailAddress'
        ]);        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 500);
        }          
        $custId = DB::table('infoCustomer')->insertGetId(
            [
                'FirstName'     => $request->firstName,
                'LastName'      => $request->lastName,
                'Name'          => $request->firstName." ".$request->lastName,
                'EmailAddress'  => $request->email
            ]
        );

        return response()->json(DB::table('infoCustomer')->where('id', $custId)->value('CustomerID'));
    }
    /**
     * Get customer detail
     */
    public function customerDetails(Request $request){
        $CustomerID=$request->post('CustomerID');
        $infoCustomer = null;

        if($CustomerID!=''){
            $infoCustomer=DB::table('infoCustomer')->where('infoCustomer.CustomerID','=',$CustomerID)
                ->leftJoin('address',function($join) {
                    $join->on( 'infoCustomer.CustomerID', '=', 'address.CustomerID')
                        ->where('address.status','DELIVERY');
                })
                ->first();
            $ltm_spend=DB::table('infoOrder')->select(['Total'])->where('CustomerID','=',$CustomerID)->where('Status','=','FULFILLED')->where('created_at','>=',date('Y-m-d',strtotime('-12 months')))->get();
            $spend=0;
            foreach ($ltm_spend as $order){
                $spend+=$order->Total;
            }
            $infoCustomer->ltm_spend=$spend;
        }

        return response()->json(['customer'=>$infoCustomer]);
    }

    /**
     * Get All customers 
     * Filter customers
     * 
     * @param skip, invoice_preference, customer_location, customer_type, last_order_start, last_order_end, total_spent
     * 
     * @return all_customers
     */
    public function getAllCustomers(Request $request){
        $customers = DB::table('infoCustomer')
                        ->join( 'address', function ($join){
                            $join->on( 'address.CustomerID', '=', 'infoCustomer.CustomerID')
                                 ->where('address.status', "DELIVERY");
                        })
                        ->Leftjoin( 'infoOrder', function ($join){
                            $join->on( 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID');
                                //  ->whereIn('infoOrder.Status', ["FULFILLED", "READY"]);
                        })
                        ->select(
                            'infoCustomer.id',
                            DB::raw('IF(infoCustomer.CustomerIDMaster = "" AND infoCustomer.CustomerIDMasterAccount = "" AND infoCustomer.IsMaster = 0 AND infoCustomer.IsMasterAccount = 0, "B2C", "B2B") as type'),
                            DB::raw('LCASE(infoCustomer.TypeDelivery) as active_in'), 
                            DB::raw('LCASE(infoCustomer.Name) as name'), 
                            DB::raw('CONCAT_WS(", ", CONCAT_WS(" ", address.address1, address.address2), address.Town, address.Country, address.postcode) as address'),
                            DB::raw('LCASE(infoCustomer.EmailAddress) as email'),
                            DB::raw('IF(infoCustomer.Phone = "", "--", infoCustomer.Phone) as phone'),
                            DB::raw('IF(DATE_FORMAT(MAX(infoOrder.created_at), "%d/%m/%y") = "", "--", DATE_FORMAT(MAX(infoOrder.created_at), "%d/%m/%y")) as last_order'),
                            // DB::raw('IF(infoCustomer.LastOrderDate = "", "--", DATE_FORMAT(infoCustomer.LastOrderDate, "%d/%m/%y")) as last_order'),
                            DB::raw('CEIL(SUM(infoOrder.Total)) as total_spent'),
                            // 'infoCustomer.TotalSpend as total_spent',
                        )
                        ->groupBy('infoCustomer.CustomerID')
                        ->orderBy('infoCustomer.id');
        if($request->selected_nav == 'B2B' || $request->customer_type == 'B2B'){
            $customers = $customers->where( function( $query ) {
                $query->where('infoCustomer.CustomerIDMaster', '!=', '')
                    ->orWhere('infoCustomer.CustomerIDMasterAccount', '!=', '')
                    ->orWhere('infoCustomer.IsMaster', 1)
                    ->orWhere('infoCustomer.IsMasterAccount', 1);
            });    
        }
        if($request->selected_nav == 'B2C' || $request->customer_type == 'B2C'){
            $customers = $customers->where('infoCustomer.CustomerIDMaster', '')
                            ->where('infoCustomer.CustomerIDMasterAccount', '')
                            ->where('infoCustomer.IsMaster', 0)
                            ->where('infoCustomer.IsMasterAccount', 0);
        }
        if( $request->customer_location !='' ){
            $customers = $customers->where('infoCustomer.TypeDelivery', $request->customer_location);
        }
        if( $request->invoice_preference !='' ){
            $customers = $customers->where('infoCustomer.invoicepreference', $request->invoice_preference);
        }
        if( $request->last_order_start !='' && $request->last_order_end ){
            $customers = $customers->whereBetween('infoOrder.created_at', [$request->last_order_start, $request->last_order_end]);
        }

        if( $request->total_spent !='' ){
            $customers =  $customers->get()
                                ->where('total_spent', '>=', explode(',', $request->total_spent)[0])
                                ->where('total_spent', '<=', explode(',', $request->total_spent)[1])
                                ->values();
            $total_count   =  $customers->count();
            $customers   =  $customers->skip($request->skip ? $request->skip : 0)
                                ->take(10)
                                ->values();
        }else{
            $total_count =  $customers->get()->count();
            $customers   =  $customers->skip($request->skip ? $request->skip : 0)
                                    ->take(10)
                                    ->get();
        }
        return response()->json([
            'customers'     => $customers,
            'total_count'   => $total_count,
        ]);
    }
    /**
     * Get Customer detailed infomation
     * 
     * @param customer_id
     * 
     * @return customer_infomation
     */
    public function getCustomerDetail(Request $request){
        $customer = DB::table('infoCustomer')
                    ->join('InfoCustomerPreference', 'InfoCustomerPreference.CustomerID', '=', 'infoCustomer.CustomerID')
                    ->select('Name as name', 'EmailAddress as email', 'Phone as phone',
                        DB::raw('IF(infoCustomer.CustomerIDMaster = "" AND infoCustomer.CustomerIDMasterAccount = "" AND infoCustomer.IsMaster = 0 AND infoCustomer.IsMasterAccount = 0, "B2C", "B2B") as cust_type'),
                        'infoCustomer.RevenueLocation as location', 'infoCustomer.CustomerNotes as notes', 'infoCustomer.id', 'infoCustomer.CustomerID',
                        DB::raw('IF(infoCustomer.DeliverybyDay = 1, "Recuring", "Normal") as booking'),
                        DB::raw( 
                            'CASE WHEN infoCustomer.IsMaster = 1 THEN "MAIN"
                                  WHEN infoCustomer.isMasterAccount = 1 THEN "MASTER"
                                  WHEN infoCustomer.isMaster = 0 AND infoCustomer.CustomerIDMaster <> "" THEN "Sub"
                                  WHEN infoCustomer.isMaster = 0 AND infoCustomer.CustomerIDMaster = "" THEN "Individual"
                            END as account_type'
                        ),
                        DB::raw( 
                            'CASE WHEN InfoCustomerPreference.Titre = "Type Customer" AND InfoCustomerPreference.Value = "VIP GOLD" THEN "VIP"
                                  WHEN InfoCustomerPreference.Titre = "Type Customer" AND InfoCustomerPreference.Value = "VIP RED" THEN "Complaint"
                                  WHEN InfoCustomerPreference.Titre = "Type Customer" AND InfoCustomerPreference.Value = "Standard" THEN "nothing"
                            END as preference'
                        ),
                    )
                    ->where('infoCustomer.id', $request->customer_id)
                    ->first();
        $total = DB::table('infoOrder')->where('CustomerID', $customer->CustomerID)
                    ->select(
                        DB::raw('CEIL(SUM(infoOrder.Total)) as total_spent'),
                        DB::raw('COUNT(infoOrder.id) as total_count'),
                    )->first();
        $customer->total_spent = $total->total_spent;
        $customer->total_count = $total->total_count;
        $customer->current_orders = DB::table('infoOrder')
                                        ->select(
                                            'infoOrder.id as order_id', 'infoInvoice.NumInvoice as sub_order', 'infoInvoice.id as sub_order_id',
                                            DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),
                                            // DB::raw('DATE_FORMAT(infoOrder.created_at, "%d %b %Y") as order_date'),
                                            'infoitems.priceTotal as price', 'infoitems.id as item_id',
                                            'infoitems.typeitem as item_name', 'infoitems.brand', 'infoitems.ItemTrackingKey as barcode',
                                            'TypePost.bg_color as location_color', 'postes.nom as location',
                                            'TypePost.circle_color', 'TypePost.process', 'infoOrder.underquote', 'infoitems.Colors as colors',
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "Store Drop Off"
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN "Pickup"
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN "Drop Off"
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "Pickup"
                                                END as order_left_text'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "In-Store Collection"
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN "Delivery"
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN "Delivery"
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "Delivery"
                                                END as order_right_text'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "In-Store Collection"
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN "Home Delivery"
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN "Delivery Only"
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "Recuring Delivery"
                                                END as order_text'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.dropoff, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(pickup.date, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(infoOrder.created_at, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_left_date'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.pickup_date, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(deliveryask.date, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(deliveryask.date, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_right_date'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.pickup_date, "%d %b %Y")
                                                WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(deliveryask.date, "%d %b %Y")
                                                WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(deliveryask.date, "%d %b %Y")
                                                WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_date'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.dropoff, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(pickup.trancheFrom, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(infoOrder.created_at, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_left_time'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.pickup_time, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(deliveryask.trancheFrom, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(deliveryask.trancheFrom, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_right_time'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN 0
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN IF(CURRENT_DATE() < pickup.date, 1, 0 )
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN 0
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN 0
                                                END as left_edit'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN IF(CURRENT_DATE() < booking_store.pickup_date, 1, 0 )
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN IF(CURRENT_DATE() < deliveryask.date, 1, 0 )
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN IF(CURRENT_DATE() < deliveryask.date, 1, 0 )
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as right_edit'
                                            ),                                            
                                        )
                                        ->join('infoInvoice', 'infoInvoice.OrderID', '=', 'infoOrder.OrderID')
                                        ->join('infoitems',function($join){
                                            $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                                                ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                                        })
                                        ->join('postes', 'infoitems.nextpost', '=', 'postes.id')
                                        ->join('TypePost', 'TypePost.id', '=', 'postes.TypePost')
                                        ->leftJoin('booking_store', 'booking_store.order_id', '=', 'infoOrder.id')
                                        ->leftJoin('pickup', 'pickup.PickupID', '=', 'infoOrder.PickupID')
                                        ->leftJoin('deliveryask', 'deliveryask.DeliveryaskID', '=', 'infoOrder.DeliveryaskID')
                                        ->where('infoitems.priceTotal', '!=', 0)
                                        ->where('infoOrder.CustomerID', $customer->CustomerID)
                                        ->whereNotIn('infoOrder.Status', ['FULLFILED', 'DELIVERED', 'CANCEL', 'DELETE', 'VOID'])
                                        ->get()->groupBy(['order_id','sub_order_id'])->reverse()->values();
        $customer->past_orders = DB::table('infoOrder')
                                        ->select(
                                            'infoOrder.id as order_id', 'infoInvoice.NumInvoice as sub_order', 'infoInvoice.id as sub_order_id',
                                            DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),
                                            // DB::raw('DATE_FORMAT(infoOrder.created_at, "%d %b %Y") as order_date'),
                                            'infoitems.priceTotal as price', 'infoitems.id as item_id',
                                            'infoitems.typeitem as item_name', 'infoitems.brand', 'infoitems.ItemTrackingKey as barcode',
                                            'TypePost.bg_color as location_color', 'postes.nom as location',
                                            'TypePost.circle_color', 'TypePost.process', 'infoOrder.underquote', 'infoitems.Colors as colors',
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "Store Drop Off"
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN "Pickup"
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN "Drop Off"
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "Pickup"
                                                END as order_left_text'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "In-Store Collection"
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN "Delivery"
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN "Delivery"
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "Delivery"
                                                END as order_right_text'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "In-Store Collection"
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN "Home Delivery"
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN "Delivery Only"
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "Recuring Delivery"
                                                END as order_text'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.dropoff, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(pickup.date, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(infoOrder.created_at, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_left_date'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.pickup_date, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(deliveryask.date, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(deliveryask.date, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_right_date'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.pickup_date, "%d %b %Y")
                                                WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(deliveryask.date, "%d %b %Y")
                                                WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(deliveryask.date, "%d %b %Y")
                                                WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_date'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.dropoff, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(pickup.trancheFrom, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(infoOrder.created_at, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_left_time'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.pickup_time, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(deliveryask.trancheFrom, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(deliveryask.trancheFrom, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_right_time'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN 0
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN IF(CURRENT_DATE() < pickup.date, 1, 0 )
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN 0
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN 0
                                                END as left_edit'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN IF(CURRENT_DATE() < booking_store.pickup_date, 1, 0 )
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN IF(CURRENT_DATE() < deliveryask.date, 1, 0 )
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN IF(CURRENT_DATE() < deliveryask.date, 1, 0 )
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as right_edit'
                                            ),                                            
                                        )
                                        ->join('infoInvoice', 'infoInvoice.OrderID', '=', 'infoOrder.OrderID')
                                        ->join('infoitems',function($join){
                                            $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                                                ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                                        })
                                        ->join('postes', 'infoitems.nextpost', '=', 'postes.id')
                                        ->join('TypePost', 'TypePost.id', '=', 'postes.TypePost')                                        
                                        ->leftJoin('booking_store', 'booking_store.order_id', '=', 'infoOrder.id')
                                        ->leftJoin('pickup', 'pickup.PickupID', '=', 'infoOrder.PickupID')
                                        ->leftJoin('deliveryask', 'deliveryask.DeliveryaskID', '=', 'infoOrder.DeliveryaskID')                                        
                                        ->where('infoitems.priceTotal', '!=', 0)
                                        ->where('infoOrder.CustomerID', $customer->CustomerID)
                                        ->whereIn('infoOrder.Status', ['FULLFILED', 'DELIVERED', 'CANCEL', 'DELETE', 'VOID'])
                                        ->get()->groupBy(['order_id', 'sub_order_id'])->reverse()->values();
        return response()->json( $customer );
    }
}
