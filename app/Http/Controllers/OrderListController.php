<?php

namespace App\Http\Controllers;

use App\Tranche;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderListController extends Controller
{
    //
    public function getorderlist(Request $request){
        $skip=$request->get('skip');
        $take=$request->get('take');
        $current_tab=$request->get('current_tab');
        $sort=$request->get('sort');
        $filters=$request->get('filters');
        $orderlist=DB::table('infoOrder')
            ->select(['infoOrder.id','infoOrder.Status','infoOrder.Total','infoCustomer.Name','infoCustomer.TypeDelivery',DB::raw('IF(infoOrder.DateDeliveryAsk="2020-01-01" OR infoOrder.DateDeliveryAsk="2000-01-01" OR infoOrder.DateDeliveryAsk="","--",DATE_FORMAT(infoOrder.DateDeliveryAsk, "%d/%m")) as PromisedDate'),DB::raw('count(infoitems.id) as numitems'),DB::raw('GROUP_CONCAT(infoitems.express) as express'),DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),'infoOrder.suggestedDeliveryDate'])
            ->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')
            ->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
            ->join('infoitems','infoInvoice.SubOrderID','infoitems.SubOrderID')
            ->where('infoOrder.OrderID','!=','')
            ->where('infoInvoice.OrderID','!=','')
            ->where('infoitems.SubOrderID','!=','');

        if($current_tab=='with_partner')
        $orderlist=$orderlist->where('infoitems.idPartner','!=','0')
            ->where('infoitems.PartnerINOUT','=','1');

        if($current_tab=='due_today')
            $orderlist=$orderlist->whereDate('infoOrder.DateDeliveryAsk','=',date('Y-m-d'));

        if($current_tab=='due_tomorrow')
            $orderlist=$orderlist->whereDate('infoOrder.DateDeliveryAsk','=',date('Y-m-d',strtotime('tomorrow')));

        if($current_tab=='customer_care'){
            $orderlist=$orderlist->where(
            function($query) {
                $query->where(function($query) {
                    $query->whereDate('infoOrder.DateDeliveryAsk', '<=', date('Y-m-d'))
                        ->whereNotIn('infoOrder.Status', ['DELIVERED', 'DELIVERD TO STORE', 'SOLD', 'DONATED', 'DONATED TO CHARITY', 'COLLECTED', 'VOIDED', 'FULFILLED', 'VOID', 'DELETE', 'SOLD']);
                })->orWhere(function($query){
                    $query->where('infoOrder.Paid','=',0)->where('infoCustomer.TypeDelivery','=','DELIVERY');
                })->orWhere(function($query){
                    $query->whereIn('infoOrder.Status',['LATE','LATE DELIVERY','OVERDUE FOR COLLECTION','MISSED PICKUP','OVERDUE STORE','FAILED DELIVERY','FAILED PAYMENT','PART ON HOLD','PART PENDING'])
                        ->where('infoOrder.DateDeliveryAsk','!=','2020-01-01');
                });
            });

        }
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
            foreach($filters as $colname=>$values){
                if($colname=='infoitems.express'){
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
                }else{
                    if(!empty($values))
                    $orderlist=$orderlist->whereIn($colname,$values);
                }
            }

        $orderlist=$orderlist->groupBy('infoOrder.id');

        //sort
        if(!empty($sort)) {
            foreach ($sort as $columns)
                foreach ($columns as $column => $direction)
                    $orderlist = $orderlist->orderBy($column, $direction);
        }else{
            $orderlist = $orderlist->orderBy('infoOrder.id', 'DESC');
        }


        $orderlist=$orderlist->skip($skip)->take($take);
       // $sql = Str::replaceArray('?', $orderlist->getBindings(), $orderlist->toSql());
        $orderlist=$orderlist->get();
// print
     //  dd($sql);
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
        $infoOrder_id=$request->post('infoOrder_id');
        $order=DB::table('infoOrder')
            ->select(['infoOrder.id','infoOrder.Status','infoOrder.Total','infoCustomer.Name','infoCustomer.TypeDelivery','infoCustomer.Phone','infoCustomer.CustomerID',DB::raw('IF(infoOrder.DateDeliveryAsk="2020-01-01" OR infoOrder.DateDeliveryAsk="2000-01-01" OR infoOrder.DateDeliveryAsk="","--",DATE_FORMAT(infoOrder.DateDeliveryAsk, "%a %d/%m")) as PromisedDate'),DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),'infoOrder.OrderID','infoOrder.suggestedDeliveryDate'])
            ->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')

            ->where('infoOrder.id','=',$infoOrder_id)->first();

            $billing_add=DB::table('address')->where('CustomerID','=',$order->CustomerID)->where('status','=','BILLING')->first();
            $delivery_add=DB::table('address')->where('CustomerID','=',$order->CustomerID)->where('status','=',$order->TypeDelivery)->first();

            $infoitems=DB::table('infoitems')->select(['infoInvoice.NumInvoice','infoitems.id as infoitems_id','infoitems.brand','infoitems.ItemTrackingKey','infoitems.colors','infoitems.typeitem','infoitems.priceTotal','infoitems.status','TypePost.Name as station',])->join('infoInvoice',function($join) use($order){
               $join->on('infoInvoice.SubOrderID','=','infoitems.SubOrderID')
               ->where('infoInvoice.OrderID','=',$order->OrderID);
            })->leftJoin('postes','postes.id','=','infoitems.nextpost')
                ->leftJoin('TypePost','TypePost.id','=','postes.TypePost')
                ->orderBy('infoInvoice.NumInvoice')->get();
        $items=[];
        $infoitems->each(function ($item) use(&$items) {
            $items[$item->NumInvoice][]=$item;//suborder grouping
        });

        if($order->Phone!=""){
            $order->Phone=json_decode($order->Phone);
            if($order->suggestedDeliveryDate!=null&&$order->suggestedDeliveryDate!=''){
                $suggestedDeliveryDate=Carbon::createFromFormat('Y-m-d',$order->suggestedDeliveryDate);
                if($suggestedDeliveryDate->isPast()){
                    $order->suggestedDeliveryDate=null;
                }
            }
        }
        $available_slots=[];
        if($order->TypeDelivery=='DELIVERY'&&$delivery_add!=null&&trim($delivery_add->postcode)!=''){
            $postcode = $delivery_add->postcode;

            $sel_postcode = "";

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

        return response()->json(['order'=>['detail'=>$order,'billing'=>$billing_add,'delivery'=>$delivery_add,'items'=>$items,'available_slots'=>$available_slots]]);
    }


    public function suggestdate(Request $request){
            $infoOrder_id=$request->post('infoOrder_id');
            $suggested_delivery_date=$request->post('suggested_delivery_date');
            $user=Auth::user();
            $update=false;
            if($user->hasRoles(['admin','Blanc Admin','user'])){ // cc cannot suggest a date
                $update= DB::table('infoOrder')->where('id','=',$infoOrder_id)->update(
                    [
                        'suggestedDeliveryDate'=>$suggested_delivery_date
                    ]
                );
            }
        return response()->json(['updated'=>$update]);
    }
}
