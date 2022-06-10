<?php
namespace App\Models;

use Carbon\Carbon;
use function GuzzleHttp\Psr7\str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Holiday;

class OrderRecurringCreator
{

    /*
 *
 * // retrieve begin of week

//end of last week (var 2weeks)

find all dates for pickup and delivery between the dates // take care for holidays/pause

if pause or holiday is parametre and we have an existing order
$createorder->pickup_Date=xxxx-xx-xx;
$createorder->Delivery_Date=xxxx-xx-xx;
bookings[]=array(
	$createorder
)

//if the pickup is in future just del existing order/pickup/delivery,  if already pickup and we have a delivery that fall on a holiday find the next delivery date , creer new deliveryask and update Order/Promised date
holidays[]=array(
	'yyyy-mm-dd'
)


the following depends if a customerID has been passed as a Parametre
//if the pickup is in future just del existing order/pickup/delivery,  if already pickup and we have a delivery that fall on a pause find the next delivery date , creer new deliveryask and update Order/Promised date
paused_bookings[]=array(
	'yyyy-mm-dd'
)

//if any booking that false withing those day is marked as cancelled//if the pickup is in future just del existing order/pickup/delivery,  if already pickup and we have a delivery that fall on a pause find the next delivery date , creer new deliveryask and update Order/Promised date

cancel_bookings[]=array(
	'yyyy-mm-dd'
)


//verify time slot


 *
 *
 * **/



    public $holidays;

    public $num_weeks=1;

    public $ENABLE_ORDER_RECURRING_LOG;

    public $file_time;


    public $possibleTimeSlots=['[1]','[3]','[5]','[7]','[9]','[11]','[13]'];

    public $FROMDATE;

    public $TODATE;

    public function __construct($CustomerID=null,$trigger=null)
    {

        $this->file_time= date('Y-m-d');
        //Storage::disk('local')->delete('order_recurring'.DIRECTORY_SEPARATOR.'OR_'.$this->file_time.'.md');
        $this->holidays=Holiday::getHolidays();

        $this->ENABLE_ORDER_RECURRING_LOG=getenv('ENABLE_ORDER_RECURRING_LOG');
        $this->l("## Cron order reccuring trigger @ ".date('Y-m-d H:i:s'));
        $this->l("\t Triggered by $trigger");
        if($CustomerID!=null)
            $this->l("**Cron triggered for a single customer: $CustomerID**");
        //find dates between which order will be created
        $FROMDATE=date('Y-m-d');
        $TODATE=date('Y-m-d',strtotime(($this->num_weeks*7).' days'));
        $this->l("Processing recurring booking for $this->num_weeks weeks between `$FROMDATE` and `$TODATE`");
        $this->FROMDATE=Carbon::createFromFormat('Y-m-d',$FROMDATE);
        $this->TODATE=Carbon::createFromFormat('Y-m-d',$TODATE);


        $customers=DB::table('infoCustomer');
        if($CustomerID!=null){
            $customers=$customers->where('CustomerID',"=",$CustomerID); // to process recurring booking for only one customer
        }
        if($trigger=='CRON JOB'){
            DB::table('settings')->where('key','=','cron.recurring_last_execution')->update([
                'value'=>date('Y-m-d H:i:s')
            ]);
        }
        $customers=$customers->get();
        foreach ($customers as $customer){
            if($trigger=='CRON JOB'){
                DB::table('settings')->where('key','=','cron.recurring_last_execution')->update([
                    'value'=>date('Y-m-d H:i:s')
                ]);
            }
            $CustomerID=$customer->CustomerID;
            $this->l("   ".'Now processing customer: `'.$customer->CustomerID.'`');
            $BOOKINGS=$this->getRecurringBookingDates($customer);


            foreach ($BOOKINGS as $PICKUP_DATE){
                    $DELIVERY_DATE=$this->getDeliveryDate($PICKUP_DATE,$customer);
                    $this->l("Creating order recurring with pickup date `$PICKUP_DATE` and delivery date `$DELIVERY_DATE`");
                    $this->createOrder($PICKUP_DATE,$DELIVERY_DATE,$customer);
            }


            //checks against holidays
            $this->l('Check 01: Checking if existing order\'s pickup date or order\'s delivery date falls on a recently added holiday.');
            $infoOrders=DB::table('infoOrder')->where('CustomerID','=',$CustomerID)->where(function($query) {
                $query->whereDate('DatePickup','>=',date('Y-m-d'))->orWhereDate('DateDeliveryAsk','>=',date('Y-m-d'));

            })->where('Status','=','RECURRING')->get();
            foreach ($infoOrders as $infoOrder){
                if($this->isDateHoliday($infoOrder->DatePickup)&&$this->isDateFuture($infoOrder->DatePickup)){

                    $this->l("The pickup date `$infoOrder->DatePickup` for Order Recurring `$infoOrder->id` is scheduled on a newly added holiday. This order will now be deleted since it has not been picked up yet.");
                    $this->delOrder($infoOrder);

                }else if($this->isDateHoliday($infoOrder->DateDeliveryAsk)&&$this->isDateFuture($infoOrder->DateDeliveryAsk)){

                    $this->l("The delivery date `$infoOrder->DateDeliveryAsk` for Order Recurring `$infoOrder->id` is scheduled on a newly added holiday. This delivery will now be reschedule on next possible date and time slot since it has already been picked up and we are obliged to deliver the item back to the customer.");
                    $this->reScheduleDelivery($infoOrder,$customer);

                }
            }
            $this->l('Check 01: Check ended');
            if($CustomerID!=null) {
                //checks againt paused dates
                $this->l('Check 02: Checking if existing order\'s pickup date or order\'s delivery date falls between a recently updated pause dates.');
                $infoOrders = DB::table('infoOrder')->where('CustomerID', '=', $CustomerID)->where(function ($query) {
                    $query->whereDate('DatePickup', '>=', date('Y-m-d'))->orWhereDate('DateDeliveryAsk', '>=', date('Y-m-d'));

                })->where('Status', '=', 'RECURRING')->get();

                foreach ($infoOrders as $infoOrder){

                    if($this->isDatePaused($infoOrder->DatePickup,$customer)&&($this->isDateFuture($infoOrder->DatePickup)||$this->isToday($infoOrder->DatePickup))) {
                        $this->l("The pickup date `$infoOrder->DatePickup` for Order Recurring `$infoOrder->id` is Paused. This order will now be deleted since it has not been picked up yet.");
                        $this->delOrder($infoOrder);
                    }else if($this->isDatePaused($infoOrder->DateDeliveryAsk,$customer)&&$this->isDateFuture($infoOrder->DateDeliveryAsk)){
                        $this->l("The delivery date `$infoOrder->DateDeliveryAsk` for Order Recurring `$infoOrder->id` is Paused. This delivery will now be reschedule on next possible date and time slot since it has already been picked up and we are obliged to deliver the item back to the customer.");
                        $this->reScheduleDelivery($infoOrder,$customer);
                    }


                }

                $this->l('Check 02: Check ended');

                //checks against cancel dates
                $this->l('Check 03: Checking if existing order\'s pickup date or order\'s delivery date falls has recently been cancelled.');
                $noBookingDays=$this->getDaysWithoutBooking($customer);
                $infoOrders = DB::table('infoOrder')->where('CustomerID', '=', $CustomerID)->where(function ($query) {
                    $query->whereDate('DatePickup', '>=', date('Y-m-d'))->orWhereDate('DateDeliveryAsk', '>=', date('Y-m-d'));

                })->where('Status', '=', 'RECURRING')->get();

                foreach ($infoOrders as $infoOrder){
                    $pickupday=date('l',strtotime($infoOrder->DatePickup));
                    $deliveryday=date('l',strtotime($infoOrder->DateDeliveryAsk));
                    if($this->isDateFuture($infoOrder->DatePickup)&&in_array($pickupday,$noBookingDays)){

                        $this->l("The order `$infoOrder->id` with pickup date `$infoOrder->DatePickup` is on a `$pickupday`. `$pickupday` is cancelled and no longer scheduled for recurring booking for this customer. Since it is an upcoming order in the future, the order will be deleted");
                        $this->delOrder($infoOrder);
                    }else if(($this->isDateFuture($infoOrder->DateDeliveryAsk)||date('Y-m-d')==$infoOrder->DateDeliveryAsk)&&in_array($deliveryday,$noBookingDays)){

                        $infoInvoices=DB::table('infoInvoice')->where('OrderID','=',$infoOrder->OrderID)->where('OrderID','!=','')->get();
                        if($infoInvoices->count()==0){
                            $this->l("The order `$infoOrder->id` with delivery date `$infoOrder->DateDeliveryAsk` is on a `$deliveryday`. `$deliveryday` is cancelled and no longer scheduled for recurring booking for this customer. Since the order does not have any item. Now deleting pickup/delivery/order");
                            $this->delOrder($infoOrder);
                        }else {
                            $this->l("The order `$infoOrder->id` with delivery date `$infoOrder->DateDeliveryAsk` is on a `$deliveryday`. `$deliveryday` is cancelled and no longer scheduled for recurring booking for this customer. Since the item has already been picked up and we are obliged to deliver the item back to the customer. Now rescheduling delivery");
                            $this->reScheduleDelivery($infoOrder, $customer);
                        }
                    }
                }

                $this->l('Check 03: Check ended');

                //check for modified timeslot
                $this->l('Check 04: Checking if existing order\'s pickup time slot or order\'s delivery time slot has changed');

                $infoOrders = DB::table('infoOrder')->where('CustomerID', '=', $CustomerID)->where(function ($query) {
                    $query->whereDate('DatePickup', '>=', date('Y-m-d'))->orWhereDate('DateDeliveryAsk', '>=', date('Y-m-d'));

                })->where('Status', '=', 'RECURRING')->get();

                foreach ($infoOrders as $infoOrder){
                    $pickup_slot=$this->getTimeSlot($infoOrder->DatePickup,$customer);

                    $pickuptime_updated=0;
                    $deliverytime_updated=0;
                   if($pickup_slot['tranchefrom']!="")
                    $pickuptime_updated=DB::table('pickup')->where('PickupID','=',$infoOrder->PickupID)->update([
                        'trancheFrom'=>$pickup_slot['tranchefrom'],
                        'trancheto'=>$pickup_slot['trancheto'],
                        'order_id'=>$infoOrder->id,
                    ]);
                    $delivery_slot=$this->getTimeSlot($infoOrder->DateDeliveryAsk,$customer);

                  if($delivery_slot['tranchefrom']!="")
                    $deliverytime_updated=DB::table('deliveryask')->where('DeliveryaskID','=',$infoOrder->DeliveryaskID)->update([
                        'trancheFrom'=>$delivery_slot['tranchefrom'],
                        'trancheto'=>$delivery_slot['trancheto'],
                        'order_id'=>$infoOrder->id,
                    ]);

                  if($pickuptime_updated>0)
                      $this->l("The pickup time slot for order `$infoOrder->id` has been updated to `".$pickup_slot['tranchefrom']."` - `".$pickup_slot['trancheto'].'`');
                  if($deliverytime_updated>0)
                        $this->l("The delivery time slot for order `$infoOrder->id` has been updated to `".$delivery_slot['tranchefrom']."` - `".$delivery_slot['trancheto'].'`');

                    $this->cur_orders[] = $infoOrder->id;
                }
                $this->l('Check 04: Check ended');
            }
            $this->l('End processing customer: `'.$customer->CustomerID.'`');

        }



        $this->l('## End Cron : '.date('Y-m-d H:i:s'));


    }
    public function isDateHoliday($date){
        return in_array($date,$this->holidays);
    }

    public function isDateFuture($date){
        $d1 = Carbon::createFromFormat('Y-m-d', $date);
        return !$d1->isPast();
    }
    public function isToday($date){
        $d1 = Carbon::createFromFormat('Y-m-d', $date);
        return $d1->isToday();
    }
    public function isDatePaused($date,$customer){
        $pausedFrom=$customer->PauseDateFrom;
        $pausedTo=$customer->PauseDateTo;

        if($pausedFrom!=''&&$pausedTo!='') {
            $d1 = Carbon::createFromFormat('Y-m-d', $pausedFrom);
            $d2 = Carbon::createFromFormat('Y-m-d', $pausedTo);
            $d3=Carbon::createFromFormat('Y-m-d',$date);
            if($d3->between($d1,$d2))
                return true;
        }else if($pausedFrom!=''){
            $d1=Carbon::createFromFormat('Y-m-d',$pausedFrom);
            $d3=Carbon::createFromFormat('Y-m-d',$date);
            if($d3->isAfter($d1))
                return true;
        }else if($pausedTo!=''){
            $d2=Carbon::createFromFormat('Y-m-d',$pausedTo);

            $d3=Carbon::createFromFormat('Y-m-d',$date);
            if($d3->isBefore($d2))
                return true;
            }
            return false;
    }
    public function scheduleBookingDay($customer){
        $ScheduleBookingDay=[];

        if (in_array($customer->DeliveryMon, $this->possibleTimeSlots))
            $ScheduleBookingDay[]='Monday';
        if (in_array($customer->DeliveryTu, $this->possibleTimeSlots))
            $ScheduleBookingDay[]='Tuesday';
        if (in_array($customer->DeliveryWed, $this->possibleTimeSlots))
            $ScheduleBookingDay[]='Wednesday';
        if (in_array($customer->DeliveryTh, $this->possibleTimeSlots))
            $ScheduleBookingDay[]='Thursday';
        if (in_array($customer->DeliveryFri, $this->possibleTimeSlots))
            $ScheduleBookingDay[]='Friday';
        if (in_array($customer->DeliverySat, $this->possibleTimeSlots))
            $ScheduleBookingDay[]='Saturday';

        //sort days by future occurence: ex: if booking is on wednesday and saturday and today is thursday then we return Saturday and Wednesday as next occurence would be Saturday
        /*if(!empty($ScheduleBookingDay)&&count($ScheduleBookingDay)==2){
            $d=$ScheduleBookingDay[0];
           if(date('Y-m-d',strtotime($d." this week"))!=date('Y-m-d',strtotime($d))){
               $ScheduleBookingDay[0]=$ScheduleBookingDay[1];
               $ScheduleBookingDay[1]=$d;
           }
        }*/
        return $ScheduleBookingDay;
    }
    public function getDaysWithoutBooking($customer){
        $noBookingDays=[];

        if (!in_array($customer->DeliveryMon, $this->possibleTimeSlots))
            $noBookingDays[]='Monday';
        if (!in_array($customer->DeliveryTu, $this->possibleTimeSlots))
            $noBookingDays[]='Tuesday';
        if (!in_array($customer->DeliveryWed, $this->possibleTimeSlots))
            $noBookingDays[]='Wednesday';
        if (!in_array($customer->DeliveryTh, $this->possibleTimeSlots))
            $noBookingDays[]='Thursday';
        if (!in_array($customer->DeliveryFri, $this->possibleTimeSlots))
            $noBookingDays[]='Friday';
        if (!in_array($customer->DeliverySat, $this->possibleTimeSlots))
            $noBookingDays[]='Saturday';

        return $noBookingDays;
    }
    public function getRecurringBookingDates($customer){
        $ScheduleBookingDay=$this->scheduleBookingDay($customer);

        if(empty($ScheduleBookingDay)){
            $this->l('No recurring booking scheduled for this customer.');
            return $ScheduleBookingDay;
        }else {
            $this->l('Bookings scheduled on `' . implode('s` and on `', $ScheduleBookingDay) . 's`');
        }

        $PossibleScheduleBookingDates=[];
        $d=0;
        $numweeks=$this->num_weeks;
        $TODATE=$this->TODATE;
        //si un recurring par semaine alors je genere sur une periode de 2 semaines sinon je genere sur une periode  dune semaine
        if($this->num_weeks==1&&count($ScheduleBookingDay)==1){
            $numweeks=2;
            $TODATE=
            $TODATE=date('Y-m-d',strtotime(($numweeks*7).' days'));
            $TODATE=Carbon::createFromFormat('Y-m-d',$TODATE);

        }
        while($d<$numweeks) {
        foreach ($ScheduleBookingDay as $dayname){
                $date = date('Y-m-d', strtotime($dayname . "+ ".($d*7)." days"));
                $date1 = Carbon::createFromFormat('Y-m-d', $date);
                if ($date1->between($this->FROMDATE, $TODATE)) {
                    $PossibleScheduleBookingDates[] = $date;
                }
            }
            $d++;
        }

        $this->l('Possible booking dates (Pickup): `'.implode('`, `',$PossibleScheduleBookingDates).'`');

        $pausedFrom=$customer->PauseDateFrom;
        $pausedTo=$customer->PauseDateTo;
        if($pausedFrom!=''){
            $this->l('Paused date from `'.$pausedFrom.'`');
        }
        if($pausedTo!=''){
            $this->l('Paused date to `'.$pausedTo.'`');
        }
        if($pausedFrom!=''&&$pausedTo!=''){
            $d1=Carbon::createFromFormat('Y-m-d',$pausedFrom);
            $d2=Carbon::createFromFormat('Y-m-d',$pausedTo);
            foreach ($PossibleScheduleBookingDates as $k=>$PossibleScheduleBookingDate){
                $d3=Carbon::createFromFormat('Y-m-d',$PossibleScheduleBookingDate);
                if($d3->between($d1,$d2))
                    unset($PossibleScheduleBookingDates[$k]);
            }
        }else if($pausedFrom!=''){
            $d1=Carbon::createFromFormat('Y-m-d',$pausedFrom);
            foreach ($PossibleScheduleBookingDates as $k=>$PossibleScheduleBookingDate){
                $d3=Carbon::createFromFormat('Y-m-d',$PossibleScheduleBookingDate);
                if($d3->isAfter($d1))
                    unset($PossibleScheduleBookingDates[$k]);
            }
        }else if($pausedTo!=''){
            $d2=Carbon::createFromFormat('Y-m-d',$pausedTo);
            foreach ($PossibleScheduleBookingDates as $k=>$PossibleScheduleBookingDate){
                $d3=Carbon::createFromFormat('Y-m-d',$PossibleScheduleBookingDate);
                if($d3->isBefore($d2))
                    unset($PossibleScheduleBookingDates[$k]);
            }
        }



        if($pausedFrom!=''||$pausedTo!='')
        $this->l('Possible booking dates (Pickup) after considering Paused dates: `'.implode('`, `',$PossibleScheduleBookingDates).'`');


        $fall_in_holiday=false;
        foreach ($PossibleScheduleBookingDates as $k=>$PossibleScheduleBookingDate){
            if(in_array($PossibleScheduleBookingDate,$this->holidays)){
                $this->l("`$PossibleScheduleBookingDate` is a holiday.");
                unset($PossibleScheduleBookingDates[$k]);
                $fall_in_holiday=true;
            }
        }
        foreach ($PossibleScheduleBookingDates as $k=>$PossibleScheduleBookingDate){
            if(date('Y-m-d')==$PossibleScheduleBookingDate){
                $timeslot=$this->getTimeSlot($PossibleScheduleBookingDate,$customer);
                $datetimeslot=Carbon::createFromFormat('Y-m-d H:i:s',$PossibleScheduleBookingDate.' '.$timeslot['trancheto']);
                if($datetimeslot->isPast()){
                    $this->l("The time slot `".$timeslot['tranchefrom']."` - `".$timeslot['trancheto']."` on $PossibleScheduleBookingDate has expired. current time: ".date('Y-m-d H:i:s'));
                    unset($PossibleScheduleBookingDates[$k]);
                    $this->l('Booking dates (Pickup) after considering Paused dates, Holidays and expired date time slot: `'.implode('`, `',$PossibleScheduleBookingDates).'`');
                }
            }
        }

        if($fall_in_holiday)
            $this->l('Booking dates (Pickup) after considering Paused dates and Holidays: `'.implode('`, `',$PossibleScheduleBookingDates).'`');

        $this->l('Check 00: Checking existing orders for the dates `'.implode('`, `',$PossibleScheduleBookingDates).'`');
        foreach ($PossibleScheduleBookingDates as $k=>$PossibleScheduleBookingDate){


            $infoOrder=DB::table('infoOrder')->where('CustomerID','=',$customer->CustomerID)
            ->where(function($query) use($PossibleScheduleBookingDate){
                $query->where(function($query2) use($PossibleScheduleBookingDate){
                    $query2->where('DatePickup','=',$PossibleScheduleBookingDate)
                    ->where('Status','=','RECURRING');

                }) ->orWhere('deliverymethod','recurring');
           
            })->first();

            if($infoOrder!=null){
         
                $this->l("A recurring order exist for `$PossibleScheduleBookingDate`. No order will be created for this date.");
                $this->l("Checking if the order delivery date `$infoOrder->DateDeliveryAsk` need to be rescheduled");
                $this->reScheduleDelivery($infoOrder,$customer);
                unset($PossibleScheduleBookingDates[$k]);
            }
        }
        $this->l('Check 00: Check ended');
        sort($PossibleScheduleBookingDates);
        if(empty($PossibleScheduleBookingDates)){
            $this->l("No new booking dates (new Pickup) found  for the next $this->num_weeks weeks after considering Paused dates and Holidays and existing Orders. No new order recurring will be created.");
        }else {
            $this->l('Final booking dates (Pickup) after considering Paused dates, Holidays, expired date time slot and existing Orders: `' . implode('`, `', $PossibleScheduleBookingDates) . '`');
        }

        return $PossibleScheduleBookingDates;

    }

    public function reScheduleDelivery($infoOrder,$customer){

        $this->l('reScheduleDelivery()');
        $ScheduleBookingDay=$this->scheduleBookingDay($customer);
        $pickupdate=Carbon::createFromFormat('Y-m-d',$infoOrder->DatePickup);
        if(empty($ScheduleBookingDay)){
            $this->l("Cannot reschedule, customer does not have any recurring booking");
            return;
        }
        $dg=new DayGenerator($ScheduleBookingDay);
        $delivery_date='';
        $d=0;
        while($delivery_date=='') {

            $dayObj=$dg->next();
            $dayname=$dayObj->dayname;
            if($dayObj->return_to_start)
                $d++;
                $possible_delivery_date = date('Y-m-d', strtotime($dayname . " this week + " . ($d * 7) . " days"));
                $d1=Carbon::createFromFormat('Y-m-d',$possible_delivery_date);
            if($d1->isSameDay($pickupdate))
            $this->l("### skipping same day: `$possible_delivery_date`");
                if($d1->isAfter($pickupdate)&&!$d1->isSameDay($pickupdate)) {
                    $this->l("New possible booking date (Delivery): `$possible_delivery_date`");
                    $pausedFrom = $customer->PauseDateFrom;
                    $pausedTo = $customer->PauseDateTo;

                    if ($pausedFrom != '' && $pausedTo != '') {
                        $d1 = Carbon::createFromFormat('Y-m-d', $pausedFrom);
                        $d2 = Carbon::createFromFormat('Y-m-d', $pausedTo);

                        $d3 = Carbon::createFromFormat('Y-m-d', $possible_delivery_date);
                        if ($d3->between($d1, $d2)) {
                            $this->l("Possible booking date (Delivery): `$possible_delivery_date` is between pause dates `$pausedFrom` and `$pausedTo`. Skipping further test.");
                            continue;
                        }


                    } else if ($pausedFrom != '') {
                        $d1 = Carbon::createFromFormat('Y-m-d', $pausedFrom);

                        $d3 = Carbon::createFromFormat('Y-m-d', $possible_delivery_date);
                        if ($d3->isAfter($d1)) {
                            $this->l("Possible booking date (Delivery): `$possible_delivery_date` is after pause start date(pause indefinitely) `$pausedFrom`.End pause not specified. Cannot assign new date.Skipping further test.");
                            $delivery_date = '2020-01-01';
                            continue;
                        }

                    } else if ($pausedTo != '') {
                        $d2 = Carbon::createFromFormat('Y-m-d', $pausedTo);

                        $d3 = Carbon::createFromFormat('Y-m-d', $possible_delivery_date);
                        if ($d3->isBefore($d2)) {
                            $this->l("Possible booking date (Delivery): `$possible_delivery_date` is before pause end date(pause to) `$pausedTo`. Skipping further test.");
                            continue;
                        }

                    }


                    if (in_array($possible_delivery_date, $this->holidays)) {
                        $this->l("`$possible_delivery_date` is a holiday. Finding next delivery date.");
                        continue;
                    }

                    $delivery_date = $possible_delivery_date;
                    $this->l("Next booking date (Delivery) is: `$delivery_date`");
                    break;
                }


        }

        if($infoOrder->DateDeliveryAsk!=$delivery_date) {
            $timeslot = $this->getTimeSlot($delivery_date, $customer);
            $this->l('The time slot is: `' . $timeslot['tranchefrom'] . '` - `' . $timeslot['trancheto'] . '`');


            $existing_deliveryask = DB::table('deliveryask')->where('CustomerID', '=', $customer->CustomerID)->where('DeliveryaskID', '=', $infoOrder->DeliveryaskID)->first();
           if($existing_deliveryask==null){
            $this->l('Error: Existing delivery ask not found');
            return;
           }
            $deliveryPref = DB::table('DeliveryPreference')->where('CustomerID', '=', $customer->CustomerID)->first();
            $deliveryask_id = DB::table('deliveryask')->insertGetId([
                'PhoneNumber' => ($deliveryPref?$deliveryPref->PhoneNumber:''),
                'CodeCountry' => ($deliveryPref?$deliveryPref->CodeCountry:''),
                'TypeDelivery' => ($deliveryPref?$deliveryPref->TypeDelivery:''),
                'CustomerID' => $customer->CustomerID,
                'AddressID' => $existing_deliveryask->AddressID,
                'id_customer' => $existing_deliveryask->id_customer,
                'comment' => ($deliveryPref?$deliveryPref->OtherInstruction:''),
                'trancheto' => $timeslot['trancheto'],
                'trancheFrom' => $timeslot['tranchefrom'],
                'address_id' => $existing_deliveryask->address_id,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'REC',
                'date' => $delivery_date,

            ]);
            $new_deliveryask = DB::table('deliveryask')->where('id', '=', $deliveryask_id)->first();
            $this->l("DeliveryaskID changing from `$infoOrder->DeliveryaskID` to `$new_deliveryask->DeliveryaskID` on order `$infoOrder->id`");
            DB::table('infoOrder')->where('id', '=', $infoOrder->id)->update([
                'DeliveryaskID' => $new_deliveryask->DeliveryaskID,
                'DateDeliveryAsk' => $delivery_date
            ]);
            $this->l("Rescheduling delivery performed on `$infoOrder->id` from `$infoOrder->DateDeliveryAsk` to `$delivery_date`");
            DB::table('deliveryask')->where('CustomerID', '=', $customer->CustomerID)->where('DeliveryaskID', '=', $infoOrder->DeliveryaskID)->update([
                'date' => '2020-01-01',
                'status' => 'DEL',
            ]);
            $this->l("Previous deliveryask with DeliveryaskID `$infoOrder->DeliveryaskID` has changed status to DEL");
            $infoInvoices = DB::table('infoInvoice')
                ->select('infoInvoice.InvoiceID')
                ->join('infoOrder', function ($join) use ($infoOrder) {
                    $join->on('infoOrder.OrderID', '=', 'infoInvoice.OrderID')
                        ->where('infoOrder.OrderID', '<>', '')
                        ->where('infoOrder.id', '=', $infoOrder->id);
                })->get();

            $promiseddate_updated = false;
            foreach ($infoInvoices as $infoInvoice) {
                DB::table('infoitems')->where('InvoiceID', '=', $infoInvoice->InvoiceID)->update([
                    'PromisedDate' => $delivery_date
                ]);
                $promiseddate_updated = true;
            }

            if ($promiseddate_updated) {
                $this->l("Promised Date updated to: `$delivery_date`");
            }
        }else{
            $this->l("Same date rescheduling avoided.");
        }
    }
    public function delOrder($infoOrder){
        DB::table('pickup')->where('PickupID', '=', $infoOrder->PickupID)->whereIn('status', ['REC','REC-NOK','REC-DONE'])
            ->update([
                'status' => 'DEL'
            ]);

        $this->l("Pickup with PickupID `$infoOrder->PickupID` updated to status DEL");

        DB::table('deliveryask')->where('DeliveryaskID', '=', $infoOrder->DeliveryaskID)->whereIn('status', ['REC','REC-NOK','REC-DONE'])
            ->update([
                'status' => 'DEL'
            ]);
        $this->l("Delivery with DeliveryaskID `$infoOrder->DeliveryaskID` updated to status DEL");
        DB::table('infoOrder')->where('id', '=', $infoOrder->id)->where('Status', '=', 'RECURRING')
            ->update([
                'DatePickup' => '2020-01-01',
                'DateDeliveryAsk' => '2020-01-01',
                'Status' => 'DELETE'
            ]);
        $this->l("infoOrder with id `$infoOrder->id` updated to Status DELETE");
    }


    public function getDeliveryDate($pickup_date,$customer){
        $this->l('getDeliveryDate()');
        $ScheduleBookingDay=$this->scheduleBookingDay($customer);
        $dg=new DayGenerator($ScheduleBookingDay);
        $pickupdate=Carbon::createFromFormat('Y-m-d',$pickup_date);

        $delivery_date='';
        $d=0;


        while($delivery_date=='') {

            $dayObj=$dg->next();
            $dayname=$dayObj->dayname;
            if($dayObj->return_to_start)
                $d++;
            $this->l('### D'.$d);
            $this->l('### pickup'.$pickupdate);
            $this->l('###'.$dayname);
                $possible_delivery_date = date('Y-m-d', strtotime($dayname . " this week + " . ($d * 7) . " days"));
            $this->l('### del'.$possible_delivery_date);
                $d1=Carbon::createFromFormat('Y-m-d',$possible_delivery_date);
            if($d1->isSameDay($pickupdate))
                $this->l("### skipping same day: `$possible_delivery_date`");
                if($d1->isAfter($pickupdate)&&!$d1->isSameDay($pickupdate)) {
                    $this->l("Possible Delivery date: `$possible_delivery_date`");
                    $pausedFrom = $customer->PauseDateFrom;
                    $pausedTo = $customer->PauseDateTo;

                    if ($pausedFrom != '' && $pausedTo != '') {
                        $d1 = Carbon::createFromFormat('Y-m-d', $pausedFrom);
                        $d2 = Carbon::createFromFormat('Y-m-d', $pausedTo);

                        $d3 = Carbon::createFromFormat('Y-m-d', $possible_delivery_date);
                        if ($d3->between($d1, $d2)) {
                            $this->l("Possible Delivery date: `$possible_delivery_date` is between pause dates `$pausedFrom` and `$pausedTo`. Skipping further test.");
                            continue;
                        }


                    } else if ($pausedFrom != '') {
                        $d1 = Carbon::createFromFormat('Y-m-d', $pausedFrom);

                        $d3 = Carbon::createFromFormat('Y-m-d', $possible_delivery_date);
                        if ($d3->isAfter($d1)) {
                            $this->l("Possible Delivery date: `$possible_delivery_date` is after pause start date(pause indefinitely) `$pausedFrom`.End pause not specified. Cannot assign new date.Skipping further test.");
                            $delivery_date = '2020-01-01';
                            continue;
                        }

                    }else if($pausedTo!=''){
                        $d2 = Carbon::createFromFormat('Y-m-d', $pausedTo);

                        $d3 = Carbon::createFromFormat('Y-m-d', $possible_delivery_date);
                        if ($d3->isBefore($d2)) {
                            $this->l("Possible Delivery date: `$possible_delivery_date` is before pause end date(pause to) `$pausedTo`. Skipping further test.");
                            continue;
                        }

                    }


                    if (in_array($possible_delivery_date, $this->holidays)) {
                        $this->l("`$possible_delivery_date` is a holiday. Finding next delivery date.");
                        continue;
                    }

                    $delivery_date = $possible_delivery_date;
                    $this->l("Delivery date is: `$delivery_date` for Pickup date `$pickup_date`");
                    break;
                }


        }
        return $delivery_date;
    }

    public function getTimeSlot($date,$customer){
            $dayname=date('l',strtotime($date));
            return Tranche::getDayTrancheForCustomer($customer,$dayname);
    }

    public function l($string){

        if($this->ENABLE_ORDER_RECURRING_LOG)
        Storage::disk('local')->append('order_recurring'.DIRECTORY_SEPARATOR.'OR_'.$this->file_time.'.md', $string."\r\n");
        //    file_put_contents(Storage::path('order_recurring'.DIRECTORY_SEPARATOR.'R_'.$this->file_time.'.md'), $string."\r\n\r\n", FILE_USE_INCLUDE_PATH | FILE_APPEND );

    }


    public function createOrder($PICKUPDATE,$DELIVERYDATE,$customer){
            $pickupTranche=$this->getTimeSlot($PICKUPDATE,$customer);
            $deliveryTranche=$this->getTimeSlot($DELIVERYDATE,$customer);
                if ($customer->TypeDelivery == "DELIVERY") {//address customer
                    $address = DB::table('address')->where('CustomerID', '=', $customer->CustomerID)->where('status', '=', 'DELIVERY')->first();
                } else {//address store
                    $address = DB::table('address')->where('status', '=', $customer->TypeDelivery)->first();
                }
                $CodeCountry='';
                $PhoneNumber=  json_decode($customer->Phone);
                if(isset($PhoneNumber[0])){
                    $tmp=explode('|',$PhoneNumber[0]);
                    if(count($tmp)>1){
                        $CodeCountry='+'.$tmp[0];
                    }
                    foreach ($PhoneNumber as $k => $phone){
                        $tmp1=explode('|',$phone);
                        if(count($tmp1)>1){
                            $PhoneNumber[$k]=$tmp1[1];
                        }
                    }
                    $PhoneNumber=implode(',',$PhoneNumber);
                }else{
                    $PhoneNumber='';
                }
            // get customer delivery preference
            $deliveryPref=DB::table('DeliveryPreference')->where('CustomerID','=',$customer->CustomerID)->first();

            $pickup_id = DB::table('pickup')->insertGetId([
                'GarmentInstruction' => "",
                'PhoneNumber' => ($deliveryPref&&trim($deliveryPref->PhoneNumber)!=""?$deliveryPref->PhoneNumber:$PhoneNumber),
                'CodeCountry' =>($deliveryPref&&trim($deliveryPref->PhoneNumber)!=""?$deliveryPref->CodeCountry:$CodeCountry),
                'TypeDelivery' => ($deliveryPref?$deliveryPref->TypeDelivery:''),
                'CustomerID' => $customer->CustomerID,
                'AddressID' => ($address != null ? $address->AddressID : ''),
                'id_customer' => 0,
                'comment' => ($deliveryPref?$deliveryPref->OtherInstruction:''),
                'trancheto' => $pickupTranche['trancheto'],
                'trancheFrom' => $pickupTranche['tranchefrom'],
                'address_id' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'REC',
                'date' => $PICKUPDATE,

            ]);
            $pickup = DB::table('pickup')->where('id', '=', $pickup_id)->first();


            $deliveryask_id = DB::table('deliveryask')->insertGetId([
                'PhoneNumber' => ($deliveryPref&&trim($deliveryPref->PhoneNumber)!=""?$deliveryPref->PhoneNumber:$PhoneNumber),
                'CodeCountry' =>($deliveryPref&&trim($deliveryPref->PhoneNumber)!=""?$deliveryPref->CodeCountry:$CodeCountry),
                'TypeDelivery' =>  ($deliveryPref?$deliveryPref->TypeDelivery:''),
                'CustomerID' => $customer->CustomerID,
                'AddressID' => ($address != null ? $address->AddressID : ''),
                'id_customer' => 0,
                'comment' => ($deliveryPref?$deliveryPref->OtherInstruction:''),
                'trancheto' => $deliveryTranche['trancheto'],
                'trancheFrom' => $deliveryTranche['tranchefrom'],
                'address_id' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'REC',
                'date' => $DELIVERYDATE,

            ]);
            $deliveryask = DB::table('deliveryask')->where('id', '=', $deliveryask_id)->first();

            $infoOrder_id = DB::table('infoOrder')->insertGetId([
                "DateDeliveryAsk" => $DELIVERYDATE,
                "DatePickup" => $PICKUPDATE,
                "PickupID" => $pickup->PickupID,
                "DeliveryaskID" => $deliveryask->DeliveryaskID,
                "CustomerID" => $customer->CustomerID,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
                "Status" => 'RECURRING',
                "deliverymethod"=>"recurring",
            ]);
            $this->l("New Order created: infoOrder_id `$infoOrder_id` with PickupID `$pickup->PickupID` and DeliveryaskID `$deliveryask->DeliveryaskID` ");

    }

    public static function processRecurringOrders($trigger=null,$CustomerID=null){
        return new OrderRecurringCreator($CustomerID,$trigger);
    }

}
