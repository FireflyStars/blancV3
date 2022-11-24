<?php

namespace App\Http\Controllers;

use App\Models\Conveyor;
use App\Models\Tranche;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Holiday;

class OrderListController extends Controller
{
    public $holidays;

    public function isDateHoliday($date){
        return in_array($date,$this->holidays);
    }
    public function isDateFuture($date){
        $d1 = Carbon::createFromFormat('Y-m-d', $date);
        return !$d1->isPast();
    }
    public function PreviousDate($date_deliv){
                $aday = 24*60*60;
                $PreviousDate = date( 'Y-m-d',(int)strtotime($date_deliv) - 86400) ;
                $final_stamp = $PreviousDate;
                $feriers = DB::table('holidays')->select('date')->get();
                if(count($feriers) > 0){
                    foreach($feriers as $k=>$v){
                        $holidays[] = $v->date;
                    }
                }

                    if((date('l',strtotime($date_deliv))) == "Sunday" || in_array( $PreviousDate,$holidays)){

                        $last_week = date( 'Y-m-d',(int)strtotime($date_deliv) + (7*$aday));
                        $dates = strtotime($last_week) - strtotime($PreviousDate);
                        $datediff = floor($dates/$aday);

                        for($i = 0; $i < $datediff + 1; $i++){

                            $date = date( 'Y-m-d',(int)strtotime($PreviousDate) - ($aday * $i));

                            if( !in_array($date ,$holidays) && (date('l',strtotime($date_deliv))) != "Sunday"){
                                $final_stamp = $date;
                                break;
                            }
                        }

                    }
                return $final_stamp;
    }

    public function getorderlist(Request $request){

        $skip=$request->get('skip');
        $take=$request->get('take');
        $current_tab=$request->get('current_tab');
        $sort=$request->get('sort');
        $filters=$request->get('filters');

        $is_customer_care = false;

        if($current_tab=='customer_care'){
            $is_customer_care = true;
        }

        $orderlist=DB::table('infoOrder');

        $orderlist->select( [
            'infoOrder.id','infoOrder.Status','infoOrder.Total','infoOrder.TypeDelivery', 'infoOrder.DateDeliveryAsk','infoOrder.DatePickup', 'infoCustomer.DeliverybyDay','infoOrder.datesold as Orderdatesold','infoOrder.deliverymethod','infoOrder.OrderID',

            ($current_tab=='customer_care'?'infoCustomer.Name AS Customer':'infoCustomer.Name'),

        'pickup.status as status_pickup' , 'deliveryask.status as status_deliveryask',

        DB::raw('DATE_FORMAT(infoOrder.detailed_at, "%d/%m/%Y") as DET'),
        //DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Prod'),
        // DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Deliv'),
        DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),
        // DB::raw('IF(MAX(infoitems.PromisedDate) = "", "", MAX(infoitems.PromisedDate)) as PromisedDate'),
        DB::raw('if(infoCustomer.CustomerIDMaster != "", infoCustomer.CustomerIDMaster , infoCustomer.CustomerID) as CustomerID'),
        DB::raw('if(infoOrder.deliverymethod != "", "POS3" , "SPOT") as delivery_method'),
        DB::raw('IF(infoCustomer.btob = 0, "B2C", "B2B") as customerType'),
        DB::raw('IF(infoCustomer.OnAccount = 1, "On Account", "Pay As You Go") as payementType'),
        DB::raw(($current_tab=='customer_care'?"infoitems.CCStatus":"''")." AS Action")

        ]);

        if($current_tab == 'customer_care'){
            $orderlist->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
            ->join('infoitems','infoInvoice.InvoiceID','infoitems.InvoiceID')
            ->where('infoInvoice.OrderID','!=','')
            ->where('infoitems.CCStatus','!=','');
        }


        $orderlist->join('infoCustomer','infoOrder.CustomerID','infoCustomer.CustomerID')
        ->leftJoin('pickup', 'infoOrder.id', '=', 'pickup.order_id')
        ->leftJoin('deliveryask', 'infoOrder.id', '=', 'deliveryask.order_id')
        ->where('infoOrder.OrderID','!=','')
        ->whereNotIn('infoOrder.Status',['VOID', 'DELETE']);

        if($current_tab != 'customer_care'){

        }else{
            /*
            $orderlist->where('infoOrder.OrderID','!=','')
                ->whereIn('OrderID',function($query){
                $query->select('infoInvoice.OrderID')
                    ->from('infoInvoice')
                    ->join('infoitems','infoInvoice.InvoiceID','infoitems.InvoiceID')
                    ->where('infoitems.InvoiceID','!=','')
                    ->whereNotIn('infoitems.Status',['DELETE','VOID'])
                    ->where('infoitems.CCStatus','!=','');

            });
*/
            $orderlist->whereDate('infoOrder.DateDeliveryAsk', '<=', date('Y-m-d'))
            ->whereNotIn('infoOrder.Status', ['DELIVERED', 'DELIVERD TO STORE', 'SOLD', 'DONATED', 'DONATED TO CHARITY', 'COLLECTED', 'VOIDED', 'FULFILLED', 'VOID', 'DELETE', 'SOLD']);


            $orderlist->orWhere(function($query){
                $query->where('infoOrder.Paid', 0)->where('infoCustomer.TypeDelivery','=','DELIVERY');
            })->orWhere(function($query){
                $query->whereIn('infoOrder.Status',['LATE','LATE DELIVERY','OVERDUE FOR COLLECTION','MISSED PICKUP','OVERDUE STORE','FAILED DELIVERY','FAILED PAYMENT','PART ON HOLD','PART PENDING'])
                    ->where('infoOrder.DateDeliveryAsk','!=','2020-01-01');
            });

//*/
            /*
            ->where(

                function($query) {
                    $query->where(function($query) {
                        $query->whereDate('infoOrder.DateDeliveryAsk', '<=', date('Y-m-d'));
                        $query->whereNotIn('infoOrder.Status', ['DELIVERED', 'DELIVERD TO STORE', 'SOLD', 'DONATED', 'DONATED TO CHARITY', 'COLLECTED', 'VOIDED', 'FULFILLED', 'VOID', 'DELETE', 'SOLD']);
                    })->orWhere(function($query){
                        $query->where('infoOrder.Paid', 0)->where('infoCustomer.TypeDelivery','=','DELIVERY');
                    })->orWhere(function($query){
                        $query->whereIn('infoOrder.Status',['LATE','LATE DELIVERY','OVERDUE FOR COLLECTION','MISSED PICKUP','OVERDUE STORE','FAILED DELIVERY','FAILED PAYMENT','PART ON HOLD','PART PENDING'])
                            ->where('infoOrder.DateDeliveryAsk','!=','2020-01-01');
                    });
                });
                //*/

        }


        /*
        if($current_tab != 'customer_care'){
            $orderlist=DB::table('infoOrder')

                ->select( [
                    'infoOrder.id','infoOrder.Status','infoOrder.Total', 'infoitems.id as item_id',
                    'infoCustomer.Name','infoOrder.TypeDelivery', 'infoitems.PromisedDate',
                    'infoOrder.DateDeliveryAsk','infoInvoice.datesold','infoOrder.DatePickup', 'infoCustomer.DeliverybyDay','infoOrder.datesold as Orderdatesold', 'infoOrder.deliverymethod','pickup.status as status_pickup' , 'deliveryask.status as status_deliveryask',
                DB::raw('count(distinct(infoInvoice.id)) as subOrderCount'),
                DB::raw('GROUP_CONCAT(infoitems.express) as express'),
                DB::raw('DATE_FORMAT(infoOrder.detailed_at, "%d/%m/%Y") as DET'),
                DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Prod'),
                DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Deliv'),
                DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),
                DB::raw('IF(MAX(infoitems.PromisedDate) = "", "", MAX(infoitems.PromisedDate)) as PromisedDate'),
                DB::raw('if(infoCustomer.CustomerIDMaster != "", infoCustomer.CustomerIDMaster , infoCustomer.CustomerID) as CustomerID'),
                DB::raw('if(infoOrder.deliverymethod != "", "POS3" , "SPOT") as delivery_method'),
                DB::raw('IF(infoCustomer.btob = 0, "B2C", "B2B") as customerType'),
                DB::raw('IF(infoCustomer.OnAccount = 1, "On Account", "Pay As You Go") as payementType')
            ])
            ->join('infoCustomer','infoOrder.CustomerID','=', DB::raw('if(infoCustomer.CustomerIDMaster != "", infoCustomer.CustomerIDMaster , infoCustomer.CustomerID)'))
            ->leftJoin('pickup', 'infoOrder.id', '=', 'pickup.order_id')
            ->leftJoin('deliveryask', 'infoOrder.id', '=', 'deliveryask.order_id')
            ->leftJoin('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
            ->leftJoin('infoitems',function($join){
                $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                    ->where('infoitems.InvoiceID','!=','')
                    ->whereNotIn('infoitems.Status',['DELETE','VOID']);
            })
            ->where('infoOrder.OrderID','!=','')
            ->whereNotIn('infoOrder.Status',['VOID', 'DELETE']);
        }else{
            $orderlist=DB::table('infoOrder')
                ->select( [
                    'infoOrder.id','infoOrder.Status','infoOrder.Total', 'infoitems.id as item_id',
                    'infoCustomer.Name as Customer','infoCustomer.TypeDelivery', 'infoInvoice.datesold','infoOrder.datesold as Orderdatesold','infoCustomer.DeliverybyDay','infoOrder.DatePickup','infoOrder.DateDeliveryAsk', 'infoOrder.deliverymethod','pickup.status as status_pickup' , 'deliveryask.status as status_deliveryask',
                    DB::raw('GROUP_CONCAT(infoitems.express) as express'),
                    DB::raw('IF(infoOrder.Paid = 0, "unpaid", "paid") as paid'),
                    'infoitems.CCStatus as Action',
                    DB::raw('DATE_FORMAT(infoOrder.detailed_at, "%d/%m/%Y") as DET'),
                    DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Prod'),
                    DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Deliv'),
                    DB::raw('IF(MAX(infoitems.PromisedDate) = "", "", MAX(infoitems.PromisedDate)) as PromisedDate'),
                    DB::raw('if(infoCustomer.CustomerIDMaster != "", infoCustomer.CustomerIDMaster , infoCustomer.CustomerID) as CustomerID'),
                    DB::raw('if(infoOrder.deliverymethod != "", "POS3" , "SPOT") as delivery_method'),
                    DB::raw('IF(infoCustomer.btob = 0, "B2C", "B2B") as customerType'),
                    DB::raw('IF(infoCustomer.OnAccount = 1, "On Account", "Pay As You Go") as payementType')
                ])
                ->join('infoCustomer','infoOrder.CustomerID','=', DB::raw('if(infoCustomer.CustomerIDMaster != "", infoCustomer.CustomerIDMaster , infoCustomer.CustomerID)'))
                ->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
                ->leftJoin('pickup', 'infoOrder.id', '=', 'pickup.order_id')
                ->leftJoin('deliveryask', 'infoOrder.id', '=', 'deliveryask.order_id')
                ->where('infoOrder.OrderID','!=','')
                ->where('infoitems.CCStatus','!=','')
                ->join('infoitems',function($join){
                    $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                        ->where('infoitems.InvoiceID','!=','')
                        ->distinct('infoitems.InvoiceID')
                        ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                })
                ->where(
                    function($query) {
                        $query->where(function($query) {
                            $query->whereDate('infoOrder.DateDeliveryAsk', '<=', date('Y-m-d'));
                            $query->whereNotIn('infoOrder.Status', ['DELIVERED', 'DELIVERD TO STORE', 'SOLD', 'DONATED', 'DONATED TO CHARITY', 'COLLECTED', 'VOIDED', 'FULFILLED', 'VOID', 'DELETE', 'SOLD']);
                        })->orWhere(function($query){
                            $query->where('infoOrder.Paid', 0)->where('infoCustomer.TypeDelivery','=','DELIVERY');
                        })->orWhere(function($query){
                            $query->whereIn('infoOrder.Status',['LATE','LATE DELIVERY','OVERDUE FOR COLLECTION','MISSED PICKUP','OVERDUE STORE','FAILED DELIVERY','FAILED PAYMENT','PART ON HOLD','PART PENDING'])
                                ->where('infoOrder.DateDeliveryAsk','!=','2020-01-01');
                        });
                    });
        }
        */

        if($current_tab=='with_partner'){
            $orderlist->whereIn('infoOrder.OrderID',function($query){
                $query->select('infoInvoice.OrderID')
                    ->from('infoInvoice')
                    ->join('infoitems','infoInvoice.InvoiceID','infoitems.InvoiceID')
                    ->where('infoitems.idPartner','!=','0')
                    ->where('infoitems.PartnerINOUT','=','1');
            });
        }
        if($current_tab=='due_today')
            $orderlist=$orderlist->whereDate('infoOrder.dateprod','=',date('Y-m-d'));

        if($current_tab=='due_tomorrow')
            $orderlist=$orderlist->whereDate('infoOrder.dateprod','=',date('Y-m-d',strtotime('tomorrow')));

        if($current_tab=='unfulfilled'){
            $orderlist=$orderlist->whereNotIn('infoOrder.Status',['FULFILLED','DELETE','DELIVERED','SOLD','VOID','CANCEL']);
        }
        if($current_tab=='without_delivery_date'){
            $orderlist=$orderlist->whereNotIn('infoOrder.status',['DELIVERED', 'DELIVERD TO STORE', 'SOLD', 'DONATED', 'DONATED TO CHARITY', 'COLLECTED','VOIDED','FULFILLED'])
                ->where('infoOrder.DateDeliveryAsk','<=',date('Y-m-d'));
            $orderlist->where('infoOrder.OrderID',function($query){
                $query->select('infoInvoice.OrderID')
                    ->from('infoInvoice')
                    ->join('infoitems','infoInvoice.InvoiceID','infoitems.InvoiceID')
                    ->where('infoitems.ItemTrackingKey','<>','');
            });

        }
        if($current_tab!='all_orders')
            $orderlist=$orderlist->whereNotIn('infoOrder.Status',['VOID', 'DELETE']);
        //filters

        /*
        if(!empty($filters))
            foreach($filters as $colname => $values){

                if($colname =='infoitems.express'){
                        $express=[];
                        if(in_array('standard',$values)){
                            $express=array_merge($express,[0,2,3]);
                        }
                    if(in_array('exp24',$values)){
                        $express=array_merge($express,[1,4,5]);
                    }
                    if(in_array('exp48',$values)){
                        $express=array_merge($express,[6]);
                    }
                    if(!empty($express))
                        $orderlist=$orderlist->whereIn($colname,$express);
                }else if( $colname !='infoitems.express' && $colname != 'infoitems.ProdDate' && $colname != 'infoitems.DelivDate' && $colname != 'infoOrder.DetDate' && $colname !='infoOrder.deliverymethod'){
                    if(!empty($values))
                    $orderlist=$orderlist->whereIn($colname, $values);
                }else if($colname == 'infoitems.ProdDate' && !empty($values)){
                    $orderlist=$orderlist->whereBetween('infoitems.PromisedDate', [ $values[0], $values[1]]);
                }else if($colname == 'infoitems.DelivDate' && !empty($values)){
                    $orderlist=$orderlist->whereBetween('infoitems.PromisedDate', [ $values[0], $values[1]]);
                }else if($colname == 'infoOrder.DetDate' && !empty($values)){
                    $start_first_day = Carbon::parse($values[0])->startOfDay()->toDateTimeString();
                    $end_first_day = Carbon::parse($values[1])->endOfDay()->toDateTimeString();
                    $orderlist=$orderlist->whereBetween('infoOrder.detailed_at', [ $start_first_day , $end_first_day]);
                }else if($colname == 'infoOrder.deliverymethod'){

                    if(count($values) < 2){
                        foreach ($values as $k){
                            if($k == 0){
                                $orderlist=$orderlist->where($colname,'!=' , '');
                            }
                            if($k == 1){
                                $orderlist=$orderlist->where($colname , '');
                            }
                        }
                    }

                }
                else{

                }
            }

        */
        //$orderlist=$orderlist->groupBy('infoOrder.id');


        if(!empty($filters)){
            foreach($filters as $colname => $values){
                if($colname =='infoitems.express'){
                    $express=[];
                    if(in_array('standard',$values)){
                        $express=array_merge($express,[0,2,3]);
                    }
                    if(in_array('exp24',$values)){
                        $express=array_merge($express,[1,4,5]);
                    }
                    if(in_array('exp48',$values)){
                        $express=array_merge($express,[6]);
                    }
                    if(!empty($express)){
                        $orderlist->whereIn('OrderID',function($query) use($express){
                            $query->select('infoInvoice.OrderID')
                                ->from('infoInvoice')
                                ->join('infoitems','infoInvoice.InvoiceID','infoitems.InvoiceID')
                                ->where('infoitems.express',$express)
                                ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                        });
                    }
                }
                else if( $colname !='infoitems.express' && $colname != 'infoitems.ProdDate' && $colname != 'infoitems.DelivDate' && $colname != 'infoOrder.DetDate' && $colname !='infoOrder.deliverymethod'){
                    if(!empty($values))
                    $orderlist=$orderlist->whereIn($colname, $values);
                }
                else if($colname == 'infoitems.ProdDate' && !empty($values)){
                    $orderlist->whereIn('OrderID',function($query) use($values){
                        $query->select('infoInvoice.OrderID')
                            ->from('infoInvoice')
                            ->join('infoitems','infoInvoice.InvoiceID','infoitems.InvoiceID')
                            ->whereBetween('infoitems.PromisedDate', [ $values[0], $values[1]])
                            ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                    });
                }
                else if($colname == 'infoitems.DelivDate' && !empty($values)){
                    $orderlist->whereIn('OrderID',function($query) use($values){
                        $query->select('infoInvoice.OrderID')
                            ->from('infoInvoice')
                            ->join('infoitems','infoInvoice.InvoiceID','infoitems.InvoiceID')
                            ->whereBetween('infoitems.PromisedDate', [ $values[0], $values[1]])
                            ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                    });
                }
                else if($colname == 'infoOrder.DetDate' && !empty($values)){
                        $start_first_day = Carbon::parse($values[0])->startOfDay()->toDateTimeString();
                        $end_first_day = Carbon::parse($values[1])->endOfDay()->toDateTimeString();
                        $orderlist=$orderlist->whereBetween('infoOrder.detailed_at', [ $start_first_day , $end_first_day]);
                }else if($colname == 'infoOrder.deliverymethod'){
                    if(count($values) < 2){
                        foreach ($values as $k){
                            if($k == 0){
                                $orderlist=$orderlist->where($colname,'!=' , '');
                            }
                            if($k == 1){
                                $orderlist=$orderlist->where($colname , '');
                            }
                        }
                    }

                }
                else{

                }
            }
        }

        if($current_tab=='customer_care'){
            $orderlist->groupBy('infoOrder.OrderID')
            ->having('infoitems.CCStatus','!=','');
        }

        //sort
        if(!empty($sort)) {
            foreach ($sort as $columns)
                foreach ($columns as $column => $direction)
                    $orderlist = $orderlist->orderBy($column, $direction);
        }else{
            $orderlist = $orderlist->orderByDesc('infoOrder.id');
        }


        $orderlist=$orderlist->skip($skip)->take($take);
        $orderlist=$orderlist->get();

        $order_ids = [];
        $count_ready_suborders = [];
        $count_ready_suborders_by_orderid = [];
        $count_suborders_orderid = [];
        $details_by_orderid = [];
        $deliveryask_by_orderid = [];
        $pickup_by_order_id = [];

        foreach ($orderlist as $order) {
            if(!in_array($order->OrderID,$order_ids)){
                array_push($order_ids,$order->OrderID);
            }
        }


        if(!empty($order_ids)){
            $orderdetails = DB::table('infoInvoice')
                ->select(['infoInvoice.OrderID','infoitems.id as item_id','infoitems.PromisedDate',
            'infoInvoice.datesold', DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Prod'),
                DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Deliv'),
                DB::raw('IF(MAX(infoitems.PromisedDate) = "", "", MAX(infoitems.PromisedDate)) as PromisedDate'),'infoitems.CCStatus as Action'])
                ->join('infoitems','infoInvoice.InvoiceID','infoitems.InvoiceID')
                ->whereIn('infoInvoice.OrderID',$order_ids)
                ->groupBy('infoInvoice.OrderID');

                $orderdetails =$orderdetails->get();

            if(count($orderdetails) > 0){
                foreach($orderdetails as $k=>$v){
                    $details_by_orderid[$v->OrderID][] = $v;
                }
            }

            foreach($orderlist as $k=>$v){

                $orderlist[$k]->Prod = (isset($details_by_orderid[$v->OrderID][0])?$details_by_orderid[$v->OrderID][0]->Prod:"");
                $orderlist[$k]->Deliv = (isset($details_by_orderid[$v->OrderID][0])?$details_by_orderid[$v->OrderID][0]->Deliv:"");
                $orderlist[$k]->item_id = (isset($details_by_orderid[$v->OrderID][0])?$details_by_orderid[$v->OrderID][0]->item_id:"");
                $orderlist[$k]->PromisedDate = (isset($details_by_orderid[$v->OrderID][0])?$details_by_orderid[$v->OrderID][0]->PromisedDate:"");
                //$orderlist[$k]->Action = (isset($details_by_orderid[$v->OrderID][0])?$details_by_orderid[$v->OrderID][0]->Action:"");
            }

        }


        // adding ready_sub_orders and deliv date
        if($current_tab != 'customer_care'){


            $sub_orders = DB::table('infoInvoice')
                ->whereIn('OrderID', $order_ids)
                ->get();

            if(count($sub_orders) > 0){
                foreach($sub_orders as $k=>$v){
                    if(in_array($v->Status,['READY','READY IN STORE','FULFILLED'])){
                        $count_ready_suborders[$v->OrderID][] = $v;
                    }

                    $count_suborders_orderid[$v->OrderID][] = $v->InvoiceID;
                }

                foreach($count_ready_suborders as $order_id=>$invoiceids){
                    $count_ready_suborders_by_orderid[$order_id] = count($invoiceids);
                }
            }
        }






        foreach ($orderlist as $order) {
            //$order->status_pickup = "";
            //$order->status_deliveryask = "";

            $order->subOrderCount = (isset($count_suborders_orderid[$order->OrderID])?count($count_suborders_orderid[$order->OrderID]):0);

            $order->ready_sub_orders = (isset($count_ready_suborders_by_orderid[$order->OrderID])?$count_ready_suborders_by_orderid[$order->OrderID]:0);

            //Booking Only
            if($order->TypeDelivery == "DELIVERY" && ($order->Status == "RECURRING" || $order->Status == "SCHEDULED")  && $order->deliverymethod == '' ){
                if($order->DateDeliveryAsk != "2020-01-01"  && !is_null($order->DateDeliveryAsk) ){

                    $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                    $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));

                } else {
                            $order->Deliv = '--' ;
                            $order->Prod = '--';
                }

            }

            //Pickup Only

            else if($order->DateDeliveryAsk == "2020-01-01" && $order->Status == "SCHEDULED"  && $order->DatePickup != "2020-01-01" ){
                $order->Deliv = '--';
                if(!is_null($order->DatePickup)){

                    $order->Prod  = date('d/m/Y',strtotime($order->DatePickup));
                }
            }

            //Store New
            else if($order->deliverymethod == 'in_store_collection' ){

                if($order->DateDeliveryAsk != "2020-01-01"  && !is_null($order->DateDeliveryAsk) ){

                    $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                    //dateProd
                    $order->Prod  = date('d/m/Y',strtotime($order->DateDeliveryAsk));

                } else {

                    if($order->status_deliveryask != "DEL" && $order->status_pickup != "DEL" ){
                        if(!is_null($order->DatePickup) && !is_null($order->DateDeliveryAsk)){

                            $pickupDate =  strtotime($order->DatePickup) ;
                            $DeliveryDate = strtotime($order->DateDeliveryAsk);

                                if($pickupDate <  $DeliveryDate){

                                    $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                    $order->Prod  = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                } else if ($pickupDate >  $DeliveryDate){
                                    $order->Deliv = date('d/m/Y',strtotime($order->DatePickup));;
                                    $order->Prod  = date('d/m/Y',strtotime($order->DatePickup));
                                }
                        } else {

                            $order->Deliv = '--' ;
                            $order->Prod = '--';
                        }

                    }
                }

            }

            //Delivery New
            else if($order->deliverymethod == 'home_delivery' ){

                if($order->DateDeliveryAsk != "2020-01-01"   && !is_null($order->DateDeliveryAsk) ){

                    $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));;
                    $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));

                } else {

                    if($order->status_deliveryask != "DEL" && $order->status_pickup != "DEL" ){
                        if(!is_null($order->DatePickup) && !is_null($order->DateDeliveryAsk)){

                            $pickupDate =  strtotime($order->DatePickup) ;
                            $DeliveryDate = strtotime($order->DateDeliveryAsk);

                                if($pickupDate <  $DeliveryDate){

                                    $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                    $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));
                                } else if ($pickupDate >  $DeliveryDate){
                                    $order->Deliv = date('d/m/Y',strtotime($order->DatePickup));
                                    $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DatePickup)));
                                }
                        } else {

                            $order->Deliv = '--' ;
                            $order->Prod = '--';
                        }

                    }
                }

            }

             //Delivery Only New
            else if($order->deliverymethod == 'delivery_only'){

                if($order->DateDeliveryAsk != "2020-01-01"   && !is_null($order->DateDeliveryAsk) ){

                    $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                    $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));

                } else {

                    if($order->status_deliveryask != "DEL" && $order->status_pickup != "DEL" ){
                        if(!is_null($order->DatePickup) && !is_null($order->DateDeliveryAsk)){

                            $pickupDate =  strtotime($order->DatePickup) ;
                            $DeliveryDate = strtotime($order->DateDeliveryAsk);

                                if($pickupDate <  $DeliveryDate){

                                    $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                    $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));
                                } else if ($pickupDate >  $DeliveryDate){
                                    $order->Deliv = date('d/m/Y',strtotime($order->DatePickup));
                                    $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DatePickup)));
                                }
                        } else {

                            $order->Deliv = '--' ;
                            $order->Prod = '--';
                        }

                    }
                }
            }

            //Store Old
            else if($order->TypeDelivery != "DELIVERY" && $order->deliverymethod == ''){

                if($order->DateDeliveryAsk != "2020-01-01"   && !is_null($order->DateDeliveryAsk) ){

                            $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                            $order->Prod  = date('d/m/Y',strtotime($order->DateDeliveryAsk));

                } else {
                    if(!is_null($order->PromisedDate)){

                        $promisedDate = date('Y-m-d',strtotime($order->PromisedDate));
                        $order->Deliv = date('d/m/Y',strtotime($order->PromisedDate));
                        $order->Prod  = date('d/m/Y',strtotime($promisedDate));

                    } else {

                        $order->Deliv = '--';
                        $order->Prod = '--';

                    }

                }

            }
            //$PreviousDate = date( 'd/m/Y',(int)strtotime($order->DateDeliveryAsk) + 86400) ;
            //Delivery Old
            else if($order->TypeDelivery == "DELIVERY" && $order->deliverymethod == ''){

                if($order->DateDeliveryAsk != "2020-01-01"  && !is_null($order->DateDeliveryAsk) ){

                    $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk)); ;
                    $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));


                } else {

                    if($order->status_deliveryask != "DEL" && $order->status_pickup != "DEL" ){
                        if(!is_null($order->DatePickup) && !is_null($order->DateDeliveryAsk)){

                            $pickupDate = strtotime($order->DatePickup);
                            $DeliveryDate = strtotime($order->DateDeliveryAsk);

                                if($pickupDate <  $DeliveryDate){
                                    $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                    $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));
                                } else if ($pickupDate >  $DeliveryDate){
                                    $order->Deliv = date('d/m/Y',strtotime($order->DatePickup));
                                    $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DatePickup)));
                                }

                        } else {

                            $order->Deliv = '--' ;
                            $order->Prod  = '--';
                        }

                    }
                }
            }

            //Fulfiled
            else if($order->Status == "FULFILLED" ){

                if($order->deliverymethod != '' && $order->Orderdatesold != '2020-01-01' && !is_null($order->Orderdatesold)){
                    $order->Deliv = date('d/m/Y',strtotime($order->Orderdatesold)) ;
                    $order->Prod  = date('d/m/Y',strtotime($order->Orderdatesold));
                }else{
                    if($order->DateDeliveryAsk != null){
                            $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                            $order->Prod  = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                        }else {
                            $order->Deliv = '--' ;
                            $order->Prod  = '--';
                        }
                }

                if($order->deliverymethod == '' && $order->Orderdatesold != '2020-01-01' && !is_null($order->Orderdatesold)){

                    $order->Deliv = date('d/m/Y',strtotime($order->Orderdatesold)) ;
                    $order->Prod  = date('d/m/Y',strtotime($order->Orderdatesold));

                    } else {
                        if($order->PromisedDate != null){
                            $order->Deliv =  date('d/m/Y',strtotime($order->PromisedDate)) ;
                            $order->Prod  = date('d/m/Y',strtotime($order->PromisedDate)) ;
                        }else {
                            $order->Deliv = '--' ;
                            $order->Prod  = '--';
                        }


                    }
            }
            //VOID && DELETE
            else if( $order->Status == "VOID " || $order->Status == "DELETE" || $order->Status == "Cancel"){

                $order->Deliv = '--';
                $order->Prod  = '--';

            }

            else {
                $order->Deliv = '--';
                $order->Prod  = '--';
            }

            if( ($order->Deliv == null || $order->Deliv == "01/01/2020") ){
                $order->Deliv = '--';
            }
            if( ($order->Prod == null || $order->Prod == "01/01/2020")){
                $order->Prod = '--';
            }

            if($order->Deliv != "--" && !is_null($order->Deliv) && $order->Status != "FULFILLED"){
                $date = str_replace('/', '-', $order->Deliv);
                // if(date('Y-m-d',strtotime($date)) < date('Y-m-d')) {
                //     $order->Deliv = '--';
                // }
            }
            if($order->Prod != "--" && !is_null($order->Prod) && $order->Status != "FULFILLED"){
                $date = str_replace('/', '-', $order->Prod);
                // if(date('Y-m-d',strtotime($date)) < date('Y-m-d')) {
                //     $order->Prod = '--';
                // }
            }

            if($order->DET == null || $order->DET == "00/00/0000"){
                $order->DET = '--';
            }
    }

        return response()->json($orderlist);
    }

    public function getorderlistbysearch(Request $request){

        $skip=$request->get('skip');
        $take=$request->get('take');
        $current_tab=$request->get('current_tab');
        $sort=$request->get('sort');
        $filters=$request->get('filters');
        $searchValue = $request->get('search_value');

        $keyword =  $searchValue;
        $keyword = str_replace(",", " ",  $keyword);
        $keywords   = explode(' ', $keyword);

        if($current_tab != 'customer_care'){
            $orderlist=DB::table('infoOrder')
            ->select( [
                'infoOrder.id','infoOrder.Status','infoOrder.Total', 'infoitems.id as item_id',
                'infoCustomer.Name','infoOrder.TypeDelivery', 'infoitems.PromisedDate',
                'infoOrder.DateDeliveryAsk','infoInvoice.datesold','infoOrder.DatePickup', 'infoCustomer.DeliverybyDay','infoOrder.datesold as Orderdatesold', 'infoOrder.deliverymethod','pickup.status as status_pickup' , 'deliveryask.status as status_deliveryask',
                DB::raw('count(distinct(infoInvoice.id)) as subOrderCount'),
                DB::raw('GROUP_CONCAT(infoitems.express) as express'),
                DB::raw('DATE_FORMAT(infoOrder.detailed_at, "%d/%m/%Y") as DET'),
                DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Prod'),
                DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Deliv'),
                DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),
                DB::raw('IF(MAX(infoitems.PromisedDate) = "", "", MAX(infoitems.PromisedDate)) as PromisedDate'),
                DB::raw('if(infoCustomer.CustomerIDMaster != "", infoCustomer.CustomerIDMaster , infoCustomer.CustomerID) as CustomerID'),
                DB::raw('if(infoOrder.deliverymethod != "", "POS3" , "SPOT") as delivery_method'),
                DB::raw('IF(infoCustomer.btob = 0, "B2C", "B2B") as customerType'),
                DB::raw('IF(infoCustomer.OnAccount = 1, "On Account", "Pay As You Go") as payementType')
            ])
            ->join( 'infoCustomer', function ($join){
                $join->on( 'infoCustomer.CustomerID', '=', 'infoOrder.CustomerID')
                ->where('infoCustomer.Actif', '=' , 1);
            })
            ->leftJoin('pickup', 'infoOrder.id', '=', 'pickup.order_id')
            ->leftJoin('deliveryask', 'infoOrder.id', '=', 'deliveryask.order_id')
            ->leftJoin('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
            ->leftJoin('infoitems',function($join){
                $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                    ->where('infoitems.InvoiceID','!=','')
                    ->whereNotIn('infoitems.Status',['DELETE','VOID']);
            });

            foreach($keywords as $searchTerm){
                $orderlist->where(function($q) use ($searchTerm){
                    $q->where('FirstName', 'like', '%'.$searchTerm.'%')
                    ->orWhere('LastName', 'like', '%'.$searchTerm.'%')
                    ->orWhere('Name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('EmailAddress', 'like', '%'.$searchTerm.'%');
                    // and so on
                });
            };

            $orderlist = $orderlist
            ->where('infoOrder.Status','!=' ,'DELETE')
            ->orWhere('infoOrder.id','LIKE' , $keyword)
            ->orWhere('infoitems.ItemTrackingKey','LIKE' ,$keyword)
            ->orWhere('infoitems.id_items',$keyword)
            ->orWhere('infoInvoice.NumInvoice', 'LIKE', $keyword)
            ->orderBy('infoOrder.created_at', 'desc');

        }else{
            $orderlist=DB::table('infoOrder')
                ->select( [
                    'infoOrder.id','infoOrder.Status','infoOrder.Total', 'infoitems.id as item_id',
                    'infoCustomer.Name as Customer','infoCustomer.TypeDelivery', 'infoInvoice.datesold', 'infoCustomer.CustomerID','infoOrder.datesold as Orderdatesold','infoCustomer.DeliverybyDay','infoOrder.DatePickup','infoOrder.DateDeliveryAsk',
                    'infoOrder.deliverymethod','pickup.status as status_pickup' , 'deliveryask.status as status_deliveryask',
                    DB::raw('GROUP_CONCAT(infoitems.express) as express'),
                    DB::raw('IF(infoOrder.Paid = 0, "unpaid", "paid") as paid'),
                    'infoitems.CCStatus as Action',
                    DB::raw('DATE_FORMAT(infoOrder.detailed_at, "%d/%m/%Y") as DET'),
                    DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Prod'),
                    DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Deliv'),
                    DB::raw('IF(MAX(infoitems.PromisedDate) = "", "", MAX(infoitems.PromisedDate)) as PromisedDate'),
                    DB::raw('if(infoCustomer.CustomerIDMaster != "", infoCustomer.CustomerIDMaster , infoCustomer.CustomerID) as CustomerID'),
                    DB::raw('if(infoOrder.deliverymethod != "", "POS3" , "SPOT") as delivery_method'),
                    DB::raw('IF(infoCustomer.btob = 0, "B2C", "B2B") as customerType'),
                    DB::raw('IF(infoCustomer.OnAccount = 1, "On Account", "Pay As You Go") as payementType')
                ])
                ->join('infoCustomer','infoOrder.CustomerID','=', DB::raw('if(infoCustomer.CustomerIDMaster = "", infoCustomer.CustomerID , infoCustomer.CustomerIDMaster)'))
                ->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
                ->leftJoin('pickup', 'infoOrder.id', '=', 'pickup.order_id')
                ->leftJoin('deliveryask', 'infoOrder.id', '=', 'deliveryask.order_id')
                ->where('infoOrder.OrderID','!=','')
                ->where('infoitems.CCStatus','!=','')
                ->join('infoitems',function($join){
                    $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                        ->where('infoitems.InvoiceID','!=','')
                        ->distinct('infoitems.InvoiceID')
                        ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                })
                ->where(
                    function($query) {
                        $query->where(function($query) {
                            $query->whereDate('infoOrder.DateDeliveryAsk', '<=', date('Y-m-d'));
                            $query->whereNotIn('infoOrder.Status', ['DELIVERED', 'DELIVERD TO STORE', 'SOLD', 'DONATED', 'DONATED TO CHARITY', 'COLLECTED', 'VOIDED', 'FULFILLED', 'VOID', 'DELETE', 'SOLD']);
                        })->orWhere(function($query){
                            $query->where('infoOrder.Paid', 0)->where('infoCustomer.TypeDelivery','=','DELIVERY');
                        })->orWhere(function($query){
                            $query->whereIn('infoOrder.Status',['LATE','LATE DELIVERY','OVERDUE FOR COLLECTION','MISSED PICKUP','OVERDUE STORE','FAILED DELIVERY','FAILED PAYMENT','PART ON HOLD','PART PENDING'])
                                ->where('infoOrder.DateDeliveryAsk','!=','2020-01-01');
                        });
                    })
                    ->where('infoOrder.OrderID','!=','')
                    ->where(function($q) use ($keyword , $keywords) {
                        $q->Where('Name','LIKE','%'.$keyword.'%')
                            ->orWhere('LastName','LIKE','%'.$keyword.'%')
                            ->orWhere('EmailAddress','LIKE','%'.$keyword.'%')
                            ->orWhere('infoOrder.id','LIKE',$keyword)
                            ->orWhere('infoitems.ItemTrackingKey','LIKE' ,$keyword)
                            ->orWhere('infoitems.id_items', $keyword)
                            ->orWhere('infoInvoice.NumInvoice', 'LIKE', $keyword)
                            ->orWhereIn('FirstName', $keywords)
                            ->orWhereIn('LastName', $keywords)
                            ->orWhereIn('EmailAddress', $keywords);
                    });
        }

        if($current_tab=='with_partner')
        $orderlist=$orderlist->where('infoitems.idPartner','!=','0')
            ->where('infoitems.PartnerINOUT','=','1');

        if($current_tab=='due_today')
            $orderlist=$orderlist->whereDate('infoOrder.dateprod','=',date('Y-m-d'));

        if($current_tab=='due_tomorrow')
            $orderlist=$orderlist->whereDate('infoOrder.dateprod','=',date('Y-m-d',strtotime('tomorrow')));

        if($current_tab=='unfulfilled'){
            $orderlist=$orderlist->whereNotIn('infoOrder.Status',['FULFILLED','DELETE','DELIVERED','SOLD','VOID','CANCEL']);
        }
        if($current_tab=='without_delivery_date'){
            $orderlist=$orderlist->whereNotIn('infoOrder.status',['DELIVERED', 'DELIVERD TO STORE', 'SOLD', 'DONATED', 'DONATED TO CHARITY', 'COLLECTED','VOIDED','FULFILLED'])
                ->where('infoOrder.DateDeliveryAsk','<=',date('Y-m-d'))
                ->where('infoitems.ItemTrackingKey','<>','');
        }


        if($current_tab!='all_orders')
            $orderlist=$orderlist->whereNotIn('infoOrder.Status',['VOID', 'DELETE']);
        //filters


        if(!empty($filters))
            foreach($filters as $colname => $values){
                if($colname =='infoitems.express'){
                        $express=[];
                        if(in_array('standard',$values)){
                            $express=array_merge($express,[0,2,3]);
                        }
                    if(in_array('exp24',$values)){
                        $express=array_merge($express,[1,4,5]);
                    }
                    if(in_array('exp48',$values)){
                        $express=array_merge($express,[6]);
                    }
                    if(!empty($express))
                        $orderlist=$orderlist->whereIn($colname,$express);
                }else if( $colname !='infoitems.express' && $colname != 'infoitems.ProdDate' && $colname != 'infoitems.DelivDate' && $colname != 'infoOrder.DetDate' && $colname !='infoOrder.deliverymethod'){
                    if(!empty($values))
                    $orderlist=$orderlist->whereIn($colname, $values);
                }else if($colname == 'infoitems.ProdDate' && !empty($values)){
                    $orderlist=$orderlist->whereBetween('infoitems.PromisedDate', [ $values[0], $values[1]]);
                }else if($colname == 'infoitems.DelivDate' && !empty($values)){
                    $orderlist=$orderlist->whereBetween('infoitems.PromisedDate', [ $values[0], $values[1]]);
                }else if($colname == 'infoOrder.DetDate' && !empty($values)){
                    $start_first_day = Carbon::parse($values[0])->startOfDay()->toDateTimeString();
                    $end_first_day = Carbon::parse($values[1])->endOfDay()->toDateTimeString();
                    $orderlist=$orderlist->whereBetween('infoOrder.detailed_at', [ $start_first_day , $end_first_day]);
                }else if($colname == 'infoOrder.deliverymethod'){

                    if(count($values) < 2){
                        foreach ($values as $k){
                            if($k == 0){
                                $orderlist=$orderlist->where($colname,'!=' , '');
                            }
                            if($k == 1){
                                $orderlist=$orderlist->where($colname , '');
                            }
                        }
                    }

                }else{

                }
            }

        $orderlist=$orderlist->groupBy('infoOrder.id');

        //sort
        if(!empty($sort)) {
            foreach ($sort as $columns)
                foreach ($columns as $column => $direction)
                    $orderlist = $orderlist->orderBy($column, $direction);
        }else{
            $orderlist = $orderlist->orderByDesc('infoOrder.id');
        }


        $orderlist=$orderlist->skip($skip)->take($take);
        $orderlist=$orderlist->get();

            // adding ready_sub_orders and deliv date
            foreach ($orderlist as $order) {

                if($current_tab != 'customer_care'){

                    $order->ready_sub_orders = DB::table('infoOrder')
                        ->join('infoInvoice', 'infoOrder.OrderID','=', 'infoInvoice.OrderID')
                        ->distinct('infoInvoice.InvoiceID')
                        ->where('infoOrder.id', $order->id)
                        ->whereIn('infoInvoice.Status', ['READY','READY IN STORE','FULFILLED'])->count();
                }



                //Booking Only
                if($order->TypeDelivery == "DELIVERY" && ($order->Status == "RECURRING" || $order->Status == "SCHEDULED")  && $order->deliverymethod == '' ){
                    if($order->DateDeliveryAsk != "2020-01-01"  && !is_null($order->DateDeliveryAsk) ){

                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));

                    } else {
                                $order->Deliv = '--' ;
                                $order->Prod = '--';
                    }

                }

                //Pickup Only

                else if($order->DateDeliveryAsk == "2020-01-01" && $order->Status == "SCHEDULED"  && $order->DatePickup != "2020-01-01" ){
                    $order->Deliv = '--';
                    if(!is_null($order->DatePickup)){

                        $order->Prod  = date('d/m/Y',strtotime($order->DatePickup));
                    }
                }

                //Store New
                else if($order->deliverymethod == 'in_store_collection' ){

                    if($order->DateDeliveryAsk != "2020-01-01" && !is_null($order->DateDeliveryAsk) ){

                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                        //dateProd
                        $order->Prod  = date('d/m/Y',strtotime($order->DateDeliveryAsk));

                    } else {

                        if($order->status_deliveryask != "DEL" && $order->status_pickup != "DEL" ){
                            if(!is_null($order->DatePickup) && !is_null($order->DateDeliveryAsk)){

                                $pickupDate =  strtotime($order->DatePickup) ;
                                $DeliveryDate = strtotime($order->DateDeliveryAsk);

                                    if($pickupDate <  $DeliveryDate){

                                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                        $order->Prod  = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                    } else if ($pickupDate >  $DeliveryDate){
                                        $order->Deliv = date('d/m/Y',strtotime($order->DatePickup));;
                                        $order->Prod  = date('d/m/Y',strtotime($order->DatePickup));
                                    }
                            } else {

                                $order->Deliv = '--' ;
                                $order->Prod = '--';
                            }

                        }
                    }

                }

                //Delivery New
                else if($order->deliverymethod == 'home_delivery' ){

                    if($order->DateDeliveryAsk != "2020-01-01"  && !is_null($order->DateDeliveryAsk) ){

                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));;
                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));

                    } else {

                        if($order->status_deliveryask != "DEL" && $order->status_pickup != "DEL" ){
                            if(!is_null($order->DatePickup) && !is_null($order->DateDeliveryAsk)){

                                $pickupDate =  strtotime($order->DatePickup) ;
                                $DeliveryDate = strtotime($order->DateDeliveryAsk);

                                    if($pickupDate <  $DeliveryDate){

                                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));
                                    } else if ($pickupDate >  $DeliveryDate){
                                        $order->Deliv = date('d/m/Y',strtotime($order->DatePickup));
                                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DatePickup)));
                                    }
                            } else {

                                $order->Deliv = '--' ;
                                $order->Prod = '--';
                            }

                        }
                    }

                }

                 //Delivery Only New
                else if($order->deliverymethod == 'delivery_only'){

                    if($order->DateDeliveryAsk != "2020-01-01"  && !is_null($order->DateDeliveryAsk) ){

                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));

                    } else {

                        if($order->status_deliveryask != "DEL" && $order->status_pickup != "DEL" ){
                            if(!is_null($order->DatePickup) && !is_null($order->DateDeliveryAsk)){

                                $pickupDate =  strtotime($order->DatePickup) ;
                                $DeliveryDate = strtotime($order->DateDeliveryAsk);

                                    if($pickupDate <  $DeliveryDate){

                                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));
                                    } else if ($pickupDate >  $DeliveryDate){
                                        $order->Deliv = date('d/m/Y',strtotime($order->DatePickup));
                                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DatePickup)));
                                    }
                            } else {

                                $order->Deliv = '--' ;
                                $order->Prod = '--';
                            }

                        }
                    }
                }

                //Store Old
                else if($order->TypeDelivery != "DELIVERY" && $order->deliverymethod == ''){

                    if($order->DateDeliveryAsk != "2020-01-01"  && !is_null($order->DateDeliveryAsk) ){

                                $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                $order->Prod  = date('d/m/Y',strtotime($order->DateDeliveryAsk));

                    } else {
                        if(!is_null($order->PromisedDate)){

                            $promisedDate = date('Y-m-d',strtotime($order->PromisedDate));
                            $order->Deliv = date('d/m/Y',strtotime($order->PromisedDate));
                            $order->Prod  = date('d/m/Y',strtotime($promisedDate));

                        } else {

                            $order->Deliv = '--';
                            $order->Prod = '--';

                        }

                    }

                }
                //$PreviousDate = date( 'd/m/Y',(int)strtotime($order->DateDeliveryAsk) + 86400) ;
                //Delivery Old
                else if($order->TypeDelivery == "DELIVERY" && $order->deliverymethod == ''){

                    if($order->DateDeliveryAsk != "2020-01-01"  && !is_null($order->DateDeliveryAsk) ){

                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk)); ;
                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));


                    } else {

                        if($order->status_deliveryask != "DEL" && $order->status_pickup != "DEL" ){
                            if(!is_null($order->DatePickup) && !is_null($order->DateDeliveryAsk)){

                                $pickupDate = strtotime($order->DatePickup);
                                $DeliveryDate = strtotime($order->DateDeliveryAsk);

                                    if($pickupDate <  $DeliveryDate){
                                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));
                                    } else if ($pickupDate >  $DeliveryDate){
                                        $order->Deliv = date('d/m/Y',strtotime($order->DatePickup));
                                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DatePickup)));
                                    }

                            } else {

                                $order->Deliv = '--' ;
                                $order->Prod  = '--';
                            }

                        }
                    }
                }

                //Fulfiled
                else if($order->Status == "FULFILLED" ){

                    if($order->deliverymethod != '' && $order->Orderdatesold != '2020-01-01' && !is_null($order->Orderdatesold)){
                        $order->Deliv = date('d/m/Y',strtotime($order->Orderdatesold)) ;
                        $order->Prod  = date('d/m/Y',strtotime($order->Orderdatesold));
                    }else{
                        if($order->DateDeliveryAsk != null){
                                $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                $order->Prod  = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                            }else {
                                $order->Deliv = '--' ;
                                $order->Prod  = '--';
                            }
                    }

                    if($order->deliverymethod == '' && $order->Orderdatesold != '2020-01-01' && !is_null($order->Orderdatesold)){

                        $order->Deliv = date('d/m/Y',strtotime($order->Orderdatesold)) ;
                        $order->Prod  = date('d/m/Y',strtotime($order->Orderdatesold));

                        } else {
                            if($order->PromisedDate != null){
                                $order->Deliv =  date('d/m/Y',strtotime($order->PromisedDate)) ;
                                $order->Prod  = date('d/m/Y',strtotime($order->PromisedDate)) ;
                            }else {
                                $order->Deliv = '--' ;
                                $order->Prod  = '--';
                            }


                        }
                }
                //VOID && DELETE
                else if( $order->Status == "VOID " || $order->Status == "DELETE" || $order->Status == "Cancel"){

                    $order->Deliv = '--';
                    $order->Prod  = '--';

                }

                else {
                    $order->Deliv = '--';
                    $order->Prod  = '--';
                }

                if( ($order->Deliv == null || $order->Deliv == "01/01/2020") ){
                    $order->Deliv = '--';
                }
                if( ($order->Prod == null || $order->Prod == "01/01/2020")){
                    $order->Prod = '--';
                }

                if($order->Deliv != "--" && !is_null($order->Deliv) && $order->Status != "FULFILLED"){
                    $date = str_replace('/', '-', $order->Deliv);
                    // if(date('Y-m-d',strtotime($date)) < date('Y-m-d')) {
                    //     $order->Deliv = '--';
                    // }
                }
                if($order->Prod != "--" && !is_null($order->Prod) && $order->Status != "FULFILLED"){
                    $date = str_replace('/', '-', $order->Prod);
                    // if(date('Y-m-d',strtotime($date)) < date('Y-m-d')) {
                    //     $order->Prod = '--';
                    // }
                }
                if($order->DET == null || $order->DET == "00/00/0000"){
                    $order->DET = '--';
                }
        }
        return response()->json($orderlist);
    }

    public function cancelorders(Request $request){

        $orderids=$request->post('orderids');

        //PERFORM DELETE QUERY HERE
        //On passe L order infoOrder.status DELETE, on passe infoInvoice.status et infoItem.status en DELETE, et infoitem.nextpost=34
        if(!empty($orderids)){

            $infoOrders=DB::table('infoOrder')->select('OrderID')->whereIn('infoOrder.id',$orderids)->get();
            $infoOrdersIds=[];

            $infoOrders->each(function ($item, $key) use(&$infoOrdersIds){
                $infoOrdersIds[]=$item->OrderID;
            });

            $infoitems=DB::table('infoitems')->select('infoitems.id')->join('infoInvoice',function($join) use ($infoOrdersIds){

                        $join->on('infoitems.SubOrderID','=','infoInvoice.SubOrderID')
                            ->whereIn('infoInvoice.OrderID',$infoOrdersIds);
            });
            $infoitems=$infoitems->get();

            $infoitemsIds=[];
            $infoitems->each(function ($item, $key) use(&$infoitemsIds){
                $infoitemsIds[]=$item->id;
            });


            DB::table('infoitems')->whereIn('id',$infoitemsIds)->update([
                'Status'=>'DELETE',
                'nextpost'=>34
            ]);
            DB::table('infoInvoice')->whereIn('infoInvoice.OrderID',$infoOrdersIds)->update([
                'Status'=>'DELETE'
            ]);
            DB::table('infoOrder')->whereIn('infoOrder.OrderID',$infoOrdersIds)->update([
                'Status'=>'DELETE'
            ]);
        }
        return response()->json(['done'=>'ok']);
    }


    public  function  markaslate(Request $request){

        $orderids=$request->post('orderids');
        if(!empty($orderids)){
            DB::table('infoOrder')->whereIn('infoOrder.id',$orderids)->update([
                'Status'=>'LATE',
                'DateDeliveryAsk'=>'2020-01-01'
            ]);
        }
        return response()->json(['done'=>'ok']);
    }

    public function getorderdetail(Request $request){
        $user=Auth::user();
        $infoOrder_id=$request->post('infoOrder_id');
        $order=DB::table('infoOrder')
            ->select(['infoOrder.id AS order_id','infoOrder.Status','infoOrder.Total','infoCustomer.Name','infoOrder.TypeDelivery','infoCustomer.CompanyName','infoCustomer.id' , 'infoOrder.DeliveryaskID' , 'infoOrder.PickupID' ,
            'infoOrder.DateDeliveryAsk','infoOrder.DatePickup' , 'infoCustomer.Phone','infoCustomer.CustomerID','booking_histories.user_id',
            'booking_histories.status','infoCustomer.OnAccount','infoCustomer.CustomerIDMaster',
            DB::raw('IF(infoOrder.DateDeliveryAsk="2020-01-01" OR infoOrder.DateDeliveryAsk="2000-01-01" OR infoOrder.DateDeliveryAsk="","--",DATE_FORMAT(infoOrder.DateDeliveryAsk, "%a %d/%m")) as PromisedDate'),
            DB::raw('IF(infoOrder.DatePickup ="2020-01-01" OR infoOrder.DatePickup ="2000-01-01" OR infoOrder.DatePickup ="","--",DATE_FORMAT(infoOrder.DatePickup , " %W %d %M %Y")) as DatePickup '),
            DB::raw('IF(pickup.date ="2020-01-01" OR pickup.date ="2000-01-01" OR pickup.date ="","--",DATE_FORMAT(pickup.date , " %W %d %M %Y")) as PickupDateNew '),
            DB::raw('IF(infoOrder.DateDeliveryAsk ="2020-01-01" OR infoOrder.DateDeliveryAsk ="2000-01-01" OR infoOrder.DateDeliveryAsk ="","--",DATE_FORMAT(infoOrder.DateDeliveryAsk , "%W %d %M %Y")) as DateDelivery '),
            DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),'infoOrder.OrderID','infoOrder.suggestedDeliveryDate',
            DB::raw('IF(infoCustomer.btob = 0, "B2C", "B2B") as cust_type'),
            DB::raw(
                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "Store Drop Off"
                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN "Pickup"
                      WHEN infoOrder.deliverymethod = "delivery_only" THEN "Drop Off"
                      WHEN infoOrder.deliverymethod = "recurring" THEN "Pickup"
                END as order_left_text'
            ),
            DB::raw(
                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "In-Store Collection"
                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN "Delivery"
                      WHEN infoOrder.deliverymethod = "delivery_only" THEN "Delivery"
                      WHEN infoOrder.deliverymethod = "recurring" THEN "Delivery"
                END as order_right_text'
            ),
            DB::raw(
                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "In-Store Collection"
                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN "Home Delivery"
                      WHEN infoOrder.deliverymethod = "delivery_only" THEN "Delivery Only"
                      WHEN infoOrder.deliverymethod = "recurring" THEN "Recuring Delivery"
                END as order_text'
            ),
            DB::raw(
                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.dropoff, "%W %d %M %Y")
                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(pickup.date, "%W %d %M %Y")
                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(infoOrder.created_at, "%W %d %M %Y")
                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                END as order_left_date'
            ),
            DB::raw(
                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.pickup_date, "%W %d %M %Y")
                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(deliveryask.date, "%W %d %M %Y")
                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(deliveryask.date, "%W %d %M %Y")
                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                END as order_right_date'
            ),
            DB::raw(
                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.pickup_date, "%d %b %Y")
                WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(deliveryask.date, "%d %b %Y")
                WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(deliveryask.date, "%d %b %Y")
                WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                END as order_date'
            ),
            DB::raw(
                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.dropoff, "%h:%i %p")
                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN CONCAT(pickup.trancheFrom,"_",pickup.trancheto)
                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(infoOrder.created_at, "%h:%i %p")
                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                END as order_left_time'
            ),
            DB::raw(
                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "6-8 pm"
                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN CONCAT(deliveryask.trancheFrom,"_",deliveryask.trancheto)
                      WHEN infoOrder.deliverymethod = "delivery_only" THEN CONCAT(deliveryask.trancheFrom,"_",deliveryask.trancheto)
                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                END as order_right_time'
            ),
            DB::raw(
                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN 0
                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN IF(CURRENT_DATE() < pickup.date, 1, 0 )
                      WHEN infoOrder.deliverymethod = "delivery_only" THEN 0
                      WHEN infoOrder.deliverymethod = "recurring" THEN 00
                END as left_edit'
            ),
            DB::raw(
                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN IF(CURRENT_DATE() < booking_store.pickup_date, 1, 0 )
                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN IF(CURRENT_DATE() < deliveryask.date, 1, 0 )
                      WHEN infoOrder.deliverymethod = "delivery_only" THEN IF(CURRENT_DATE() < deliveryask.date, 1, 0 )
                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                END as right_edit'
            ),
            DB::raw(
                'CASE WHEN infoCustomer.isMaster = 1 AND infoCustomer.CustomerIDMaster = "" THEN "MAIN"
                      WHEN infoCustomer.isMaster = 0 AND infoCustomer.CustomerIDMaster <> "" THEN "Sub"
                END as account_type'
            ),
            ])
            ->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')
            ->leftJoin('booking_store', 'booking_store.order_id', '=', 'infoOrder.id')
            ->leftJoin('pickup', 'pickup.PickupID', '=', 'infoOrder.PickupID')
            ->leftJoin('deliveryask', 'deliveryask.DeliveryaskID', '=', 'infoOrder.DeliveryaskID')
            ->leftJoin('booking_histories', 'booking_histories.order_id', '=', 'infoOrder.id')
            ->where('infoOrder.id','=',$infoOrder_id)->first();

            if($order->account_type == "Sub"){
                 $company = DB::table('infoCustomer')->select('infoCustomer.CompanyName')->where('infoCustomer.CustomerID','=',$order->CustomerIDMaster)->first();
                 $order->CompanyName = $company->CompanyName;
            }
            else if($order->account_type == "Main"){
                $company = DB::table('infoCustomer')->select('infoCustomer.CompanyName')->where('infoCustomer.CustomerID','=',$order->CustomerID)->first();
                $order->CompanyName = $company->CompanyName;
            }

            $Booking_histories=DB::table('booking_histories')->select(['booking_histories.user_id' , 'users.name' ,'booking_histories.created_at',
            DB::raw('IF(booking_histories.created_at="2020-01-01" OR booking_histories.created_at="2000-01-01" OR booking_histories.created_at="","--",DATE_FORMAT(booking_histories.created_at, "%a %d/%m/%Y")) as CreatedDate'),
            DB::raw('DATE_FORMAT(booking_histories.created_at,"%H:%i") as time'),
            ])
            ->join('users','users.id','=','booking_histories.user_id')
            ->where('booking_histories.order_id','=',$order->order_id)
            ->where('booking_histories.user_id','!=',0)
            ->first();

            $tranches_slots = Tranche::getDeliveryPlanningTranchesForApi();

            $tranche_left = $order->order_left_time;
            $tranche_arr_left = explode("_",$tranche_left);
            if(isset($tranche_arr_left[0]) && isset($tranche_arr_left[1])){
                $slot = Tranche::getSlotFromTranche($tranche_arr_left[0],$tranche_arr_left[1]);
                $timeslot = $tranches_slots[$slot];
                $order->order_left_time = $timeslot;
            }

            $tranche_right = $order->order_right_time;
            $tranche_arr = explode("_",$tranche_right);

            if(isset($tranche_arr[0]) && isset($tranche_arr[1])){
                $slot = Tranche::getSlotFromTranche($tranche_arr[0],$tranche_arr[1]);
                if(!is_null($slot)){
                    $timeslot = $tranches_slots[$slot];
                    $order->order_right_time = $timeslot;
                }
            }

            $billing_add=DB::table('address')->where('CustomerID','=',$order->CustomerID)->where('status','=','BILLING')->first();
            $delivery_add=DB::table('address')->where('CustomerID','=',$order->CustomerID)->where('status','=',$order->TypeDelivery)->first();

            $infoitems=DB::table('itemhistorique')->select(['infoInvoice.NumInvoice','infoInvoice.InvoiceID' ,"infoInvoice.Status as Invoice_Status", 'infoitems.id as infoitems_id','infoitems.brand','infoitems.ItemTrackingKey','itemhistorique.ItemTrackingKey' , 'infoitems.colors','infoitems.typeitem','infoitems.priceTotal','infoitems.status', 'infoitems.id_items as itemproduction' , 'itemhistorique.ID_item as productionitem' , 'postes.nominterface as station'])
            ->join('infoInvoice',function($join) use($order){
                $join->on('infoInvoice.InvoiceID','=','itemhistorique.InvoiceID')
                ->where('infoInvoice.OrderID','=',$order->OrderID);
             })->leftJoin('infoitems','infoitems.ItemTrackingKey','=','itemhistorique.ItemTrackingKey')
                ->leftJoin('postes','postes.id','=','infoitems.nextpost')
                ->leftJoin('TypePost','TypePost.id','=','postes.TypePost')
                ->distinct('infoitems.id')
                ->orderBy('infoInvoice.NumInvoice')->get();

        $items=[];
        $infoitems->each(function ($item) use(&$items , $order) {

            if($order->Status == "DELIVERED"){
                $nextpost = DB::table('postes')->select(['postes.nominterface'])
                ->where('postes.id','=', 39)->first();
                $item->station = $nextpost->nominterface;
            }
            if($order->Status == "FULFILLED"){
                $nextpost = DB::table('postes')->select(['postes.nominterface'])
                ->where('postes.id','=', 28)->first();
                $item->station = $nextpost->nominterface;
            }
            if($order->Status == "VOID" || $order->Status == "DELETE"){
                $nextpost = DB::table('postes')->select(['postes.nominterface'])
                ->where('postes.id','=', 34)->first();
                $item->station = $nextpost->nominterface;
            }
            $Price = DB::table('detailingitem')->select(['detailingitem.dry_cleaning_price' , 'detailingitem.cleaning_addon_price' , 'detailingitem.tailoring_price' ])
            ->where('detailingitem.InvoiceID','=',$item->InvoiceID)
            ->where('detailingitem.tracking','=',$item->ItemTrackingKey)->first();
            if(!empty($Price)){
                $item->priceTotal = $Price->dry_cleaning_price + $Price->cleaning_addon_price + $Price->tailoring_price ;
            } else {
                $item->priceTotal = $item->priceTotal;
            }
            $items[$item->NumInvoice][]=$item;//suborder grouping

        });

        $order->alreadypickuped=false;

        if($order->DatePickup == "--"){
            $order->alreadypickuped=false;
        }else{
            $pkdate=Carbon::parse($order->DatePickup);
            if($pkdate->isPast()||$pkdate->isToday())
            $order->alreadypickuped=true;
        }


        if($order->Phone!=""){
            $order->Phone=json_decode($order->Phone);

        }
        $available_slots=[];
        $sel_postcode = "";
        if($order->TypeDelivery=='DELIVERY'&&$delivery_add!=null&&trim($delivery_add->postcode)!=''){
            $postcode = $delivery_add->postcode;



            $allpostcodes = DB::table('tranchepostcode')
                ->select(DB::raw('DISTINCT(Postcode) AS post_code'))
                ->get();

            $postcode = str_replace(' ','',$postcode);
            $postcode = substr($postcode,0,-3);

            if (count($allpostcodes) > 0) {
                foreach ($allpostcodes as $k=>$v){
                    $cur_postcode = $v->post_code;
                    if($postcode==$cur_postcode){
                        $sel_postcode = $cur_postcode;
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
            if(count($tranche_details) > 0){
                foreach($tranche_details as $k=>$v){
                    $details = json_decode($v->tranche);
                    $formatted_tranche[$v->day] = $details;
                    $slot_dates=Tranche::getAvailableSlotAndDates($v->day,$details,true,3);
                    foreach ($slot_dates as $slot_key=>$dates){
                        foreach ($dates as $date)
                            $available_slots[$date][]=$slot_key;
                    }

                }
            }
        }
        $detailingitemlist = DB::table('detailingitem')
        ->where('detailingitem.order_id', '=', $infoOrder_id)
        ->orderBy('detailingitem.id','ASC')
        ->get();

        return response()->json(['order'=>['detail'=>$order,'billing'=>$billing_add,'delivery'=>$delivery_add,'items'=>$items,'available_slots'=>$available_slots ,'detailingitemlist' => $detailingitemlist,'postcode'=>$sel_postcode , 'booking' => $Booking_histories , 'totalitems' => count($infoitems) ,'user' => $user->role_id]] );
    }

     public function setInvoiceFulfilled(Request $request){

        $invoice_id = $request->post('invoice_id');
        $endpoint = "http://blancspot.vpc-direct-service.com/fulfiled-v2.php";
        //$endpoint = "http://blancspot.vpc-direct-service.com/fulfiled-v2-test.php";
        $client = new \GuzzleHttp\Client();
        $content = "";

        $user = Auth::user();

        $params = [
            'token'=>'GhtfvbbG4489hGtyEfgARRGht3',
            'invoiceid'=>$invoice_id,
            'userid'=>$user->id,
        ];

        $response = $client->request('GET', $endpoint, ['query' => $params]);

        $statusCode = $response->getStatusCode();
        @$content = $response->getBody();

        $content = str_replace('\"', '', $content);

        $output = @json_decode($content);

        if($output && isset($output->result) && $output->result=='ok'){
            $items = DB::table('infoitems')->where('InvoiceID',$invoice_id)->get();

            if(count($items) > 0){
                foreach($items as $k=>$v){

                    DB::table('production')->insert([
                                            'user_id'=>$user->id,
                                            'ID_item'=>$v->id_items,
                                            'item_id'=>$v->id,
                                            'poste_id'=> 28,
                                            'date_add'=>date('Y-m-d H:i:s')
                                         ]);
            }
        }
    }

        return \response()->json([
            'url'=>$endpoint."?token=GhtfvbbG4489hGtyEfgARRGht3&invoiceid=$invoice_id&userid=".$user->id,
            'output'=>$output,
            'status_code'=>$statusCode,
            'status_message'=>$output->result
        ]);
    }

    public function SplitSubOrder(Request $request){

        $invoice_id = $request->post('invoice_id');
        $items = $request->post('items');
        $data = serialize($items);
        $arr = $items[0];
        $array_item = http_build_query($arr,"item[");
       // echo preg_replace('/\[\d/', '\\0]', array_item);



        $endpoint = "http://blancspot.vpc-direct-service.com/split-v1.php";
        // $arr = http_build_query($items,"item[");
        $array_item = preg_replace('/\[\d/', '\\0]', $array_item);
        $client = new \GuzzleHttp\Client();
        $content = "";

        $user = Auth::user();

        $params = [
            'token'=>'GhtfvbbG4489hGtyEfgARRGht3',
            'invoiceid'=>$invoice_id,
            'item' =>$data,
            'userid'=>$user->id,
        ];
            $response = $client->request('GET', $endpoint, ['query' => $params]);
            $statusCode = $response->getStatusCode();
            $statusText = $response->getReasonPhrase();
            return \response()->json([
                'url'=>$endpoint."?token=GhtfvbbG4489hGtyEfgARRGht3&invoiceid=$invoice_id&item=$data&userid=$user->id",
                'status_code'=>$statusCode,
                'status_message'=>$statusText
            ]);
    }

    public function getitemdetail(Request $request){
        $itemInfo = DB::table('infoitems')
                      ->join('infoInvoice', 'infoitems.InvoiceID', '=', 'infoInvoice.InvoiceID')
                      ->join('infoCustomer', 'infoInvoice.CustomerID', '=', 'infoCustomer.CustomerID')
                      ->join('infoOrder','infoInvoice.OrderID','=','infoOrder.OrderID')
                      ->join('postes', 'infoitems.nextpost', '=', 'postes.id')
                      ->join('TypePost', 'TypePost.id', '=', 'postes.TypePost')
                      ->where('infoitems.id', $request->item_id)
                      ->select(
                          'infoitems.id', 'infoitems.ItemTrackingKey as item_key', 'infoitems.Colors as colors','infoInvoice.InvoiceID',
                          'infoitems.Fabrics as fabrics', 'infoitems.Patterns as patterns', 'infoitems.Size as size','infoitems.brand',
                          'infoitems.StoreName as store_name', 'infoitems.store', 'infoitems.damage', 'infoitems.id_items',
                          'infoitems.typeitem as item_name', 'TypePost.bg_color as location_color', 'postes.nom as location', 'TypePost.circle_color', 'TypePost.process',
                          'infoCustomer.Name as customer_name', 'infoCustomer.CustomerIDMaster', 'infoCustomer.CustomerIDMasterAccount',
                          'infoCustomer.IsMaster', 'infoCustomer.IsMasterAccount', 'postes.id as poste_id', 'infoOrder.id as order_id','infoOrder.Status'
                          )->first();

        if($request->invoice_Id != null){
            $InvoiceId = $request->invoice_Id;
            $subOrder = DB::table('itemhistorique')
            ->join('infoInvoice', 'itemhistorique.InvoiceID', '=', 'infoInvoice.InvoiceID')
            ->join('infoOrder','infoOrder.OrderID','=','infoInvoice.OrderID')
            ->join('infoitems','infoitems.ItemTrackingKey','=','itemhistorique.ItemTrackingKey')
            ->where('itemhistorique.InvoiceID', $InvoiceId)
            ->select('infoitems.id_items as itemproduction' ,'infoitems.ItemTrackingKey' ,  'itemhistorique.ID_item as productionitem' ,'infoInvoice.NumInvoice as NumInvoice', 'infoInvoice.InvoiceID' , 'infoOrder.Status' , 'infoOrder.id as orderId')
            ->first();
        }else {
            $InvoiceId = $itemInfo->InvoiceID;
            $subOrder = DB::table('itemhistorique')->select('infoitems.id_items as itemproduction' ,'infoitems.ItemTrackingKey' ,  'itemhistorique.ID_item as productionitem' ,'infoInvoice.NumInvoice as NumInvoice', 'infoInvoice.InvoiceID', 'infoOrder.Status' , 'infoOrder.id as orderId')
            ->join('infoInvoice', 'itemhistorique.InvoiceID', '=', 'infoInvoice.InvoiceID')
            ->join('infoOrder','infoOrder.OrderID','=','infoInvoice.OrderID')
            ->join('infoitems','infoitems.ItemTrackingKey','=','itemhistorique.ItemTrackingKey')
            ->where('itemhistorique.InvoiceID', $InvoiceId)
            ->first();
        }

        if($subOrder){
            if($subOrder->Status == "DELIVERED"){
                $nextpost = DB::table('postes')->select(['postes.nominterface'])
                ->where('postes.id','=', 39)->first();
                $itemInfo->location = $nextpost->nominterface;
            }
            if($subOrder->Status == "FULFILLED"){
                $nextpost = DB::table('postes')->select(['postes.nominterface'])
                ->where('postes.id','=', 28)->first();
                $itemInfo->location = $nextpost->nominterface;
            }
            if($subOrder->Status == "VOID" || $subOrder->Status == "DELETE"){
                $nextpost = DB::table('postes')->select(['postes.nominterface'])
                ->where('postes.id','=', 34)->first();
                $itemInfo->location = $nextpost->nominterface;
            }
        }
        $itemsList = DB::table('itemhistorique')->select([ 'infoitems.id_items as itemproduction' , 'itemhistorique.ID_item as productionitem'])
            ->join('infoitems','infoitems.ItemTrackingKey','=','itemhistorique.ItemTrackingKey')
            ->where('itemhistorique.InvoiceID', '=' , $InvoiceId)
            ->where('infoitems.id', $request->item_id)
            ->distinct('infoitems.id_items')
            ->get();

            $location_history = [];

            foreach ($itemsList as $item) {
                if($item->productionitem == 0 ){
                    $history = DB::table('production')
                              ->join('postes', 'production.poste_id', '=', 'postes.id')
                              ->join('TypePost', 'TypePost.id', '=', 'postes.TypePost')
                              ->join('users', 'production.user_id', '=', 'users.id')
                              ->select(
                                    'TypePost.bg_color as location_color', 'postes.nom as location',
                                    'TypePost.process', 'TypePost.circle_color',
                                    // DB::raw('DATE_FORMAT(production.date_add,"%a") as day'),
                                    DB::raw('DATE_FORMAT(DATE_ADD(production.date_add , INTERVAL 1 HOUR),"%a %d/%m/%Y %H:%i") as date'),
                                    // DB::raw('DATE_FORMAT(production.date_add,"%H:%i") as time'),
                                    'users.name'
                                )
                              ->where('production.ID_item', $item->itemproduction)
                              ->orderByDesc('production.date_add')
                              ->get();

                } else if($item->productionitem != 0 ){
                    $history = DB::table('production')
                              ->join('postes', 'production.poste_id', '=', 'postes.id')
                              ->join('TypePost', 'TypePost.id', '=', 'postes.TypePost')
                              ->join('users', 'production.user_id', '=', 'users.id')
                              ->select(
                                    'TypePost.bg_color as location_color', 'postes.nom as location',
                                    'TypePost.process', 'TypePost.circle_color',
                                    // DB::raw('DATE_FORMAT(production.date_add,"%a") as day'),
                                    DB::raw('DATE_FORMAT(DATE_ADD(production.date_add , INTERVAL 1 HOUR),"%a %d/%m/%Y %H:%i") as date'),
                                    // DB::raw('DATE_FORMAT(production.date_add,"%H:%i") as time'),
                                    'users.name'
                                )
                              ->where('production.ID_item', $item->productionitem)
                              ->orderByDesc('production.date_add')
                              ->get();

                };
            $location_history = $history;
            }

            $Issues = DB::table('detailingitem')->select('detailingitem.stainstext' , 'detailingitem.stains','detailingitem.damagestext' , 'detailingitem.damages',
            'detailingitem.damagesissues' ,'detailingitem.stainsissue' )
            ->where('detailingitem.item_id', $request->item_id)
            ->where('detailingitem.InvoiceID',$InvoiceId)
            ->first();

            if(!is_null($Issues)){

                if(!is_null($Issues->stains)){
                $stains_decode =json_decode($Issues->stains);
                $stains_stainsissue_decode =json_decode($Issues->stainsissue);
                $Issues->stains = DB::table('itemzones')->whereIn('id', array_column($stains_decode, 'id_zone'))->get();
                if(!empty($stains_stainsissue_decode)){
                    $Issues->stainsissue = DB::table('issues_tag')->select('id','name')->whereIn('id', $stains_stainsissue_decode)->get();
                }
                }

                if(!is_null($Issues->damages)){
                    $damages_decode =json_decode($Issues->damages);
                    $stains_damagesissues_decode =json_decode($Issues->damagesissues);
                    $Issues->damages =  DB::table('itemzones')->whereIn('id', array_column($damages_decode, 'id_zone'))->get();
                    if(!empty($stains_damagesissues_decode)){
                    $Issues->damagesissues = DB::table('issues_tag')->select('id','name')->whereIn('id', $stains_damagesissues_decode)->get();
                    }
                }
            }

            $services = DB::table('detailingitem')->select('detailingitem.cleaning_services' , 'detailingitem.tailoring_services' , 'detailingitem.describeprixnow' , 'detailingitem.describeprixnowtailoring')
            ->where('detailingitem.item_id', $request->item_id)
            ->where('detailingitem.InvoiceID',$InvoiceId)
            ->first();

            if(!is_null($services)){

                if(!is_null($services->cleaning_services)){
                    $cleaning_services_decode =json_decode($services->cleaning_services);
                    $services->cleaning_services = DB::table('cleaningservices')->select('id','name')->whereIn('id', $cleaning_services_decode)->get();
                }

                if(!is_null($services->tailoring_services)){
                    $tailoring_services_decode =json_decode($services->tailoring_services);
                    $services->tailoring_services = DB::table('tailoring_services')->select('id','name')->whereIn('id', $tailoring_services_decode)->get();
                }
            }

        return response()->json([
            'item_detail'=>[
                'breif_info'        => $itemInfo,
                'location_history'  => $location_history,
                'item_list'         => $itemsList,
                'suborder'          => $subOrder,
                'Issues'            => $Issues,
                'Services'          => $services
            ]
        ]);
    }


    public function suggestdate(Request $request){
            $infoOrder_id=$request->post('infoOrder_id');
            $suggested_delivery_date=$request->post('suggested_delivery_date');
            $user=Auth::user();
            $update=false;
            if($user->hasRoles(['admin','Blanc Admin','user','Super Admin'])){ // cc cannot suggest a date
                $update= DB::table('infoOrder')->where('id','=',$infoOrder_id)->update(
                    [
                        'suggestedDeliveryDate'=>$suggested_delivery_date
                    ]
                );
            }
        return response()->json(['updated'=>$update]);
    }

    public function newdeliverydate(Request $request){
        $infoOrder_id=$request->post('infoOrder_id');
        $PromisedDate=$request->post('PromisedDate');
        $timeslot=$request->post('timeslot');
        $user=Auth::user();
        $update=false;

        // if($user->hasRoles(['admin','Blanc Admin','cc'])){
        if(in_array($user->role_id,[1,4])){ // Production operator cannot set a new delivery date

                $infoOrder=DB::table('infoOrder')->select(['CustomerID','DeliveryaskID','TypeDelivery'])->where('id','=',$infoOrder_id)->first();

                if($infoOrder==null)
                    return response()->json(['updated'=>$update,'message'=>'Order not found.']);

                if($infoOrder->TypeDelivery!='DELIVERY'){
                    $pickupTimeTranche=Tranche::getFormattedTranche(11);
                    DB::table('infoOrder')->where('id',$infoOrder_id)->update(
                        [
                            'DateDeliveryAsk'=>$PromisedDate,
                            'Status'=>'In process'
                        ]
                    );
                    DB::table('booking_store')->where('order_id',$infoOrder_id)->update(
                        [
                            'pickup_date'=>$PromisedDate,
                            'pickup_timeslot'=>11,
                            'pickup_time'=>$pickupTimeTranche['tranchefrom']
                        ]
                    );

                    $infoInvoices=DB::table('infoInvoice')
                    ->join('infoOrder',function ($join) {
                        $join->on('infoInvoice.OrderID', '=', 'infoOrder.OrderID')->where('infoOrder.OrderID','<>','');
                    })->where('infoOrder.id','=',$infoOrder_id)->select(['infoInvoice.InvoiceID'])->get();

                $InvoiceIDs=[];
                foreach ($infoInvoices as $infoInvoice) {
                    $InvoiceIDs[]=$infoInvoice->InvoiceID;
                }
                DB::table('infoitems')->whereIn('InvoiceID',$InvoiceIDs)->update([
                    'PromisedDate'=>$PromisedDate,
                    'dateJour'=>date('l',strtotime($PromisedDate))
                ]);
                    $update=true;
                }else{

                $cust_details =  DB::table('infoCustomer')
                    ->select('infoCustomer.id AS customer_id','infoCustomer.*','address.*')
                    ->join('address','infoCustomer.CustomerID','address.CustomerID')
                    ->where('infoCustomer.CustomerID',$infoOrder->CustomerID)
                    ->whereColumn('address.status','=','infoCustomer.TypeDelivery')
                    ->first();
                if($cust_details==null)
                    return response()->json(['updated'=>$update,'message'=>'Customer or address not found.']);


                //retrieve comment on previous delivery ask
                $previous_delivery_ask=DB::table('deliveryask')->where('DeliveryaskID','=',$infoOrder->DeliveryaskID)->first();




                if($PromisedDate!='' && $timeslot!= '') {

                    $deliveryTimeTranche=Tranche::getFormattedTranche($timeslot);
                    $inserteddelivery= DB::table('deliveryask')
                        ->insertGetId(
                            [
                                'id_customer'=>$cust_details->customer_id,
                                'CustomerID'=>$cust_details->CustomerID,
                                'AddressID'=>$cust_details->AddressID,
                                'created_at'=>date('Y-m-d H:i:s'),
                                'updated_at'=>date('Y-m-d H:i:s'),
                                'comment'=>($previous_delivery_ask!=null?$previous_delivery_ask->comment:''),
                                'trancheto'=>$deliveryTimeTranche['trancheto'],
                                'trancheFrom'=>$deliveryTimeTranche['tranchefrom'],
                                'status'=>'PMS',
                                'date'=>$PromisedDate
                            ]
                        );

                    DB::table('booking_histories')->insert([
                        'booking_id'=>$inserteddelivery,
                        'order_id'=>$infoOrder_id,
                        'customer_id'=>$cust_details->customer_id,
                        'user_id'=>$user->id,
                        'type'=>'deliveryask',
                        'status'=>'NEW',
                        'created_at'=>date('Y-m-d H:i:s'),
                    ]);

                    $deliveryAsk=DB::table('deliveryask')->where('id',$inserteddelivery)->first();
                    DB::table('infoOrder')->where('id',$infoOrder_id)->update(
                        [
                            'DeliveryaskID'=>$deliveryAsk->DeliveryaskID,
                            'DateDeliveryAsk'=>$deliveryAsk->date,
                            'Status'=>'In process'
                        ]
                    );

                    $infoInvoices=DB::table('infoInvoice')
                        ->join('infoOrder',function ($join) {
                            $join->on('infoInvoice.OrderID', '=', 'infoOrder.OrderID')->where('infoOrder.OrderID','<>','');
                        })->where('infoOrder.id','=',$infoOrder_id)->select(['infoInvoice.InvoiceID'])->get();

                    $InvoiceIDs=[];
                    foreach ($infoInvoices as $infoInvoice) {
                        $InvoiceIDs[]=$infoInvoice->InvoiceID;
                    }
                    DB::table('infoitems')->whereIn('InvoiceID',$InvoiceIDs)->update([
                        'PromisedDate'=>$deliveryAsk->date,
                        'dateJour'=>date('l',strtotime($deliveryAsk->date))
                    ]);
                    //*/
                    $update=true;
                }
            }
        }
        return response()->json(['updated'=>$update,'message'=>'','post'=>$request->all()]);
    }


    public function newPickupdate(Request $request){

        $infoOrder_id=$request->post('infoOrder_id');
        $PickupDate=$request->post('PickupDate');
        $deliveryDate=$request->post('deliveryDate');
        $timeslotPickup=$request->post('timeslotPickup');
        $timeslotDelivery=$request->post('timeslotDelivery');

        $pickupTimeTranche=Tranche::getFormattedTranche($timeslotPickup);
        $deliveryTimeTranche=Tranche::getFormattedTranche($timeslotDelivery);



        $infoOrder=DB::table('infoOrder')->select(['CustomerID','PickupID','DeliveryaskID'])->where('id','=',$infoOrder_id)->first();
                if($infoOrder==null)
                    return response()->json(['updated'=>$update,'message'=>'Order not found.']);

        $pickup=DB::table('pickup')->where('PickupID','=',$infoOrder->PickupID)->first();

        $deliveryask=null;
        //Create new order
        $order = DB::table('infoOrder')->where('id','=',$infoOrder_id)->first();
        $new_order = (array)$order;
        $pickup_deleted=DB::table('pickup')->where('CustomerID','=',$infoOrder->CustomerID)->where('PickupID','=',$infoOrder->PickupID)->update(["status"=>"DEL"]);
        $infoOrder=Db::table('infoOrder')->where('PickupID',$infoOrder->PickupID)->first();
        if($infoOrder!=null&&$infoOrder->Status=="SCHEDULED"){
            if($infoOrder->DeliveryaskID!=''){
                $deliveryask=DB::table('deliveryask')->where('CustomerID','=',$infoOrder->CustomerID)->where('DeliveryaskID','=',$infoOrder->DeliveryaskID)->first();
                DB::table('deliveryask')->where('CustomerID','=',$infoOrder->CustomerID)->where('DeliveryaskID','=',$infoOrder->DeliveryaskID)->update(["status"=>"DEL"]);

                DB::table('infoOrder')->where('id','=',$infoOrder->id)->update(['status'=>'DELETE']);
            }
        }
                //retrieve comment on previous pickup
                $previous_pickup=DB::table('pickup')->where('PickupID','=',$infoOrder->PickupID)->first();

                if($PickupDate!='' && $timeslotPickup!= '') {

                    $pickup_id = DB::table('pickup')->insertGetId([
                        'GarmentInstruction' => "",
                        'PhoneNumber' => $previous_pickup->PhoneNumber,
                        'CodeCountry' => $previous_pickup->CodeCountry,
                        'TypeDelivery' => $previous_pickup->TypeDelivery,
                        'CustomerID' => $previous_pickup->CustomerID,
                        'AddressID' => $previous_pickup->AddressID,
                        'id_customer' => $previous_pickup->id_customer,
                        'comment' => $previous_pickup->comment,
                        'trancheto' => $pickupTimeTranche['trancheto'],
                        'trancheFrom' => $pickupTimeTranche['tranchefrom'],
                        'address_id' => $previous_pickup->address_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at'=> date('Y-m-d H:i:s'),
                        'status' => $previous_pickup->status,
                        'date' => $PickupDate,

                    ]);
                    $newpickup = DB::table('pickup')->where('id', '=', $pickup_id)->first();

                    //retrieve comment on delivery
                    $previous_delivery=DB::table('deliveryask')->where('DeliveryaskID','=', $infoOrder->DeliveryaskID)->first();

                    $deliveryask_id = DB::table('deliveryask')->insertGetId([
                        'PhoneNumber' => $previous_delivery->PhoneNumber,
                        'CodeCountry' => $previous_delivery->CodeCountry,
                        'TypeDelivery' => $previous_delivery->TypeDelivery,
                        'CustomerID' => $previous_delivery->CustomerID,
                        'AddressID' => $previous_delivery->AddressID,
                        'id_customer' => $previous_delivery->id_customer,
                        'comment' => $previous_delivery->comment,
                        'trancheto' => ($timeslotDelivery != 0)?$deliveryTimeTranche['trancheto']:$previous_delivery->trancheto,
                        'trancheFrom' => ($timeslotDelivery != 0)?$deliveryTimeTranche['trancheFrom']:$previous_delivery->trancheFrom,
                        'address_id' => $previous_delivery->address_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at'=> date('Y-m-d H:i:s'),
                        'status' => $previous_delivery->status,
                        'date' => $deliveryDate,
                    ]);

                    $newdeliveryask = DB::table('deliveryask')->where('id', '=', $deliveryask_id)->first();

                    foreach($new_order as $k=>$v){
                        if(in_array($k,['id'])){
                            unset($new_order[$k]);
                        }
                    }

                    $new_order_uuid = Str::uuid()->toString();

                   $new_order['created_at'] = date('Y-m-d H:i:s');
                   $new_order['updated_at'] = date('Y-m-d H:i:s');
                   $new_order['OrderID'] = $new_order_uuid;
                   $new_order['PickupID'] = $newpickup->PickupID;
                   $new_order['DatePickup'] = $PickupDate;
                   $new_order['DateDeliveryAsk'] = $deliveryDate;

                   $last_id_order_inserted = DB::table('infoOrder')->insertGetId($new_order);

                   }

        return response()->json(['message'=>'updated' , 'pickup'=> $newpickup , 'delivery' => $newdeliveryask , 'neworder' => $new_order , '$last_id_order_inserted' => $last_id_order_inserted ]);
    }

    public function splititems(Request $request){
        $suborder_items= $request->post('items');
        $infoOrder_id= $request->post('infoOrder_id');
        $upsert = false;
        $infoOrder_idS=[];
        foreach ($suborder_items as $suborder=>$items) {
            //copied from blancpos
            foreach ($items as $item_id) {


                $item = DB::table('infoitems')
                    ->select('infoitems.id_items', 'infoOrder.id AS order_id', 'infoitems.PromisedDate', 'infoOrder.CustomerID', 'infoInvoice.*', 'infoitems.nextpost', 'infoitems.ItemTrackingKey', 'infoInvoice.Client')
                    ->join('infoInvoice', 'infoitems.SubOrderID', 'infoInvoice.SubOrderID')
                    ->join('infoOrder', 'infoInvoice.OrderID', 'infoOrder.orderID')
                    ->where('infoitems.id', $item_id)
                    ->first();

                if ($item) {

                    Conveyor::addLineIn($item->ItemTrackingKey, $item->NumInvoice, 'Split');


                    $other_items = DB::table('infoitems')
                        ->join('infoInvoice', 'infoitems.SubOrderID', 'infoInvoice.SubOrderID')
                        ->join('infoOrder', 'infoInvoice.OrderID', 'infoOrder.OrderID')
                        ->where('infoOrder.id', '=', $item->order_id)
                        ->where('infoitems.id', '!=', $item_id)
                        ->get();


                    if (count($other_items) > 0) { //si on a > 1 item pour le sub-order

                        //Create new order
                        $order = DB::table('infoOrder')->where('id', $item->order_id)->first();
                        $new_order = (array)$order;

                        foreach ($new_order as $k => $v) {
                            if (in_array($k, ['id', 'DatePickup', 'DateDeliveryAsk', 'PickupID', 'DeliveryAskID'])) {
                                unset($new_order[$k]);
                            }
                        }

                        $new_order_uuid = Str::uuid()->toString();

                        $new_order['created_at'] = date('Y-m-d H:i:s');
                        $new_order['updated_at'] = date('Y-m-d H:i:s');
                        $new_order['OrderID'] = $new_order_uuid;

                        $last_id_order_inserted = DB::table('infoOrder')->insertGetId($new_order);
                        $infoOrder_idS[]=$last_id_order_inserted;

                        //Create new suborder

                        $new_invoice = (array)$item;

                        foreach ($new_invoice as $k => $v) {
                            if (in_array($k, ['id_items', 'order_id', 'id', 'PromisedDate', 'InvoiceID', 'nextpost', 'ItemTrackingKey'])) {
                                unset($new_invoice[$k]);
                            }
                        }


                        $inv_part1 = date('m');


                        $last_id_split = 0;
                        $last_split = DB::table('split')->latest('id')->first();

                        if ($last_split) {
                            $last_id_split = $last_split->id;
                        }

                        $inv_part2 = 900000 + $last_id_split + 1;

                        $new_invoice_uuid = Str::uuid()->toString();
                        $new_invoice['NumInvoice'] = $inv_part1 . "-" . $inv_part2;
                        $new_invoice['date_add'] = date('Y-m-d H:i:s');
                        $new_invoice['OrderID'] = $new_order_uuid;
                        $new_invoice['id_invoice'] = 0;
                        $new_invoice['InvoiceID'] = $new_invoice_uuid;
                        $new_invoice['SubOrderID'] = '';

                        $last_id_invoice_inserted = DB::table('infoInvoice')->insertGetId($new_invoice);


                        $new_inv = DB::table('infoInvoice')->where('id', $last_id_invoice_inserted)->first();


                        if ($new_inv) {
                            $upsert = DB::table('infoitems')->where('id_items', $item->id_items)
                                ->update([
                                    'PromisedDate' => '2000-01-01',
                                    'InvoiceID' => $new_invoice_uuid,
                                    'id_invoice' => 0,
                                    'SubOrderID' => $new_inv->SubOrderID,
                                ]);

                            DB::table('infoOrder')->where('id', $item->order_id)->update(['Split' => 1]);
                        }

                        $upsert = DB::table('split')->insert([
                            'item_id' => $item_id,
                            'old_order_id' => $item->order_id,
                            'order_id' => $last_id_order_inserted,
                            'old_suborder_id' => $item->id_invoice,
                            'suborder_id' => $last_id_invoice_inserted,
                            'old_promised_date' => $item->PromisedDate,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);

                        Conveyor::addLineIn($item->ItemTrackingKey, $new_inv->NumInvoice, 'New Record');


                    } else {
                        //Mettre date item, order a 2000-01-01
                        $upsert = DB::table('infoitems')->where('id_items', $item->id_items)->update(['PromisedDate' => '2000-01-01']);
                    }


                }
            }
        }

        $order=DB::table('infoOrder')->where('id','=',$infoOrder_id)->first();

        $infoitems=DB::table('infoitems')->select(['infoInvoice.NumInvoice','infoitems.id as infoitems_id','infoitems.brand','infoitems.ItemTrackingKey','infoitems.colors','infoitems.typeitem','infoitems.priceTotal','infoitems.status','TypePost.Name as station',])->join('infoInvoice',function($join) use($order){
            $join->on('infoInvoice.SubOrderID','=','infoitems.SubOrderID')
                ->where('infoInvoice.OrderID','=',$order->OrderID);
        })->leftJoin('postes','postes.id','=','infoitems.nextpost')
            ->leftJoin('TypePost','TypePost.id','=','postes.TypePost')
            ->whereNotIn('infoitems.Status',['DELETE','VOID'])
            ->orderBy('infoInvoice.NumInvoice')->get();
        $items=[];
        $infoitems->each(function ($item) use(&$items) {
            $items[$item->NumInvoice][]=$item;//suborder grouping
        });

        return response()->json([
            'updated'=>$upsert,
            'items'=>$items,
            'add_order_to_list'=>$this->getOrders($infoOrder_idS)
        ]);
    }


    public function getOrders($infoOrder_idS=[]){

        $orderlist=DB::table('infoOrder')
            ->select(['infoOrder.id','infoOrder.Status','infoOrder.Total','infoCustomer.Name','infoCustomer.TypeDelivery',DB::raw('IF(infoOrder.DateDeliveryAsk="2020-01-01" OR infoOrder.DateDeliveryAsk="2000-01-01" OR infoOrder.DateDeliveryAsk="","--",DATE_FORMAT(infoOrder.DateDeliveryAsk, "%d/%m")) as PromisedDate'),DB::raw('count(infoitems.id) as numitems'),DB::raw('GROUP_CONCAT(infoitems.express) as express'),DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),'infoOrder.suggestedDeliveryDate'])
            ->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')
            ->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
            ->leftJoin('infoitems',function($join){
                $join->on('infoInvoice.SubOrderID','=','infoitems.SubOrderID')
                    ->where('infoitems.SubOrderID','!=','')
                    ->whereNotIn('infoitems.Status',['DELETE','VOID']);
            })
            ->whereIn('infoOrder.id',$infoOrder_idS)
            ->where('infoInvoice.OrderID','!=','');

            return $orderlist;



    }
    public function getOrdersByCustomerId(Request $request){
        $skip=$request->get('skip');
        $take=$request->get('take');
        $current_tab=$request->get('current_tab');
        $sort=$request->get('sort');
        $filters=$request->get('filters');
        $customerId = $request->get('customerID');

        if($current_tab != 'customer_care'){
            $orderlist=DB::table('infoOrder')
            ->select( [
                'infoOrder.id','infoOrder.Status','infoOrder.Total', 'infoitems.id as item_id','infoCustomer.CustomerIDMaster',
                'infoCustomer.Name','infoOrder.TypeDelivery', 'infoitems.PromisedDate',
                'infoOrder.DateDeliveryAsk','infoInvoice.datesold','infoOrder.DatePickup', 'infoCustomer.DeliverybyDay','infoOrder.datesold as Orderdatesold', 'infoOrder.deliverymethod','pickup.status as status_pickup' , 'deliveryask.status as status_deliveryask',
                DB::raw('count(distinct(infoInvoice.id)) as subOrderCount'),
                DB::raw('GROUP_CONCAT(infoitems.express) as express'),
                DB::raw('DATE_FORMAT(infoOrder.detailed_at, "%d/%m/%Y") as DET'),
                DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Prod'),
                DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Deliv'),
                DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),
                DB::raw('IF(MAX(infoitems.PromisedDate) = "", "", MAX(infoitems.PromisedDate)) as PromisedDate'),
                DB::raw('if(infoCustomer.CustomerIDMaster != "", infoCustomer.CustomerIDMaster , infoCustomer.CustomerID) as CustomerID'),
                DB::raw('if(infoOrder.deliverymethod != "", "POS3" , "SPOT") as delivery_method'),
                DB::raw('IF(infoCustomer.btob = 0, "B2C", "B2B") as customerType'),
                DB::raw('IF(infoCustomer.OnAccount = 1, "On Account", "Pay As You Go") as payementType')
            ])
            ->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')
            ->leftJoin('pickup', 'infoOrder.id', '=', 'pickup.order_id')
            ->leftJoin('deliveryask', 'infoOrder.id', '=', 'deliveryask.order_id')
            ->leftJoin('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
            ->leftJoin('infoitems',function($join){
                $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                    ->where('infoitems.InvoiceID','!=','')
                    ->whereNotIn('infoitems.Status',['DELETE','VOID']);
            })
            ->where('infoOrder.OrderID','!=','')
            ->where('infoOrder.CustomerID','=',$customerId)
            ->whereNotIn('infoOrder.Status',['VOID', 'DELETE']);

        }else{
            $orderlist=DB::table('infoOrder')
                ->select( [
                    'infoOrder.id','infoOrder.Status','infoOrder.Total', 'infoitems.id as item_id','infoCustomer.CustomerIDMaster',
                    'infoCustomer.Name as Customer','infoCustomer.TypeDelivery', 'infoInvoice.datesold', 'infoCustomer.CustomerID','infoOrder.datesold as Orderdatesold','infoCustomer.DeliverybyDay','infoOrder.DatePickup','infoOrder.DateDeliveryAsk',
                    'infoOrder.deliverymethod','pickup.status as status_pickup' , 'deliveryask.status as status_deliveryask',
                    DB::raw('GROUP_CONCAT(infoitems.express) as express'),
                    DB::raw('IF(infoOrder.Paid = 0, "unpaid", "paid") as paid'),
                    'infoitems.CCStatus as Action',
                    DB::raw('DATE_FORMAT(infoOrder.detailed_at, "%d/%m/%Y") as DET'),
                    DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Prod'),
                    DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Deliv'),
                    DB::raw('IF(MAX(infoitems.PromisedDate) = "", "", MAX(infoitems.PromisedDate)) as PromisedDate'),
                    DB::raw('if(infoCustomer.CustomerIDMaster != "", infoCustomer.CustomerIDMaster , infoCustomer.CustomerID) as CustomerID'),
                    DB::raw('if(infoOrder.deliverymethod != "", "POS3" , "SPOT") as delivery_method'),
                    DB::raw('IF(infoCustomer.btob = 0, "B2C", "B2B") as customerType'),
                    DB::raw('IF(infoCustomer.OnAccount = 1, "On Account", "Pay As You Go") as payementType')
                ])
                ->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')
                ->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
                ->leftJoin('pickup', 'infoOrder.id', '=', 'pickup.order_id')
                ->leftJoin('deliveryask', 'infoOrder.id', '=', 'deliveryask.order_id')
                ->where('infoOrder.OrderID','!=','')
                ->where('infoitems.CCStatus','!=','')
                ->join('infoitems',function($join){
                    $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                        ->where('infoitems.InvoiceID','!=','')
                        ->distinct('infoitems.InvoiceID')
                        ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                })
                ->where(
                    function($query) {
                        $query->where(function($query) {
                            $query->whereDate('infoOrder.DateDeliveryAsk', '<=', date('Y-m-d'));
                            $query->whereNotIn('infoOrder.Status', ['DELIVERED', 'DELIVERD TO STORE', 'SOLD', 'DONATED', 'DONATED TO CHARITY', 'COLLECTED', 'VOIDED', 'FULFILLED', 'VOID', 'DELETE', 'SOLD']);
                        })->orWhere(function($query){
                            $query->where('infoOrder.Paid', 0)->where('infoCustomer.TypeDelivery','=','DELIVERY');
                        })->orWhere(function($query){
                            $query->whereIn('infoOrder.Status',['LATE','LATE DELIVERY','OVERDUE FOR COLLECTION','MISSED PICKUP','OVERDUE STORE','FAILED DELIVERY','FAILED PAYMENT','PART ON HOLD','PART PENDING'])
                                ->where('infoOrder.DateDeliveryAsk','!=','2020-01-01');
                        });
                    })
                  ->where('infoCustomer.CustomerID','=',$customerId);
        }

        if($current_tab=='with_partner')
        $orderlist=$orderlist->where('infoitems.idPartner','!=','0')
            ->where('infoitems.PartnerINOUT','=','1');

        if($current_tab=='due_today')
            $orderlist=$orderlist->whereDate('infoOrder.dateprod','=',date('Y-m-d'));

        if($current_tab=='due_tomorrow')
            $orderlist=$orderlist->whereDate('infoOrder.dateprod','=',date('Y-m-d',strtotime('tomorrow')));

        if($current_tab=='unfulfilled'){
            $orderlist=$orderlist->whereNotIn('infoOrder.Status',['FULFILLED','DELETE','DELIVERED','SOLD','VOID','CANCEL']);
        }
        if($current_tab=='without_delivery_date'){
            $orderlist=$orderlist->whereNotIn('infoOrder.status',['DELIVERED', 'DELIVERD TO STORE', 'SOLD', 'DONATED', 'DONATED TO CHARITY', 'COLLECTED','VOIDED','FULFILLED'])
                ->where('infoOrder.DateDeliveryAsk','<=',date('Y-m-d'))
                ->where('infoitems.ItemTrackingKey','<>','');
        }


        if($current_tab!='all_orders')
            $orderlist=$orderlist->whereNotIn('infoOrder.Status',['VOID', 'DELETE']);
        //filters


        if(!empty($filters))
            foreach($filters as $colname => $values){
                if($colname =='infoitems.express'){
                        $express=[];
                        if(in_array('standard',$values)){
                            $express=array_merge($express,[0,2,3]);
                        }
                    if(in_array('exp24',$values)){
                        $express=array_merge($express,[1,4,5]);
                    }
                    if(in_array('exp48',$values)){
                        $express=array_merge($express,[6]);
                    }
                    if(!empty($express))
                        $orderlist=$orderlist->whereIn($colname,$express);
                }else if( $colname !='infoitems.express' && $colname != 'infoitems.ProdDate' && $colname != 'infoitems.DelivDate' && $colname != 'infoOrder.DetDate' && $colname !='infoOrder.deliverymethod'){
                    if(!empty($values))
                    $orderlist=$orderlist->whereIn($colname, $values);
                }else if($colname == 'infoitems.ProdDate' && !empty($values)){
                    $orderlist=$orderlist->whereBetween('infoitems.PromisedDate', [ $values[0], $values[1]]);
                }else if($colname == 'infoitems.DelivDate' && !empty($values)){
                    $orderlist=$orderlist->whereBetween('infoitems.PromisedDate', [ $values[0], $values[1]]);
                }else if($colname == 'infoOrder.DetDate' && !empty($values)){
                    $start_first_day = Carbon::parse($values[0])->startOfDay()->toDateTimeString();
                    $end_first_day = Carbon::parse($values[1])->endOfDay()->toDateTimeString();
                    $orderlist=$orderlist->whereBetween('infoOrder.detailed_at', [ $start_first_day , $end_first_day]);
                }else if($colname == 'infoOrder.deliverymethod'){

                    if(count($values) < 2){
                        foreach ($values as $k){
                            if($k == 0){
                                $orderlist=$orderlist->where($colname,'!=' , '');
                            }
                            if($k == 1){
                                $orderlist=$orderlist->where($colname , '');
                            }
                        }
                    }

                }else{

                }
            }

        $orderlist=$orderlist->groupBy('infoOrder.id');

        //sort
        if(!empty($sort)) {
            foreach ($sort as $columns)
                foreach ($columns as $column => $direction)
                    $orderlist = $orderlist->orderBy($column, $direction);
        }else{
            $orderlist = $orderlist->orderByDesc('infoOrder.id');
        }


        $orderlist=$orderlist->skip($skip)->take($take);
        $orderlist=$orderlist->get();

            // adding ready_sub_orders and deliv date
            foreach ($orderlist as $order) {

                if($current_tab != 'customer_care'){

                    $order->ready_sub_orders = DB::table('infoOrder')
                        ->join('infoInvoice', 'infoOrder.OrderID','=', 'infoInvoice.OrderID')
                        ->distinct('infoInvoice.InvoiceID')
                        ->where('infoOrder.id', $order->id)
                        ->whereIn('infoInvoice.Status', ['READY','READY IN STORE','FULFILLED'])->count();
                }



                //Booking Only
                if($order->TypeDelivery == "DELIVERY" && ($order->Status == "RECURRING" || $order->Status == "SCHEDULED")  && $order->deliverymethod == '' ){
                    if($order->DateDeliveryAsk != "2020-01-01"  && !is_null($order->DateDeliveryAsk) ){

                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));

                    } else {
                                $order->Deliv = '--' ;
                                $order->Prod = '--';
                    }

                }
                //Pickup Only

                else if($order->DateDeliveryAsk == "2020-01-01" && $order->Status == "SCHEDULED"  && $order->DatePickup != "2020-01-01" ){
                    $order->Deliv = '--';
                    if(!is_null($order->DatePickup)){

                        $order->Prod  = date('d/m/Y',strtotime($order->DatePickup));
                    }
                }

                //Store New
                else if($order->deliverymethod == 'in_store_collection' ){

                    if($order->DateDeliveryAsk != "2020-01-01"  && !is_null($order->DateDeliveryAsk) ){

                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                        //dateProd
                        $order->Prod  = date('d/m/Y',strtotime($order->DateDeliveryAsk));

                    } else {

                        if($order->status_deliveryask != "DEL" && $order->status_pickup != "DEL" ){
                            if(!is_null($order->DatePickup) && !is_null($order->DateDeliveryAsk)){

                                $pickupDate =  strtotime($order->DatePickup) ;
                                $DeliveryDate = strtotime($order->DateDeliveryAsk);

                                    if($pickupDate <  $DeliveryDate){

                                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                        $order->Prod  = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                    } else if ($pickupDate >  $DeliveryDate){
                                        $order->Deliv = date('d/m/Y',strtotime($order->DatePickup));;
                                        $order->Prod  = date('d/m/Y',strtotime($order->DatePickup));
                                    }
                            } else {

                                $order->Deliv = '--' ;
                                $order->Prod = '--';
                            }

                        }
                    }

                }

                //Delivery New
                else if($order->deliverymethod == 'home_delivery' ){

                    if($order->DateDeliveryAsk != "2020-01-01"  && !is_null($order->DateDeliveryAsk) ){

                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));;
                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));

                    } else {

                        if($order->status_deliveryask != "DEL" && $order->status_pickup != "DEL" ){
                            if(!is_null($order->DatePickup) && !is_null($order->DateDeliveryAsk)){

                                $pickupDate =  strtotime($order->DatePickup) ;
                                $DeliveryDate = strtotime($order->DateDeliveryAsk);

                                    if($pickupDate <  $DeliveryDate){

                                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));
                                    } else if ($pickupDate >  $DeliveryDate){
                                        $order->Deliv = date('d/m/Y',strtotime($order->DatePickup));
                                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DatePickup)));
                                    }
                            } else {

                                $order->Deliv = '--' ;
                                $order->Prod = '--';
                            }

                        }
                    }

                }

                 //Delivery Only New
                else if($order->deliverymethod == 'delivery_only'){

                    if($order->DateDeliveryAsk != "2020-01-01"  && !is_null($order->DateDeliveryAsk) ){

                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));

                    } else {

                        if($order->status_deliveryask != "DEL" && $order->status_pickup != "DEL" ){
                            if(!is_null($order->DatePickup) && !is_null($order->DateDeliveryAsk)){

                                $pickupDate =  strtotime($order->DatePickup) ;
                                $DeliveryDate = strtotime($order->DateDeliveryAsk);

                                    if($pickupDate <  $DeliveryDate){

                                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));
                                    } else if ($pickupDate >  $DeliveryDate){
                                        $order->Deliv = date('d/m/Y',strtotime($order->DatePickup));
                                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DatePickup)));
                                    }
                            } else {

                                $order->Deliv = '--' ;
                                $order->Prod = '--';
                            }

                        }
                    }
                }

                //Store Old
                else if($order->TypeDelivery != "DELIVERY" && $order->deliverymethod == ''){

                    if($order->DateDeliveryAsk != "2020-01-01"  && !is_null($order->DateDeliveryAsk) ){

                                $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                $order->Prod  = date('d/m/Y',strtotime($order->DateDeliveryAsk));

                    } else {
                        if(!is_null($order->PromisedDate)){

                            $promisedDate = date('Y-m-d',strtotime($order->PromisedDate));
                            $order->Deliv = date('d/m/Y',strtotime($order->PromisedDate));
                            $order->Prod  = date('d/m/Y',strtotime($promisedDate));

                        } else {

                            $order->Deliv = '--';
                            $order->Prod = '--';

                        }

                    }

                }
                //$PreviousDate = date( 'd/m/Y',(int)strtotime($order->DateDeliveryAsk) + 86400) ;
                //Delivery Old
                else if($order->TypeDelivery == "DELIVERY" && $order->deliverymethod == ''){

                    if($order->DateDeliveryAsk != "2020-01-01"  && !is_null($order->DateDeliveryAsk) ){

                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk)); ;
                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));


                    } else {

                        if($order->status_deliveryask != "DEL" && $order->status_pickup != "DEL" ){
                            if(!is_null($order->DatePickup) && !is_null($order->DateDeliveryAsk)){

                                $pickupDate = strtotime($order->DatePickup);
                                $DeliveryDate = strtotime($order->DateDeliveryAsk);

                                    if($pickupDate <  $DeliveryDate){
                                        $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DateDeliveryAsk)));
                                    } else if ($pickupDate >  $DeliveryDate){
                                        $order->Deliv = date('d/m/Y',strtotime($order->DatePickup));
                                        $order->Prod  = date('d/m/Y',strtotime($this->PreviousDate($order->DatePickup)));
                                    }

                            } else {

                                $order->Deliv = '--' ;
                                $order->Prod  = '--';
                            }

                        }
                    }
                }

                //Fulfiled
                else if($order->Status == "FULFILLED" ){

                    if($order->deliverymethod != '' && $order->Orderdatesold != '2020-01-01' && !is_null($order->Orderdatesold)){
                        $order->Deliv = date('d/m/Y',strtotime($order->Orderdatesold)) ;
                        $order->Prod  = date('d/m/Y',strtotime($order->Orderdatesold));
                    }else{
                        if($order->DateDeliveryAsk != null){
                                $order->Deliv = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                                $order->Prod  = date('d/m/Y',strtotime($order->DateDeliveryAsk));
                            }else {
                                $order->Deliv = '--' ;
                                $order->Prod  = '--';
                            }
                    }

                    if($order->deliverymethod == '' && $order->Orderdatesold != '2020-01-01' && !is_null($order->Orderdatesold)){

                        $order->Deliv = date('d/m/Y',strtotime($order->Orderdatesold)) ;
                        $order->Prod  = date('d/m/Y',strtotime($order->Orderdatesold));

                        } else {
                            if($order->PromisedDate != null){
                                $order->Deliv =  date('d/m/Y',strtotime($order->PromisedDate)) ;
                                $order->Prod  = date('d/m/Y',strtotime($order->PromisedDate)) ;
                            }else {
                                $order->Deliv = '--' ;
                                $order->Prod  = '--';
                            }


                        }
                }
                //VOID && DELETE
                else if( $order->Status == "VOID " || $order->Status == "DELETE" || $order->Status == "Cancel"){

                    $order->Deliv = '--';
                    $order->Prod  = '--';

                }

                else {
                    $order->Deliv = '--';
                    $order->Prod  = '--';
                }

                if( ($order->Deliv == null || $order->Deliv == "01/01/2020") ){
                    $order->Deliv = '--';
                }
                if( ($order->Prod == null || $order->Prod == "01/01/2020")){
                    $order->Prod = '--';
                }

                if($order->Deliv != "--" && !is_null($order->Deliv) && $order->Status != "FULFILLED"){
                    $date = str_replace('/', '-', $order->Deliv);
                    // if(date('Y-m-d',strtotime($date)) < date('Y-m-d')) {
                    //     $order->Deliv = '--';
                    // }
                }
                if($order->Prod != "--" && !is_null($order->Prod) && $order->Status != "FULFILLED"){
                    $date = str_replace('/', '-', $order->Prod);
                    // if(date('Y-m-d',strtotime($date)) < date('Y-m-d')) {
                    //     $order->Prod = '--';
                    // }
                }
                if($order->DET == null || $order->DET == "00/00/0000"){
                    $order->DET = '--';
                }
        }
        return response()->json($orderlist);
    }

    public function getStatusOrders(){

        $Status= [] ;
        $orderstatus = DB::table('infoOrder')->select('Status')->distinct('Status')->get();
        $tmp = $orderstatus[1];
        $orderstatus[1] = $orderstatus[0];
        $orderstatus[0] = $tmp;
            foreach ($orderstatus as $k => $v) {
                $Status[$k] = $v->Status;
            }
            $result = json_decode(json_encode(array_combine($Status,$Status)), FALSE);
            return response()->json(
                ['Status'=>$result]
            );
    }

}
