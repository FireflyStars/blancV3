<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\InfoCustomer;


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

        $new_order_id = 0;
        $created_stamp = date('Y-m-d H:i:s');

        $customer = DB::table('infoCustomer')->where('CustomerID',$new_order['CustomerID'])->first();

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

        $new_order_id = DB::table('infoOrder')
            ->insertGetId($order_to_insert);


        //Add in_store_collection booking details
        if($new_order['deliverymethod']=='in_store_collection'){
            DB::table('booking_store')->insert([
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


        return response()->json([
            //'post'=>$request->all(),
            'new_order_id'=>$new_order_id,
        ]);
    }
}
