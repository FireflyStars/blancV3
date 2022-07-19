<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Tranche;
use App\Models\Delivery;

class CustomerController extends Controller
{

    /**
     * Create Customer
     */

    public function createCustomer(Request $request){

       if($request->typeDelivery == "DELIVERY"){

            $validator = Validator::make($request->all(), [

                'firstName' => 'required',
                'lastName' => 'required',
                'phoneNumber' => 'required',
                'postCode' => 'required',
                'deliveryAddress1' => 'required',
                'city' => 'required',

            ]);
        } else {

            $validator = Validator::make($request->all(), [
                'firstName' => 'required',
                'lastName' => 'required',
            ]);


        }

        if ($validator->fails()) {
            return response()->json(['error'=> $validator->errors()]);
        }


        // add a new record to infoCustomer table
        $info_customer = [
            'isMaster'      => $request->accountType == 'Main' ? 1 : 0,
            'TypeDelivery'  => $request->typeDelivery,
            'btob'          => $request->customerType == 'B2B' ? 1 : 0,
            'FirstName'     => $request->firstName,
            'LastName'      => $request->lastName,
            'EmailAddress'  => $request->email !='' ? $request->email : (Str::random(10).'@noemail.com'),
            'Name'          => $request->firstName.", ".$request->lastName,
            'Phone'         => $request->phoneNumber != '' ? '["'.$request->phoneCountryCode.'|'.$request->phoneNumber.']"' : '',
            'bycard'        => $request->paymentMethod == 'Credit Card' ? 1 : 0,
            'cardvip'       => $request->kioskNumber,
            'discount'      => (intval($request->discountLevel) / 100),
            'credit'        => 0,
            'SignupDate'    => Carbon::now()->format('Y-m-d'),
        ];

        if($request->CustomerID !=''){
            try {
                DB::table('infoCustomer')->where('CustomerID', $request->CustomerID)->update($info_customer);
                $CustomerUUID = $request->CustomerID;
            }catch (\Exception $e) {
                return response()->json($e->getMessage(), 500);
            }
        }else{
            try {
                $info_customer['CustomerID'] = '';
                $custId = DB::table('infoCustomer')->insertGetId($info_customer);
                $CustomerUUID = DB::table('infoCustomer')->where('id', $custId)->value('CustomerID');
            } catch (\Exception $e) {
                return response()->json($e->getMessage(), 500);
            }
        }
        // set CustomerIdMaster of sub account as Main customer's CustomerID
        if(count($request->linkedAccounts) > 1){
            foreach ($request->linkedAccounts as $account) {
                try {
                    DB::table('infoCustomer')->where('id', $account['id'])->update(['CustomerIDMaster' => $CustomerUUID]);
                } catch (\Exception $e) {
                    return response()->json($e->getMessage(), 500);
                }
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
        try {
            $addressId = DB::table('address')->insertGetId($address);
            $addressUUID = DB::table('address')->where('id', $addressId)->value('AddressID');
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
        // add a new record to NewAddress table
        $new_address = [
            'CustomerID'    => $CustomerUUID,
            'AddressID'     => $addressUUID,
            'NewEmail'      => $request->email !='' ? $request->email : (Str::random(10).'@noemail.com'),
            'City'          => $request->city,
            'State'         => $request->state,
            'postcode'      => $request->postCode,
            'address1'      => $request->deliveryAddress1,
            'address2'      => $request->deliveryAddress2,
            'created_at'    => now(),
            'updated_at'    => now(),
            'status'        => 'DONE',
        ];
        try {
            DB::table('NewAddress')->insert($new_address);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
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

        try {
            DB::table('NewCustomer')->insert($new_customer);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
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
                    'cardHolderName'    => $request->cardHolderName,
                    'type'              => $card->card->brand,
                    'cardNumber'        => substr_replace($request->cardDetails, str_repeat('*', strlen($request->cardDetails) - 6), 3, -3),
                    'dateexpiration'    => $request->cardExpDate,
                    'stripe_customer_id'=> $stripe_customer->id,
                    'stripe_card_id'    => $card->id,
                    'created_at'        => now(),
                    'updated_at'        => now(),
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
                    if($payment_intent->status == 'succeeded')
                        DB::table('infoCustomer')->where('CustomerID', $CustomerUUID)->update(['credit'=> $request->addCredit]);
                }
            } catch (\Exception $e) {
                return response()->json($e->getMessage(), 500);
            }
        }
        if($request->paymentMethod == 'BACS'){ // paymentMethod is BACS, we add extra records to several table.
            $billing_address = [
                'CustomerID'    => $CustomerUUID,
                'AddressID'     => '',
                'longitude'     => $request->customerLon,
                'Latitude'      => $request->customerLat,
                'Town'          => $request->companyCity,
                'County'        => $request->companyCounty,
                'Country'       => $request->companyCountry,
                'postcode'      => $request->companyPostCode,
                'address1'      => $request->companyAddress1,
                'address2'      => $request->companyAddress2,
                'status'        => 'BILLING',
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
            try {
                $billing_address_id = DB::table('address')->insertGetId($billing_address);
            } catch (\Exception $e) {
                return response()->json($e->getMessage(), 500);
            }

            $contact = [
                'CustomerID'    => $CustomerUUID,
                'address_id'    => $billing_address_id,
                'name'          => $info_customer['Name'],
                'firstname'     => $request->companyRepFirstName,
                'company'       => $request->companyLegalName,
                'email'         => $request->invoiceEmail1,
                'Phone'         => $request->companyPhoneNumber != '' ? '["'.$request->companyPhoneCountryCode.'|'.$request->companyPhoneNumber.']"' : '',
                'created_at'    => now(),
                'updated_at'    => now(),
                'type'          => 'BILLING',
            ];
            try {
                DB::table('contacts')->insert($contact);
            } catch (\Exception $e) {
                return response()->json($e->getMessage(), 500);
            }

        }

            foreach ($request->preferences as $group) {
                foreach ($group['data'] as $item) {
                    $customer_preferences[] = [
                        'CustomerID' => $CustomerUUID,
                        'Titre' => $item['title'],
                        'Value' => $item['value'],
                        'id_preference' => $item['id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        $customer_preferences[] = [
            'CustomerID' => $CustomerUUID,
            'Titre' => 'Type Customer',
            'Value' => $request->programmeType,
            'id_preference' => DB::table('customerpreferences')->where('title', 'Type Customer')->value('id'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        try {
            DB::table('InfoCustomerPreference')->insert($customer_preferences);
        } catch (\Throwable $e) {
            throw $e;
        }
        $delivery_preference = [
            'CustomerId'    => $CustomerUUID,
            'TypeDelivery'  => $request->altTypeDelivery ? $request->altTypeDelivery : 'N/A' ,
            'Name'          => $request->altName,
            'CodeCountry'   => $request->altPhoneCountryCode,
            'PhoneNumber'   => $request->altPhoneNumber,
            'OtherInstruction' => $request->altDriverInstruction,
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
        try {
            DB::table('DeliveryPreference')->insert($delivery_preference);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
        return response()->json($CustomerUUID);
    }
    /**
     * Get customer preferences
     *
     */
    public function getCustomerPreferences(Request $request){
        $preferences = DB::table('customerpreferences')->where('deleted', 0)
                        ->where('category', '!=', 'Other')
                        ->select('title', 'category', 'description', 'id', 'value', 'preference_type as type', 'dropdown_values')
                        ->get()->groupBy('category');
        return response()->json($preferences);
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
            return response()->json(['error'=> $validator->errors()]);
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
            'Phone'         => $request->phoneNumber != '' ? '["'.$request->phoneCountryCode.'|'.$request->phoneNumber.']"' : '',
            'SignupDate'    => Carbon::now()->format('Y-m-d'),
        ];
        try {
            $custId = DB::table('infoCustomer')->insertGetId($info_customer);
            $customerUUID = DB::table('infoCustomer')->where('id', $custId)->value('CustomerID');
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
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
        try {
            $addressId = DB::table('address')->insertGetId($address);
            $addressUUID = DB::table('address')->where('id', $addressId)->value('AddressID');
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
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
        try {
            DB::table('NewAddress')->insert($new_address);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
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
        try {
            DB::table('NewCustomer')->insert($new_customer);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
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
            return response()->json(['error'=> $validator->errors()]);
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

        $delivery_ask_status_to_include = Delivery::getDeliveryAskStatusToInclude();
        $pickup_status_to_include = Delivery::getPickupStatutToInclude();


        if($CustomerID!=''){
            $infoCustomer=DB::table('infoCustomer')->where('infoCustomer.CustomerID','=',$CustomerID)
                ->leftJoin('address',function($join) {
                    $join->on( 'infoCustomer.CustomerID', '=', 'address.CustomerID')
                        ->where('address.status','DELIVERY');
                })
                ->first();

                //if sub account
                if($infoCustomer->CustomerIDMaster!=''){
                    $infoCustomer=DB::table('infoCustomer')->where('infoCustomer.CustomerID','=',$CustomerID)
                    ->leftJoin('address',function($join) {
                        $join->on( 'infoCustomer.CustomerIDMaster', '=', 'address.CustomerID')
                            ->where('address.status','DELIVERY');
                    })
                    ->first();
                    $infoCustomer->main_account=DB::table('infoCustomer')->where('infoCustomer.CustomerID','=',$infoCustomer->CustomerIDMaster)->first();

                    $infoCustomer->holidays=Holiday::all();
                    $deliveryask=DB::table('deliveryask')->where('CustomerID','=',$infoCustomer->CustomerIDMaster)->whereDate('date','>=',date('Y-m-d'))->whereIn('status',$delivery_ask_status_to_include)->first();

                    $pickup = DB::table('pickup')->where('CustomerID','=',$infoCustomer->CustomerIDMaster)->whereDate('date','>=',date('Y-m-d'))->whereIn('status',$pickup_status_to_include)->first();

                    $booking = null;

                    if($pickup && !$deliveryask){
                        $booking = $pickup;
                    }elseif(!$pickup && $deliveryask){
                        $booking = $deliveryask;
                    }elseif($pickup && $deliveryask){
                        $booking = $pickup;
                        if($pickup->date > $deliveryask->date){
                            $booking = $deliveryask;
                        }
                    }

                    if(!is_null($booking)){
                        $booking->slot = Tranche::getSlotFromTranche($booking->trancheFrom,$deliveryask->trancheto);
                        if(isset($booking->DeliveryaskID)){
                            $booking->type = 'deliveryask';
                        }elseif(isset($booking->PickupID)){
                            $booking->type = 'pickup';
                        }
                    }


                    $infoCustomer->main_account->recent_deliveryask=$booking;
                }



                $allpostcodes = DB::table('tranchepostcode')
                ->select(DB::raw('DISTINCT(Postcode) AS post_code'))
                ->get();

                $postcode = $infoCustomer->postcode;

                $postcode = str_replace(' ','',$postcode);
                $postcode = substr($postcode,0,-3);

                if (count($allpostcodes) > 0) {
                    foreach ($allpostcodes as $k=>$v){
                        $cur_postcode = $v->post_code;
                        if($postcode==$cur_postcode){
                            $sel_postcode = $cur_postcode;
                        }
                    }
                }

                $tranche_details = [];
                $formatted_tranche = [];

                if($sel_postcode !=''){
                    $tranche_details = DB::table('tranchepostcode')
                        ->where('Postcode',$sel_postcode)
                        ->get();
                }

                $infoCustomer->available_days = [];

                $days = ['SUNDAY'=>0,'MONDAY'=>1,'TUESDAY'=>2,'WEDNESDAY'=>3,'THURSDAY'=>4,'FRIDAY'=>5,'SATURDAY'=>6];

                $available_days = [];
                $available_slots = [];
                if(count($tranche_details) > 0){
                    foreach($tranche_details as $k=>$v){
                        if(isset($days[$v->day])){
                            $available_days[] = $days[$v->day];
                            $available_slots[$days[$v->day]] = @json_decode($v->tranche);
                        }
                    }
                }
                $infoCustomer->trancheDetails = $tranche_details;
                $infoCustomer->available_days = $available_days;
                $infoCustomer->available_slots = $available_slots;

            $ltm_spend=DB::table('infoOrder')->select(['Total'])->where('CustomerID','=',$CustomerID)->where('Status','=','FULFILLED')->where('created_at','>=',date('Y-m-d',strtotime('-12 months')))->get();
            $spend=0;
            foreach ($ltm_spend as $order){
                $spend+=$order->Total;
            }
            $infoCustomer->ltm_spend=$spend;

            $infoCustomer->card_details = null;

            $card = DB::table('cards')->where('CustomerID',$CustomerID)->where('Actif',1)->first();

            if($card){
                $infoCustomer->card_details = $card;
            }

            $infoCustomer->delivery_preference = null;

            $delivery_preference = DB::table('DeliveryPreference')->where('CustomerID',$CustomerID)->first();

            if($delivery_preference){
                $infoCustomer->delivery_preference = $delivery_preference;
            }
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

        if($request->Customername !=''){

            $keywordRaw = $request->Customername;
            $keywordRaw = str_replace(",", " ",  $keywordRaw);
            $keywords   = explode(' ', $keywordRaw);

            $customers = DB::table('infoCustomer')
            ->Leftjoin( 'address', function ($join){
                $join->on( 'address.CustomerID', '=', 'infoCustomer.CustomerID');
            })
            ->Leftjoin( 'infoOrder', function ($join){
                $join->on( 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID');
            })
            ->select('infoCustomer.id',
              DB::raw(' IF(infoCustomer.CustomerIDMaster = "" AND infoCustomer.CustomerIDMasterAccount = "" AND infoCustomer.IsMaster = 0 AND infoCustomer.IsMasterAccount = 0, "B2C", "B2B") as type'),
              DB::raw('LCASE(infoCustomer.TypeDelivery) as active_in'),
              DB::raw('LCASE(infoCustomer.Name) as name'),
              DB::raw('IF(infoCustomer.Phone = "", "--", infoCustomer.Phone) as phone'),
              DB::raw('LCASE(infoCustomer.EmailAddress) as email'),
              DB::raw('CONCAT_WS(", ", CONCAT_WS(" ", address.address1, address.address2), address.Town, address.Country, address.postcode) as address'),
              DB::raw('IF(DATE_FORMAT(MAX(infoOrder.created_at), "%d/%m/%y") = "", "--", DATE_FORMAT(MAX(infoOrder.created_at), "%d/%m/%y")) as last_order'),
              DB::raw('CEIL(SUM(infoOrder.Total)) as total_spent'),

             );
             foreach($keywords as $searchTerm){
                $customers->where(function($q) use ($searchTerm){
                    $q->where('infoCustomer.FirstName', 'like', '%'.$searchTerm.'%')
                    ->orWhere('infoCustomer.LastName', 'like', '%'.$searchTerm.'%')
                    ->orWhere('infoCustomer.Name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('infoCustomer.EmailAddress', 'like', '%'.$searchTerm.'%');
                    // and so on
                });
            }

            $customers = $customers->groupBy('infoCustomer.CustomerID');

        }else {
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
        }
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
                    ->leftJoin('InfoCustomerPreference', 'InfoCustomerPreference.CustomerID', '=', 'infoCustomer.CustomerID')
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
                                  WHEN InfoCustomerPreference.Titre = "Type Customer" AND InfoCustomerPreference.Value = "Standard" THEN "Normal"
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

    /**
     * get customer full details for customer view page
     */
    public function getCustomerFullDetail(Request $request){
        $customer = DB::table('infoCustomer')
                    ->select('infoCustomer.FirstName as firstName', 'infoCustomer.LastName as lastName', 'infoCustomer.Name as Name' ,  'infoCustomer.EmailAddress as email', 'infoCustomer.Phone as phone',
                        'infoCustomer.TotalSpend as totalSpent', 'infoCustomer.cardvip as kioskNumber', 'bycard as paymentMethod',
                        DB::raw('IF(infoCustomer.btob = 0, "B2C", "B2B") as customerType'), 'infoCustomer.TypeDelivery as typeDelivery',
                        'infoCustomer.CustomerNotes', 'infoCustomer.id', 'infoCustomer.CustomerID',
                        DB::raw('IF(infoCustomer.DeliverybyDay = 1, "Recuring", "Normal") as booking'), 'discount', 'credit',
                    )
                    ->where('infoCustomer.id', $request->customer_id)
                    ->first();
        $customer->programmeType = DB::table('InfoCustomerPreference')->where('Titre', 'Type Customer')->where('CustomerId', $customer->CustomerID)->value('Value');
        $customer->address = DB::table('address')
                                ->select('Country as country', 'Town as city', 'postcode as postCode', 'address1', 'address2')
                                ->where('CustomerID', $customer->CustomerID)
                                ->whereNotIn('status', ['DEL', 'BILLING'])->first();
        if($customer->paymentMethod == 1){
            $customer->card = DB::table('cards')->select('cardNumber', 'type', 'dateexpiration as expDate', 'cardHolderName', 'id' , 'Actif')
                                ->where('Actif' , 1)
                                ->where('CustomerID', $customer->CustomerID)->first();
        }else{
            // $customer->billing = DB::table('address')->where('CusotmerID', $customer->CustomerID)
            //                     ->select('')
        }

        $customer->currentOrders = false;
        $customer->pastOrders = false;

        //TO UNCOMMENT AFTER SAVE
/*
        $customer->currentOrders = DB::table('infoOrder')
                    ->select(
                        'infoOrder.id as order_id',
                        DB::raw('if(infoOrder.Paid=0,"unpaid","paid") as paid'), 'infoOrder.Total as total',
                        DB::raw('DATE_FORMAT(infoOrder.created_at, "%d/%m/%Y") as items_received'),
                        'infoOrder.underquote', 'infoOrder.TypeDelivery as destination', 'infoOrder.Status as status',
                        DB::raw('IF(
                            infoitems.PromisedDate > CURRENT_DATE(),
                            IF(pickup.date > deliveryask.date, DATE_FORMAT(deliveryask.date, "%d/%m"), DATE_FORMAT(pickup.date, "%d/%m")),
                            DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y")) as deliv'),
                            DB::raw('count(distinct(infoitems.id)) as items'),
                            'TypePost.bg_color as location_color', 'postes.nom as location',
                            'TypePost.process', 'TypePost.circle_color',
                    )
                    ->leftJoin('pickup', 'pickup.PickupID', '=', 'infoOrder.PickupID')
                    ->leftJoin('deliveryask', 'deliveryask.DeliveryaskID', '=', 'infoOrder.DeliveryaskID')
                    ->join('postes', 'infoOrder.Status', '=', 'postes.nominterface')
                    ->join('TypePost', 'TypePost.id', '=', 'postes.TypePost')
                    ->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
                    ->join('infoitems',function($join){
                        $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                            ->where('infoitems.InvoiceID','!=','')
                            ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                    })
                    ->where('infoOrder.OrderID','!=','')
                    ->where('infoOrder.CustomerID', $customer->CustomerID)
                    ->whereNotIn('infoOrder.Status', ['FULLFILED', 'DELIVERED', 'CANCEL', 'DELETE', 'VOID'])
                    ->groupBy('infoOrder.id')
                    ->get();
        $customer->pastOrders = DB::table('infoOrder')
                    ->select(
                        'infoOrder.id as order_id',
                        DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'), 'infoOrder.Total as total',
                        DB::raw('DATE_FORMAT(infoOrder.created_at, "%d/%m/%Y") as items_received'),
                        'infoOrder.underquote', 'infoOrder.TypeDelivery as destination', 'infoOrder.Status as status',
                        DB::raw('IF(
                            infoitems.PromisedDate > CURRENT_DATE(),
                            IF(pickup.date > deliveryask.date, DATE_FORMAT(deliveryask.date, "%d/%m"), DATE_FORMAT(pickup.date, "%d/%m")),
                            DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y")) as deliv'),
                            DB::raw('count(distinct(infoitems.id)) as items'),
                            'TypePost.bg_color as location_color', 'postes.nom as location',
                            'TypePost.process', 'TypePost.circle_color',
                    )
                    ->leftJoin('pickup', 'pickup.PickupID', '=', 'infoOrder.PickupID')
                    ->leftJoin('deliveryask', 'deliveryask.DeliveryaskID', '=', 'infoOrder.DeliveryaskID')
                    ->join('postes', 'infoOrder.Status', '=', 'postes.nominterface')
                    ->join('TypePost', 'TypePost.id', '=', 'postes.TypePost')
                    ->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
                    ->join('infoitems',function($join){
                        $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                            ->where('infoitems.InvoiceID','!=','')
                            ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                    })
                    ->where('infoOrder.OrderID','!=','')
                    ->where('infoOrder.CustomerID', $customer->CustomerID)
                    ->whereIn('infoOrder.Status', ['FULLFILED', 'DELIVERED', 'CANCEL', 'DELETE', 'VOID'])
                    ->groupBy('infoOrder.id')
                    ->get();
        //*/
        $preferences = DB::table('customerpreferences')->where('deleted', 0)
                    ->where('category', '!=', 'Other')
                    ->select('title', 'category', 'description', 'id', 'value', 'preference_type as type', 'dropdown_values')
                    ->get();
        foreach ($preferences as $item) {
            $value_selected = DB::table('InfoCustomerPreference')->where('CustomerID', $customer->CustomerID)
                            ->where('Titre', $item->title)->value('Value');
            if($value_selected)
                $item->value = $value_selected;
        }

        $customer->preferences = $preferences->groupBy('category');

        $customer->deliveryPreferences = DB::table('DeliveryPreference')
                                        ->where('CustomerID', $customer->CustomerID)
                                        ->select('TypeDelivery as altTypeDelivery', 'CodeCountry as altPhoneCountryCode',
                                        'OtherInstruction as altDriverInstruction', 'PhoneNumber as altPhoneNumber',
                                        'Name as altName')->first();

        $customer->linkedAccounts = DB::table('infoCustomer')
                                    ->where('CustomerID', $customer->CustomerID)
                                    ->orWhere('CustomerIDMaster', $customer->CustomerID)
                                    ->select(
                                        DB::raw('IF(isMaster = 1, "Main", "Sub") as accountType'),
                                        'Name as name', 'Phone as phone', 'EmailAddress as email',
                                        DB::raw('IF(SignupDateOnline = "2000-01-01", DATE_FORMAT(SignupDate, "%d/%m/%Y"), DATE_FORMAT(SignupDateOnline, "%d/%m/%Y")) as date'),
                                        'TotalSpend as spent', 'id'
                                    )->get();
        $user = Auth::user();
        $customer->current_user = null;
        if($user){
            $customer->current_user = $user;
        }
        return response()->json($customer);
    }


    public function AddCreditCustomer(Request $request){
        $user = Auth::user();

        $info_credit = [

            'user_id'   =>  $user->id,
            'montant'   =>  $request->credit,
            'CustomerID'=>  $request->customer_id,
            'created_at'=> now(),
        ];

        DB::table('credits')->insert($info_credit);

        $cust = DB::table('infoCustomer')->where('id',$request->customer_id)->first();
        $credit_to_add = $request->credit;

        $credit_total = $cust->credit + $credit_to_add;

        $customer = DB::table('infoCustomer')
        ->where('infoCustomer.id', $request->customer_id)->update(['credit' => $credit_total]);

        $cust2 = DB::table('infoCustomer')->where('id',$request->customer_id)->first();

        return response()->json( $cust2);
    }

    public function AddCreditCard(Request $request){

        $stripe_key = 'STRIPE_LIVE_SECURITY_KEY';

        $stripe_test = env('STRIPE_TEST');
        if($stripe_test){
            $stripe_key = 'STRIPE_TEST_SECURITY_KEY';
        }

        $stripe = new \Stripe\StripeClient(env($stripe_key));

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
                'CustomerID'        => $request->CustomerID
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
                    'CustomerID'        => $request->customerID,
                    'cardHolderName'    => $request->cardHolderName,
                    'type'              => $card->card->brand,
                    'Actif'             => 1 ,
                    'cardNumber'        => substr_replace($request->cardDetails, str_repeat('*', strlen($request->cardDetails) - 6), 3, -3),
                    'dateexpiration'    => $request->cardExpDate,
                    'stripe_customer_id'=> $stripe_customer->id,
                    'stripe_card_id'    => $card->id,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];

                $credit_card_id = DB::table('cards')->insertGetId($credit_card);
                 return response()->json( $credit_card_id );

    }

    public function DeleteCreditCard(Request $request){

        $card = DB::table('cards')
        ->where('cards.id', $request->id)->update(['Actif' => 0]);
        $card2 = DB::table('cards')->where('cards.id', $request->id)->first();
        return response()->json( $card2 );

    }

    public function setCustomerDiscount(Request $request){
        $customer_id = $request->customer_id;
        $discount = $request->discount;

        $updated = DB::table('infoCustomer')->where('id',$customer_id)->update(['discount'=>$discount]);

        return response()->json([
            'updated'=>$updated,
            'discount'=>$discount,
        ]);
    }

    public function updateCustomerContact(Request $request){
        $customer_id = $request->customer_id;
        $customer_type = $request->customer_type;
        $phone_country_code = $request->phone_country_code;
        $phone_num = $request->phone_num;

        $updated = false;

        $cust = DB::table('infoCustomer')->where('id',$customer_id)->first();

        if($cust){
            //$phones = $cust->Phone;

            $phone_arr = [];
            /*
            if($phones !=''){
                $phone_arr = @json_decode($phones);
            }
            */

            if($phone_country_code !='' && $phone_num !=''){
                array_push($phone_arr,str_replace("+","",$phone_country_code)."|".$phone_num);
            }


            $updated = DB::table('InfoCustomerPreference')
                ->where('CustomerID',$cust->CustomerID)
                ->where('Titre','Type Customer')
                ->where('Delete',0)
                ->update(['value'=>$request->programme_type,'updated_at'=>date('Y-m-d H:i:s')]);

            DB::table('infoCustomer')->where('id',$customer_id)->update([
                    'TypeDelivery'=>strtoupper($request->type_delivery),
                    'btob'=>($request->customer_type=='B2B'?1:0),
                    'cardvip'=>$request->kiosk_number,
                    'FirstName'=>$request->firstname,
                    'LastName'=>$request->lastname,
                    'Name'=>$request->lastname.($request->firstname!=''?", ":"").$request->firstname,
                    'EmailAddress'=>$request->email,
                    'Phone'=>(!empty($phone_arr)?json_encode($phone_arr):""),
                ]);


        }

        return response()->json([
            'post'=>$request->all(),
            'updated'=>$updated,
        ]);
    }

    public function updateCustomerAddress(Request $request){
        $customer_id = $request->customer_id;

        $updated = false;
        $cust = DB::table('infoCustomer')->where('id',$customer_id)->first();
        $new_address_id = 0;

        if($cust){
            $addr = DB::table('address')->where('CustomerID',$cust->CustomerID)->where('status','DELIVERY')->first();

            $updated = DB::table('address')->where('CustomerID',$cust->CustomerID)->where('status','DELIVERY')->update([
                'address1'=>$request->address1,
                'address2'=>$request->address2,
                'postcode'=>$request->postcode,
                //'County'=>$request->county,
                'Town'=>$request->city,
            ]);

            $new_address_id = DB::table('NewAddress')->insertGetId([
                'AddressID'=>'',
                'CustomerID'=>$cust->CustomerID,
                'created_at'=>date('Y-m-d H:i:s'),
                'address1'=>$request->address1,
                'address2'=>$request->address2,
                'postcode'=>$request->postcode,
                //'State'=>$request->county,
                'City'=>$request->city,
                'Status'=>($addr?'EXIST':'NEW'),


            ]);

        }

        return response()->json([
            'post'=>$request->all(),
            'new_address_id'=>$new_address_id,
            'updated'=>$updated,
        ]);
    }

    public function updateCustomerNote(Request $request){
        $customer_id = $request->customer_id;
        $notes = $request->notes;

        $updated = DB::table('infoCustomer')->where('id',$customer_id)->update(['CustomerNotes'=>$notes]);

        return response()->json([
            'updated'=>$updated,
        ]);
    }

    public function saveCustomerPreferences(Request $request){
        $customer_id = $request->customer_id;
        $preferences = (array) @json_decode($request->preferences);
        $updated = false;

        $cust = DB::table('infoCustomer')->where('id',$customer_id)->first();

        if($cust){
            if(count($preferences) > 0){
                foreach($preferences as $k=>$v){
                    $updated = DB::table('InfoCustomerPreference')
                        ->where('CustomerID',$cust->CustomerID)
                        ->where('Delete',0)
                        ->where('id_preference',$k)
                        ->update([
                            'Value'=>$v,
                            'updated_at'=>date('Y-m-d H:i:s')
                        ]);
                }
            }
        }

        return response()->json([
            //'post'=>$request->all(),
            'cust_id'=>$cust->CustomerID,
            'preferences'=>$preferences,
            'updated'=>$updated,
        ]);
    }

    public function saveCustomerDeliveryInstructions(Request $request){
        $customer_id = $request->customer_id;
        $updated = false;

        $cust = DB::table('infoCustomer')->where('id',$customer_id)->first();
        if($cust){
            $updated = DB::table('DeliveryPreference')->updateOrInsert(['CustomerID'=>$cust->CustomerID],[
                'CustomerID'=>$cust->CustomerID,
                'TypeDelivery'=>$request->type_delivery,
                'Name'=>$request->name,
                'CodeCountry'=>$request->country_code,
                'PhoneNumber'=>$request->phone_num,
                'OtherInstruction'=>$request->driver_instructions,
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s')
            ]);
        }


        return response()->json([
            'updated'=>$updated,
            'post'=>$request->all(),
        ]);
    }

    public function getCustomerOrderDetails(Request $request){
        $customer_id = $request->customer_id;

        $customer = DB::table('infoCustomer')->where('id',$customer_id)->first();

        if($customer){
            $customer->currentOrders = false;
            $customer->pastOrders = false;

        //TO UNCOMMENT AFTER SAVE

        $customer->currentOrders = DB::table('infoOrder')
                    ->select(
                        'infoOrder.id as order_id',
                        DB::raw('if(infoOrder.Paid=0,"unpaid","paid") as paid'), 'infoOrder.Total as total',
                        DB::raw('DATE_FORMAT(infoOrder.created_at, "%d/%m/%Y") as items_received'),
                        'infoOrder.underquote', 'infoOrder.TypeDelivery as destination', 'infoOrder.Status as status',
                        DB::raw('IF(
                            infoitems.PromisedDate > CURRENT_DATE(),
                            IF(pickup.date > deliveryask.date, DATE_FORMAT(deliveryask.date, "%d/%m"), DATE_FORMAT(pickup.date, "%d/%m")),
                            DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y")) as deliv'),
                            DB::raw('count(distinct(infoitems.id)) as items'),
                            'TypePost.bg_color as location_color', 'postes.nom as location',
                            'TypePost.process', 'TypePost.circle_color',
                    )
                    ->leftJoin('pickup', 'pickup.PickupID', '=', 'infoOrder.PickupID')
                    ->leftJoin('deliveryask', 'deliveryask.DeliveryaskID', '=', 'infoOrder.DeliveryaskID')
                    ->join('postes', 'infoOrder.Status', '=', 'postes.nominterface')
                    ->join('TypePost', 'TypePost.id', '=', 'postes.TypePost')
                    ->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
                    ->join('infoitems',function($join){
                        $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                            ->where('infoitems.InvoiceID','!=','')
                            ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                    })
                    ->where('infoOrder.OrderID','!=','')
                    ->where('infoOrder.CustomerID', $customer->CustomerID)
                    ->whereNotIn('infoOrder.Status', ['FULLFILED', 'DELIVERED', 'CANCEL', 'DELETE', 'VOID'])
                    ->groupBy('infoOrder.id')
                    ->get();
        $customer->pastOrders = DB::table('infoOrder')
                    ->select(
                        'infoOrder.id as order_id',
                        DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'), 'infoOrder.Total as total',
                        DB::raw('DATE_FORMAT(infoOrder.created_at, "%d/%m/%Y") as items_received'),
                        'infoOrder.underquote', 'infoOrder.TypeDelivery as destination', 'infoOrder.Status as status',
                        DB::raw('IF(
                            infoitems.PromisedDate > CURRENT_DATE(),
                            IF(pickup.date > deliveryask.date, DATE_FORMAT(deliveryask.date, "%d/%m"), DATE_FORMAT(pickup.date, "%d/%m")),
                            DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y")) as deliv'),
                            DB::raw('count(distinct(infoitems.id)) as items'),
                            'TypePost.bg_color as location_color', 'postes.nom as location',
                            'TypePost.process', 'TypePost.circle_color',
                    )
                    ->leftJoin('pickup', 'pickup.PickupID', '=', 'infoOrder.PickupID')
                    ->leftJoin('deliveryask', 'deliveryask.DeliveryaskID', '=', 'infoOrder.DeliveryaskID')
                    ->join('postes', 'infoOrder.Status', '=', 'postes.nominterface')
                    ->join('TypePost', 'TypePost.id', '=', 'postes.TypePost')
                    ->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
                    ->join('infoitems',function($join){
                        $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                            ->where('infoitems.InvoiceID','!=','')
                            ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                    })
                    ->where('infoOrder.OrderID','!=','')
                    ->where('infoOrder.CustomerID', $customer->CustomerID)
                    ->whereIn('infoOrder.Status', ['FULLFILED', 'DELIVERED', 'CANCEL', 'DELETE', 'VOID'])
                    ->groupBy('infoOrder.id')
                    ->get();
        //*/

        }

        return response()->json($customer);
    }
}
