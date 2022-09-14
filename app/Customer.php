<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    public static function logBookingHistory($booking_id,$order_id,$customer_id,$user_id,$type,$status='NEW'){
        $booking_hist_id = DB::table('booking_histories')->insertGetId([
            'booking_id'=>$booking_id,
            'order_id'=>$order_id,
            'user_id'=>$user_id,
            'customer_id'=>$customer_id,
            'user_id'=>$user_id,
            'type'=>$type,
            'status'=>$status,
            'created_at'=>date('Y-m-d H:i:s')
        ]);

        return $booking_hist_id;
    }

    
}
