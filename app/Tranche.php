<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Tranche extends Model
{

    public static function getAvailableSlotAndDates($day,$tranche,$returnBySlotKey=false,$weeks=2){

        $NbrDeliverySlotMax=DB::table('settings')->select('value')->where('key','=','site.NbrDeliverySlotMax')->value('value');
        $NbrSlot=count($tranche);


        $slot_dates=[];
        $slots=Tranche::getDeliveryPlanningTranchesForApi();
        $holidays=Holiday::getHolidays();

        $now=Carbon::now();
        $today=date('Y-m-d');

        $customers_pickupbookings=array();
        $customers_deliveryaskbookings=array();

        $customers_pickup_daybookings=array();
        $customers_deliveryask_daybookings=array();
        foreach ($slots as $slotkey =>$slot) {
            if (!in_array($slotkey, $tranche)) continue;
            $c = 0;

            while ($c < $weeks) {

                $date = date('Y-m-d', strtotime($day . ' +' . ($c * 7) . 'days'));

                if ($date === $today) { // exclure les slot deja passer dans la journee
                    $slotendtime = date('Y-m-d H:i:s', strtotime(explode('-', $slot)[0]));
                    $slotendtime = Carbon::parse($slotendtime);
                    if ($now->greaterThanOrEqualTo($slotendtime)) {
                        $date = '';
                    }

                }



                $c++;
                if($date==='')continue;



                if(in_array($date,$holidays))    //exclude holiday dates
                    continue;

                $customers_pickupbooking=DB::table('pickup')->select(['CustomerID','trancheFrom','trancheto'])->whereNotIn('status',['DEL','DEL-DONE','DEL-NOK'])
                    ->whereDate('date',$date)
                    ->whereNotIn('id',function ($query) use ($date){
                        $query->select('id')->from('pickup')->where('trancheFrom','=','08:00:00')->where('trancheto','=','20:00:00')->whereDate('date',$date);
                    })
                    ->get();

                foreach ($customers_pickupbooking as $pickup){
                    $customers_pickupbookings[$date.'_'.$pickup->trancheFrom.'_'.$pickup->trancheto][]=$pickup->CustomerID;
                }


                $customers_deliveryaskbooking=DB::table('deliveryask')->select(['CustomerID','trancheFrom','trancheto'])->whereNotIn('status',['DEL','DEL-DONE','DEL-NOK'])
                    ->whereDate('date',$date)
                    ->whereNotIn('id',function ($query) use ($date){
                        $query->select('id')->from('deliveryask')->where('trancheFrom','=','08:00:00')->where('trancheto','=','20:00:00')->whereDate('date',$date);
                    })
                    ->get();
                foreach ($customers_deliveryaskbooking as $deliveryask){
                    $customers_deliveryaskbookings[$date.'_'.$deliveryask->trancheFrom.'_'.$deliveryask->trancheto][]=$deliveryask->CustomerID;
                }

                $customers_pickup_daybooking=DB::table('pickup')->select(['CustomerID','trancheFrom','trancheto'])->whereNotIn('status',['DEL','DEL-DONE','DEL-NOK'])
                    ->whereDate('date',$date)
                    ->where('trancheFrom','=','08:00:00')->where('trancheto','=','20:00:00')
                    ->get();
                foreach ($customers_pickup_daybooking as $pickup){
                    $customers_pickup_daybookings[$date.'_'.$pickup->trancheFrom.'_'.$pickup->trancheto][]=$pickup->CustomerID;
                }


                $customers_deliveryask_daybooking=DB::table('deliveryask')->select(['CustomerID','trancheFrom','trancheto'])->whereNotIn('status',['DEL','DEL-DONE','DEL-NOK'])
                    ->whereDate('date',$date)
                    ->where('trancheFrom','=','08:00:00')->where('trancheto','=','20:00:00')
                    ->get();
                foreach ($customers_deliveryask_daybooking as $deliveryask){
                    $customers_deliveryask_daybookings[$date.'_'.$deliveryask->trancheFrom.'_'.$deliveryask->trancheto][]=$deliveryask->CustomerID;
                }
            }

        }

        foreach ($slots as $slotkey =>$slot) {
            if(!in_array($slotkey,$tranche))continue;
            $c=0;

            while ($c < $weeks) {

                $date = date('Y-m-d', strtotime($day . ' +' . ($c * 7) . 'days'));

                if($date===$today){ // exclure les slot deja passer dans la journee
                    $slotendtime=date('Y-m-d H:i:s',strtotime(explode('-',$slot)[0]));
                    $slotendtime=Carbon::parse($slotendtime);
                    if($now->greaterThanOrEqualTo($slotendtime)){
                        $date='';
                    }

                }



                $c++;
                if($date==='')continue;



                if(in_array($date,$holidays))    //exclude holiday dates
                    continue;

                $formatted_tranche=Tranche::getFormattedTranche($slotkey);


                $allCustomerIDForSlot=[];
                $allCustomerIDForWholeDay=[];//8-8pm



                if(isset($customers_pickupbookings[$date.'_'.$formatted_tranche['tranchefrom'].'_'.$formatted_tranche['trancheto']]))
                    foreach ($customers_pickupbookings[$date.'_'.$formatted_tranche['tranchefrom'].'_'.$formatted_tranche['trancheto']] as $customerID){
                        if(!in_array($customerID,$allCustomerIDForSlot)){
                            $allCustomerIDForSlot[]=$customerID;
                        }
                    }
                if(isset($customers_deliveryaskbookings[$date.'_'.$formatted_tranche['tranchefrom'].'_'.$formatted_tranche['trancheto']]))
                    foreach ($customers_deliveryaskbookings[$date.'_'.$formatted_tranche['tranchefrom'].'_'.$formatted_tranche['trancheto']] as $customerID){
                        if(!in_array($customerID,$allCustomerIDForSlot)){
                            $allCustomerIDForSlot[]=$customerID;
                        }
                    }

                $NbrBookingBySlot=count($allCustomerIDForSlot); // number simple booking for current slot



                if(isset($customers_pickup_daybookings[$date.'_'.$formatted_tranche['tranchefrom'].'_'.$formatted_tranche['trancheto']]))
                    foreach ($customers_pickup_daybookings[$date.'_'.$formatted_tranche['tranchefrom'].'_'.$formatted_tranche['trancheto']] as $customerID){
                        if(!in_array($customerID,$allCustomerIDForWholeDay)){
                            $allCustomerIDForWholeDay[]=$customerID;
                        }
                    }
                if(isset($customers_deliveryask_daybookings[$date.'_'.$formatted_tranche['tranchefrom'].'_'.$formatted_tranche['trancheto']]))
                    foreach ($customers_deliveryask_daybookings[$date.'_'.$formatted_tranche['tranchefrom'].'_'.$formatted_tranche['trancheto']] as $customerID){
                        if(!in_array($customerID,$allCustomerIDForWholeDay)){
                            $allCustomerIDForWholeDay[]=$customerID;
                        }
                    }
                $NbrBookingDay=count($allCustomerIDForWholeDay);



                $NbrBookingDaybySlot=round(($NbrBookingDay/$NbrSlot));



                //calcul
                if($NbrBookingBySlot+$NbrBookingDaybySlot<$NbrDeliverySlotMax){
                    //do nothing
                }else{
                    $date='';
                }

                if ($date !=='')
                    if($returnBySlotKey){
                        $slot_dates[$slotkey][] = $date;
                    }else {
                        if($date!==$today)
                            $slot_dates[$slot][] = $date;
                    }
            }

        }

        return $slot_dates;

    }

    public static function getDeliveryPlanningTranchesForApi(){
        return [
            1=>'8-10 am',3=>'10-12 pm',5=>'12-2 pm',7=>'2-4 pm',9=>'4-6 pm',11=>'6-8 pm',13=>'8-8 pm'
        ];
    }

    public static function getFormattedTranche($slotkey){
        $trancheto='';
        $tranchefrom='';

        if($slotkey==1){
            $tranchefrom='08:00:00';
            $trancheto='10:00:00';
        }
        if($slotkey==3){
            $tranchefrom='10:00:00';
            $trancheto='12:00:00';
        }
        if($slotkey==5){
            $tranchefrom='12:00:00';
            $trancheto='14:00:00';
        }
        if($slotkey==7){
            $tranchefrom='14:00:00';
            $trancheto='16:00:00';
        }
        if($slotkey==9){
            $tranchefrom='16:00:00';
            $trancheto='18:00:00';
        }

        if($slotkey==11){
            $tranchefrom='18:00:00';
            $trancheto='20:00:00';
        }
        if($slotkey==13){
            $tranchefrom='08:00:00';
            $trancheto='20:00:00';
        }

        return array(
            'tranchefrom'=>$tranchefrom,
            'trancheto'=>$trancheto
        );
    }

}
