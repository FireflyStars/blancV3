<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $orderlist=$orderlist->where('infoCustomer.TypeDelivery','!=','DELIVERY');

        $orderlist=$orderlist->groupBy('infoOrder.id');

        //sort
        if(!empty($sort)) {
            foreach ($sort as $columns)
                foreach ($columns as $column => $direction)
                    $orderlist = $orderlist->orderBy($column, $direction);
        }else{
            $orderlist = $orderlist->orderBy('infoOrder.id', 'DESC');
        }


        $orderlist=$orderlist->skip($skip)->take($take)->get();

        return response()->json($orderlist);
    }
}
