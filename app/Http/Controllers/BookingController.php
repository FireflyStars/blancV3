<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Tranche;


class BookingController extends Controller {
    public function getTrancheByPostcode(Request $request){
        $postcode = $request->post('postcode');

        $allpostcodes = DB::table('tranchepostcode')
        ->select(DB::raw('DISTINCT(Postcode) AS post_code'))
        ->get();

        $postcode = str_replace(' ','',$postcode);
        $postcode = substr($postcode,0,-3);

        $sel_postcode = '';

        if (count($allpostcodes) > 0) {
            foreach ($allpostcodes as $k=>$v){
                $cur_postcode = $v->post_code;
                //if(strpos($postcode,$cur_postcode)===0){
                if($postcode==$cur_postcode){
                    $sel_postcode = $cur_postcode;
                    //break;
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

        $available_slots=[];

        if(count($tranche_details) > 0){
            foreach($tranche_details as $k=>$v){
                $details = json_decode($v->tranche);
                //$tranche_details[$k]->detail_tranche = $details;
                $formatted_tranche[$v->day] = $details;
                $slot_dates=Tranche::getAvailableSlotAndDates($v->day,$details,true,6);
                foreach ($slot_dates as $slot_key=>$dates){
                        foreach ($dates as $date)
                    $available_slots[$slot_key][]=$date;
                }

            }
        }


        return response()->json([
            'postcode'=>$postcode,
            'available_slots'=>$available_slots, //$tranche_details
        ]);
    }

    public static function getBookingDetailFromSlot($timeslot){
        $trancheto='';
        $tranchefrom='';

        if($timeslot==1){
            $tranchefrom='08:00:00';
            $trancheto='10:00:00';
        }
        if($timeslot==3){
            $tranchefrom='10:00:00';
            $trancheto='12:00:00';
        }
        if($timeslot==5){
            $tranchefrom='12:00:00';
            $trancheto='14:00:00';
        }
        if($timeslot==7){
            $tranchefrom='14:00:00';
            $trancheto='16:00:00';
        }
        if($timeslot==9){
            $tranchefrom='16:00:00';
            $trancheto='18:00:00';
        }
        if($timeslot==11){
            $tranchefrom='18:00:00';
            $trancheto='20:00:00';
        }
        if($timeslot==13){
            $tranchefrom='20:00:00';
            $trancheto='20:00:00';
        }

        return array(
            'tranchefrom'=>$tranchefrom,
            'trancheto'=>$trancheto
        );

    }


    public static function logBookingHistory($id_booking,$order_id,$customer_id,$user_id,$delivery_method,$status='NEW'){
        DB::table('booking_histories')->insert([
            'booking_id'=>$id_booking,
            'order_id'=>$order_id,
            'customer_id'=>$customer_id,
            'user_id'=>$user_id,
            'type'=>$delivery_method,
            'status'=>$status,
            'created_at'=>date('Y-m-d H:i:s'),
        ]);
    }

    public function getSlotsByDay(Request $request){
        $postcode = $request->postcode;

        $allpostcodes = DB::table('tranchepostcode')
        ->select(DB::raw('DISTINCT(Postcode) AS post_code'))
        ->get();


        $sel_postcode = "";

        $postcode = str_replace(' ', '', $postcode);
        $postcode = substr($postcode, 0, -3);

        if (count($allpostcodes) > 0) {
            foreach ($allpostcodes as $k => $v) {
                $cur_postcode = $v->post_code;
                //if(strpos($postcode,$cur_postcode)===0){
                if ($postcode == $cur_postcode) {
                    $sel_postcode = $cur_postcode;
                    //break;
                }
            }
        }
        $day_keys = [];

        $array_map = [
            'MONDAY'=>'DeliveryMon',
            'TUESDAY'=>'DeliveryTu',
            'WEDNESDAY'=>'DeliveryWed',
            'THURSDAY'=>'DeliveryTh',
            'FRIDAY'=>'DeliveryFri',
            'SATURDAY'=>'DeliverySat'
        ];

        foreach($array_map as $k=>$v){
            $day_keys[] = $v;
        }

        $tranche_details = [];
        $formatted_tranche = [];

        if($sel_postcode !=''){
            $tranche_details = DB::table('tranchepostcode')
                ->where('Postcode',$sel_postcode)
                ->get();
        }

        if(count($tranche_details) > 0){
            foreach($tranche_details as $k=>$v){

                $details = json_decode($v->tranche);
                if(!empty($details)) {
                    foreach($details as $id=>$val){
                        $details[$id] = (int) $val;
                    }
                    $details[] = 13;
                }
                //$formatted_tranche[$v->day] = $details;
                if(isset($array_map[$v->day])){
                    $day_key = $array_map[$v->day];
                    $formatted_tranche[$day_key] = $details;
                }
            }

            foreach($day_keys as $k=>$v){
                if(!isset($formatted_tranche[$v])){
                    $formatted_tranche[$v] = [];
                }
            }
        }

        return response()->json([
            'tranches'=>$formatted_tranche,
        ]);
    }

    public function getHolidays(){
        $holidays = DB::table('holidays')->get();

        $dates = [];
        foreach($holidays as $k=>$v){
            $dates[] = $v->date;
        }

        return response()->json([
            'dates'=>$dates,
        ]);
    }

}

