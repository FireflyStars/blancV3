<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\InfoCustomer;
use App\Http\Controllers\BookingController;
use App\Models\OrderRecurringCreator;
use App\Models\Delivery;
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

        if($new_order['deliverymethod'] !='recurring'){
            $order_to_insert = [
                'deliverymethod'=>$new_order['deliverymethod'],
                'CustomerID'=>$customerid,
                'OrderID'=>'',
                'firstorder'=>(count($previous_orders)>0?0:1),
                'express'=>$new_order['express'],
                'Status'=>'PENDING',//($new_order['deliverymethod']=='recurring'?'RECURRING':'PENDING'),
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
                    'trancheFrom'=>$tranche['tranchefrom'],
                    'trancheto'=>$tranche['trancheto'],
                    'address_id'=>($address?$address->id:0),
                    'date'=>$new_order['do_delivery'],
                    'status'=>'NEW',//'PMS'
                    'order_id'=>$new_order_id,

                ];

                $id_booking = DB::table('deliveryask')->insertGetId($delivery_arr);
            }

            if($id_booking !=0){
                $booking_do = DB::table('deliveryask')->where('id',$id_booking)->first();

                DB::table('infoOrder')->where('id',$new_order_id)->update([
                    'DeliveryaskID'=>$booking_do->DeliveryaskID,
                    'DateDeliveryAsk'=>$new_order['do_delivery']
                ]);
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




            $delivery_slot = $new_order['hd_delivery_timeslot'];
            $tranche_delivery = BookingController::getBookingDetailFromSlot($delivery_slot);

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
            if($cust &&  $addr){
             //create a card object to stripe
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
                                        'city'          => $addr->Town,
                                        'state'         => $addr->County,
                                        'country'       => $addr->Country,
                                        'postal_code'   => $addr->postcode,
                                        'line1'         => $addr->address1,
                                        'line2'         => $addr->address2,
                                    ]
            ]);



            $credit_card = [
                'CustomerID'        => $cust->CustomerID,
                'cardHolderName'    => $cardholder_name,
                'type'              => $card->card->brand,
                'cardNumber'        => substr_replace($card_num, str_repeat('*', strlen($card_num) - 6), 3, -3),
                'dateexpiration'    => $card_exp,
                'stripe_customer_id'=> $stripe_customer->id,
                'stripe_card_id'    => $card->id,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];

            $card_id = DB::table('cards')->insertGetId($credit_card);
            $card = DB::table('cards')->where('id',$card_id)->first();
            }else{

                if(is_null($addr)){
                    $err_txt = "No address for customer";
                }

            }
        }

        return response()->json([
            'has_card'=>$has_card,
            'card'=>$card,
            'err'=>$err_txt,
        ]);
    }

    public function makePaymentOrCreateCard(Request $request){
        $card = null;
        $payment_intent = null;
        $err_payment = "";
        $paid = false;

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

            if($cust && $addr){
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
                                            'city'          => $addr->Town,
                                            'state'         => $addr->County,
                                            'country'       => $addr->Country,
                                            'postal_code'   => $addr->postcode,
                                            'line1'         => $addr->address1,
                                            'line2'         => $addr->address2,
                                        ]
                ]);



                $credit_card = [
                    'CustomerID'        => $cust->CustomerID,
                    'cardHolderName'    => $cardholder_name,
                    'type'              => $card->card->brand,
                    'cardNumber'        => substr_replace($card_num, str_repeat('*', strlen($card_num) - 6), 3, -3),
                    'dateexpiration'    => $card_exp,
                    'stripe_customer_id'=> $stripe_customer->id,
                    'stripe_card_id'    => $card->id,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];

                $card_id = DB::table('cards')->insertGetId($credit_card);
                $card = DB::table('cards')->where('id',$card_id)->first();
            }

        }


        //Effecting payment


        if($payment_type !='Save' && $card && $order && $order->CustomerID==$card->CustomerID){
            $amount_two_dp = number_format($amount_to_pay,2);

            $total_amount = 100*$amount_two_dp;

            $payment_intent = $stripe->paymentIntents->create([
                'amount'            => $total_amount, //100*0.01
                'currency'          => 'gbp',
                'confirm'           => true,
                "payment_method"    => $card->stripe_card_id,
                "customer"          => $card->stripe_customer_id,
                "capture_method"    => "automatic",
                'payment_method_types' => ['card'],
                "description"=>"Order: ".$order_id,
                "receipt_email"=>$cust->EmailAddress, //To change for customer email
            ]);


            if($payment_intent && isset($payment_intent->status)){
                $payment_arr = [
                    'type'=>'card',
                    'datepayment'=>date('Y-m-d H:i:s'),
                    'status'=>$payment_intent->status,
                    'montant'=>number_format($amount_two_dp,2),
                    'order_id'=>$order_id,
                    'card_id'=>$card->id,
                    'CustomerID'=>$order->CustomerID,
                    'created_at'=>date('Y-m-d H:i:s'),
                ];

                $payment_id = DB::table('payments')->insertGetId($payment_arr);

                if($payment_intent->status == 'succeeded'){
                    //Update order
                    DB::table('infoOrder')->where('id',$order_id)->update([
                        'Paid'=>1,
                        'payment_id'=>$payment_id,
                    ]);

                    $paid = true;

                    //Create/update items, itempost and invoices


                }else{
                    $err_payment = $payment_intent->status;
                }
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
        ]);
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
                $promised_date = $booking->pickup_date;
                $storename = $booking->store_name;
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
                $promised_date = $booking->date;
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
                        if(count($is_new_invoice) == 0){
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
            'token'=>$token
        ]]);

        $statusCode = $response->getStatusCode();
        @$content = $response->getBody();
        $content = str_replace('\\"','',$content);

        $res = @json_decode($content);
        //*/

        if(is_object($res) && isset($res->result) && $res->result=='ok'){
            if(count($is_new_invoice)==0){
                DB::table('infoOrder')->where('id',$order_id)->update(['Status'=>'In process']);

                $items = DB::table('infoitems')->whereIn('id',$items_created)->get();

                $item_historique = [];

                foreach($items as $k=>$v){
                    if($v->InvoiceID !=''){
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
        }

        return $res;

    }

    public function completeCheckout(Request $request){
        $order_id = $request->order_id;
        $balance = number_format($request->balance,2);
        $amount_to_pay = number_format($request->amount_to_pay,2);

        $order = DB::table('infoOrder')->where('id',$order_id)->first();
        $cust = DB::table('infoCustomer')->where('CustomerID',$order->CustomerID)->first();

        $credit_remaining = $cust->credit;

        if($cust->credit >= 0){
            if($cust->credit > $balance){
                $credit_remaining = $cust->credit - $balance;

                if($cust->credit > $balance){
                    DB::table('infoOrder')->where('id',$order_id)->update(['Paid'=>1]);
                }
            }else{
                $credit_remaining = 0;
            }

            $credit_remaining = number_format($credit_remaining,2);

            DB::table('infoCustomer')->where('id',$cust->id)->update(['credit'=>($credit_remaining>0?$credit_remaining:0)]);

            $stamp = date('Y-m-d H:i:s');

            DB::table('payments')->insert([
                'type'=>'cust_credit',
                'order_id'=>$order_id,
                'datepayment'=>$stamp,
                'status'=>'succeeded',
                'montant'=>($cust->credit > $balance?$balance:$cust->credit),
                'CustomerID'=>$order->CustomerID,
                'created_at'=>$stamp,
                'info'=>'',
            ]);
        }

        $order_res = OrderController::createOrderItems($order_id);

        return response()->json([
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
}
