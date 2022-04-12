<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\InfoCustomer;
use App\Http\Controllers\BookingController;
use App\Models\OrderRecurringCreator;

use function PHPUnit\Framework\isNull;

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


        $customer = DB::table('infoCustomer')->where('CustomerID',$new_order['CustomerID'])->first();
        $address = DB::table('address')->where('AddressID',$address_uuid)->first();

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
                'Status'=>($new_order['deliverymethod']=='recurring'?'RECURRING':'PENDING'),
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

        }

        //Add booking for delivery only
        $delivery_arr = [];
        if($new_order['deliverymethod']=='delivery_only'){
            $slot = $new_order['do_delivery_timeslot'];

            $tranche = BookingController::getBookingDetailFromSlot($slot);


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

            $booking_do = DB::table('deliveryask')->where('id',$id_booking)->first();

            DB::table('infoOrder')->where('id',$new_order_id)->update([
                'DeliveryaskID'=>$booking_do->DeliveryaskID,
                'DateDeliveryAsk'=>$new_order['do_delivery']
            ]);



            //NOTIFICATION



            $save_future_instruction = $new_order['save_instruction_check'];

            if($save_future_instruction && !is_null($new_order['customer_instructions'])){
                DB::table('infoCustomer')->where('CustomerID',$customerid)->update(['commentDelivery'=>$new_order['customer_instructions']]);
            }
        }

        $id_booking_pickup = 0;


        //Add booking for home_Delivery
        if($new_order['deliverymethod']=='home_delivery'){

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

        $recurring_data = [];
        if($new_order['deliverymethod']=='recurring'){
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


            if($recur_obj){
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


        if($id_booking_pickup > 0){
            BookingController::logBookingHistory($id_booking_pickup,$new_order_id,$customer->id,$user->id,'pickup');
        }
        return response()->json([
            'new_order_id'=>$new_order_id,
            'recurring'=>$recurring_data,
            //'delivery_arr'=>$delivery_arr,
        ]);
    }
}
