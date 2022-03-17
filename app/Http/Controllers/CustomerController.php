<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function customerDetails(Request $request){
        $CustomerID=$request->post('CustomerID');
        $infoCustomer = null;

        if($CustomerID!=''){
            $infoCustomer=DB::table('infoCustomer')->where('infoCustomer.CustomerID','=',$CustomerID)
                ->leftJoin('address',function($join) {
                    $join->on( 'infoCustomer.CustomerID', '=', 'address.CustomerID')
                        ->where('address.status','DELIVERY');
                })
                ->first();
            $ltm_spend=DB::table('infoOrder')->select(['Total'])->where('CustomerID','=',$CustomerID)->where('Status','=','FULFILLED')->where('created_at','>=',date('Y-m-d',strtotime('-12 months')))->get();
            $spend=0;
            foreach ($ltm_spend as $order){
                $spend+=$order->Total;
            }
            $infoCustomer->ltm_spend=$spend;
        }

        return response()->json(['customer'=>$infoCustomer]);
    }

    public function getAllCustomers(Request $request){
        $customers = DB::table('infoCustomer')
                        ->join( 'address', function ($join){
                            $join->on( 'address.CustomerID', '=', 'infoCustomer.CustomerID')
                                 ->where('address.status', "DELIVERY");
                        })
                        ->Leftjoin( 'infoOrder', function ($join){
                            $join->on( 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID');
                                //  ->whereIn('infoOrder.Status', ["FULFILLED", "READY"]);
                        })
                        ->select(
                            'infoCustomer.id',
                            DB::raw('IF(infoCustomer.CustomerIDMaster = "" AND infoCustomer.CustomerIDMasterAccount = "" AND infoCustomer.IsMaster = 0 AND infoCustomer.IsMasterAccount = 0, "B2C", "B2B") as type'),
                            DB::raw('LCASE(infoCustomer.TypeDelivery) as active_in'), 
                            DB::raw('LCASE(infoCustomer.Name) as name'), 
                            DB::raw('CONCAT_WS(", ", CONCAT_WS(" ", address.address1, address.address2), address.Town, address.Country, address.postcode) as address'),
                            DB::raw('LCASE(infoCustomer.EmailAddress) as email'),
                            DB::raw('IF(infoCustomer.Phone = "", "--", infoCustomer.Phone) as phone'),
                            DB::raw('IF(DATE_FORMAT(MAX(infoOrder.created_at), "%d/%m/%y") = "", "--", DATE_FORMAT(MAX(infoOrder.created_at), "%d/%m/%y")) as last_order'),
                            // DB::raw('IF(infoCustomer.LastOrderDate = "", "--", DATE_FORMAT(infoCustomer.LastOrderDate, "%d/%m/%y")) as last_order'),
                            DB::raw('CEIL(SUM(infoOrder.Total)) as total_spent'),
                            // 'infoCustomer.TotalSpend as total_spent',
                        )
                        ->groupBy('infoCustomer.CustomerID')
                        ->orderBy('infoCustomer.id');
        if($request->selected_nav == 'B2B' || $request->customer_type == 'B2B'){
            $customers = $customers->where( function( $query ) {
                $query->where('infoCustomer.CustomerIDMaster', '!=', '')
                    ->orWhere('infoCustomer.CustomerIDMasterAccount', '!=', '')
                    ->orWhere('infoCustomer.IsMaster', 1)
                    ->orWhere('infoCustomer.IsMasterAccount', 1);
            });    
        }
        if($request->selected_nav == 'B2C' || $request->customer_type = 'B2C'){
            $customers = $customers->where('infoCustomer.CustomerIDMaster', '')
                            ->where('infoCustomer.CustomerIDMasterAccount', '')
                            ->where('infoCustomer.IsMaster', 0)
                            ->where('infoCustomer.IsMasterAccount', 0);
        }
        if( $request->customer_location !='' ){
            $customers = $customers->where('infoCustomer.TypeDelivery', $request->customer_location);
        }
        if( $request->invoice_preference !='' ){
            $customers = $customers->where('infoCustomer.invoicepreference', $request->invoice_preference);
        }
        if( $request->last_order_start !='' && $request->last_order_end ){
            $customers = $customers->whereBetween('infoOrder.created_at', [$request->last_order_start, $request->last_order_end]);
        }

        if( $request->total_spent !='' ){
            $customers =  $customers->get()
                                ->where('total_spent', '>=', explode(',', $request->total_spent)[0])
                                ->where('total_spent', '<=', explode(',', $request->total_spent)[1])
                                ->values();
            $total_count   =  $customers->count();
            $customers   =  $customers->skip($request->skip ? $request->skip : 0)
                                ->take(10)
                                ->values();
        }else{
            $total_count =  $customers->get()->count();
            $customers   =  $customers->skip($request->skip ? $request->skip : 0)
                                    ->take(10)
                                    ->get();
        }
        return response()->json([
            'customers'     => $customers,
            'total_count'   => $total_count,
        ]);
    }
}
