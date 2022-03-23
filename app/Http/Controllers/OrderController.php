<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\InfoCustomer;
use App\Http\Controllers\BookingController;

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

        $order_to_insert = [
            'deliverymethod'=>$new_order['deliverymethod'],
            'CustomerID'=>$customerid,
            'OrderID'=>'',
            'firstorder'=>(count($previous_orders)>0?0:1),
            'express'=>$new_order['express'],
            'Status'=>'PENDING',
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
                'status'=>'',//'PMS'

            ];

            $id_booking = DB::table('deliveryask')->insertGetId($delivery_arr);

            //NOTIFICATION



            $save_future_instruction = $new_order['save_instruction_check'];

            if($save_future_instruction && !is_null($new_order['customer_instructions'])){
                DB::table('infoCustomer')->where('CustomerID',$customerid)->update(['commentDelivery'=>$new_order['customer_instructions']]);
            }
        }

        //Add booking for home_Delivery
        if($new_order['deliverymethod']=='home_delivery'){


        }



        //Logs booking history
        if($id_booking > 0){
            DB::table('booking_histories')->insert([
                'booking_id'=>$id_booking,
                'order_id'=>$new_order_id,
                'customer_id'=>$customer->id,
                'user_id'=>$user->id,
                'type'=>$new_order['deliverymethod'],
                'status'=>'NEW',//(in_array($new_order['deliverymethod'],['delivery_only','home_delivery','recurring'])?'PMS':'NEW'),
                'created_at'=>$created_stamp,
            ]);
        }


        return response()->json([
            'new_order_id'=>$new_order_id,
            //'delivery_arr'=>$delivery_arr,
        ]);
    }
}
