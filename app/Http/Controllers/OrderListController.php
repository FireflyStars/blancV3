<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $orderlist=DB::table('infoOrder')
            ->select(['infoOrder.id','infoOrder.Status','infoOrder.Total','infoCustomer.Name','infoCustomer.TypeDelivery',DB::raw('DATE_FORMAT(infoOrder.DateDeliveryAsk, "%d/%m") as PromisedDate'),DB::raw('count(infoitems.id) as numitems'),DB::raw('GROUP_CONCAT(infoitems.express) as express'),DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid')])
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
                        ->whereNotIn('infoOrder.status', ['DELIVERED', 'DELIVERD TO STORE', 'SOLD', 'DONATED', 'DONATED TO CHARITY', 'COLLECTED', 'VOIDED', 'FULFILLED', 'VOID', 'DELETE', 'SOLD']);
                })->orWhere(function($query){
                    $query->where('infoOrder.Paid','=',0)->where('infoCustomer.TypeDelivery','=','DELIVERY');
                })->orWhere(function($query){
                    $query->whereIn('infoOrder.Status',['LATE','LATE DELIVERY','OVERDUE FOR COLLECTION','MISSED PICKUP','OVERDUE STORE','FAILED DELIVERY','FAILED PAYMENT','PART ON HOLD','PART PENDING'])
                        ->where('infoOrder.DateDeliveryAsk','!=','2020-01-01');
                });
            });

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
}
