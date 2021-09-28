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
    $PerPage = $request['PerPage'];
    $orders = DB::table('infoOrder')->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')
    ->where('FirstName', 'LIKE', $query . '%')
    ->whereNotIn('Status',['DELETE','VOID'])
    ->groupBy('infoOrder.CustomerID')
    ->orderBy('Name')
    ->paginate($PerPage);

    $users = DB::table('infoCustomer')
    ->where('FirstName', 'LIKE', $query . '%')
    ->paginate($PerPage);
    foreach ($users as $item) {
        $item->Phone=json_decode($item->Phone);
    }

    $emails = DB::table('infoCustomer')
    ->where('EmailAddress', 'LIKE', $query . '%')
    ->paginate($PerPage);
    
    foreach ($emails as $item) {
        $item->Phone=json_decode($item->Phone);
    }

        return response()->json( ['customers_orders'=>$orders ,  'customers_emails'=>$emails , 'customers'=>$users  ]);

}
}

