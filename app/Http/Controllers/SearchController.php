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
    ->leftJoin('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
    ->leftJoin('infoitems',function($join){
    $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
    //->where('infoitems.InvoiceID','!=','')
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

public function SearchByCustomer(Request $request)
 {
    $query = $request['query'];
    $PerPage = $request['PerPage'];
    $Customer = DB::table('infoCustomer')->select(['infoCustomer.id','infoCustomer.Name','infoCustomer.TypeDelivery','infoCustomer.CustomerID','infoCustomer.Phone','infoCustomer.EmailAddress'])
    ->where('Name', 'LIKE', '%' . $query . '%')
    ->orWhere('EmailAddress', 'LIKE', $query . '%')
    ->orWhere('Phone', 'LIKE', '%' . $query . '%')
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
                        DB::raw('IF(CustomerIDMaster = "" AND CustomerIDMasterAccount = "" AND IsMaster = 0 AND IsMasterAccount = 0, "B2C", "B2B") as type'),
                        DB::raw('IF(SignupDate = "2000-01-01", SignupDateOnline, SignupDate) as date'),
                        'TotalSpend as spent'
                    )
                    ->where(function($query){
                        $query->where('SignupDate', '!=', '2000-01-01')->orWhere('SignupDateOnline', '!=', '2000-01-01');
                    })
                    ->where('Name', 'LIKE', '%' .$request['query']. '%')
                    ->orWhere('EmailAddress', 'LIKE', '%' . $request['query'] . '%')
                    ->orWhere('Phone', 'LIKE', '%' . $request['query'] . '%')
                    ->orderByDesc('Name')->get();
    return response()->json($customers);
  }
}
