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
use App\Models\InfoCustomer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\OrderRecurringCreator;
use App\Http\Controllers\NotificationController;

class CustomerController extends Controller
{

    /**
     * Create Customer
     */

    public function createCustomer(Request $request){

       if($request->typeDelivery == "DELIVERY"){
            $validator = Validator::make($request->all(), [
                'lastName' => 'required',
                'postCode'  => 'required',
                'email'     => $request->email !='' ? 'required|email|unique:infoCustomer,EmailAddress': '',
                'deliveryAddress1' => 'required',
                'city' => 'required',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'lastName' => 'required',
                'email'     => $request->email !='' ? 'required|email|unique:infoCustomer,EmailAddress': '',
            ]);
        }

        if ($validator->fails()) {
            return response()->json(['error'=> $validator->errors()]);
        }


        // add a new record to infoCustomer table
        $emailAddress = $request->email !='' ? $request->email : (Str::random(10).'@noemail.com');
        $info_customer = [
            'isMaster'      => $request->accountType == 'Main' ? 1 : 0,
            'TypeDelivery'  => $request->typeDelivery,
            'btob'          => $request->customerType == 'B2B' ? 1 : 0,
            'FirstName'     => $request->firstName,
            'LastName'      => $request->lastName,
            'EmailAddress'  => $emailAddress,
            'Name'          => $request->firstName.", ".$request->lastName,
            'Phone'         => $request->phoneNumber != '' ? '["'.$request->phoneCountryCode.'|'.$request->phoneNumber.']"' : '',
            'bycard'        => $request->paymentMethod == 'Credit Card' ? 1 : 0,
            'cardvip'       => $request->kioskNumber,
            'discount'      => (intval($request->discountLevel) / 100),
            'credit'        => 0,
            'SignupDate'    => Carbon::now()->format('Y-m-d'),
            'AcceptMarketing'        => $request->acceptMarketing,
            'AcceptSMSMarketing'        => $request->acceptSMSMarketing,
            'OnAccount'      => $request->CustomerPayemenProfile,
        ];
        if($request->deliveryByday == '1'){
            $info_customer['DeliverybyDay'] = 1;
            foreach ($request->pickupSlots as $slot) {
                $info_customer[$slot['key']] = json_encode($slot['value']);
            }
        }

        if($request->CustomerID !=''){
            try {
                DB::table('infoCustomer')->where('CustomerID', $request->CustomerID)->update($info_customer);
                $CustomerUUID = $request->CustomerID;
            }catch (\Exception $e) {
                return response()->json(['error'=> $e->getMessage()]);
            }
        }else{
            try {
                $info_customer['CustomerID'] = '';
                $custId = DB::table('infoCustomer')->insertGetId($info_customer);
                $CustomerUUID = DB::table('infoCustomer')->where('id', $custId)->value('CustomerID');
            } catch (\Exception $e) {
                return response()->json(['error'=> $e->getMessage()]);
            }
        }
        // set CustomerIdMaster of sub account as Main customer's CustomerID
        if(count($request->linkedAccounts) > 1){
            if($request->accountType == 'Main'){
                foreach ($request->linkedAccounts as $index => $account) {
                    if($index != 0){
                        try {
                            if($account['id'] != 0){
                                DB::table('infoCustomer')->where('id', $account['id'])->update(['CustomerIDMaster' => $CustomerUUID]);
                            }else if($account['id'] == 0 && $account['accountType'] == 'Sub' ){
                                $info_customer_sub = [
                                    'CustomerID'    => '',
                                    'CustomerIDMaster'=> $CustomerUUID,
                                    'OnAccount'     => $request->CustomerPayemenProfile,
                                    'TypeDelivery'     => $request->typeDelivery,
                                    'isMaster'      => 0,
                                    'btob'          => 1,
                                    'FirstName'     => $account['firstName'],
                                    'LastName'      => $account['lastName'],
                                    'Name'          => $account['name'],
                                    'EmailAddress'  => $account['email'],
                                    'Phone'         => $account['phoneNumber']!= '' ? '["'.$account['phoneCountryCode'].'|'.$account['phoneNumber'].']"' : '',
                                    'SignupDate'    => Carbon::now()->format('Y-m-d'),
                                ];
                                try {
                                    $cust_Id = DB::table('infoCustomer')->insertGetId($info_customer_sub);
                                    $customerUUID_sub = DB::table('infoCustomer')->where('id', $cust_Id)->value('CustomerID');
                                } catch (\Exception $e) {
                                    return response()->json($e->getMessage(), 500);
                                }
                                $response = [
                                    'id'        => $cust_Id,
                                    'name'      => $info_customer_sub['Name'],
                                    'email'     => $info_customer_sub['EmailAddress'],
                                    'phone'     => $info_customer_sub['Phone'],
                                    'date'      => $info_customer_sub['SignupDate'],
                                    'spent'     => 0,
                                ];

                                $new_customer_sub = [
                                    'CustomerID'    => $customerUUID_sub,
                                    'Name'          => $info_customer_sub['Name'],
                                    'Phone'         => $info_customer_sub['Phone'],
                                    'EmailAddress'  => $info_customer_sub['EmailAddress'],
                                    'LastName'      => $info_customer_sub['LastName'],
                                    'FirstName'     => $info_customer_sub['FirstName'],
                                    'status'        => 'NEW',
                                    'created_at'    => now(),
                                    'updated_at'    => now(),
                                ];
                                try {
                                    DB::table('NewCustomer')->insert($new_customer_sub);
                                } catch (\Exception $e) {
                                    return response()->json($e->getMessage(), 500);
                                }

                            }

                        } catch (\Exception $e) {
                            return response()->json(['error'=> $e->getMessage()]);
                        }
                    }else{

                    }
                }
            }else{
                $masterUUID = $request->linkedAccounts[0]['customerId'];
                DB::table('infoCustomer')->where('CustomerID', $CustomerUUID)->update(['CustomerIDMaster' => $masterUUID]);
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

        $addressUUID = '';
        if($request->typeDelivery == "DELIVERY"){
            try {
                $addressId = DB::table('address')->insertGetId($address);
                $addressUUID = DB::table('address')->where('id', $addressId)->value('AddressID');
            } catch (\Exception $e) {
                return response()->json(['error'=> $e->getMessage()]);
            }
        } else {
            try {
                if($request->postCode != null || $request->deliveryAddress1 != null || $request->city != null ){
                    $validatorAddress = Validator::make($request->all(), [
                        'postCode' => 'required',
                        'deliveryAddress1' => 'required',
                        'city' => 'required',
                    ]);
                    if ($validatorAddress->fails()) {
                        return response()->json(['error'=> $validatorAddress->errors()]);
                    }else{
                        $addressId = DB::table('address')->insertGetId($address);
                        $addressUUID = DB::table('address')->where('id', $addressId)->value('AddressID');
                    }

                }
            } catch (\Exception $e) {
                dd( $e->getMessage());
                return response()->json(['error'=> $e->getMessage()]);
            }
        }
        // add a new record to NewAddress table
        $new_address = [
            'CustomerID'    => $CustomerUUID,
            'AddressID'     => $addressUUID,
            'NewEmail'      => $emailAddress,
            'City'          => $request->city,
            'State'         => $request->state,
            'postcode'      => $request->postCode,
            'address1'      => $request->deliveryAddress1,
            'address2'      => $request->deliveryAddress2,
            'created_at'    => now(),
            'updated_at'    => now(),
            'status'        => 'DONE',
        ];

        if($request->typeDelivery == "DELIVERY"){
            try {
                DB::table('NewAddress')->insert($new_address);
            } catch (\Exception $e) {
                return response()->json(['error'=> $e->getMessage()]);
            }
        } else {

            try {
                if($request->postCode != null || $request->deliveryAddress1 != null || $request->city != null ){
                    $validatorAddress = Validator::make($request->all(), [
                        'postCode' => 'required',
                        'deliveryAddress1' => 'required',
                        'city' => 'required',
                    ]);
                    if ($validatorAddress->fails()) {
                        return response()->json(['error'=> $validatorAddress->errors()]);
                    }else{
                        DB::table('NewAddress')->insert($new_address);
                    }

                }
            } catch (\Exception $e) {
                dd( $e->getMessage());
                return response()->json(['error'=> $e->getMessage()]);
            }
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
            return response()->json(['error'=> $e->getMessage()]);
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
                        'exp_month'   => intval(explode('/', str_replace(' ','',$request->cardExpDate))[0]),
                        'exp_year'    => intval(explode('/', str_replace(' ','',$request->cardExpDate))[1]),
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


                $si = $stripe->setupIntents->create([
                    'customer' => $stripe_customer->id,
                    'payment_method_types' => ['card'],
                ]);


                //add a new record to cards table
                $credit_card = [
                    'CustomerID'        => $CustomerUUID,
                    'cardHolderName'    => $request->cardHolderName,
                    'type'              => $card->card->brand,
                    'cardNumber'        => substr_replace($request->cardDetails, str_repeat('*', strlen($request->cardDetails) - 6), 3, -3),
                    'dateexpiration'    => str_replace(' ','',$request->cardExpDate),
                    'stripe_customer_id'=> $stripe_customer->id,
                    'stripe_card_id'    => $card->id,
                    'setup_intent_id'   => $si->id,
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
                return response()->json(['error'=> $e->getMessage()]);
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
                return response()->json(['error'=> $e->getMessage()]);
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
                    return response()->json(['error'=> $e->getMessage()]);
             }
            }

            if($request->CustomerPayemenProfile == "1" && $request->accountType != "Sub" ){

                $billing_address = [
                    'CustomerID'    => $CustomerUUID,
                    'AddressID'     => '',
                    'longitude'     => $request->customerLon,
                    'Latitude'      => $request->customerLat,
                    'Town'          => $request->billingCity,
                    'Country'       => "GB",
                    'postcode'      => $request->billingPostcode,
                    'address1'      => $request->billingAddress1,
                    'address2'      => $request->billingAddress2,
                    'status'        => 'BILLING',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
                try {
                    $billing_address_id = DB::table('address')->insertGetId($billing_address);
                } catch (\Exception $e) {
                    return response()->json(['error'=> $e->getMessage()]);
                }

                $contact = [
                    'CustomerID'    => $CustomerUUID,
                    'address_id'    => $billing_address_id,
                    'name'          => $info_customer['Name'],
                    'firstname'     => $request->invoiceFirstname,
                    'company'       => $request->companyLegalName,
                    'email'         => $request->invoiceAddressEmail1,
                    'email2'         => $request->invoiceAddressEmail2,
                    'Phone'         => $request->companyPhoneNumber != '' ? '["'.$request->companyPhoneCountryCode.'|'.$request->companyPhoneNumber.']"' : '',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
                try {
                    DB::table('contacts')->insert($contact);
                } catch (\Exception $e) {
                    return response()->json(['error'=> $e->getMessage()]);
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
            DB::table('InfoCustomerPreference')->where('CustomerID', '=', $CustomerUUID)->update(['Delete' => 1,'updated_at'=>date('Y-m-d H:i:s')]);
            DB::table('InfoCustomerPreference')->insert($customer_preferences);
        } catch (\Throwable $e) {
            return response()->json(['error'=> $e->getMessage()]);
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
            return response()->json(['error'=> $e->getMessage()]);
        }
        return response()->json($custId);
    }

    public function createCustomerSubAccount(Request $request){
        $emailAddress = $request->customer_data['email'] !='' ? $request->customer_data['email'] : (Str::random(10).'@noemail.com');

        if($request->customer_data['customerId'] != "" && $request->customer_data['id'] != 0 ){
            DB::table('infoCustomer')->where('id', $request->customer_data['id'])
            ->update([
                'CustomerIDMaster' => $request->customer_id,
                'OnAccount'        => $request->CustomerPayemenProfile,
                'TypeDelivery'     => $request->typeDelivery
            ]);
        }else {

            $info_customer_sub = [
                'CustomerID'    => '',
                'CustomerIDMaster'=> $request->customer_id,
                'OnAccount'       => $request->CustomerPayemenProfile,
                'TypeDelivery'    => $request->typeDelivery,
                'isMaster'      => 0,
                'btob'          => 1,
                'FirstName'     => $request->customer_data['firstName'],
                'LastName'      => $request->customer_data['lastName'],
                'Name'          => $request->customer_data['name'],
                'EmailAddress'  => $request->customer_data['email'],
                'Phone'         => '["'.$request->customer_data['phoneCountryCode'].'|'.$request->customer_data['phoneNumber'].'"]' ,
                'SignupDate'    => Carbon::now()->format('Y-m-d'),
            ];
            try {
                $cust_Id = DB::table('infoCustomer')->insertGetId($info_customer_sub);
                $customerUUID_sub = DB::table('infoCustomer')->where('id', $cust_Id)->value('CustomerID');
            } catch (\Exception $e) {
                return response()->json($e->getMessage(), 500);
            }
            $response = [
                'id'        => $cust_Id,
                'name'      => $info_customer_sub['Name'],
                'email'     => $info_customer_sub['EmailAddress'],
                'phone'     => $info_customer_sub['Phone'],
                'date'      => $info_customer_sub['SignupDate'],
                'spent'     => 0,
            ];

            $new_customer_sub = [
                'CustomerID'    => $customerUUID_sub,
                'Name'          => $info_customer_sub['Name'],
                'Phone'         => $info_customer_sub['Phone'],
                'EmailAddress'  => $info_customer_sub['EmailAddress'],
                'LastName'      => $info_customer_sub['LastName'],
                'FirstName'     => $info_customer_sub['FirstName'],
                'status'        => 'NEW',
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
            try {
                DB::table('NewCustomer')->insert($new_customer_sub);
            } catch (\Exception $e) {
                return response()->json($e->getMessage(), 500);
            }
            return response()->json($response );
        }

    }

    public function checkCustomerUnique(Request $request){
        $validator = Validator::make($request->all(), [
            'lastName' => 'unique:infoCustomer,lastName',
            'email'     => $request->email !='' ? 'unique:infoCustomer,EmailAddress': '',
            'phoneNumber' => 'unique:infoCustomer,Phone',
        ]);
        if($validator->fails()){
            return response()->json(['unique'=> false]);
        }else{
            return response()->json(['unique'=> true]);
        }
    }
    /**
     * Get customer preferences
     *
     */
    public function getCustomerPreferences(Request $request){
        $preferences = DB::table('customerpreferences')->where('deleted', 0)
                        ->where('category', '!=', 'Other')
                        ->where('title', '!=', 'Type Customer')
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
                    'exp_month'   => explode('/', str_replace(' ','',$request->cardExpDate))[0],
                    'exp_year'    => explode('/', str_replace(' ','',$request->cardExpDate))[1],
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
     * Unlink Account
     */
    public function unlinkAccount(Request $request){

        try {
            DB::table('infoCustomer')->where('id',$request->customer_id)
            ->update([
                'CustomerIDMaster'=> "",
                'IsMaster'=> 1,
            ]);
      } catch (\Exception $e) {
          return response()->json(['error'=> $e->getMessage()]);
      }
      return response()->json(['message'=>'OK']);
    }
    /**
     * Get customer detail
     */
    public function customerDetails(Request $request){
        $CustomerID=$request->post('CustomerID');
        $infoCustomer = null;
        $sel_postcode = null;
        $user = Auth::user();

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
                        $booking->slot = Tranche::getSlotFromTranche($booking->trancheFrom,$booking->trancheto);
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

            $infoCustomer->current_user = $user;
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
              DB::raw(
                'CASE WHEN (infoCustomer.isMaster = 1 OR infoCustomer.CustomerIDMaster = "") AND infoCustomer.isMasterAccount = 0  THEN "Main"
                      WHEN infoCustomer.isMasterAccount = 1 THEN "Master"
                      WHEN infoCustomer.CustomerIDMaster <> "" THEN "Sub"
                END as level'
                ),
              DB::raw('LCASE(infoCustomer.TypeDelivery) as active_in'),
              DB::raw('LCASE(infoCustomer.Name) as name'),
              DB::raw('IF(infoCustomer.Phone = "", "--", infoCustomer.Phone) as phone'),
              DB::raw('LCASE(infoCustomer.EmailAddress) as email'),
              //DB::raw('CONCAT_WS(", ", CONCAT_WS(" ", address.address1, address.address2), address.Town, address.Country, address.postcode) as address'),
              'address1','address2','postcode',
              DB::raw('IF(DATE_FORMAT(MAX(infoOrder.created_at), "%d/%m/%y") = "", "--", DATE_FORMAT(MAX(infoOrder.created_at), "%d/%m/%y")) as last_order'),
              //DB::raw('CEIL(SUM(infoOrder.Total)) as total_spent'),
              'infoCustomer.TotalSpend as total_spent'

             )->where('infoCustomer.Actif', '=', 1);
             foreach($keywords as $searchTerm){
                $customers->where(function($q) use ($searchTerm){
                    $q->where('infoCustomer.FirstName', 'like', '%'.$searchTerm.'%')
                    ->orWhere('infoCustomer.LastName', 'like', '%'.$searchTerm.'%')
                    ->orWhere('infoCustomer.Name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('infoCustomer.EmailAddress', 'like', '%'.$searchTerm.'%');
                    // and so on
                });
            }
            $customers = $customers->groupBy('infoCustomer.CustomerID')->orderBy('infoCustomer.id', 'DESC');

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
                            DB::raw(
                                'CASE WHEN (infoCustomer.isMaster = 1 OR infoCustomer.CustomerIDMaster = "") AND infoCustomer.isMasterAccount = 0  THEN "Main"
                                      WHEN infoCustomer.isMasterAccount = 1 THEN "Master"
                                      WHEN infoCustomer.CustomerIDMaster <> "" THEN "Sub"
                                END as level'
                            ),
                            DB::raw('LCASE(infoCustomer.TypeDelivery) as active_in'),
                            DB::raw('LCASE(infoCustomer.Name) as name'),
                            //DB::raw('CONCAT_WS(", ", CONCAT_WS(" ", address.address1, address.address2), address.Town, address.Country, address.postcode) as address'),
                            'address1','address2','postcode',
                            DB::raw('LCASE(infoCustomer.EmailAddress) as email'),
                            DB::raw('IF(infoCustomer.Phone = "", "--", infoCustomer.Phone) as phone'),
                            DB::raw('IF(DATE_FORMAT(MAX(infoOrder.created_at), "%d/%m/%y") = "", "--", DATE_FORMAT(MAX(infoOrder.created_at), "%d/%m/%y")) as last_order'),
                            // DB::raw('IF(infoCustomer.LastOrderDate = "", "--", DATE_FORMAT(infoCustomer.LastOrderDate, "%d/%m/%y")) as last_order'),
                            //DB::raw('CEIL(SUM(infoOrder.Total)) as total_spent'),
                            'infoCustomer.TotalSpend as total_spent',
                        )
                        ->groupBy('infoCustomer.CustomerID')
                        ->orderBy('infoCustomer.id', 'DESC');
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
                        DB::raw('IF(btob = 0, "B2C", "B2B") as cust_type'),
                        'infoCustomer.TypeDelivery as location', 'infoCustomer.CustomerNotes as notes', 'infoCustomer.id', 'infoCustomer.CustomerID','infoCustomer.CustomerIDMaster',
                        DB::raw('IF(infoCustomer.DeliverybyDay = 1, "Recurring", "Normal") as booking'),
                        DB::raw(
                            'CASE WHEN infoCustomer.IsMaster = 1 AND infoCustomer.CustomerIDMaster = "" THEN "MAIN"
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
                    if($customer->account_type == "Sub"){
                        $company = DB::table('infoCustomer')->select('infoCustomer.CompanyName')->where('infoCustomer.CustomerID','=',$customer->CustomerIDMaster)->first();
                        $customer->CompanyName = $company->CompanyName;
                   }
                   else if($customer->account_type == "Main"){
                       $company = DB::table('infoCustomer')->select('infoCustomer.CompanyName')->where('infoCustomer.CustomerID','=',$customer->CustomerID)->first();
                       $customer->CompanyName = $company->CompanyName;
                   }

        $total = DB::table('infoOrder')->where('CustomerID', $customer->CustomerID)
                    ->select(
                        DB::raw('CEIL(SUM(infoOrder.Total)) as total_spent'),
                        DB::raw('COUNT(infoOrder.id) as total_count'),
                    )->first();

        $customer->total_spent = $total->total_spent;
        $customer->total_count = $total->total_count;


        $tranches_slots = Tranche::getDeliveryPlanningTranchesForApi();

        $current_orders = DB::table('infoOrder')
                                        ->select(
                                            'infoOrder.id as order_id', 'infoInvoice.NumInvoice as sub_order', 'infoInvoice.id as sub_order_id','infoInvoice.InvoiceID','infoOrder.Status',
                                            DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),
                                            // DB::raw('DATE_FORMAT(infoOrder.created_at, "%d %b %Y") as order_date'),
                                            'infoitems.priceTotal as price', 'infoitems.id as item_id',
                                            'infoitems.typeitem as item_name', 'infoitems.brand', 'infoitems.ItemTrackingKey as barcode',
                                            'TypePost.bg_color as location_color', 'postes.nom as location',
                                            'TypePost.circle_color', 'TypePost.process', 'infoOrder.underquote', 'infoitems.Colors as colors','infoOrder.deliverymethod',
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
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN CONCAT(pickup.trancheFrom,"_",pickup.trancheto)
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(infoOrder.created_at, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_left_time'
                                            ),
                                            DB::raw(
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "6-8pm"
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN CONCAT(deliveryask.trancheFrom,"_",deliveryask.trancheto)
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN CONCAT(deliveryask.trancheFrom,"_",deliveryask.trancheto)
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_right_time'
                                            ),
                                            DB::raw(
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN 0
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN IF(CURRENT_DATE() < pickup.date, 1, 0 )
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN 0
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN 00
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
                                        ->whereNotIn('infoOrder.Status', ['FULFILLED', 'DELIVERED', 'CANCEL', 'DELETE', 'VOID'])
                                        ->get()->groupBy(['order_id','sub_order_id'])->reverse()->values();

                                        foreach($current_orders as $k=>$v){
                                            foreach($v as $i=>$x){
                                                 foreach($x as $key=>$item){
                                                     $delivery_method = $item->deliverymethod;
                                                     //if(in_array($delivery_method,['home_delivery','delivery_only'])){
                                                         $tranche = $item->order_right_time;
                                                         $tranche_arr = explode("_",$tranche);
                                                         if(isset($tranche_arr[0]) && isset($tranche_arr[1])){
                                                             $slot = Tranche::getSlotFromTranche($tranche_arr[0],$tranche_arr[1]);
                                                             $timeslot = $tranches_slots[$slot];
                                                             $current_orders[$k][$i][$key]->order_right_time = $timeslot;
                                                         }
                                                     //leftTime
                                                         $tranche_left = $item->order_left_time;
                                                         $tranche_arr_left = explode("_",$tranche_left);
                                                         if(isset($tranche_arr_left[0]) && isset($tranche_arr_left[1])){
                                                             $slot = Tranche::getSlotFromTranche($tranche_arr_left[0],$tranche_arr_left[1]);
                                                             $timeslot = $tranches_slots[$slot];
                                                             $current_orders[$k][$i][$key]->order_left_time = $timeslot;
                                                         }
                                                     //}
                                                 }

                                            }
                                         }

        $customer->current_orders = $current_orders;

        $past_orders = DB::table('infoOrder')
                                        ->select(
                                            'infoOrder.id as order_id', 'infoInvoice.NumInvoice as sub_order','infoInvoice.InvoiceID', 'infoInvoice.id as sub_order_id','infoOrder.Status',
                                            DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),
                                            // DB::raw('DATE_FORMAT(infoOrder.created_at, "%d %b %Y") as order_date'),
                                            'infoitems.priceTotal as price', 'infoitems.id as item_id',
                                            'infoitems.typeitem as item_name', 'infoitems.brand', 'infoitems.ItemTrackingKey as barcode',
                                            'TypePost.bg_color as location_color', 'postes.nom as location',
                                            'TypePost.circle_color', 'TypePost.process', 'infoOrder.underquote', 'infoitems.Colors as colors','infoOrder.deliverymethod',
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
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN CONCAT(pickup.trancheFrom,"_",pickup.trancheto)
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(infoOrder.created_at, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_left_time'
                                            ),
                                            DB::raw(
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "6-8pm"
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN CONCAT(deliveryask.trancheFrom,"_",deliveryask.trancheto)
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN CONCAT(deliveryask.trancheFrom,"_",deliveryask.trancheto)
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
                                        ->whereIn('infoOrder.Status', ['FULFILLED', 'DELIVERED', 'CANCEL', 'DELETE', 'VOID'])
                                        ->get()->groupBy(['order_id', 'sub_order_id'])->reverse()->values();


                                        foreach($past_orders as $k=>$v){
                                            foreach($v as $i=>$x){
                                                 foreach($x as $key=>$item){
                                                     $delivery_method = $item->deliverymethod;
                                                     //if(in_array($delivery_method,['home_delivery','delivery_only'])){
                                                         $tranche = $item->order_right_time;
                                                         $tranche_arr = explode("_",$tranche);
                                                         if(isset($tranche_arr[0]) && isset($tranche_arr[1])){
                                                             $slot = Tranche::getSlotFromTranche($tranche_arr[0],$tranche_arr[1]);
                                                             $timeslot = $tranches_slots[$slot];
                                                             $past_orders[$k][$i][$key]->order_right_time = $timeslot;
                                                         }
                                                         //leftTime
                                                         $tranche_left = $item->order_left_time;
                                                         $tranche_arr_left = explode("_",$tranche_left);
                                                         if(isset($tranche_arr_left[0]) && isset($tranche_arr_left[1])){
                                                             $slot = Tranche::getSlotFromTranche($tranche_arr_left[0],$tranche_arr_left[1]);
                                                             $timeslot = $tranches_slots[$slot];
                                                             $past_orders[$k][$i][$key]->order_left_time = $timeslot;
                                                         }
                                                     //}
                                                     //}
                                                 }

                                            }
                                         }


        $customer->past_orders = $past_orders;


        $scheduled_orders = DB::table('infoOrder')
                    ->select(
                        'infoOrder.id as order_id','infoOrder.Status',
                        DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'), 'infoOrder.underquote','infoOrder.deliverymethod',
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
                              WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN CONCAT(pickup.trancheFrom,"_",pickup.trancheto)
                              WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(infoOrder.created_at, "%h:%i %p")
                              WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                        END as order_left_time'
                    ),
                    DB::raw(
                        'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "6-8pm"
                            WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN CONCAT(deliveryask.trancheFrom,"_",deliveryask.trancheto)
                            WHEN infoOrder.deliverymethod = "delivery_only" THEN CONCAT(deliveryask.trancheFrom,"_",deliveryask.trancheto)
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
                ->leftJoin('booking_store', 'booking_store.order_id', '=', 'infoOrder.id')
                ->leftJoin('pickup', 'pickup.PickupID', '=', 'infoOrder.PickupID')
                ->leftJoin('deliveryask', 'deliveryask.DeliveryaskID', '=', 'infoOrder.DeliveryaskID')
                ->where('infoOrder.CustomerID', $customer->CustomerID)
                ->whereIn('infoOrder.Status', ['SCHEDULED' ,'RECURRING' ])
                ->get()->groupBy(['order_id', 'sub_order_id'])->reverse()->values();


                                        foreach($scheduled_orders as $k=>$v){
                                            foreach($v as $i=>$x){
                                                 foreach($x as $key=>$item){
                                                     $delivery_method = $item->deliverymethod;
                                                     //if(in_array($delivery_method,['home_delivery','delivery_only'])){
                                                         $tranche = $item->order_right_time;
                                                         $tranche_arr = explode("_",$tranche);
                                                         if(isset($tranche_arr[0]) && isset($tranche_arr[1])){
                                                             $slot = Tranche::getSlotFromTranche($tranche_arr[0],$tranche_arr[1]);
                                                             $timeslot = $tranches_slots[$slot];
                                                             $scheduled_orders[$k][$i][$key]->order_right_time = $timeslot;
                                                         }
                                                         //leftTime
                                                         $tranche_left = $item->order_left_time;
                                                         $tranche_arr_left = explode("_",$tranche_left);
                                                         if(isset($tranche_arr_left[0]) && isset($tranche_arr_left[1])){
                                                             $slot = Tranche::getSlotFromTranche($tranche_arr_left[0],$tranche_arr_left[1]);
                                                             $timeslot = $tranches_slots[$slot];
                                                             $scheduled_orders[$k][$i][$key]->order_left_time = $timeslot;
                                                         }
                                                     //}
                                                 }

                                            }
                                         }

        $customer->scheduled_orders = $scheduled_orders;


        return response()->json( $customer );
    }

    /**
     * get customer full details for customer view page
     */
    public function getCustomerFullDetail(Request $request){
        $customer = DB::table('infoCustomer')
                    ->select('infoCustomer.FirstName as firstName', 'infoCustomer.LastName as lastName', 'infoCustomer.Name as Name' ,'infoCustomer.CompanyName' ,  'infoCustomer.EmailAddress as email', 'infoCustomer.Phone as phone',
                        'infoCustomer.TotalSpend as totalSpent', 'infoCustomer.cardvip as kioskNumber', 'bycard as paymentMethod', 'infoCustomer.OnAccount' ,
                        DB::raw('IF(infoCustomer.btob = 0, "B2C", "B2B") as customerType'), DB::raw('IF(infoCustomer.CustomerIDMaster = "", "Main", "Sub") as accountType'),
                         'infoCustomer.TypeDelivery as typeDelivery','infoCustomer.CustomerIDMaster','infoCustomer.OnAccount',
                        'infoCustomer.CustomerNotes', 'infoCustomer.id', 'infoCustomer.CustomerID',
                        DB::raw('IF(infoCustomer.DeliverybyDay = 1, "Recurring", "Normal") as booking'), 'discount', 'credit',
                        'infoCustomer.DeliverybyDay as deliveryByDay', 'DeliveryMon', 'DeliveryTu', 'DeliveryWed', 'DeliveryTh', 'DeliveryFri', 'DeliverySat',
                        'AcceptSMSMarketing as acceptSMSMarketing', 'AcceptMarketing as acceptMarketing', 'PauseDateTo as pauseDateTo', 'PauseDateFrom as pauseDateFrom'
                    )
                    ->where('infoCustomer.id', $request->customer_id)
                    ->first();
        $customer->DeliveryMon = json_decode($customer->DeliveryMon);
        $customer->DeliveryTu = json_decode($customer->DeliveryTu);
        $customer->DeliveryWed = json_decode($customer->DeliveryWed);
        $customer->DeliveryTh = json_decode($customer->DeliveryTh);
        $customer->DeliveryFri = json_decode($customer->DeliveryFri);
        $customer->DeliverySat = json_decode($customer->DeliverySat);
        $customer->programmeType = DB::table('InfoCustomerPreference')->where('Titre', 'Type Customer')->where('CustomerId', $customer->CustomerID)->value('Value');
        $customer->address = DB::table('address')
                                ->select('Country as country', 'Town as city', 'postcode as postCode', 'address1', 'address2')
                                ->where('CustomerID', $customer->CustomerID)
                                ->whereNotIn('status', ['DEL', 'BILLING'])->first();
        if($customer->address == null){
            $customer->address = DB::table('NewAddress')
                                            ->select( 'City as city', 'postcode as postCode', 'address1', 'address2')
                                            ->where('CustomerID', $customer->CustomerID)
                                            ->whereNotIn('status', ['DEL', 'BILLING'])->first();
        }

        if($customer->paymentMethod == 1){
            $customer->card = DB::table('cards')->select('cardNumber', 'type', 'dateexpiration as expDate', 'cardHolderName', 'id' , 'Actif')
                                ->where('Actif' , 1)
                                ->where('CustomerID', $customer->CustomerID)->first();
        }else{
            // $customer->billing = DB::table('address')->where('CusotmerID', $customer->CustomerID)
            //                     ->select('')
        }

        $customer->billing = DB::table('address')->select('AddressID' , 'id' ,'address1', 'address2', 'postcode', 'Town')
                                ->where('CustomerID', $customer->CustomerID)
                                ->where('status', "BILLING")
                                ->first();

        $customer->invoice = DB::table('contacts')
        ->where('CustomerID', $customer->CustomerID)
        ->first();
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
                    ->where('title', '!=', 'Type Customer')
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
                                    ->orWhere(function($query) use ($customer) {
                                        $query->Where('CustomerID','=',$customer->CustomerIDMaster)
                                              ->Where('CustomerID','!=',"");
                                    })
                                    ->select(
                                        DB::raw('IF(isMaster = 1, "Main", "Sub") as accountType'),'FirstName as firstName', 'LastName as lastName',
                                        'Name as name', 'Phone as phone', 'EmailAddress as email',
                                        DB::raw('IF(SignupDateOnline = "2000-01-01", DATE_FORMAT(SignupDate, "%d/%m/%Y"), DATE_FORMAT(SignupDateOnline, "%d/%m/%Y")) as date'),
                                        'TotalSpend as spent', 'id'
                                    )->get();
        $user = Auth::user();
        $customer->current_user = null;
        if($user){
            $customer->current_user = $user;
        }
        $available_days = [];
        $available_slots = [];
        if($customer->address){
            $postcode = $customer->address->postCode;
            $postcode = str_replace(' ', '', $postcode);
            $postcode = substr($postcode, 0, -3);


            $tranche_details = [];
            $tranche_details = DB::table('tranchepostcode')
                ->where('Postcode', $postcode)
                ->where('tranche', '!=', '[]')
                ->get();

            $available_days = [];

            $days = ['MONDAY'=> 'DeliveryMon', 'TUESDAY'=>'DeliveryTu','WEDNESDAY'=>'DeliveryWed','THURSDAY'=>'DeliveryTh','FRIDAY'=>'DeliveryFri','SATURDAY'=>'DeliverySat'];

            $available_days = [];
            $available_slots = [];
            if(count($tranche_details) > 0){
                foreach ($days as $key => $day) {
                    $slot = $tranche_details->where('day', $key)->first();
                    if( $slot ){
                        $available_days[] = [
                            'name'      => substr($key, 0, 1),
                            'longName'  => ucfirst(strtolower(substr($key, 0, 3))),
                            'key'       => $day,
                            'active'    => false
                        ];
                        $available_slots[$day] = $this->formatTimeSlot(@json_decode($slot->tranche));
                    }
                }
            }
        }
        return response()->json([
            'customer'          => $customer,
            'available_slots'   => $available_slots,
            'available_days'    => $available_days,
        ]);
    }

    /**
     * Get booking timeslot
     *
     */
    public function getRecurringBookingTimeSlot(Request $request){
        $postcode = $request->postCode;

        $postcode = str_replace(' ', '', $postcode);
        $postcode = substr($postcode, 0, -3);


        $tranche_details = [];
        $tranche_details = DB::table('tranchepostcode')
            ->where('Postcode', $postcode)
            ->where('tranche', '!=', '[]')
            ->get();

        $available_days = [];

        $days = ['MONDAY'=> 'DeliveryMon', 'TUESDAY'=>'DeliveryTu','WEDNESDAY'=>'DeliveryWed','THURSDAY'=>'DeliveryTh','FRIDAY'=>'DeliveryFri','SATURDAY'=>'DeliverySat'];

        $available_days = [];
        $available_slots = [];
        if(count($tranche_details) > 0){
            foreach ($days as $key => $day) {
                $slot = $tranche_details->where('day', $key)->first();
                if( $slot ){
                    $available_days[] = [
                        'name'      => substr($key, 0, 1),
                        'longName'  => ucfirst(strtolower(substr($key, 0, 3))),
                        'key'       => $day,
                        'active'    => false
                    ];
                    $available_slots[$day] = $this->formatTimeSlot(@json_decode($slot->tranche));
                }
            }
        }/* else{
            foreach ($days as $key => $day) {
                $available_slots[$day] = [];
                $available_days = [];
            }
        } */
        return response()->json([
            'postcode'=> $postcode,
            'available_days'=> $available_days,
            'available_slots'=> $available_slots,
        ]);
    }

    public function formatTimeSlot($slots){
        $formated_slots = [];
        foreach ($slots as $slot) {
            $formated_slots[] = [
                'display'=> Tranche::getDeliveryPlanningTranchesForApi()[$slot],
                'value'=> $slot,
            ];
        }

        return $formated_slots;
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

        if(trim($request->cardHolderName)!=""&&trim($request->cardDetails)!=""&&trim($request->cardExpDate)!=""&&trim($request->cardCVV)!=""){
        $stripe = new \Stripe\StripeClient(env($stripe_key));

            //create a card object to stripe
            $card = $stripe->paymentMethods->create([
                'type' => 'card',
                'card' => [
                    'number'      => $request->cardDetails,
                    'exp_month'   => intval(explode('/', str_replace(' ','',$request->cardExpDate))[0]),
                    'exp_year'    => intval(explode('/', str_replace(' ','',$request->cardExpDate))[1]),
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

            $si = $stripe->setupIntents->create([
                'customer' => $stripe_customer->id,
                'payment_method_types' => ['card'],
            ]);


            //add a new record to cards table
                $credit_card = [
                    'CustomerID'        => $request->customerID,
                    'cardHolderName'    => $request->cardHolderName,
                    'type'              => $card->card->brand,
                    'Actif'             => 1 ,
                    'cardNumber'        => substr_replace($request->cardDetails, str_repeat('*', strlen($request->cardDetails) - 6), 3, -3),
                    'dateexpiration'    => str_replace(' ','',$request->cardExpDate),
                    'stripe_customer_id'=> $stripe_customer->id,
                    'stripe_card_id'    => $card->id,
                    'setup_intnet_id'   => $si->id,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
                $credit_card_id = DB::table('cards')->insertGetId($credit_card);
                return response()->json( $credit_card_id );

            }else{
                DB::table('infoCustomer')->where('CustomerID','=',$request->customerID)->update(array('bycard'=>1));
            }

            return response()->json( 0 );

    }

    public function DeleteCreditCard(Request $request){
        $c=  DB::table('cards')
        ->where('cards.id', $request->id)->first();

        $card = DB::table('cards')
        ->where('cards.CustomerID', $c->CustomerID)->update(['Actif' => 0]);
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
                    'OnAccount'=>$request->CustomerPayemenProfile,
                    'CompanyName'=>$request->CompanyName,
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

            if($addr){
                $updated = DB::table('address')->where('CustomerID',$cust->CustomerID)->where('status','DELIVERY')->update([
                    'status'=>'DEL'
                ]);
            }

             // add a new record to address table
            $address = [
                'CustomerID'    => $cust->CustomerID,
                'AddressID'     => '',
                'Town'          => $request->city,
                'County'        => $request->county,
                'Country'       => 'GB',
                'postcode'      => $request->postcode,
                'address1'      => $request->address1,
                'address2'      => $request->address2,
                'status'        => 'DELIVERY',
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
                try {
                    $addressId = DB::table('address')->insertGetId($address);
                } catch (\Exception $e) {
                    return response()->json(['error'=> $e->getMessage()]);
                }

            // add a new record to new address table
           $new_address_id = [
                'AddressID'=>'',
                'CustomerID'=>$cust->CustomerID,
                'address1'=>$request->address1,
                'address2'=>$request->address2,
                'postcode'=>$request->postcode,
                'City'=>$request->city,
                'Status'=>'NEW',
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
                try {
                    $addressId = DB::table('NewAddress')->insertGetId($new_address_id);
                } catch (\Exception $e) {
                    return response()->json(['error'=> $e->getMessage()]);
                }
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

    public function saveCustomerCommunication(Request $request){

        $success = DB::table('infoCustomer')->where('id',$request->customerId)->update([
            'AcceptMarketing' => $request->acceptMarketing,
            'AcceptSMSMarketing' => $request->acceptSMSMarketing
        ]);

        return response()->json([
            'success'   =>  $success
        ]);
    }

    public function saveCustomerRecurring(Request $request){
        $info_customer['DeliverybyDay'] = $request->deliveryByday;
        $info_customer['DeliveryMon'] = '';
        $info_customer['DeliveryTu'] = '';
        $info_customer['DeliveryWed'] = '';
        $info_customer['DeliveryTh'] = '';
        $info_customer['DeliveryFri'] = '';
        $info_customer['DeliverySat'] = '';
        $info_customer['DeliverySat'] = '';
        $info_customer['PauseDateTo'] = null;
        $info_customer['PauseDateFrom'] = null;

        $has_recurring = [];
        $success = false;

        $array_map = [
            'DeliveryMon'=>'MONDAY',
            'DeliveryTu'=>'TUESDAY',
            'DeliveryWed'=>'WEDNESDAY',
            'DeliveryTh'=>'THURSDAY',
            'DeliveryFri'=>'FRIDAY',
            'DeliverySat'=>'SATURDAY'
        ];
        $arr=[];
        foreach ($request->pickupSlots as $slot) {
            if(!is_null($slot['value'])){
                $arr[]= $array_map[$slot['key']].'_'.$slot['value'][0];
                $info_customer[$slot['key']] = '['.$slot['value'][0].']';
                $has_recurring[$slot['key']] = '['.$slot['value'][0].']';
            }
        }

        $infocustomer = DB::table('infoCustomer')->where('id',$request->customerId)->first();
        if($infocustomer && !empty($has_recurring)){
            //Add template


            $success = DB::table('infoCustomer')->where('id',$request->customerId)->update($info_customer);

            $mail_vars = [];


                $addr = DB::table('address')
                    ->where('CustomerID',$infocustomer->CustomerID)
                    ->where('status','DELIVERY')
                    ->first();

                $full_address = "";

                if($addr) {
                    $full_address = $addr->address1 . ($addr->address2 != '' ? ", " . $addr->address2 : "") . ", " . $addr->postcode . ", " . $addr->Town . (!is_null($addr->County) ? ", " . $addr->County : "") . (!is_null($addr->Country) ? ", " . $addr->Country : "") ;
                }

                $pickups = [];

                foreach($arr as $k=>$v){
                    $part = explode("_",$v);
                    $tranche = Delivery::getTrancheByIndex($part[1]);
                    $day = $part[0];

                    $pickups[] = ucfirst(strtolower($day))."s from ".Tranche::formatToAmPm($tranche['from'],$tranche['to']);

                }

                $mail_vars = [
                    'FirstName'=>$infocustomer->FirstName,
                    'CreatedOn'=>date('l d F @H:i:s'),
                    'FullName'=>$infocustomer->Name,
                    'UserAddress'=>$full_address,
                    'Frequency'=>count($arr),
                    'PickUpSlot'=>(isset($pickups[0])?$pickups[0]:''),
                    'PickUpSlot2'=>(isset($pickups[1])?$pickups[1]:''),

                ];

            NotificationController::Notify($infocustomer->EmailAddress, '+123456789', '4A_RECURRING_CONFIRM', '', $mail_vars, false, 0, $infocustomer->CustomerID);

            //Create recurring
            OrderRecurringCreator::processRecurringOrders('SAVE RECCURING BOOKING',$infocustomer->CustomerID);
        }


        if($request->deliveryByday==0){
            $success = DB::table('infoCustomer')->where('id',$request->customerId)->update($info_customer);
            OrderRecurringCreator::processRecurringOrders('DEL RECCURING BOOKING',$infocustomer->CustomerID);
            NotificationController::Notify($infocustomer->EmailAddress, '+123456789', '4C_RECURRING_CANCELLED', '', ['FirstName'=>$infocustomer->FirstName], false, 0,$infocustomer->CustomerID);
        }


        return response()->json([
            'success'=>$success,
            'post'=>$request->all(),
            'has_recurring'=>$has_recurring,
        ]);
    }
    /**
     * Pause Customer recurring
     */
    public function pauseCustomerRecurring(Request $request){
        $success = DB::table('infoCustomer')->where('id', $request->customerId)->update([
            'PauseDateTo'   => $request->pauseDateTo,
            'PauseDateFrom' => $request->pauseDateFrom,
        ]);
        $infocustomer = DB::table('infoCustomer')->where('id',$request->customerId)->first();
        OrderRecurringCreator::processRecurringOrders('PAUSE RECCURING BOOKING',$infocustomer->CustomerID);
        return response()->json($success);
    }

    /**
     * Pause Customer recurring
     */
    public function unpauseCustomerRecurring(Request $request){
        $success = DB::table('infoCustomer')->where('id',$request->customerId)->update([
            'PauseDateTo'=> null,
            'PauseDateFrom'=> null,
        ]);
        $infocustomer = DB::table('infoCustomer')->where('id',$request->customerId)->first();
        OrderRecurringCreator::processRecurringOrders('UNPAUSE RECCURING BOOKING',$infocustomer->CustomerID);
        return response()->json($success);
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
                    ->whereNotIn('infoOrder.Status', ['FULFILLED', 'DELIVERED', 'CANCEL', 'DELETE', 'VOID'])
                    ->groupBy('infoOrder.id')
                    ->orderBy('infoitems.PromisedDate', 'DESC')
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
                    ->whereIn('infoOrder.Status', ['FULFILLED', 'DELIVERED', 'CANCEL', 'DELETE', 'VOID'])
                    ->groupBy('infoOrder.id')
                    ->orderBy('infoitems.PromisedDate', 'DESC')
                    ->get();
        //*/

        }

        return response()->json($customer);
    }


    public function getArCustomers(Request $request){
        $customers = DB::table('infoCustomer')
            ->where('OnAccount',1)
            ->get();

        $bacs_cust_id = [];
        $list = [];

        if(count($customers) > 0){
            foreach($customers as $k=>$v){
                $bacs_cust_id[] = $v->CustomerID;
            }
        }

        $grouped_by_cust_id = [];
        $grouped_by_cust_order_date = [];
        $custid_with_orders = [];
        $master_cust = [];

        $orders = DB::table('infoOrder')
            ->select('infoOrder.id as order_id','infoOrder.created_at','infoOrder.Total','infoOrder.CustomerID','infoOrder.OrderDiscount')
            ->join('detailingitem','infoOrder.id','detailingitem.order_id')
            ->join('NewInvoice','NewInvoice.order_id','infoOrder.id')
            ->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
            ->join('infoitems','infoInvoice.InvoiceID','infoitems.InvoiceID')
            ->where('infoOrder.orderinvoiced',0)
            ->whereNotIn('infoInvoice.Status',['DELETE', 'DELETED', 'VOID', 'VOIDED', 'CANCEL', 'CANCELED'])
            ->whereIn('infoOrder.CustomerID',$bacs_cust_id)
            ->get();


        foreach($orders as $k=>$v){
            $grouped_by_cust_id[$v->CustomerID][$v->order_id] = $v->Total;
            $grouped_by_cust_order_date[$v->CustomerID][] = $v->created_at;

            if(!in_array($v->CustomerID,$custid_with_orders)){
                array_push($custid_with_orders,$v->CustomerID);
            }
        }


        foreach($grouped_by_cust_order_date as $k=>$v){
            usort($grouped_by_cust_order_date[$k],function($a, $b) {
                return strtotime($b) - strtotime($a);
            });
        }

        $cust_with_orders = DB::table('infoCustomer')->whereIn('CustomerID',$custid_with_orders)->get();


        if(count($cust_with_orders) > 0){
            foreach($cust_with_orders as $k=>$v){
                if($v->CustomerIDMaster !=''){
                    $master_cust[$v->CustomerID] = $v->CustomerIDMaster;
                }
            }
        }


        foreach($grouped_by_cust_id as $k=>$v){
            if(isset($master_cust[$k])){
                $list[$master_cust[$k]]['order_total'][] = array_sum($v);
            }else{
                $list[$k]['order_total'][] = array_sum($v);
            }
        }


        foreach($grouped_by_cust_order_date as $k=>$v){
            if(isset($master_cust[$k])){
                $list[$master_cust[$k]]['order_date'][] = (isset($v[0])?$v[0]:"");
            }else{
                $list[$k]['order_date'][] = (isset($v[0])?$v[0]:"");
            }
        }

        $final_cust_id = [];
        foreach($list as $k=>$v){
            $final_cust_id[] = $k;
        }

        $final_cust = DB::table('infoCustomer')->whereIn('CustomerID',$final_cust_id)->get();

        $final_cust_addr = DB::table('address')->whereIn('CustomerID',$final_cust_id)->where('status','BILLING')->get();
        $final_customers = [];


        if(count($final_cust) > 0){
            foreach($final_cust as $k=>$v){
                if(isset($list[$v->CustomerID])){
                    /*
                        IF(infoCustomer.CustomerIDMaster = "" AND infoCustomer.CustomerIDMasterAccount = "" AND infoCustomer.IsMaster = 0 AND infoCustomer.IsMasterAccount = 0
                    */

                    $ctype = "B2C";
                    if($v->CustomerIDMaster=='' && $v->CustomerIDMasterAccount=='' && $v->IsMaster==0 && $v->IsMasterAccount==0){
                        $ctype = "B2B";
                    }
                    $list[$v->CustomerID]['id'] = $v->id;
                    $list[$v->CustomerID]['type'] = ($v->btob==0?"B2C":"B2B");
                    $list[$v->CustomerID]['active_in'] = $v->TypeDelivery;
                    $list[$v->CustomerID]['name'] = $v->Name;
                    $list[$v->CustomerID]['email'] = $v->EmailAddress;
                    $list[$v->CustomerID]['phone'] = $v->Phone;

                    if(($v->CustomerIDMaster==''|| $v->IsMaster==1) && $v->IsMasterAccount== 0){
                        $list[$v->CustomerID]['level'] = "Main";
                    } else if ($v->CustomerIDMaster!=''){
                        $list[$v->CustomerID]['level'] = "Sub";
                    } else if ($v->IsMasterAccount== 1){
                        $list[$v->CustomerID]['level'] = "Master";
                    }
                }
            }
        }

        if(count($final_cust_addr) > 0){
            foreach($final_cust_addr as $k=>$v){
                if(isset($list[$v->CustomerID])){
                    $list[$v->CustomerID]['address1'] = $v->address1;
                    $list[$v->CustomerID]['postcode'] = $v->postcode;
                }
            }
        }


        foreach($list as $k=>$v){
        $order_total = $list[$k]['order_total'];
        $list[$k]['order_total'] = array_sum($order_total);

        usort($list[$k]['order_date'],function($a, $b) {
                return strtotime($b) - strtotime($a);
            });
        }

        foreach($list as $k=>$v){
            $list[$k]['last_order_date'] = "";

            if(isset($list[$k]['order_date'][0])){
                $list[$k]['last_order_date'] = date("d/m/y",strtotime($list[$k]['order_date'][0]));
            }
        }

        return response()->json([
            'list'=>$list,
        ]);
    }
    public function setCustomerSmsDelivery(Request $request){

        foreach ($request->listCustomers as $customer) {
            try {

                  DB::table('infoCustomer')->where('CustomerID', $customer)->update(['SMS6I' => 1]);

                  $ListItems = DB::table('infoInvoice')->select('infoInvoice.InvoiceID', 'infoitems.id' , 'infoitems.CCStatus')
                  ->join('infoitems',function($join){
                    $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                        ->where('infoitems.CCStatus','=','No Delivery Date');
                    })
                  ->where('infoInvoice.CustomerID' , $customer)->get();

                  if(!empty($ListItems)){

                    foreach($ListItems as $item){
                        DB::table('infoitems')->where('infoitems.id', $item->id)->update(['CCStatus' => ""]);
                      }
                  }

            } catch (\Exception $e) {
                return response()->json(['error'=> $e->getMessage()]);
            }
        }
        return response()->json(['message'=>'OK']);

    }

    public function getCurrentUser(Request $request){
        return response()->json([
            'user'=>Auth::user(),
        ]);
    }

    public static function logInfoOrderPrint($customer_ids,$emailed=false){
        $all_customer_ids = [];
        $cust_master_ids = [];
        $map_master_id = [];
        $map_sub_id = [];
        $order_per_customer = [];
        $row_ids = [];

        $customers = DB::table('infoCustomer')->whereIn('id',$customer_ids)->get();

        if(count($customers) > 0){
            foreach($customers as $k=>$v){
                if($v->IsMaster==0){
                    $all_customer_ids[] = $v->CustomerID;
                }else{
                    $cust_master_ids[] = $v->CustomerID;
                }
            }
        }

        if(!empty($cust_master_ids)){
            $sub_customers = DB::table('infoCustomer')->WhereIn('CustomerIDMaster',$cust_master_ids)->get();

            if(count($sub_customers) > 0){
                foreach($sub_customers as $k=>$v){
                    $all_customer_ids[] = $v->CustomerID;
                    $map_master_id[$v->CustomerIDMaster][] = $v->CustomerID;
                    $map_sub_id[$v->CustomerID] = $v->CustomerIDMaster;
                }
            }

            $all_customer_ids = array_merge($all_customer_ids,$cust_master_ids);
        }


        $orders = DB::table('infoOrder')
                ->select('infoCustomer.Name','infoOrder.id as order_id','infoOrder.created_at','infoOrder.Total','infoOrder.CustomerID','NewInvoice.InvoiceID AS Invoice_id','infoInvoice.*','infoitems.*')
                ->join('detailingitem','infoOrder.id','detailingitem.order_id')
                ->join('NewInvoice','NewInvoice.order_id','infoOrder.id')
                ->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
                ->join('infoCustomer','infoOrder.CustomerID','infoCustomer.CustomerID')
                ->join('infoitems','infoInvoice.InvoiceID','infoitems.InvoiceID')
                ->whereNotIn('infoInvoice.Status',['DELETE', 'DELETED', 'VOID', 'VOIDED', 'CANCEL', 'CANCELED'])
                ->where('infoOrder.orderinvoiced',0)
                ->whereIn('infoOrder.CustomerID',$all_customer_ids)
                ->get();

        $invoices_per_order = [];
        $grouped_by_customer = [];
        $total_per_order = [];
        $simplified_invoices_per_order = [];

        $all_orders = [];

        foreach($orders as $k=>$v){
            $total_per_order[$v->order_id] = $v->Total;

            if(!in_array($v->order_id,$all_orders)){
                array_push($all_orders,$v->order_id);
            }

           $invoices_per_order[$v->order_id][$v->InvoiceID][$v->ItemID] = [
                                                                                'NumInvoice'=>$v->NumInvoice,
                                                                                'InvoiceID'=>$v->InvoiceID,
                                                                                'Tracking'=>$v->ItemTrackingKey,
                                                                                'PromisedDate'=>$v->created_at,
                                                                                'Department'=>$v->DepartmentName,
                                                                                'Description'=>$v->typeitem,
                                                                                'brand'=>$v->brand,
                                                                                'priceTotal'=>$v->priceTotal,
                                                                                'CustomerID'=>$v->CustomerID,
                                                                                'order_id'=>$v->order_id,
                                                                            ];
            $order_per_customer[$v->CustomerID][] = $v->order_id;
        }


        foreach($invoices_per_order as $orderid=>$invoice){
            foreach($invoice as $itemid=>$item){
                foreach($item as $id=>$val){
                    $simplified_invoices_per_order[$orderid][] = $val;
                }
            }
        }


        foreach($order_per_customer as $k=>$v){
            $order_per_customer[$k] = array_unique($v);
        }


        foreach($order_per_customer as $k=>$v){
            if(isset($map_sub_id[$k])){
               $grouped_by_customer[$map_sub_id[$k]][] = $v;
            }else{
                $grouped_by_customer[$k][] = $v;
            }
        }

        $simplified_grouped_by_customer = [];

        foreach($grouped_by_customer as $k=>$v){
            foreach($v as $id=>$val){
                foreach($val as $index=>$orderid){
                    $simplified_grouped_by_customer[$k][] = $orderid;
                }
            }
        }

        $to_insert = [];

        foreach($simplified_grouped_by_customer as $k=>$v){
            $total = 0;
            $info = [];
            $orders = [];
            foreach($v as $index=>$orderid){
                $total += $total_per_order[$orderid];
                $info[$orderid] = $simplified_invoices_per_order[$orderid];
                $orders[] = $orderid;
            }

            $to_insert = [
                'FactureID'=>'',
                'CustomerID'=>$k,
                'infoOrder_id'=>json_encode($orders),
                'montant'=>$total,
                'info'=>json_encode($info),
                'emailed'=>($emailed?1:0),
                'created_at'=>date('Y-m-d H:i:s'),
            ];

            $row_id = DB::table('infoOrderPrint')->insertGetId($to_insert);
            $row_ids[] = $row_id;

            $num_facture = 'INV'.date('Ymd').'-'.sprintf('%04d', $row_id);

            //To add notification

            DB::table('infoOrderPrint')->where('id',$row_id)->update(['NumFact'=>$num_facture]);

        }

        if($emailed){
            DB::table('infoOrder')->where('id',$all_orders)->update(['orderinvoiced'=>1]);
        }

        return $row_ids;

    }


    public static function getArPDFData($details){
        $customer_ids = [];
        $grouped_by_customer = [];
        $cust_names = [];
        $cust_addresses = [];

        $customer = DB::table('infoCustomer')->where('CustomerID',$details->CustomerID)->first();
        $addr = Delivery::getAddressByCustomerUUID($details->CustomerID,true);

        $contact = false;

        if($addr){
            $contact = DB::table('contacts')->where('address_id',$addr->id)->first();
        }

        $order_details = (array) @json_decode($details->info);

        if(count($order_details) > 0){
            foreach($order_details as $k=>$v){
                foreach($v as $id=>$detail){
                    if(!in_array($detail->CustomerID,$customer_ids)){
                        array_push($customer_ids,$detail->CustomerID);
                    }
                    $grouped_by_customer[$detail->CustomerID][$detail->order_id][$detail->InvoiceID][] = $detail;
                }
            }
        }


        $customers = DB::table('infoCustomer')->whereIn('CustomerID',$customer_ids)->get();


        foreach($customers as $k=>$v){
            $cust_names[$v->CustomerID] = $v->Name;
        }


        $order_details = [];
        $order_totals = [];

        $facture_net = [];
        $facture_amount_net = 0;

        $orderids = [];

        foreach($grouped_by_customer as $customerid=>$orders){
            $order_net = 0;
            $order_vat = 0;
            $order_total = 0;

            $orderid_per_customer = [];

           foreach($orders as $orderid=>$invoices){
                if(!in_array($orderid,$orderid_per_customer)){
                    array_push($orderid_per_customer,$orderid);
                }


                $orderids[] = $orderid;

                foreach($invoices as $invoiceid=>$items){

                    $inv = DB::table('infoInvoice')->where('InvoiceID',$invoiceid)->first();
                    $order_details[$customerid][$invoiceid] = [];

                    if($inv && !in_array($inv->Status,['DELETE', 'DELETED', 'VOID', 'VOIDED', 'CANCEL', 'CANCELED'])){


                        $dept = [];
                        $net = 0;
                        $vat = 0;
                        $total = 0;
                        $items_text = [];
                        $promised_dates = [];



                        foreach($items as $k=>$v){
                            $promised_dates[] = $v->PromisedDate;

                            $item_txt = $v->brand." ".str_replace(' ',' ',$v->Description);

                            $dept[$v->Department][] = $item_txt;
                            $total += $v->priceTotal;
                        }
                        $items_per_dept[$v->Department] = array_count_values($dept[$v->Department]);

                        usort($promised_dates,function($a,$b){
                            return strtotime($b) - strtotime($a);
                        });

                        $order_total += $total;
                        $net = $total/1.2;

                        $vat = $total - $net;

                        $order_details[$customerid][$invoiceid]['orderid'] = $orderid;
                        $order_details[$customerid][$invoiceid]['date'] = (isset($promised_dates[0]) && $promised_dates[0]!='0000-00-00'?date('d/m/y',strtotime($promised_dates[0])):"--");
                        $order_details[$customerid][$invoiceid]['items'] = $items_per_dept;
                        $order_details[$customerid][$invoiceid]['net'] = number_format($net,2);
                        $order_details[$customerid][$invoiceid]['vat'] = number_format($vat,2);
                        $order_details[$customerid][$invoiceid]['total'] = number_format($total,2);
                        $order_details[$customerid][$invoiceid]['numinvoice'] = $inv->NumInvoice;


                    }
                }
           }

           foreach($order_details as $customerid=>$invoices){
            foreach($invoices as $invoiceid=>$invoice){
                if(empty($invoice)){
                    unset($order_details[$customerid][$invoiceid]);
                }
            }

            if(empty($order_details[$customerid])){
                unset($order_details[$customerid]);
            }
           }

           $discount_per_customer = 0;

           $orders_per_cust = DB::table('infoOrder')->whereIn('id',$orderid_per_customer)->get();

           foreach($orders_per_cust as $ok=>$oc){
            $discount_per_customer += $oc->OrderDiscount;
           }

           $order_total_exc_discount = $order_total;
           $order_total = $order_total - $discount_per_customer;

           $order_net = $order_total_exc_discount/1.2;
           $facture_total[] = $order_total;


           $order_vat = $order_total_exc_discount - $order_net;



           $order_totals[$customerid]['order_net'] = number_format($order_net,2);
           $order_totals[$customerid]['order_vat'] = number_format($order_vat,2);
           $order_totals[$customerid]['order_total'] = number_format($order_total,2);
           $order_totals[$customerid]['discount'] = number_format($discount_per_customer,2);
           $order_totals[$customerid]['order_without_discount'] = number_format($order_total_exc_discount,2);

        }

        $orders = DB::table('infoOrder')->whereIn('id',$orderids)->get();
        $discount = 0;
        /*
        if(count($orders) > 0){
            foreach($orders as $k=>$v){
                $discount += $v->OrderDiscount;
            }
        }
        */

        $facture_amount_total = array_sum($facture_total);
        $discounted_amount = $facture_amount_total - $discount;
        $facture_amount_net = $discounted_amount/1.2;

        $facture_amount_vat = $discounted_amount - $facture_amount_net;

        $data = [
           'customer'=>$customer,
           'address'=>$addr,
           'contact'=>$contact,
           'grouped_by_customer'=>$grouped_by_customer,
           'cust_names'=>$cust_names,
           'invoice_date'=>date('d/m/Y'),
           'facture'=>$details,
           'order_details'=>$order_details,
           'order_totals'=>$order_totals,
           'facture_net'=>number_format($facture_amount_net,2),
           'facture_discount'=>number_format($discount,2),
           'facture_vat'=>number_format($facture_amount_vat,2),
           'facture_total'=>number_format($facture_amount_total,2),
           'date_due'=>date('d/m/Y',strtotime('+15day')),
        ];

        return $data;

    }


    public function generateArInvoice(Request $request){
        if(!is_dir(storage_path('app/pdf'))){
            mkdir(storage_path('app/pdf'),0777);
        }


        $customer_ids = @json_decode($request->customer_ids);
        $type = $request->type;
        $has_rows = $request->has_rows;

        $row_ids = @json_decode($request->row_ids);

        if($has_rows==0){
            $row_ids = CustomerController::logInfoOrderPrint($customer_ids,($type=='mail'?true:false));
        }


        $all_details = DB::table('infoOrderPrint')->whereIn('id',$row_ids)->get();
        $details_per_cust = [];

        if($type=='pdf'){
            $path = glob(storage_path('app/pdf/').'*.*');

            //Remove previous files
            if(!empty($path)){
                foreach($path as $k=>$v){
                    unlink($v);
                }
            }


            foreach($all_details as $key=>$details){
                $details_per_cust[] = CustomerController::getArPDFData($details);
            }

            foreach($details_per_cust as $k=>$v){
                if(empty($v['order_details'])){
                    unset($details_per_cust[$k]);
                }
            }


            if(!empty($details_per_cust)){

                Pdf::setOptions(['dpi' => 300, 'defaultFont' => 'Helvetica']);

                $arrfiles = [];

                foreach($details_per_cust as $k=>$v){
                    $data['v'] = $v;
                    $pdf = Pdf::loadView('pdf/ar_pdf', $data);

                    $pdf->output();

                    $canvas = $pdf->getDomPDF()->getCanvas();

                    $fontMetrics = $pdf->getDomPDF()->getFontMetrics();
                    $font = $fontMetrics->getFont('Times-Roman');

                    $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
                    $size = 10;

                    $width = $fontMetrics->getTextWidth($text, $font, $size) / 2;

                    $x = $canvas->get_width() - $width;
                    $y = $canvas->get_height() - 35;

                    $canvas->page_text($x, $y, $text , $font, 10, array(0, 0, 0));

                    //return $pdf->download('invoice'.$details->CustomerID.'.pdf');


                    $filename = 'invoice_'.$k.'_'.strtotime('now').'.pdf';


                    $pdfstr=$pdf->output();
                    Storage::disk('local')->put('pdf'.DIRECTORY_SEPARATOR.$filename, $pdfstr);

                    $arrfiles[] = storage_path('app/pdf/').$filename;
                }

                $files = implode(" ",$arrfiles);

                $output_file = storage_path('app/pdf/').'invoices'.strtotime('now').'.pdf';

                //print_r($files);

                shell_exec("gs -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE=$output_file -dBATCH $files");

                //Remove individual files
                foreach($arrfiles as $k=>$v){
                    unlink($v);
                }

                $url = 'pdf'.DIRECTORY_SEPARATOR.basename($output_file);

                return response()->json(
                    ['url'=>route('download-ar-pdf',['filename'=>$url])]
                );

                /*
                if(count($row_ids)==1){
                    $data['v'] = $details_per_cust[0];
                    $pdf = Pdf::loadView('pdf/ar_pdf', $data);

                }else{

                    $data = [
                        'details_per_cust'=>$details_per_cust,
                    ];

                    $pdf = Pdf::loadView('pdf/ar_all_pdf', $data);
                }

                $pdf->output();

                $canvas = $pdf->getDomPDF()->getCanvas();

                $fontMetrics = $pdf->getDomPDF()->getFontMetrics();
                $font = $fontMetrics->getFont('Times-Roman');

                $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
                $size = 10;

                $width = $fontMetrics->getTextWidth($text, $font, $size) / 2;

                $x = $canvas->get_width() - $width;
                $y = $canvas->get_height() - 35;

                $canvas->page_text($x, $y, $text , $font, 10, array(0, 0, 0));

                //return $pdf->download('invoice'.$details->CustomerID.'.pdf');

                $filename = 'invoices_'.strtotime('now').'.pdf';

                if(count($row_ids)==1){
                    $filename = 'invoice_'.$row_ids[0].'_'.strtotime('now').'.pdf';
                }

                $pdfstr=$pdf->output();
                Storage::disk('local')->put('pdf'.DIRECTORY_SEPARATOR.$filename, $pdfstr);
                $url = 'pdf'.DIRECTORY_SEPARATOR.$filename;

                return response()->json(
                    ['url'=>route('download-ar-pdf',['filename'=>$url])]
                );
                */
            }
        }

        $sent = false;

        if($type=='mail'){

            foreach($all_details as $k=>$v){
                $cust = DB::table('infoCustomer')->where('CustomerID',$v->CustomerID)->first();

                $addr = Delivery::getAddressByCustomerUUID($cust->CustomerID,true);

                $contact = false;

                if($addr){
                    $contact = DB::table('contacts')->where('address_id',$addr->id)->first();
                }


                $info = @json_decode($v->info);

                $orderid = 0;
                $i = 0;
                foreach($info as $order_id=>$invoices){
                    if($i==0){
                        $orderid = $order_id;
                    }
                    $i++;
                }


                $order_url = "http://app.blancliving.co/order-store?email=".$cust->EmailAddress."&Id=".$cust->CustomerID."&orderId=".$orderid;

                if($cust){
                    $mail_vars = [
                        'FirstName'=>$cust->FirstName,
                        'url_invoice'=>\Illuminate\Support\Facades\URL::to("/inv-pdf")."?id=".$v->FactureID,
                        'url_order'=>$order_url,
                    ];
                }

                $email = $cust->EmailAddress;
                if($contact){
                    if($contact->email!=''){
                        $email = $contact->email;
                    }
                    else if($contact->email=='' && $contact->email2!=''){
                        $email = $contact->email2;
                    }
                }

                $sent = NotificationController::Notify($email, '+123456789', '5K_EMAIL_B2B_INVOICE', '', $mail_vars, true, 0, '');
            }
        }

        return response()->json([
            'row_ids'=>$row_ids,
            'customer_ids'=>$customer_ids,
            'type'=>$type,
            'details_per_cust'=>$details_per_cust,
            'sent'=>$sent,
        ]);
    }

    public function downloadArPdf(Request $request){
        $filename=$request->get('filename');
        return Storage::download($filename);
    }

    public function updateCustomerBillingAddress(Request $request){
        $customer_id = $request->customer_id;

        $updated = false;
        $cust = DB::table('infoCustomer')->where('id',$customer_id)->first();

        if($cust){
            $addr = DB::table('address')->where('CustomerID',$cust->CustomerID)->where('status','BILLING')->first();

            if($addr){
                $updated = DB::table('address')->where('CustomerID',$cust->CustomerID)->where('status','BILLING')->update([
                    'address1'=>$request->address1,
                    'address2'=>$request->address2,
                    'postcode'=>$request->postcode,
                    'Town'=>$request->city,
                    'updated_at'    => now(),
                ]);
            }else{

            $billing_address = [
                'CustomerID'    => $cust->CustomerID,
                'AddressID'     => '',
                'longitude'     => $request->customerLon,
                'Latitude'      => $request->customerLat,
                'Town'          => $request->companyCity,
                'County'        => $request->companyCounty,
                'Country'       => 'GB',
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
                return response()->json(['error'=> $e->getMessage()]);
            }
        }
    }

        return response()->json([
            'post'=>$request->all(),
            'updated'=>$updated,
        ]);
    }

    public function updateInvoiceDetails(Request $request){
        $customer_id = $request->customer_id;

        $updated = false;
        $cust = DB::table('infoCustomer')->where('id',$customer_id)->first();

        if($cust){
            $contact = DB::table('contacts')->where('CustomerID',$cust->CustomerID)->first();
            if($contact ){
                $updated = DB::table('contacts')->where('CustomerID',$cust->CustomerID)->update([
                    'company'=>$request->company,
                    'email'=>$request->email,
                    'email2'=>$request->email2,
                    'Phone'=>$request->Phone,
                    'firstname'=>$request->firstname,
                    'name'=>$request->name,
                    'updated_at'    => now(),
                ]);
            }else{

                $contact = [
                    'CustomerID'    => $customer_id,
                    'address_id'    => '',
                    'name'          => $request->name,
                    'firstname'     => $request->firstname,
                    'company'       => $request->company,
                    'email'         => $request->email,
                    'email2'         => $request->email2,
                    'Phone'         => $request->companyPhoneNumber != '' ? '["'.$request->companyPhoneCountryCode.'|'.$request->companyPhoneNumber.']"' : '',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
                try {
                    DB::table('contacts')->insert($contact);
                } catch (\Exception $e) {
                    return response()->json(['error'=> $e->getMessage()]);
             }
        }
    }

        return response()->json([
            'post'=>$request->all(),
            'updated'=>$updated,
        ]);
    }

    public function getArInvoicedCustomers(Request $request){
        $customers = DB::table('infoOrderPrint')
            ->select('*','infoOrderPrint.id as row_id')
            ->join('infoCustomer','infoOrderPrint.CustomerID','infoCustomer.CustomerID')
            ->where('infoOrderPrint.emailed',1)->get();

        $list = [];

        $customer_ids = [];
        $addr = [];

        if(count($customers) > 0){
            foreach($customers as $k=>$v){
                if(!in_array($v->CustomerID,$customer_ids)){
                    array_push($customer_ids,$v->CustomerID);
                }
            }

            $addresses = DB::table('address')->where('status','BILLING')->whereIn('CustomerID',$customer_ids)->get();

            foreach($addresses as $k=>$v){
                $addr[$v->CustomerID] = $v;
            }

            foreach($customers as $k=>$v){


                $level = "";
                if(($v->CustomerIDMaster==''|| $v->IsMaster==1) && $v->IsMasterAccount== 0){
                    $level = "Main";
                } else if ($v->CustomerIDMaster!=''){
                    $level = "Sub";
                } else if ($v->IsMasterAccount== 1){
                    $level = "Master";
                }

                $orders = @json_decode($v->infoOrder_id);

                $list[] = [
                    'id'=>$v->row_id,
                    'NumFact'=>$v->NumFact,
                    'type'=>($v->btob==0?"B2C":"B2B"),
                    'active_in'=>$v->TypeDelivery,
                    'name'=>$v->Name,
                    'email'=>$v->EmailAddress,
                    'phone'=>$v->Phone,
                    'level'=>$level,
                    'orders'=>implode(",",$orders),
                    'order_total'=>$v->montant,
                    'url_invoice'=>\Illuminate\Support\Facades\URL::to("/inv-pdf")."?id=".$v->FactureID,
                ];

            }
        }

        return response()->json([
            'list'=>$list,
        ]);
    }

    public function generateInvoicedPdf(Request $request){
        return response()->json([
            'post'=>$request->all(),
        ]);
    }
}
