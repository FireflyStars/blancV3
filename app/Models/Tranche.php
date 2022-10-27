<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Twilio\Rest\Api\V2010\Account\TranscriptionContext;

class Tranche extends Model
{
    //
    public static function getTranches(){
        return [
          1=>'8am-10am',3=>'10am-12pm',5=>'12pm-2pm',7=>'2pm-4pm',9=>'4pm-6pm',11=>'6pm-8pm',13=>'8am-8pm'
        ];
    }

    public static function getDays(){
        return [
            'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY'
        ];
    }

    public static function getDeliveryPlanningTranches(){
        return [
            1=>'8am-10am',3=>'10am-12pm',5=>'12pm-2pm',7=>'2pm-4pm',9=>'4pm-6pm',11=>'6pm-8pm'
        ];
    }

    public static function getDeliveryPlanningTranchesForApi(){
        return [
            1=>'8-10 am',3=>'10-12 pm',5=>'12-2 pm',7=>'2-4 pm',9=>'4-6 pm',11=>'6-8 pm',13=>'8-8 pm'
        ];
    }


    public static function getDeliveryPlanningDays(){
        return [
            'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY'
        ];
    }

    public static function getTrancheTime(){
        $time_tranche = [];
        $start = 8*3600;
        $end = 20*3600;
        $interval = 2*3600;

        $index = 1;
        while($start < $end){
            $start_time =  gmdate('H:i:s',$start);
            $end_time = gmdate('H:i:s',($start+$interval));
            $time_tranche[$index] = ['from'=>$start_time,'to'=>$end_time];

            $index +=2;

            $start += $interval;
        }

        return $time_tranche;
    }

    public static function guessReccuringDate($day,$slotendtime){
        if(date('Y-m-d')==date('Y-m-d',strtotime($day))){
            $slotendtime=date('Y-m-d H:i:s',strtotime($slotendtime));

            if(Carbon::now()->greaterThan(Carbon::createFromTimeString($slotendtime)))
              $day="next ".$day;
        }
        return $day;
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

    /***
     * @param mixed $slot
     * @param string $format
     * @return bool
     */
    public static function isSlotValid($slot,$format='H:i:s'){

        if($format=='H:i:s'){
            $slot[0]=date('H:i:s',strtotime($slot[0]));
            $slot[1]=date('H:i:s',strtotime($slot[1]));
            if($slot[0]=='08:00:00'&&$slot[1]=='10:00:00')
                return true;
            if($slot[0]=='10:00:00'&&$slot[1]=='12:00:00')
                return true;
            if($slot[0]=='12:00:00'&&$slot[1]=='14:00:00')
                return true;
            if($slot[0]=='14:00:00'&&$slot[1]=='16:00:00')
                return true;
            if($slot[0]=='16:00:00'&&$slot[1]=='18:00:00')
                return true;
            if($slot[0]=='18:00:00'&&$slot[1]=='20:00:00')
                return true;
            if($slot[0]=='08:00:00'&&$slot[1]=='20:00:00')
                return true;
        }

        return false;
    }
    public static function getAvailableSlotAndDates($day,$tranche,$returnBySlotKey=false,$weeks=3){

        $NbrDeliverySlotMax=setting('site.NbrDeliverySlotMax');
        $NbrSlot=count($tranche);


        $slot_dates=[];
        $slots=Tranche::getDeliveryPlanningTranchesForApi();
        $holidays=Holiday::getHolidays();



        $cutOffTime=setting('site.delivery_cutoff_time');
        $skipDates=[];
        if($cutOffTime!='') {

            $cutOffTime = Carbon::createFromFormat('H',$cutOffTime);
            $nl="\n";
            $explain='CUTOFFTIME: '.$cutOffTime->format('H:i').$nl;
            if($cutOffTime->isPast()){
                $explain.='CURRENT_TIME @'.date('H:i').$nl;

                $tomorrow=Carbon::tomorrow();
                $DATE=$tomorrow;
                $explain.='SKIPPING TOMORROW: '.$tomorrow->format('Y-m-d').$nl;
                $skipDates[]=$tomorrow->format('Y-m-d');
                while($DATE->isSunday()||in_array($DATE->format('Y-m-d'),$holidays)){//isSunday
                    if($DATE->isSunday()) {
                        $explain .= $DATE->format('Y-m-d') . ' is Sunday, Now skipping Monday: ';
                    }else{
                        $explain .= $DATE->format('Y-m-d') . ' is a Holiday, Now skipping next date: ';
                    }

                    $nextDate=$DATE->addDays(1);


                    $skipDates[]=$nextDate->format('Y-m-d');
                    $explain.=end($skipDates).$nl;
                    $DATE=$nextDate;
                }

               // dump($explain);
            }
        }

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

                if(in_array($date,$skipDates)) //skipdates due to cutoff time
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

                if(in_array($date,$skipDates)) //skipdates due to cutoff time
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

                $max=$NbrDeliverySlotMax;
                if($slotkey==3&&in_array(strtoupper($day),['MONDAY',"WEDNESDAY",'THURSDAY','FRIDAY'])){
                    $max=20;
                }
                //calcul
                if($NbrBookingBySlot+$NbrBookingDaybySlot<$max){
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



    public static function getDayTrancheForCustomer($customer,$day){

        if(strtoupper($day)=="MONDAY"){
            $tranche=Tranche::getFormattedTranche(json_decode($customer->DeliveryMon)[0]);
            return $tranche;
        }
        if(strtoupper($day)=="TUESDAY"){
            $tranche=Tranche::getFormattedTranche(json_decode($customer->DeliveryTu)[0]);
            return $tranche;
        }
        if(strtoupper($day)=="WEDNESDAY"){
            $tranche=Tranche::getFormattedTranche(json_decode($customer->DeliveryWed)[0]);
            return $tranche;
        }
        if(strtoupper($day)=="THURSDAY"){
            $tranche=Tranche::getFormattedTranche(json_decode($customer->DeliveryTh)[0]);
            return $tranche;
        }
        if(strtoupper($day)=="FRIDAY"){
            $tranche=Tranche::getFormattedTranche(json_decode($customer->DeliveryFri)[0]);
            return $tranche;
        }
        if(strtoupper($day)=="SATURDAY"){
            $tranche=Tranche::getFormattedTranche(json_decode($customer->DeliverySat)[0]);
            return $tranche;
        }


    }

    public static function formatToAmPm($time,$time2=''){

        $time=date('hA',strtotime($time));
        if(substr($time,0,1)=='0'){
            $time=str_replace('0','',$time);
        }
        $time2=($time2!=''?date('hA',strtotime($time2)):'');
        if(substr($time2,0,1)=='0'){
            $time2=str_replace('0','',$time2);
        }
        return $time.($time2!=''?' - '.$time2:'');
    }

    public static function getSlotFromTranche($from,$to){
        if($from=='08:00:00'&&$to=='10:00:00')
            return 1;
        if($from=='10:00:00'&&$to=='12:00:00')
            return 3;
        if($from=='12:00:00'&&$to=='14:00:00')
            return 5;
        if($from=='14:00:00'&&$to=='16:00:00')
            return 7;
        if($from=='16:00:00'&&$to=='18:00:00')
            return 9;
        if($from=='18:00:00'&&$to=='20:00:00')
            return 11;
        if($from=='08:00:00'&&$to=='20:00:00')
            return 13;
    }
}
