<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\InfoOrder;
use App\Models\InfoCustomer;
use App\Models\Infoitem;

class StatisticsController extends Controller
{
    public function getStatistics(Request $request)
    {
        $customFilter   =   $request->post('customFilter');
        $startDate      =   $request->post('startDate');
        $endDate        =   $request->post('endDate');
        $dateRangeType  =   $request->post('dateRangeType');

        $period         = [ Carbon::parse($startDate)->startOfDay()->toDateString(), Carbon::parse($endDate)->endOfDay()->toDateString() ];
        
        // new code added by YH
        if( !$customFilter ){
            $start_first_quarter_day = Carbon::now()->subYear()->startOfYear();
            $end_first_quarter_day = Carbon::parse($start_first_quarter_day)->lastOfQuarter();
            if( $dateRangeType == 'Today' ){
                $period = [Carbon::now()->startOfDay()->toDateString(), Carbon::now()->endOfDay()->toDateString()];
            }else if ( $dateRangeType == 'Yesterday' ){
                $period = [Carbon::yesterday()->startOfDay()->toDateString(), Carbon::yesterday()->endOfDay()->toDateString()];
            }else if ( $dateRangeType == 'Last 7 days' ){
                $period = [Carbon::now()->subYear()->startOfDay()->toDateString(), Carbon::now()->endOfDay()->toDateString()];
            }else if ( $dateRangeType == 'Last 90 days' ){
                $period = [Carbon::now()->subDays(90)->startOfDay()->toDateString(), Carbon::now()->endOfDay()->toDateString()];
            }else if ( $dateRangeType == 'Last Month' ){
                $period = [Carbon::now()->subMonth()->startOfDay()->toDateString(), Carbon::now()->endOfDay()->toDateString()];
            }else if ( $dateRangeType == 'Year to date' ){
                $period = [Carbon::now()->startOfYear()->toDateString(), Carbon::now()->endOfDay()->toDateString()];
            }else if ( $dateRangeType == '4th Quarter' ){
                $period = [Carbon::parse($start_first_quarter_day)->addMonths(9)->startOfDay()->toDateString(), Carbon::parse($end_first_quarter_day)->addMonths(9)->endOfDay()->toDateString()];
            }else if ( $dateRangeType == '3rd Quarter' ){
                $period = [Carbon::parse($start_first_quarter_day)->addMonths(6)->startOfDay()->toDateString(), Carbon::parse($end_first_quarter_day)->addMonths(6)->endOfDay()->toDateString()];
            }else if ( $dateRangeType == '2nd Quarter' ){
                $period = [Carbon::parse($start_first_quarter_day)->addMonths(3)->startOfDay()->toDateString(), Carbon::parse($end_first_quarter_day)->addMonths(3)->endOfDay()->toDateString()];
            }else{
                $start = Carbon::now()->subYear()->startOfYear();
                $period = [$start_first_quarter_day, $end_first_quarter_day];
            }
        }
        $total_sale_stores = InfoOrder::whereBetween('created_at', $period)
                                    ->select(DB::raw('AVG(Total) as avg'), DB::raw('SUM(Total) as total'), 'TypeDelivery')
                                    ->groupBy('TypeDelivery')
                                    ->get();
        
        // return response()->json($total_sale_stores);
        $stats = [];
        $statistique['mb'] = 0;
        $statistique['nh'] = 0;
        $statistique['sk'] = 0;
        $statistique['ch'] = 0;
        $total_sales_store = 0;
        $avg_store_order = 0;
        $avg_delivery_order = 0;
        $toal_delivery_sale = 0;
        $delivery_count = 0;
        foreach ($total_sale_stores as $key => $store) {
            if($store->TypeDelivery == 'MARYLEBONE') {
                $total_sales_store  +=  $store->total;
                // $avg_store_order    +=  $store->avg;
                $statistique['mb'] = number_format($store->total, 2) ;
            }
            if($store->TypeDelivery == 'NOTTING HILL') {
                $total_sales_store  += $store->total;
                // $avg_store_order    +=  $store->avg;
                $statistique['nh'] = number_format($store->total, 2);
            }
            if($store->TypeDelivery == 'SOUTH KEN') {
                $total_sales_store  += $store->total;
                // $avg_store_order    +=  $store->avg;
                $statistique['sk'] = number_format($store->total, 2);
            }
            if($store->TypeDelivery == 'CHELSEA') {
                $total_sales_store  += $store->total;
                // $avg_store_order    +=  $store->avg;
                $statistique['ch'] = number_format($store->total, 2);
            }
            if($store->TypeDelivery == 'DELIVERY') {
                $toal_delivery_sale += $store->total;
                $delivery_count ++;
                // $avg_delivery_order +=  $store->avg;
            }
        }
        // end total sales store
        
        // start total sales deliveries (B2B, B2C)

        $statistique['avg_store_order'] = ($total_sale_stores->count() - $delivery_count) <= 0 ? 0 : number_format( $total_sales_store / ($total_sale_stores->count() - $delivery_count), 2);
        $statistique['avg_delivery_order'] = number_format( $avg_delivery_order, 2);
        $total_sales_b2b = InfoOrder::join('infoCustomer', 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')
                                    ->whereBetween('infoOrder.created_at', $period)
                                    ->where('infoOrder.TypeDelivery', 'DELIVERY')
                                    ->where( function( $query ) {
                                        $query->where('infoCustomer.CustomerIDMaster', '!=', '')
                                                ->orWhere('infoCustomer.CustomerIDMasterAccount', '!=', '')
                                                ->orWhere('infoCustomer.IsMaster', 1)
                                                ->orWhere('infoCustomer.IsMasterAccount', 1);
                                    } )                                    
                                    ->select(DB::raw('AVG(Total) as avg'), DB::raw('SUM(Total) as total'))
                                    ->first();
        $statistique['b2b'] = number_format( $total_sales_b2b->total, 2);
        $statistique['avg_b2b_order'] = number_format( $total_sales_b2b->avg, 2);
        $total_sales_b2c = InfoOrder::join('infoCustomer', 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')
                                    ->whereBetween('infoOrder.created_at', $period)
                                    ->where('infoOrder.TypeDelivery', 'DELIVERY')
                                    ->where( function( $query ) {
                                        $query->where('infoCustomer.CustomerIDMaster', '')
                                                ->orWhere('infoCustomer.CustomerIDMasterAccount', '')
                                                ->orWhere('infoCustomer.IsMaster', '!=',1)
                                                ->orWhere('infoCustomer.IsMasterAccount', '!=',1);
                                    } )                                    
                                    ->select(DB::raw('AVG(Total) as avg'), DB::raw('SUM(Total) as total'))
                                    ->first();
        $statistique['b2c'] = number_format( $total_sales_b2c->total, 2);
        $statistique['avg_b2c_order'] = number_format( $total_sales_b2c->avg, 2);
        $statistique['avg_total_order'] = ($total_sale_stores->count() + $total_sales_b2b->count()+ $total_sales_b2c->count()) <= 0 ? 0 : number_format( ($total_sales_store + $total_sales_b2b->total + $total_sales_b2c->total + $toal_delivery_sale ) / ($total_sale_stores->count() + $total_sales_b2b->count()+ $total_sales_b2c->count()), 2);
        $statistique['total_sales'] = number_format( $total_sales_store + $total_sales_b2c->total + $total_sales_b2b->total, 2 );
        $first_time_total_sale_stores = InfoOrder::whereBetween('created_at', $period)
                                    ->where('firstorder', 1)
                                    ->whereIn('TypeDelivery', ['MARYLEBONE', 'NOTTING HILL', 'SOUTH KEN', 'CHELSEA'])
                                    ->select(DB::raw('SUM(Subtotal) as sub_total'), DB::raw('SUM(Total) as total'), 'TypeDelivery')
                                    ->groupBy('TypeDelivery')
                                    ->get();
        
        $statistique['first_time_mb'] = 0;
        $statistique['first_time_nh'] = 0;
        $statistique['first_time_sk'] = 0;
        $statistique['first_time_ch'] = 0;
        $first_time_total_sales_store = 0;
        foreach ($first_time_total_sale_stores as $key => $store) {
            $first_time_total_sales_store += $store->total;
            if($store->TypeDelivery == 'MARYLEBONE') $statistique['first_time_mb'] = number_format($store->total, 2) ;
            if($store->TypeDelivery == 'NOTTING HILL') $statistique['first_time_nh'] = number_format($store->total, 2);
            if($store->TypeDelivery == 'SOUTH KEN') $statistique['first_time_sk'] = number_format($store->total, 2);
            if($store->TypeDelivery == 'CHELSEA') $statistique['first_time_ch'] = number_format($store->total, 2);
        }
        // end total sales store

        // start total sales deliveries (B2B, B2C)
        $first_time_total_sales_b2b = InfoOrder::join('infoCustomer', 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')
                                    ->whereBetween('infoOrder.created_at', $period)
                                    ->where('infoOrder.TypeDelivery', 'DELIVERY')
                                    ->where('infoOrder.firstorder', 1)
                                    ->where( function( $query ) {
                                        $query->where('infoCustomer.CustomerIDMaster', '!=', '')
                                                ->orWhere('infoCustomer.CustomerIDMasterAccount', '!=', '')
                                                ->orWhere('infoCustomer.IsMaster', 1)
                                                ->orWhere('infoCustomer.IsMasterAccount', 1);
                                    } )
                                    ->select(DB::raw('SUM(Total) as total'))
                                    ->first()->total;
        $statistique['first_time_b2b'] = number_format( $first_time_total_sales_b2b, 2);
        
        $first_time_total_sales_app = InfoOrder::whereBetween('infoOrder.created_at', $period)
                                        ->where('firstorder', 1)
                                        ->where('fromapp', 1)
                                        ->select(DB::raw('SUM(Total) as total'))
                                        ->first()->total;
        $statistique['first_time_app'] = number_format( $first_time_total_sales_app, 2);
        
        $first_time_total_sales_normal = InfoOrder::whereBetween('infoOrder.created_at', $period)
                                        ->where('firstorder', 1)
                                        ->where('fromapp', 0)
                                        ->select(DB::raw('SUM(Total) as total'))
                                        ->first()->total;
        $statistique['first_time_normal'] = number_format( $first_time_total_sales_normal, 2);

        $statistique['first_time_total_sales'] = number_format( $first_time_total_sales_store + $first_time_total_sales_app + $first_time_total_sales_b2b + $first_time_total_sales_normal, 2 );

        // start new signup data
        $stores_new_signup = InfoCustomer::whereBetween('SignupDate', $period)
                                        ->select(DB::raw('count(*) as count'))->first()->count;
        $delivery_new_signup = InfoCustomer::whereBetween('SignupDateOnline', $period)
                                        ->select(DB::raw('count(*) as count'))->first()->count;
        $b2b_new_signup = InfoCustomer::whereBetween('SignupDateOnline', $period)
                                        ->where( function( $query ) {
                                            $query->where('CustomerIDMaster', '!=', '')
                                                    ->orWhere('CustomerIDMasterAccount', '!=', '')
                                                    ->orWhere('IsMaster', 1)
                                                    ->orWhere('IsMasterAccount', 1);
                                        } )
                                        ->select(DB::raw('count(*) as count'))->first()->count;
        $b2c_new_signup = InfoCustomer::whereBetween('SignupDateOnline', $period)
                                        ->where( function( $query ) {
                                            $query->where('CustomerIDMaster', '')
                                                    ->orWhere('CustomerIDMasterAccount', '')
                                                    ->orWhere('IsMaster', '!=',1)
                                                    ->orWhere('IsMasterAccount', '!=',1);
                                        } )        
                                        ->select(DB::raw('count(*) as count'))->first()->count;
        $statistique['b2c_new_signup'] = $b2c_new_signup;
        $statistique['b2b_new_signup'] = $b2b_new_signup;
        $statistique['delivery_new_signup'] = $delivery_new_signup;
        $statistique['stores_new_signup'] = $stores_new_signup;
        // end new signup data

        // service section data
        $service_items = Infoitem::whereBetween('PromisedDate', $period)
                                ->select(DB::raw('SUM(priceTotal) as total'), 'DepartmentName')
                                ->groupBy('DepartmentName')
                                ->orderByRaw("FIELD(DepartmentName, 'Garment Care','Shirts Hung','Shirts Folded','Shirts','Business','Laundry','Bedlinen','Household','Suede/Leather/Wax','Laundry Bag','Other')")
                                ->get();
        $statistique['service_items'] = $service_items;        
        $statistique['service_items_total'] = number_format($service_items->sum('total'), 2);
        $services_group = Infoitem::whereBetween('PromisedDate', $period)
                                ->select(DB::raw('SUM(priceTotal) as total'), 'DepartmentName')
                                ->groupBy('DepartmentName')
                                ->orderByRaw("FIELD(DepartmentName, 'Garment Care','Shirts Hung','Shirts Folded','Shirts','Business','Laundry','Bedlinen','Household','Suede/Leather/Wax','Laundry Bag','Other')")
                                ->get();
        $statistique['services_group'] = $services_group;        
        $statistique['services_group_total'] = number_format($services_group->sum('total'), 2);                                
        return response()->json(
            [
                'statistique'=> $statistique
            ]
        );
    }
}
