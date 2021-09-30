<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SearchController extends Controller
{
   



public function SearchCustomer(Request $request)
{
    $query = $request['query'];
    $PerPageOrder = $request['PerPageOrder'];
    $PerPageUser = $request['PerPageUser'];
    $PerPageEmails = $request['PerPageEmails'];

    $orders = DB::table('infoOrder')->select(['infoOrder.id','infoOrder.Status','infoOrder.DateDeliveryAsk','infoCustomer.Name','infoCustomer.TypeDelivery','infoCustomer.CustomerID',DB::raw('IF(infoOrder.DateDeliveryAsk="2020-01-01" OR infoOrder.DateDeliveryAsk="2000-01-01" OR infoOrder.DateDeliveryAsk="","--",DATE_FORMAT(infoOrder.DateDeliveryAsk, "%a %d/%m")) as PromisedDate'),'infoOrder.OrderID','infoOrder.suggestedDeliveryDate'])
    ->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')
    ->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
    ->leftJoin('infoitems',function($join){
    $join->on('infoInvoice.SubOrderID','=','infoitems.SubOrderID')
    ->where('infoitems.SubOrderID','!=','')
    ->whereNotIn('infoitems.Status',['DELETE','VOID']);
     })
    ->where('Name', 'LIKE', $query . '%')
    ->orWhere('infoOrder.id',$query)
    ->orWhere('infoitems.ItemTrackingKey',$query)
    ->groupBy('infoOrder.CustomerID')
    ->orderBy('Name')
    ->paginate($PerPageOrder);


    $users = DB::table('infoCustomer')
    ->where('Name', 'LIKE', $query . '%')
    ->paginate($PerPageUser);
    foreach ($users as $item) {
        $item->Phone=json_decode($item->Phone);
    }

    $emails = DB::table('infoCustomer')
    ->where('EmailAddress', 'LIKE', $query . '%')
    ->paginate($PerPageEmails);
    
    foreach ($emails as $item) {
        $item->Phone=json_decode($item->Phone);
    }

        return response()->json( ['customers_orders'=>$orders ,  'customers_emails'=>$emails , 'customers'=>$users  ]);

}
}

