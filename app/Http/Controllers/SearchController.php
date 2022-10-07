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
    $keywordRaw = $request['query'];
    $keywordRaw = str_replace(",", " ",  $keywordRaw);
    $keywords   = explode(' ', $keywordRaw);
 
    $query = $request['query'];
    $PerPageOrder = $request['PerPageOrder'];
    $PerPageUser = $request['PerPageUser'];
    $PerPageEmails = $request['PerPageEmails'];
    $PerPageItems = $request['PerPageItems'];

    $orders = DB::table('infoOrder')->select(['infoOrder.created_at' ,'infoOrder.id','infoOrder.Status','infoOrder.DateDeliveryAsk','infoCustomer.Name','infoOrder.TypeDelivery','infoCustomer.Actif' , 'infoCustomer.CustomerID',DB::raw('IF(infoOrder.DateDeliveryAsk="2020-01-01" OR infoOrder.DateDeliveryAsk="2000-01-01" OR infoOrder.DateDeliveryAsk="","--",DATE_FORMAT(infoOrder.DateDeliveryAsk, "%a %d/%m")) as PromisedDate'),'infoOrder.OrderID','infoOrder.suggestedDeliveryDate'])
    ->join( 'infoCustomer', function ($join){
        $join->on( 'infoCustomer.CustomerID', '=', 'infoOrder.CustomerID')
        ->where('infoCustomer.Actif', '=' , 1);
    })
    ->leftJoin('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
    ->leftJoin('infoitems',function($join){
    $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
    ->whereNotIn('infoitems.Status',['DELETE','VOID']);
     });
     foreach($keywords as $searchTerm){
        $orders->where(function($q) use ($searchTerm){
            $q->where('FirstName', 'like', '%'.$searchTerm.'%')
            ->orWhere('LastName', 'like', '%'.$searchTerm.'%')
            ->orWhere('Name', 'like', '%'.$searchTerm.'%')
            ->orWhere('EmailAddress', 'like', '%'.$searchTerm.'%');
            // and so on
        });
    };

    $orders = $orders
    ->where('infoOrder.Status','!=' ,'DELETE')
    ->orWhere('infoOrder.id','LIKE' , $query)
    ->orWhere('infoitems.ItemTrackingKey','LIKE' ,$query)
    ->orWhere('infoitems.id_items',$query)
    ->orWhere('infoInvoice.NumInvoice', 'LIKE', $query)
    ->orderBy('infoOrder.created_at', 'desc')
    ->distinct('infoOrder.id')
    ->paginate($PerPageOrder);


    $users = DB::table('infoCustomer')
    ->select('Name', 'EmailAddress', 'Phone','infoCustomer.id', 'infoCustomer.CustomerID' , 'infoCustomer.Actif','infoCustomer.CustomerIDMaster',
    DB::raw(' IF(infoCustomer.CustomerIDMaster = "" AND infoCustomer.CustomerIDMasterAccount = "" AND infoCustomer.IsMaster = 0 AND infoCustomer.IsMasterAccount = 0, "B2C", "B2B") as cust_type'),
    DB::raw(' IF(infoCustomer.CustomerIDMaster = "",  "Main", "Sub") as customer_account')
    )->where('infoCustomer.Actif', '=', 1);
    foreach($keywords as $searchTerm){
        $users->where(function($q) use ($searchTerm){
            $q->where('FirstName', 'like', '%'.$searchTerm.'%')
            ->orWhere('LastName', 'like', '%'.$searchTerm.'%')
            ->orWhere('Name', 'like', '%'.$searchTerm.'%')
            ->orWhere('EmailAddress', 'like', '%'.$searchTerm.'%');
            // and so on
        });
    };
    
    $users = $users->paginate($PerPageUser);
    foreach ($users as $item) {
        $item->Phone=json_decode($item->Phone);
    }
    foreach ($users as $item) {

        if($item->customer_account == "Main"){
            $item->Pickup = DB::table('pickup')->select('status as status_pickup', 'date as date_pickup')
            ->where('pickup.CustomerID','=',$item->CustomerID)
            ->where('pickup.status', 'not LIKE', '%DEL%')
            ->whereDate('pickup.date', '>=', date('Y-m-d'))
            ->get();
        }
        if($item->customer_account == "Sub"){
            $item->Pickup = DB::table('pickup')->select('status as status_pickup', 'date as date_pickup')
            ->where('pickup.CustomerID','=',$item->CustomerIDMaster)
            ->where('pickup.status','not LIKE', '%DEL%')
            ->whereDate('pickup.date', '>=', date('Y-m-d'))
            ->get();
        }    
    }

    $emails = DB::table('infoCustomer')
    ->where('FirstName', 'LIKE', '%'. $query . '%')
    ->orWhere('LastName','LIKE', '%'. $query . '%')
    ->paginate($PerPageEmails);

    $items = DB::table('infoitems') 
    ->select( 
        'infoInvoice.CustomerID', 'infoInvoice.NumInvoice AS sub_order', 'infoitems.ItemTrackingKey', 
        'infoitems.typeitem as iteminfo', 'infoitems.id AS item_id','infoitems.brand','infoCustomer.Actif',
        'infoitems.nextpost', 'infoitems.store',  'postes.nom as location', 'postes.nominterface','infoCustomer.FirstName','infoCustomer.LastName', 
        'infoOrder.OrderID'
    )
    ->Join('infoInvoice','infoitems.InvoiceID','infoInvoice.InvoiceID')
    ->leftJoin('infoOrder','infoInvoice.OrderID','infoOrder.OrderID')
    ->leftJoin( 'infoCustomer', function ($join){
        $join->on( 'infoCustomer.CustomerID', '=', 'infoOrder.CustomerID')
        ->where('infoCustomer.Actif', '=' , 1);
    })
    ->leftJoin('postes','postes.id','=','infoitems.nextpost');

     if(str_contains($query , "-")){
        $items = $items->Where('infoInvoice.NumInvoice', '=', $query);
     } else {
        $items = $items ->Where('infoitems.ItemTrackingKey', 'LIKE', $query)
        ->orWhere('infoitems.id', 'LIKE', $query)
        ->orWhere('infoitems.id_items', 'LIKE', $query)
        ->orWhere('infoOrder.id', 'LIKE', $query)
        ->orWhere('infoCustomer.FirstName', 'LIKE', $query)
        ->orWhere('infoCustomer.LastName', 'LIKE', $query)
        ->orWhereIn('infoCustomer.FirstName', $keywords)
        ->orWhereIn('infoCustomer.LastName', $keywords)
        ->orWhereIn('infoCustomer.EmailAddress', $keywords);
     }

   
     $items = $items->orderBy('infoitems.date_add', 'desc')
    ->paginate($PerPageItems);


        return response()->json( ['customers_orders'=>$orders ,  'customers_emails'=>$emails , 'customers'=>$users , 'items' => $items  ]);
}

public function SearchByCustomer(Request $request)
 {
    $query = $request['query'];
    $PerPage = $request['PerPage'];
    $Customer = DB::table('infoCustomer')->select(['infoCustomer.Actif','infoCustomer.id','infoCustomer.Name','infoCustomer.TypeDelivery','infoCustomer.CustomerID','infoCustomer.Phone','infoCustomer.EmailAddress',
    DB::raw('IF(btob = 0, "B2C", "B2B") as cust_type'),
    ])
    ->where('infoCustomer.Actif', '=', 1)
    ->where(function($q) use ($query) {
        $q->where('Name', 'LIKE', '%' . $query . '%')
        ->orWhere('EmailAddress', 'LIKE', $query . '%')
        ->orWhere('Phone', 'LIKE', '%' . $query . '%');
    })
    ->orderBy('Name')
    ->paginate($PerPage);

    foreach ($Customer as $item) {
        $item->Phone=json_decode($item->Phone);
    }
    return response()->json($Customer);
 }

 /**
  * Search customers 
  * @param query
  * @return customers with name, type, email, and phone
  */
  public function SearchCustomersToLink(Request $request){
      $customers = DB::table('infoCustomer')
                    ->select(
                        'id', 'Name as name', 'EmailAddress as email', 'Phone as phone',
                        DB::raw('IF(CustomerIDMaster = "" AND CustomerIDMasterAccount = "" AND IsMaster = 0 AND IsMasterAccount = 0, "B2C", "B2B") as customerType'),
                        DB::raw('IF(IsMaster = 1, "Main", "Sub") as accountType'),
                        DB::raw('IF(SignupDate = "2000-01-01", SignupDateOnline, SignupDate) as date'),
                        'TotalSpend as spent',
                        'CustomerID as customerId'
                    )
                    ->where('infoCustomer.Actif', '=', 1)
                    ->where('infoCustomer.CustomerIDMaster','!=', $request->customerID)
                    ->where(function($query) use ($request) {
                        $query->where('Name', 'LIKE', '%' .$request['query']. '%')
                            ->orWhere('EmailAddress', 'LIKE', '%' . $request['query'] . '%')
                            ->orWhere('FirstName', 'LIKE', '%' . $request['query'] . '%')
                            ->orWhere('LastName', 'LIKE', '%' . $request['query'] . '%')
                            ->orWhere('Phone', 'LIKE', '%' . $request['query'] . '%');
                    })
                    ->orderByDesc('Name')->get();
    return response()->json($customers);
  }
}
