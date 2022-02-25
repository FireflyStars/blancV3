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
            //'tranche_details'=>$tranche_details,
            'available_slots'=>$available_slots,
        ]);
    }

}

