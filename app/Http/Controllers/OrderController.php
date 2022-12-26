<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\InfoCustomer;
use App\Http\Controllers\BookingController;
use App\Models\OrderRecurringCreator;
use App\Models\Delivery;
use App\Http\Controllers\DetailingController;
use App\Models\Tranche;
use Exception;
use stdClass;

class OrderController extends Controller
{

    public function preloadOrderFormInfo(Request $request){
            $TypeDeliveries=DB::table('infoCustomer')->select(DB::raw('DISTINCT TypeDelivery as delivery_methods'))->get();

            $delivery_methods=[];
            foreach ($TypeDeliveries as $del_method)
                $delivery_methods[]=$del_method->delivery_methods;

            return response()->json(array(
                'delivery_methods'=>$delivery_methods
            ));
    }

    public function createNewOrder(Request $request){
        $user = Auth::user();

        $new_order = $request->post('new_order');
        $customerid = $new_order['CustomerID'];
        $dropoff_stamp = $new_order['dropoff_stamp'];
        $address_uuid = $new_order['AddressID'];

        $new_order_id = 0;
        $created_stamp = date('Y-m-d H:i:s');
        $id_booking = 0;
        $id_master_pickup = 0;

        $recurring_data = [];
        $recur_obj = false;

        $is_sub_account = $new_order['sub_account_cust'];
        $main_account_booking_id = $new_order['sub_account_booking_id'];
        $main_account_booking_type = $new_order['sub_account_booking_type'];
        $payment_method = $new_order['payment_method'];


        $customer = DB::table('infoCustomer')->where('CustomerID',$new_order['CustomerID'])->first();
        $address = DB::table('address')->where('AddressID',$address_uuid)->first();

        if($customer && $payment_method=='BACS'){
            DB::table('infoCustomer')->where('id',$customer->id)->update(['bycard'=>0]);
        }

        $phones = [];
        $code_country = "";

        if(trim($customer->Phone) !=''){
            $phones_arr = @json_decode($customer->Phone);

            if(!empty($phones_arr)){
                foreach($phones_arr as $k=>$v){
                    $ph = explode("|",$v);

                    if(isset($ph[0])){
                        $code_country = "+".$ph[0];
                    }

                    if(isset($ph[1])){
                        $phones[] = $ph[1];
                    }
                }
            }
        }

        $previous_orders = InfoCustomer::getPreviousOrders($customerid);

        if($new_order['deliverymethod']=='in_store_collection'){
            $order_to_insert['DatePickup']= substr($dropoff_stamp,0,10);
            $order_to_insert['DateDeliveryAsk']= $new_order['isc_pickup'];
        }



        if($new_order['deliverymethod'] !='recurring'){
            $order_to_insert = [
                'deliverymethod'=>$new_order['deliverymethod'],
                'CustomerID'=>$customerid,
                'OrderID'=>'',
                'firstorder'=>(count($previous_orders)>0?0:1),
                'express'=>$new_order['express'],
                'Status'=>'SCHEDULED',//($new_order['deliverymethod']=='recurring'?'RECURRING':'PENDING'),
                'created_at'=>$created_stamp,
            ];

            //Fields for in_store_collection
            if($new_order['deliverymethod']=='in_store_collection'){
                $order_to_insert['TypeDelivery'] = $new_order['store_name'];
            }

            //Fields for delivery
            if(in_array($new_order['deliverymethod'],['delivery_only','home_delivery','recurring'])){
                $order_to_insert['TypeDelivery'] = 'DELIVERY';
            }

            if(isset($new_order['DeliveryaskID'])){
                $order_to_insert['DeliveryaskID'] = $new_order['DeliveryaskID'];
            }
            //else{
            //     $order_to_insert['express'] = 1;
            // }
            $new_order_id = DB::table('infoOrder')
                ->insertGetId($order_to_insert);
        }


        //Add in_store_collection booking details
        if($new_order['deliverymethod']=='in_store_collection'){
            $id_booking = DB::table('booking_store')->insertGetId([
                'order_id'=>$new_order_id,
                'customer_id'=>$customer->id,
                'CustomerID'=>$customerid,
                'user_id'=>$user->id,
                'store_name'=>$new_order['store_name'],
                'dropoff'=>$dropoff_stamp,
                'pickup_date'=>$new_order['isc_pickup'],
                'pickup_time'=>$new_order['isc_pickup_time'],
                'pickup_timeslot'=>$new_order['isc_pickup_timeslot'],
                'status'=>'NEW',
                'created_at'=>$created_stamp,
            ]);


            DB::table('infoOrder')->where('id',$new_order_id)->update([
                'DatePickup'=>substr($dropoff_stamp,0,10),
                'DateDeliveryAsk'=>$new_order['isc_pickup'],
            ]);


        }

        //Add booking for delivery only
        $delivery_arr = [];
        if($new_order['deliverymethod']=='delivery_only'){
            $slot = $new_order['do_delivery_timeslot'];

            $tranche = BookingController::getBookingDetailFromSlot($slot);

            if($is_sub_account){
                if($main_account_booking_type=='pickup'){
                    $id_master_pickup = $main_account_booking_id;
                }elseif($main_account_booking_type=='deliveryask'){
                    $id_booking = $main_account_booking_id;
                }
            }else if(!isset($new_order['DeliveryaskID'])){
                $delivery_arr = [
                    'PhoneNumber'=>(!empty($phones)?implode(",",$phones):""),
                    'CodeCountry'=>$code_country,
                    'TypeDelivery'=>'', //A confirmer
                    'AddressID'=>$address_uuid,
                    'CustomerID'=>$customerid,
                    'DeliveryAskID'=>'',
                    'id_customer'=>0,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'comment'=>(!is_null($new_order['customer_instructions'])?$new_order['customer_instructions']:''),
                    'trancheFrom'=>$tranche['tranchefrom'],
                    'trancheto'=>$tranche['trancheto'],
                    'address_id'=>($address?$address->id:0),
                    'date'=>$new_order['do_delivery'],
                    'status'=>'NEW',//'PMS'
                    'order_id'=>$new_order_id,

                ];
                if($new_order['do_delivery']){
                   $id_booking = DB::table('deliveryask')->insertGetId($delivery_arr);
                }
            }

            if($id_booking !=0){
                $booking_do = DB::table('deliveryask')->where('id',$id_booking)->first();

                DB::table('infoOrder')->where('id',$new_order_id)->update([
                    'DeliveryaskID'=>$booking_do->DeliveryaskID,
                    'DateDeliveryAsk'=>$new_order['do_delivery']
                ]);
            }

            if(isset($new_order['DeliveryaskID'])){
                if(!is_null($new_order['DeliveryaskID'])){
                    DB::table('infoOrder')->where('id',$new_order_id)->update([
                        'DeliveryaskID'=>$new_order['DeliveryaskID'],
                        'DateDeliveryAsk'=>$new_order['do_delivery']
                    ]);
             }
            }

            if($id_master_pickup !=0){
                $master_pickup =  DB::table('pickup')->where('id',$id_master_pickup)->first();

                DB::table('infoOrder')->where('id',$new_order_id)->update([
                    'PickupID'=>$master_pickup->PickupID,
                    'DatePickup'=>$new_order['do_delivery']
                ]);
            }



            //NOTIFICATION



            $save_future_instruction = $new_order['save_instruction_check'];

            if($save_future_instruction && !is_null($new_order['customer_instructions'])){
                DB::table('infoCustomer')->where('CustomerID',$customerid)->update(['commentDelivery'=>$new_order['customer_instructions']]);
            }
        }

        $id_booking_pickup = 0;


        //Add booking for home_Delivery
        if(!$is_sub_account && $new_order['deliverymethod']=='home_delivery'){

            $pickup_slot = $new_order['hd_pickup_timeslot'];
            $tranche_pickup = BookingController::getBookingDetailFromSlot($pickup_slot);


            $pickup_arr = [
                'PhoneNumber'=>(!empty($phones)?implode(",",$phones):""),
                'CodeCountry'=>$code_country,
                'TypeDelivery'=>'', //A confirmer
                'AddressID'=>$address_uuid,
                'CustomerID'=>$customerid,
                'PickupID'=>'',
                'id_customer'=>0,
                'created_at'=>date('Y-m-d H:i:s'),
                'comment'=>(!is_null($new_order['customer_instructions'])?$new_order['customer_instructions']:''),
                'trancheFrom'=>$tranche_pickup['tranchefrom'],
                'trancheto'=>$tranche_pickup['trancheto'],
                'address_id'=>($address?$address->id:0),
                'date'=>$new_order['hd_pickup'],
                'status'=>'NEW',//'PMS'
                'order_id'=>$new_order_id,
            ];

            $id_booking_pickup = DB::table('pickup')->insertGetId($pickup_arr);
            $booking_hd_pickup = DB::table('pickup')->where('id',$id_booking_pickup)->first();

            DB::table('infoOrder')->where('id',$new_order_id)->update([
                'PickupID'=>$booking_hd_pickup->PickupID,
                'DatePickup'=>$new_order['hd_pickup']
            ]);


         if($new_order['hd_delivery']){

            $delivery_slot = $new_order['hd_delivery_timeslot'];
            $tranche_delivery = BookingController::getBookingDetailFromSlot($delivery_slot);
            if(isset($new_order['DeliveryaskID'])){
                if(!is_null($new_order['DeliveryaskID'])){
                    DB::table('infoOrder')->where('id',$new_order_id)->update([
                        'DeliveryaskID'=>$new_order['DeliveryaskID'],
                        'DateDeliveryAsk'=>$new_order['hd_delivery']
                    ]);
                }
            }else{
                $delivery_arr = [
                    'PhoneNumber'=>(!empty($phones)?implode(",",$phones):""),
                    'CodeCountry'=>$code_country,
                    'TypeDelivery'=>'', //A confirmer
                    'AddressID'=>$address_uuid,
                    'CustomerID'=>$customerid,
                    'DeliveryAskID'=>'',
                    'id_customer'=>0,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'comment'=>(!is_null($new_order['customer_instructions'])?$new_order['customer_instructions']:''),
                    'trancheFrom'=>$tranche_delivery['tranchefrom'],
                    'trancheto'=>$tranche_delivery['trancheto'],
                    'address_id'=>($address?$address->id:0),
                    'date'=>$new_order['hd_delivery'],
                    'status'=>'NEW',//'PMS'
                    'order_id'=>$new_order_id,
    
                ];
               
                $id_booking = DB::table('deliveryask')->insertGetId($delivery_arr);
                $booking_hd_delivery = DB::table('deliveryask')->where('id',$id_booking)->first();
    
                DB::table('infoOrder')->where('id',$new_order_id)->update([
                    'DeliveryaskID'=>$booking_hd_delivery->DeliveryaskID,
                    'DateDeliveryAsk'=>$new_order['hd_delivery']
                ]);
            }
            
         }
            //Notification

        }



        if(!$is_sub_account && $new_order['deliverymethod']=='recurring'){
            $recurring_data = @json_decode($new_order['recurring_data']);

            $recurring = [
                'DeliveryMon'=>[],
                'DeliveryTu'=>[],
                'DeliveryWed'=>[],
                'DeliveryTh'=>[],
                'DeliveryFri'=>[],
                'DeliverySat'=>[],
            ];


            foreach($recurring_data as $k=>$v){
                if(isset($recurring[$v->value])){
                    $recurring[$v->value] = array($v->slot);
                }
            }

            $to_update = [];
            foreach($recurring as $k=>$v){
                $to_update[$k] = json_encode($v);
            }
            $to_update['DeliverybyDay'] = 1;

            DB::table('infoCustomer')->where('CustomerID',$customerid)->update($to_update);

            //Call to recurringCreator
            $recur_obj = OrderRecurringCreator::processRecurringOrders('SAVE RECCURING BOOKING',$customerid);


            if($recur_obj && isset($recur_obj->cur_orders[0])){
                $new_order_id = $recur_obj->cur_orders[0];

                foreach($recur_obj->cur_orders as $k=>$v){
                    BookingController::logBookingHistory(0,$v,$customer->id,$user->id,'recurring');
                }
            }

            //Notification

        }


        //Logs booking history
        if($id_booking > 0){
            BookingController::logBookingHistory($id_booking,$new_order_id,$customer->id,$user->id,($new_order['deliverymethod']=='home_delivery'?'delivery_ask':$new_order['deliverymethod']));
        }

        if($id_master_pickup > 0){
            BookingController::logBookingHistory($id_master_pickup,$new_order_id,$customer->id,$user->id,'pickup');
        }


        if($id_booking_pickup > 0){
            BookingController::logBookingHistory($id_booking_pickup,$new_order_id,$customer->id,$user->id,'pickup');
        }

        //*/
        return response()->json([
            'new_order_id'=>$new_order_id,
            'recurring'=>$recurring_data,
            'recur_obj'=>$recur_obj,
            'post'=>$request->all(),

            //'delivery_arr'=>$delivery_arr,
        ]);
    }

    public function saveCardDetails(Request $request){
        $stripe_key = 'STRIPE_LIVE_SECURITY_KEY';

        $stripe_test = env('STRIPE_TEST');
        if($stripe_test){
            $stripe_key = 'STRIPE_TEST_SECURITY_KEY';
        }

        $stripe = new \Stripe\StripeClient(env($stripe_key));

        $customer_id = $request->CustomerID;

        $card_exp = $request->cardExpDate;
        $card_num =  $request->cardDetails;
        $cardholder_name = $request->cardHolderName;
        $card_cvv = $request->cardCVV;

        $card_exp_arr = explode("/",$card_exp);

        $stripe_customer = null;
        $three_d_res = null;

        $cust = DB::table('infoCustomer')->where('CustomerID',$customer_id)->first();
        $addr = DB::table('address')->where('CustomerID',$cust->CustomerID)->where('status','DELIVERY')->first();

        $err_txt = "";

        $card_id = 0;

        $card = DB::table('cards')->where('CustomerID',$customer_id)->where('Actif',1)->first();

        $has_card = false;
        if($card){
            $has_card = true;
            $stripe_customer = $stripe->customers->retrieve(
                $card->stripe_customer_id,
                []
              );
            $card_id = $card->id;

        }else{
            if($cust){
             //create a card object to stripe
             try{
             $card = $stripe->paymentMethods->create([
                'type' => 'card',
                'card' => [
                    'number'      => $card_num ,
                    'exp_month'   => (int) $card_exp_arr[0],
                    'exp_year'    => (int) $card_exp_arr[1],
                    'cvc'         => $card_cvv,
                ],
            ]);


            //create a customer object to stripe
            $stripe_customer = $stripe->customers->create([
                'name'              => $cardholder_name,
                'email'             => $cust->EmailAddress,
                'payment_method'    => $card->id,
                'invoice_settings'  => ['default_payment_method' => $card->id],
                'metadata'          => [
                                            'CustomerID' => $cust->CustomerID
                                    ],
                'address'           => [
                                        'city'          => ($addr?$addr->Town:''),
                                        'state'         => ($addr?$addr->County:''),
                                        'country'       => ($addr?$addr->Country:''),
                                        'postal_code'   => ($addr?$addr->postcode:''),
                                        'line1'         => ($addr?$addr->address1:''),
                                        'line2'         => ($addr?$addr->address2:''),
                                    ]
            ]);

            $si = $stripe->setupIntents->create([
                'customer' => $stripe_customer->id,
                'payment_method'=>$card->id,
                'payment_method_types' => ['card'],
                'usage'=>'off_session',
            ]);

            $site_url = \Illuminate\Support\Facades\URL::to("/");

            $three_d_res = $stripe->setupIntents->confirm($si->id,[
                'return_url'=>$site_url."/?si=".$si->id,
            ]);


            $credit_card = [
                'CustomerID'        => $cust->CustomerID,
                'cardHolderName'    => $cardholder_name,
                'type'              => $card->card->brand,
                'cardNumber'        => substr_replace($card_num, str_repeat('*', strlen($card_num) - 6), 3, -3),
                'dateexpiration'    => $card_exp,
                'stripe_customer_id'=> $stripe_customer->id,
                'stripe_card_id'    => $card->id,
                'setup_intnet_id'   => $si->id,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];

            $card_id = DB::table('cards')->insertGetId($credit_card);
            $card = DB::table('cards')->where('id',$card_id)->first();

            if($three_d_res->status=='succeeded'){
                DB::table('cards')->where('id',$card_id)->update(['three_d_secure'=>1]);
            }else{
                $err_txt = "3DS ERROR: Card saved but not validated for 3D secure";
            }
        }catch(\Stripe\Exception\CardException $e){
            $err_txt = $e->getError()->message." Code: ".$e->getError()->decline_code;
            /*return response()->json([
                'error_stripe'=>$e->getError()->message." Code: ".$e->getError()->decline_code,
            ]);*/
        }catch(\Exception $e){
            $err_txt = $e->getMessage();
            /*
            return response()->json([
                'error_stripe'=>$e->getMessage(),
            ]);
            */
        }

            }else{
                /*
                if(is_null($addr)){
                    $err_txt = "No address for customer";
                }*/

            }
        }

        return response()->json([
            'has_card'=>$has_card,
            'card'=>$card,
            'err'=>$err_txt,
            'three_d_res'=>$three_d_res,
        ]);
    }

    public function makePaymentOrCreateCard(Request $request){
        $card = null;
        $payment_intent = null;
        $err_payment = "";
        $paid = false;
        $three_d_res = null;

        $card_id = $request->card_id;
        $order_id = (int) $request->order_id;

        $card_exp = $request->cardExpDate;
        $card_num =  $request->cardDetails;
        $cardholder_name = $request->cardHolderName;
        $card_cvv = $request->cardCVV;
        $payment_type= $request->payment_type;
        $editcard = $request->editcard;
        $amount_to_pay = $request->amount_to_pay;

        $stripe_key = 'STRIPE_LIVE_SECURITY_KEY';

        $stripe_test = env('STRIPE_TEST');
        if($stripe_test){
            $stripe_key = 'STRIPE_TEST_SECURITY_KEY';
        }

        $stripe = new \Stripe\StripeClient(env($stripe_key));

        $order = DB::table('infoOrder')->where('id',$order_id)->first();

        $cust = DB::table('infoCustomer')->where('CustomerID',$order->CustomerID)->first();


        if(!is_null($card_id) && !$editcard){
            $card = DB::table('cards')->where('id',$card_id)->first();
        }else{
            if($editcard){
                DB::table('cards')->where('id',$card_id)->update(['Actif'=>0]);
            }

            //Create card

            $card_exp_arr = explode("/",$card_exp);

            $stripe_customer = null;


            $addr = DB::table('address')->where('CustomerID',$cust->CustomerID)->where('status','DELIVERY')->first();

            $err_txt = "";

            $card_id = 0;

            if($cust){
                try{
                    $card = $stripe->paymentMethods->create([
                        'type' => 'card',
                        'card' => [
                            'number'      => $card_num ,
                            'exp_month'   => (int) $card_exp_arr[0],
                            'exp_year'    => (int) $card_exp_arr[1],
                            'cvc'         => $card_cvv,
                        ],
                    ]);


                    //create a customer object to stripe

                    $stripe_customer = $stripe->customers->create([
                        'name'              => $cardholder_name,
                        'email'             => $cust->EmailAddress,
                        'payment_method'    => $card->id,
                        'invoice_settings'  => ['default_payment_method' => $card->id],
                        'metadata'          => [
                                                    'CustomerID' => $cust->CustomerID
                                            ],
                        'address'           => [
                                                'city'          => ($addr?$addr->Town:''),
                                                'state'         => ($addr?$addr->County:''),
                                                'country'       => ($addr?$addr->Country:''),
                                                'postal_code'   => ($addr?$addr->postcode:''),
                                                'line1'         => ($addr?$addr->address1:''),
                                                'line2'         => ($addr?$addr->address2:''),
                                            ]
                    ]);

                    $si = $stripe->setupIntents->create([
                        'customer' => $stripe_customer->id,
                        'payment_method_types' => ['card'],
                        'payment_method'=>$card->id,
                        'usage'=>'off_session',
                    ]);

                    $site_url = \Illuminate\Support\Facades\URL::to("/");

                    $three_d_res = $stripe->setupIntents->confirm($si->id,[
                        'return_url'=>$site_url."/?si=".$si->id,
                    ]);


                    $credit_card = [
                        'CustomerID'        => $cust->CustomerID,
                        'cardHolderName'    => $cardholder_name,
                        'type'              => $card->card->brand,
                        'cardNumber'        => substr_replace($card_num, str_repeat('*', strlen($card_num) - 6), 3, -3),
                        'dateexpiration'    => $card_exp,
                        'stripe_customer_id'=> $stripe_customer->id,
                        'stripe_card_id'    => $card->id,
                        'setup_intent_id'   => $si->id,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];
                    $card_id = DB::table('cards')->insertGetId($credit_card);
                    $card = DB::table('cards')->where('id',$card_id)->first();

                    if($three_d_res->status=='succeeded'){
                        DB::table('cards')->where('id',$card_id)->update(['three_d_secure'=>1]);
                    }else{
                        return response()->json([
                            'error_stripe'=>'3DS ERROR: Ask customer to enter card details on app',
                        ]);
                    }


                }catch(\Stripe\Exception\CardException $e){
                    return response()->json([
                        'error_stripe'=>$e->getError()->message." Code: ".$e->getError()->decline_code,
                    ]);
                }catch(\Exception $e){
                    return response()->json([
                        'error_stripe'=>$e->getMessage(),
                    ]);
                }

            }

        }
        //Effecting payment
/*
        $amount_paid = 0;
        $existing_payments = DB::table('payments')->where('order_id',$order->id)->where('status','=','succeeded')->get();
        if(count($existing_payments) > 0){
            foreach($existing_payments as $k=>$v){
                $amount_paid += $v->montant;
            }
        }

        $amount_diff = $amount_to_pay - $amount_paid;
*/

        $amount_diff = DetailingController::getAmountToPay($order_id);
        $card_pay = false;

        if($payment_type !='Save' && $card && $order && $order->CustomerID==$card->CustomerID && $amount_diff > 0){
            $amount_two_dp = number_format($amount_to_pay,2);

            $card_pay = true;

            $total_amount = 100*$amount_two_dp;

            $payment_arr = [
                'type'=>'card',
                'datepayment'=>date('Y-m-d H:i:s'),
                'status'=>'',
                'montant'=>number_format($amount_two_dp,2),
                'order_id'=>$order_id,
                'card_id'=>$card->id,
                'CustomerID'=>$order->CustomerID,
                'created_at'=>date('Y-m-d H:i:s'),
            ];

            $payment_id = DB::table('payments')->insertGetId($payment_arr);

            try{
                $payment_intent = $stripe->paymentIntents->create([
                    'amount'            => $total_amount, //100*0.01
                    'currency'          => 'gbp',
                    'confirm'           => true,
                    "payment_method"    => ($stripe_test?'pm_1LnoA2B2SbORtEDspPBXGNAJ':$card->stripe_card_id),
                    "customer"          => ($stripe_test?'cus_MWrt7Gggau7yrE':$card->stripe_customer_id),
                    "capture_method"    => "automatic",
                    'payment_method_types' => ['card'],
                    "description"=>$order_id,
                    "receipt_email"=>($stripe_test?'rushdi@vpc-direct-service.com':$cust->EmailAddress), //To change for customer email
                    'off_session' => true,
                    'confirm' => true,
                ]);

                if($payment_intent && isset($payment_intent->status)){
                    DB::table('payments')->where('id',$payment_id)->update(['payment_intent_id'=>$payment_intent->id]);

                    if($payment_intent->status == 'succeeded'){

                        DB::table('payments')->where('id',$payment_id)->update(['status'=>'succeeded']);

                        //Update order
                        DB::table('infoOrder')->where('id',$order_id)->update([
                            //'Paid'=>1,
                            'payment_id'=>$payment_id,
                        ]);

                        $paid = true;

                        //Create/update items, itempost and invoices


                    }
                }

            }catch(\Stripe\Exception\CardException $e) {
                OrderController::logErrorPayment($payment_id,$e->getError());

                $err_payment = "Payment unsuccessful";

                return response([
                    'error_stripe'=>"Stripe error: ".$e->getError()->code,
                ]);

              }catch (\Stripe\Exception\InvalidRequestException $e) {

                OrderController::logErrorPayment($payment_id,$e->getError());

                $err_payment = "Payment unsuccessful";

                return response([
                    'error_stripe'=>"Stripe error: ".$e->getError()->code,
                ]);
              }catch (Exception $e) {
                OrderController::logErrorPayment($payment_id,$e);

                $err_payment = "Payment unsuccessful";

                return response([
                    'error_stripe'=>"Another problem occurred, maybe unrelated to Stripe",
                ]);
              }
        }


        //*/

        return response()->json([
            'card'=>$card,
            'payment_intent'=>$payment_intent,
            'post'=>$request->all(),
            'err_payment'=>$err_payment,
            'paid'=>$paid,
            'payment_type'=>$payment_type,
            'post'=>$request->all(),
            'order'=>$order,
            'card_pay'=>$card_pay,
            'amount_diff'=>$amount_diff,
            'three_d_res'=>$three_d_res,
        ]);
    }

    public function cancelBooking(Request $request){

        $orderid=$request->post('OrderID');

        if(!empty($orderid)){

            $infoOrder=DB::table('infoOrder')->where('infoOrder.OrderID',$orderid)->first();

            DB::table('infoOrder')->where('infoOrder.OrderID',$orderid)->update([
                'Status'=>'DELETE'
            ]);

            DB::table('deliveryask')->where( 'deliveryask.DeliveryaskID', $infoOrder->DeliveryaskID )->update([
                'Status'=>'DEL'
            ]);

            DB::table('pickup')->where( 'pickup.PickupID', $infoOrder->PickupID)->update([
                'Status'=>'DEL'
            ]);
        }
        return response()->json(['done'=>'ok']);
    }

    public function voidSuborder(Request $request){

        $invoiceid=$request->post('invoiceId');

        if(!empty($invoiceid)){

            $invoice = DB::table('infoInvoice')->where('infoInvoice.InvoiceID','=' ,$invoiceid )->first();

            DB::table('infoInvoice')->where('infoInvoice.id',$invoice->id)->update([
                'Status'=>'VOID'
            ]);

            $infoitems=DB::table('infoitems')->select('infoitems.id')
             ->where('infoitems.InvoiceID', '=' , $invoiceid)->get();

            $infoitemsIds=[];
            $infoitems->each(function ($item, $key) use(&$infoitemsIds){
                $infoitemsIds[]=$item->id;
            });

            foreach ($infoitemsIds as $item) {

                $affected = DB::table('infoitems')
                  ->where('id', $item)
                  ->update([
                    'Status' => 'VOID',
                    'Actif' => '0',
                    'nextpost' => '34',
                    'PromisedDate' => '2020-01-01'
                ]);

            }
      }

      return response()->json(['done'=>'ok']);
    }

    public static function createOrderItems($order_id){
        $delivery_ask_status_to_include = Delivery::getDeliveryAskStatusToInclude();
        $pickup_status_to_include = Delivery::getPickupStatutToInclude();


        $order = DB::table('infoOrder')->where('id',$order_id)->first();

        $is_new_invoice = DB::table('NewInvoice')->where('order_id',$order_id)->get();


        $items_created = [];

        if($order){
            $cust = DB::table('infoCustomer')->where('CustomerID',$order->CustomerID)->first();

            //Retrieve booking for order
            $promised_date = "";
            $storename = 'ATELIER';
            $stores = 'DELIVERY';

            //In store collection
            if($order->deliverymethod=='in_store_collection'){
                $booking = DB::table('booking_store')->where('order_id',$order->id)->first();
                if($booking){
                    $promised_date = $booking->pickup_date;
                    $storename = $booking->store_name;
                }
                $stores = 'STORES';
            }
            //Delivery
            elseif(in_array($order->deliverymethod,['delivery_only','home_delivery'])){
                $booking = DB::table('deliveryask')->where('order_id',$order->id)->where('status','NEW')->first();
                if($booking){
                    $promised_date = $booking->date;
                }
            }
            //Recurring
            elseif($order->deliverymethod=='recurring'){


                if($cust){
                    $booking = DB::table('deliveryask')->where('order_id',$order->id)->where('status','REC')->first();
                    //To confirm for pickup

                    if($booking){
                        $promised_date = $booking->date;
                    }
                }
            }
            //SubAccount
            elseif($order->deliverymethod='delivery_only' && $cust->CustomerIDMaster!=''){
                $deliveryask=DB::table('deliveryask')->where('CustomerID','=',$cust->CustomerIDMaster)->whereDate('date','>=',date('Y-m-d'))->whereIn('status',$delivery_ask_status_to_include)->first();

                $pickup = DB::table('pickup')->where('CustomerID','=',$cust->CustomerIDMaster)->whereDate('date','>=',date('Y-m-d'))->whereIn('status',$pickup_status_to_include)->first();

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
                if($booking){
                    $promised_date = $booking->date;
                }
            }


            $daynames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

            $day_promised_date = date('w',strtotime($promised_date));
            $date_jour = $daynames[$day_promised_date];


            $detailing_items = DB::table('detailingitem')
                ->select('detailingitem.*','typeitem.name as typeitem','typeitem.PERC','typeitem.process','departments.name as department')
                ->join('typeitem','detailingitem.typeitem_id','typeitem.id')
                ->join('departments','typeitem.department_id','departments.id')
                ->where('detailingitem.order_id',$order_id)
                ->get();


            $customer_type = DB::table('infoCustomer')
                ->select('InfoCustomerPreference.*')
                ->join('InfoCustomerPreference','infoCustomer.CustomerID','InfoCustomerPreference.CustomerID')
                ->where('infoCustomer.CustomerID',$order->CustomerID)
                ->where('InfoCustomerPreference.Delete',0)
                ->where('Titre','Type Customer')
                ->first();

            $size_map = [];
            $sizes = DB::table('sizes')->get();
            if(count($sizes) > 0){
                foreach($sizes as $k=>$v){
                    $size_map[$v->id] = $v->name;
                }
            }

            $vip = 0;
            $star = 0;

            if($customer_type=='VIP GOLD'){
                $vip = 1;
                $star = 1;
            }elseif($customer_type=='VIP RED'){
                $vip = 2;
                $star = 2;
            }

            $items_to_insert = [];

            $detailing_map = [];




            if(count($detailing_items)> 0){
                foreach($detailing_items as $k=>$v){

                    $tailoring_services = $v->tailoring_services;

                    $tailoring_arr = [];
                    if(!is_null($tailoring_services ) && $tailoring_services !=''){
                        $tailoring_arr = @json_encode($tailoring_services);
                    }

                    //Calculate New Post et processus

                    $process_arr = DetailingController::calculateNextPost($v->id);


                    $item = new stdClass();
                    $item->spot = 0;
                    $item->typeitem = $v->typeitem;
                    $item->Status = 'In process';
                    $item->PERC = $v->PERC;
                    $item->Actif = 1;
                    $item->tailoring = (!empty($tailoring_arr)?1:0);
                    $item->DepartmentName = $v->department;
                    $item->PromisedDate = $promised_date;


                    $item->nextpost = $process_arr['nextpost'];
                    $item->process = json_encode($process_arr['process']);

                    $item->StoreName = $storename;
                    $item->store = $stores;
                    $item->express = $order->express;
                    $item->priceClean = $v->pricecleaning;
                    $item->ItemTrackingKey = $v->tracking;

                    //Size
                    if(!is_null($v->size_id) && isset($size_map[$v->size_id])){
                        $item->Size = $size_map[$v->size_id];
                    }

                    //Patterns
                    $pattern_text = "";
                    if($v->pattern_id !='' && !is_null($v->pattern_id)){
                        $pattern = DB::table('patterns')->where('id',$v->pattern_id)->first();
                        if($pattern){
                            $pattern_text = $pattern->name;
                        }
                    }
                    $item->Patterns = $pattern_text;


                    //Fabrics
                    $fabric_text = "";
                    if($v->fabric_id!='' && !is_null($v->fabric_id)){
                        $fabric_id_arr = @json_decode($v->fabric_id);
                        $fabric_names = [];
                        if(is_array($fabric_id_arr) && !empty($fabric_id_arr)){
                            $fabrics = DB::table('fabrics')->whereIn('id',$fabric_id_arr)->get();

                            if(count($fabrics) > 0){
                                foreach($fabrics as $key=>$val){
                                    $fabric_names[] = $val->Name;
                                }

                                $fabric_text = implode(",",$fabric_names);
                            }
                        }
                    }
                    $item->Fabrics = $fabric_text;

                    //Colors
                    $color_text = "";
                    if($v->color_id!='' && !is_null($v->color_id)){
                        $color_id_arr = @json_decode($v->color_id);
                        $color_names = [];

                        if(is_array($color_id_arr) && !empty($color_id_arr)){
                            $colors = DB::table('colours')->whereIn('id',$color_id_arr)->get();

                            if(count($colors) > 0){
                                foreach($colors as $key=>$val){
                                    $color_names[] = $val->name;
                                }
                                $color_text = implode(",",$color_names);
                            }
                        }
                    }

                    $item->Colors = $color_text;


                    //Complexities
                    $complexities_text = "";
                    if($v->complexities_id !='' && !is_null($v->complexities_id)){
                        $complexities_id_arr = @json_decode($v->complexities_id);
                        $complexities_names = [];



                        if(is_array($complexities_id_arr) && !empty($complexities_id_arr)){
                            $complexities = DB::table('complexities')->whereIn('id',$complexities_id_arr)->get();


                            if(count($complexities) > 0){
                                foreach($complexities as $key=>$val){
                                    $complexities_names[] = $val->name;
                                }

                                $complexities_text = implode("/",$complexities_names);
                            }
                        }
                    }



                    $item->Complexities = $complexities_text;


                    //Condition
                    $condition = DB::table('conditions')->where('id',$v->condition_id)->first();
                    $item->generalState = ($condition?$condition->name:"");


                    //Brand
                    $brand = DB::table('brands')->where('id',$v->brand_id)->first();
                    $item->brand = ($brand?$brand->name:"");



                    $item->express = $order->express;
                    $item->priceClean = $v->dry_cleaning_price+$v->cleaning_addon_price;
                    $item->priceTail = $v->tailoring_price;
                    $item->priceTotal = $v->dry_cleaning_price+$v->cleaning_addon_price+$v->tailoring_price;
                    $item->PromisedDate = $promised_date;
                    $item->dateJour = $date_jour;
                    $item->date_add = date('Y-m-d H:i:s');

                    //Damages
                    if($v->damages!='' && !is_null($v->damages) && is_array(@json_decode($v->damages)) && !empty($v->damages)){
                        $damages = @json_decode($v->damages);

                        $damages_to_save = [];

                        foreach($damages as $id=>$damage){
                            $dmg = new stdClass;
                            $damage_txt = "";
                            $zone_txt = "";
                            $dmg->Titre = "";

                            if($damage->id_issue > 0){
                                $damage_txt = DB::table('issues_tag')->where('id',$damage->id_issue)->value('name');
                            }

                            if($damage->id_zone > 0){
                                $zone = DB::table('itemzones')->where('id',$damage->id_zone)->first();

                                if($zone){
                                    $zone_txt = strtoupper($zone->face)." ".str_replace("_"," ",$zone->description);
                                }
                            }



                            $dmg->Value = $damage_txt.($damage_txt!=""?" - ":"").$zone_txt;

                            $damages_to_save[] = $dmg;
                        }

                        $item->damage = json_encode($damages_to_save);
                    }



                    //To confirm
                    $item->Vip = $vip;
                    $item->star = $star;

                    $cur_item = DB::table('infoitems')->where('ItemTrackingKey',$v->tracking)->first();

                    $cur_item_id = 0;

                    if($cur_item){
                        $item_to_update = (array) $item;
                        if(count($is_new_invoice) > 0){
                            unset($item_to_update['nextpost']);
                            unset($item_to_update['process']);
                            unset($item_to_update['Status']);
                        }

                        DB::table("infoitems")->where('id',$cur_item->id)->update($item_to_update);
                        $cur_item_id = $cur_item->id;
                    }else{
                        $item->ItemID = '';
                        $cur_item_id = DB::table("infoitems")->insertGetId((array) $item);
                    }


                    DB::table('detailingitem')->where('id',$v->id)->update([
                        'item_id'=>$cur_item_id,
                    ]);

                    $items_created[] = $cur_item_id;

                    $detailing_map[$cur_item_id] = $v->id;
               }

            }
        }

        $endpoint = "";
        $token = "GhtfvbbG4489hGtyEfgARRGht3";

        $site_url = \Illuminate\Support\Facades\URL::to("/");

        if(strpos($site_url,'blancposdev') > -1){
            $endpoint = "http://blancspot.vpc-direct-service.com/validorder.php";
        }elseif(strpos($site_url,'fullcirclepos') > -1){
            $endpoint = "http://blancspot.vpc-direct-service.com/validorder-prod.php";
        }

        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', $endpoint, ['query' => [
            'order_id'=>$order_id,
            'token'=>$token,
            'userid'=>Auth::user()->id,
        ]]);

        $statusCode = $response->getStatusCode();
        @$content = $response->getBody();
        $content = str_replace('\\"','',$content);

        $res = @json_decode($content);


        if(is_object($res) && isset($res->result) && $res->result=='ok'){
            if(count($is_new_invoice)==0){
                DB::table('infoOrder')->where('id',$order_id)->update(['Status'=>'In process']);

                $items = DB::table('infoitems')->whereIn('id',$items_created)->get();

                $item_historique = [];
                $invoices_id = [];

                foreach($items as $k=>$v){
                    if($v->InvoiceID !=''){
                        if(!in_array($v->InvoiceID,$invoices_id)){
                            array_push($invoices_id,$v->InvoiceID);
                        }

                        if(isset($detailing_map[$v->id])){
                            DB::table('detailingitem')->where('id',$detailing_map[$v->id])->update(['InvoiceID'=>$v->InvoiceID]);
                        }
                        $inv = DB::table("infoInvoice")->where('InvoiceID',$v->InvoiceID)->first();

                        if($inv){
                            $item_historique[] = [
                                'CustomerID'=>$inv->CustomerID,
                                'StoreName'=>$inv->StoreName,
                                'ItemTrackingKey'=>$v->ItemTrackingKey,
                                'NumInvoice'=>$inv->NumInvoice,
                                'InvoiceID'=>$v->InvoiceID,
                                'created_at'=>date('Y-m-d H:i:s'),
                            ];
                        }
                    }
                }

                if(!empty($item_historique)){
                    DB::table('itemhistorique')->insert($item_historique);
                }
            }

            //To add paid

            $amount_paid = 0;
            $payments = DB::table("payments")->where('order_id',$order_id)->where('status','succeeded')->get();
            if(count($payments) > 0){
                foreach($payments as $k=>$v){
                    $amount_paid += $v->montant;
                }
            }

            if($order && number_format($amount_paid,2) >= number_format($order->Total,2)){
                DB::table('infoOrder')->where('id',$order_id)->update(['Paid'=>1,'TotalDue'=>0]);

                if(!empty($invoices_id)){
                    DB::table('infoInvoice')->whereIn('InvoiceID',$invoices_id)->update(['Paid'=>1]);
                }
            }




        }
        //*/

        return $content;

    }

    public function completeCheckout(Request $request){
        $order_id = $request->order_id;
        $balance = number_format($request->balance,2);
        $amount_to_pay = number_format($request->amount_to_pay,2);
        $credit_to_deduct = number_format($request->credit_to_deduct,2);
        $paylater = $request->paylater;

        $order = DB::table('infoOrder')->where('id',$order_id)->first();

        if($order && $paylater==1){
            DB::table('infoOrder')->where('id',$order_id)->update(['Paid'=>0]);
            DB::table('infoInvoice')->where('OrderID',$order->OrderID)->update(['Paid'=>0]);
        }
        $cust = DB::table('infoCustomer')->where('CustomerID',$order->CustomerID)->first();

        $credit_remaining = $cust->credit;

        if($cust->credit >= 0 && $credit_to_deduct > 0){
            /*
            if($cust->credit > $balance){
                $credit_remaining = $cust->credit - $balance;

                if($cust->credit > $balance){
                    DB::table('infoOrder')->where('id',$order_id)->update(['Paid'=>1]);
                }
            }else{
                $credit_remaining = 0;
            }
            */

            $credit_remaining = $cust->credit - $credit_to_deduct;

            //$credit_remaining = number_format($credit_remaining,2);

            DB::table('infoCustomer')->where('id',$cust->id)->update(['credit'=>($credit_remaining>0?$credit_remaining:0)]);

            $stamp = date('Y-m-d H:i:s');

            if($credit_to_deduct > 0){
                DB::table('payments')->insert([
                    'type'=>'cust_credit',
                    'order_id'=>$order_id,
                    'datepayment'=>$stamp,
                    'status'=>'succeeded',
                    'montant'=>$credit_to_deduct,
                    'CustomerID'=>$order->CustomerID,
                    'created_at'=>$stamp,
                    'info'=>'',
                ]);
            }
        }

        $content = OrderController::createOrderItems($order_id);
        $order_res =  @json_decode($content);


        return response()->json([
            'content'=>$content,
            'output'=>$order_res,

        ]);
    }

    public function updateOrderTerminal(Request $request){
        $order_id = $request->order_id;
        $terminal = $request->terminal;
        $amount = $request->amount;
        $status = $request->status;
        $info = $request->info;

        $stamp = date('Y-m-d H:i:s');

        $order = DB::table("infoOrder")->where('id',$order_id)->first();

        DB::table('payment')->insert([
            'type'=>$terminal,
            'datepayment'=>$stamp,
            'status'=>$status,
            'montant'=>$amount,
            'CustomerID'=>$order->CustomerID,
            'created_at'=>$stamp,
            'info'=>$info,
        ]);

        if($status=='succeeded'){
            $updated = DB::table("infoOrder")->where('id',$order_id)->update(['Paid'=>1]);
        }

        return response()->json([
           'updated'=>$updated,
        ]);
    }

    public function updateOrderToDetailing(Request $request){
        $order_id = $request->order_id;

        $updated = DB::table("infoOrder")->where('id',$order_id)->update(['Status'=>'IN DETAILING']);

        return response()->json([
            'updated'=>$updated,
        ]);
    }

    public function validateCheckOutOrder(Request $request){
        $order_id = $request->order_id;

        $content = OrderController::createOrderItems($order_id);
        $order_res = @json_decode($content);

        return response()->json([
            'output'=>$order_res,
        ]);
    }

    public function voidOrder(Request $request){

       $order_id=$request->post('order_id');
       $ListSubOrder=$request->post('items');
       $ListInvoice=[];
       $infoitemsIds=[];

           $orderInfo  =  DB::table('infoOrder')->where('infoOrder.id',$order_id)
                        ->join('infoCustomer','infoOrder.CustomerID','infoCustomer.CustomerID')
                        ->first();

           $revenuInfo = DB::table('revenu')->select('revenu.*',
                            DB::raw('ROUND(SUM(Total), 2) as Totalfinal'),
                            DB::raw('ROUND(SUM(AccountDiscounts), 2)  as AccountDiscountsFinal'),
                            DB::raw('ROUND(SUM(OrderDiscounts), 2)  as OrderDiscountsfinal'),
                            DB::raw('ROUND(SUM(DeliveryFees), 2)  as DeliveryFeesFinal'),
                            DB::raw('ROUND(SUM(ExpressCharge), 2)  as ExpressChargefinal'),
                            DB::raw('ROUND(SUM(bundles), 2)  as bundlesFinal'),
                            DB::raw('ROUND(SUM(FailedDeliveryCharge), 2)  as FailedDeliveryChargeFinal'),
                        )
                        ->where('revenu.order_id',$order_id)
                        ->first();

           $order_revenu = [
                'OrderRevenueLocation'=> $orderInfo->OrderRevenueLocation,
                'Total'=>($revenuInfo->Totalfinal?-($revenuInfo->Totalfinal):0),
                'AccountDiscounts'=>($revenuInfo->AccountDiscountsFinal?-($revenuInfo->AccountDiscountsFinal):0),
                'OrderDiscounts'=>($revenuInfo->OrderDiscountsfinal?-($revenuInfo->OrderDiscountsfinal):0),
                'DeliveryFees'=>($revenuInfo->DeliveryFeesFinal?-($revenuInfo->DeliveryFeesFinal):0),
                'ExpressCharge'=>($revenuInfo->ExpressChargefinal?-($revenuInfo->ExpressChargefinal):0),
                'bundles'=>($revenuInfo->bundlesFinal?-($revenuInfo->bundlesFinal):0),
                'FailedDeliveryCharge'=>($revenuInfo->FailedDeliveryChargeFinal?-($revenuInfo->FailedDeliveryChargeFinal):0),
                'TypeDelivery'=>$orderInfo->TypeDelivery,
                'CustomerID'=>$orderInfo->CustomerID,
                'btob'=>$orderInfo->btob,
                'order_id'=>$order_id,
                'Status'=>"ADJVOID",
                'users_id'=>Auth::user()->id,
                'created_at'=>date('Y-m-d H:i:s')
           ];

           DB::table('revenu')->insert($order_revenu);


           $order =  DB::table('infoOrder')->where('infoOrder.id',$order_id)->update([
            'datevoid' =>date('Y-m-d H:i:s'),
            'Status'   => "VOID",
            'Total'    => 0,
         ]);
           foreach ($ListSubOrder as $suborder) {
                foreach ($suborder as $key=>$invoice) {
                       DB::table('infoInvoice')->where('infoInvoice.InvoiceID', $invoice['InvoiceID'] )
                                ->update([
                                    'Status'=>'VOID'
                                ]);
                $ListInvoice[] = DB::table('infoInvoice')->select('infoInvoice.NumInvoice' , 'infoInvoice.InvoiceID')->where('infoInvoice.InvoiceID',$invoice['InvoiceID'])->get();
                }
           };

           if(!empty($ListInvoice)){

                foreach ($ListInvoice as $key=>$invoice) {

                        $infoitems=DB::table('infoitems')->select('infoitems.id')
                                    ->where('infoitems.InvoiceID', '=' , $invoice[0]->InvoiceID)
                                    ->get();

                        $infoitems->each(function ($item, $key) use(&$infoitemsIds){
                                $infoitemsIds[]=$item->id;
                            });
                };

                $infoitemsIds= array_unique($infoitemsIds);

                foreach ($infoitemsIds as $item) {

                    $affected = DB::table('infoitems')
                    ->where('id', $item)
                    ->update([
                        'Status' => 'VOID',
                        'Actif' => '0',
                        'nextpost' => '34',
                        'PromisedDate' => '2020-01-01'
                    ]);

                }
           }

      return response()->json(['done'=>'ok']);
    }

    public function deleteorder(Request $request){

        $orderid=$request->post('order_id');

            $infoOrder=DB::table('infoOrder')->where('infoOrder.id',$orderid)->first();

            if($infoOrder){
                $pickup = DB::table('pickup')->where('pickup.order_id',$orderid)->first();

                    if(empty($pickup)){
                        DB::table('infoOrder')->where('infoOrder.id',$orderid)->update([
                            'Status'=>'DELETE'
                        ]);
                    }else if(!empty($pickup)){

                        $items=DB::table('detailingitem')->where('detailingitem.order_id',$infoOrder->id)->get();
                        if(!empty($items)){
                            foreach($items as $k=>$v){
                                $deleted = DB::table('detailingitem')->where('id',$v->id)->delete();
                            }
                        }

                        DB::table('infoOrder')->where('infoOrder.id',$orderid)->update([
                            'Status'=>'SCHEDULED'
                        ]);
                    }
            }
        return response()->json(['done'=>'ok']);
    }

    public function getOrderToFulfill(Request $request){
        $order_id = $request->order_id;

        $order = DB::table('infoOrder')->where('id',$order_id)->first();
        $user = Auth::user();

        return response()->json([
            'order'=>$order,
            'user'=>$user,
        ]);

    }

    public function fulfillOrder(Request $request){
        $order_id = $request->order_id;
        $paid = $request->paid;

        $order = DB::table('infoOrder')->where('id',$order_id)->first();

        if($paid){
            DB::table('infoOrder')->where('id',$order_id)->update(['Paid'=>1,'TotalDue'=>0]);
            DB::table('infoInvoice')->where('OrderID',$order->OrderID)->update(['Paid'=>1]);
        }

        $statusCode = "";
        $content = "";
        $res = false;

        $site_url = \Illuminate\Support\Facades\URL::to("/");

        if(strpos($site_url,'blancposdev') > -1){
            //Do nothing
        }elseif(strpos($site_url,'fullcirclepos') > -1){
            $endpoint = "http://blancspot.vpc-direct-service.com/fulfiled-v1-order.php";
            $token = "GhtfvbbG4489hGtyEfgARRGht3";

            $site_url = \Illuminate\Support\Facades\URL::to("/");

            $client = new \GuzzleHttp\Client();

            $response = $client->request('GET', $endpoint, ['query' => [
                'order_id'=>$order->OrderID,
                'token'=>$token,
                'userid'=>Auth::user()->id,
            ]]);

            $statusCode = $response->getStatusCode();
            $content = $response->getBody();
            $content = str_replace('\\"','',$content);

            $res = @json_decode($content);

            //*/
        }

        return response()->json([
            'post'=>$request->all(),
            'status'=>$statusCode,
            'content'=>$content,
            'output'=>$res,
        ]);
    }

    public static function logErrorPayment($payment_id,$err){
        DB::table('payments')->where('id',$payment_id)->update(['status'=>'card failed','info'=>json_encode($err)]);

        $payment_line = DB::table('payments')->where('id',$payment_id)->first();

        DB::table('unpaid_orders')->insert([
            'payment_intent_id'=>'',
            'order_id'=>$payment_line->order_id,
            'created_at'=>date('Y-m-d H:i:s'),
        ]);
    }

    public function getPayementOrder(Request $request){

        $order_id=$request->post('order_id');

            $orderInfo  =  DB::table('payments')->select('status' , 'type' , 'datepayment')
                                                ->where('payments.order_id',$order_id)
                                                ->first();

            return response()->json([
                'order_id'=> $order_id,
                'data'=>$orderInfo
            ]);
     }

     public static function logRefund($payment_id,$amount){
        $user = Auth::user();
        $err_txt = "";

        $payment = DB::table('payments')->where('id',$payment_id)->first();

        $stripe_key = 'STRIPE_LIVE_SECURITY_KEY';

        $stripe_test = env('STRIPE_TEST');
        if($stripe_test){
            $stripe_key = 'STRIPE_TEST_SECURITY_KEY';
        }

        $stripe = new \Stripe\StripeClient(env($stripe_key));

        //$payment_intent_id = $payment->payment_intent_id;
        $payment_intent_id = 'pi_3MCNkJB2SbORtEDs1mcg2D9j';

        $payment_intent = $stripe->paymentIntents->retrieve($payment_intent_id,[]);

        $charge = $payment_intent->charges->data[0];

        try{

            $refund = $stripe->refunds->create([
                'charge'=>$charge->id,
                'amount'=>$amount*100,
                'reason'=>'requested_by_customer', //duplicate, fraudulent, or requested_by_customer
                'metadata'=>[
                    'description'=>'this is a test refund'
                ],
            ]);


            $chargeAfterRefund = $stripe->charges->retrieve($charge->id,[]);

            //Log line in charge
            DB::table('refunds')->insert([
                'payment_id'=>$payment_id,
                'order_id'=>($payment?$payment->order_id:0),
                'user_id'=>($user?$user->id:0),
                'amount'=>$amount,
                'stripe_charge_id'=>$charge->id,
                'created_at'=>date('Y-m-d H:i:s')
            ]);


            //log negative line in payments
            DB::table('payments')->insert([
                'type'=>'card',
                'datepayment'=>date('Y-m-d H:i:s'),
                'status'=>'succeeded',
                'montant'=>0-$amount,
                'order_id'=>($payment?$payment->order_id:0),
                'card_id'=>($payment?$payment->card_id:0),
                'payment_intent_id'=>$payment_intent_id,
                'CustomerID'=>($payment?$payment->CustomerID:''),
                'info'=>'',
                'created_at'=>date('Y-m-d H:i:s'),
            ]);

            DB::table('payments')->where('id',$payment_id)->update(['refunded'=>$chargeAfterRefund->amount_refunded/100]);


        }catch(\Stripe\Exception\InvalidRequestException $e){ //Child Exception class
            //echo "InvalidRequestException <br/><pre>";
            $err_txt = $e->getError()->message;
        }catch(\Stripe\Exception\ApiErrorException $e){ //parent Exception class
            //echo "ApiErrorException <br/><pre>";
            $err_txt = $e->getError()->message;
        }
        catch(Exception $e){ // grandparent Exception class
            // Exception
            $err_txt = $e->getMessage();
        }

        // $chargeAfterRefund = $stripe->charges->retrieve($charge->id,[]);
        // echo "<pre>";
        // print_r($chargeAfterRefund);

        return $err_txt;
        //*/

    }

    public function getDeliveyDateCustomer(Request $request){

       $CustomerID=$request->post('Customer');

       $cust = DB::table('infoCustomer')->select('infoCustomer.Name' , 'infoCustomer.id' ,'infoCustomer.CustomerIDMaster',
            DB::raw('IF(infoCustomer.CustomerIDMaster = "", "MAIN", "Sub") as account_type'),
            DB::raw('IF(infoCustomer.btob = 0, "B2C", "B2B") as cust_type'),)
            ->where('CustomerID',$CustomerID)->first();

       $orders = [];
       $orders_list = [];
       $tranches_slots = Tranche::getDeliveryPlanningTranches();

       if($cust){

            if($cust->account_type == "Sub"){

                $orders=DB::table('infoOrder')
                ->select(['infoOrder.id AS order_id','infoOrder.Status','infoCustomer.Name','infoOrder.TypeDelivery','infoCustomer.id' , 'infoOrder.DeliveryaskID','infoOrder.DatePickup',
                'infoOrder.DateDeliveryAsk','infoCustomer.OnAccount','infoCustomer.CustomerIDMaster',
                    DB::raw('IF(infoOrder.DateDeliveryAsk ="2020-01-01" OR infoOrder.DateDeliveryAsk ="2000-01-01" OR infoOrder.DateDeliveryAsk ="","--",DATE_FORMAT(infoOrder.DateDeliveryAsk , "%W %d %M")) as DateDelivery '),
                    DB::raw('IF(infoOrder.DatePickup ="2020-01-01" OR infoOrder.DatePickup ="2000-01-01" OR infoOrder.DatePickup ="","--",DATE_FORMAT(infoOrder.DatePickup , " %W %d %M")) as DatePickup '),
                    DB::raw('IF(infoCustomer.CustomerIDMaster = "", "MAIN", "Sub") as account_type'),
                    DB::raw('CONCAT(deliveryask.trancheFrom,"_",deliveryask.trancheto) as order_time'),
                ])
                ->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')
                ->leftJoin('deliveryask', 'deliveryask.DeliveryaskID', '=', 'infoOrder.DeliveryaskID')
                ->where('infoOrder.DateDeliveryAsk','>=', date('Y-m-d'))
                ->whereNotIn('infoOrder.Status', ['VOIDED', 'FULFILLED', 'VOID', 'DELETE'])
                ->where('deliveryask.status','!=', 'DEL')
                ->where('infoOrder.CustomerID','=',$cust->CustomerIDMaster)->get();

            }else{
                $orders=DB::table('infoOrder')
                ->select(['infoOrder.id AS order_id','infoOrder.Status','infoCustomer.Name','infoOrder.TypeDelivery','infoCustomer.id' , 'infoOrder.DeliveryaskID','infoOrder.DatePickup',
                'infoOrder.DateDeliveryAsk','infoCustomer.OnAccount','infoCustomer.CustomerIDMaster',
                    DB::raw('IF(infoOrder.DateDeliveryAsk ="2020-01-01" OR infoOrder.DateDeliveryAsk ="2000-01-01" OR infoOrder.DateDeliveryAsk ="","--",DATE_FORMAT(infoOrder.DateDeliveryAsk , "%W %d %M")) as DateDelivery '),
                    DB::raw('IF(infoCustomer.btob = 0, "B2C", "B2B") as cust_type'),
                    DB::raw('IF(infoCustomer.CustomerIDMaster = "", "MAIN", "Sub") as account_type'),
                    DB::raw('CONCAT(deliveryask.trancheFrom,"_",deliveryask.trancheto) as order_time'),
                ])
                ->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')
                ->leftJoin('deliveryask', 'deliveryask.DeliveryaskID', '=', 'infoOrder.DeliveryaskID')
                ->whereNotIn('infoOrder.Status', ['VOIDED', 'FULFILLED', 'VOID', 'DELETE'])
                ->where('infoOrder.DateDeliveryAsk','>=', date('Y-m-d'))
                ->where('infoOrder.CustomerID','=',$CustomerID)->get();

            }
        }

        foreach($orders as $k=>$order){

            if($order->order_time != null){
                $tranche_left = $order->order_time;
                $tranche_arr_left = explode("_",$tranche_left);
                if(isset($tranche_arr_left[0]) && isset($tranche_arr_left[1])){
                    $slot = Tranche::getSlotFromTranche($tranche_arr_left[0],$tranche_arr_left[1]);
                    if($slot){
                        $timeslot = $tranches_slots[$slot];
                        $order->order_time = $timeslot;
                    }
                   
                }
            }
        }

        return response()->json([
            'orders'=>$orders
        ]);
    }
}
