<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SearchController extends Controller
{
   

 public function SearchCustomerByOrder(Request $request)
{
    $query = $request['query'];
    $orders = DB::table('infoorder')->join('infoCustomer','infoorder.CustomerID','=','infoCustomer.CustomerID')
    ->where('FirstName', 'LIKE', $query . '%')
    ->whereNotIn('Status',['DELETE','VOID'])
    ->groupBy('infoorder.CustomerID')
    ->orderBy('Name')
    ->paginate(3);
    return response()->json( ['customers_orders'=>$orders ]);
     
}

public function SearchCustomerByName(Request $request)
{
    $query = $request['query'];

    $users = DB::table('infocustomer')
    ->where('FirstName', 'LIKE', $query . '%')
    ->paginate(3);

    foreach ($users as $item) {
        $item->Phone=json_decode($item->Phone);
    }

    return response()->json(
        ['customers'=>$users ]
    );
}

public function SearchCustomerByEmail(Request $request)
{
    $query = $request['query'];

    $emails = DB::table('infocustomer')
    ->where('EmailAddress', 'LIKE', $query . '%')
    ->paginate(3);
    
    foreach ($emails as $item) {
        $item->Phone=json_decode($item->Phone);
    }
    return response()->json(
        ['customers_emails'=>$emails ]
    );
}

}
