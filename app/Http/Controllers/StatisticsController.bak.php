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

        $period         = [ Carbon::parse($startDate)->subYear()->startOfDay(), Carbon::parse($endDate)->subYear()->endOfDay() ];
        // new code added by YH
        if( !$customFilter ){
            $start_first_quarter_day = Carbon::now()->subYear()->startOfYear();
            $end_first_quarter_day = Carbon::parse($start_first_quarter_day)->lastOfQuarter();
            if( $dateRangeType == 'Today' ){
                $period = [Carbon::now()->subYear()->startOfDay(), Carbon::now()->subYear()->endOfDay()];
            }else if ( $dateRangeType == 'Yesterday' ){
                $period = [Carbon::yesterday()->subYear()->startOfDay(), Carbon::yesterday()->subYear()->endOfDay()];
            }else if ( $dateRangeType == 'Last 7 days' ){
                $period = [Carbon::now()->subYear()->subDays(7)->startOfDay(), Carbon::now()->subYear()->endOfDay()];
            }else if ( $dateRangeType == 'Last 90 days' ){
                $period = [Carbon::now()->subYear()->subDays(90)->startOfDay(), Carbon::now()->subYear()->endOfDay()];
            }else if ( $dateRangeType == 'Last Month' ){
                $period = [Carbon::now()->subYear()->subMonth()->startOfDay(), Carbon::now()->subYear()->endOfDay()];
            }else if ( $dateRangeType == 'Year to date' ){
                $period = [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfDay()];
            }else if ( $dateRangeType == '4th Quarter' ){
                $period = [Carbon::parse($start_first_quarter_day)->addMonths(9), Carbon::parse($end_first_quarter_day)->addMonths(9)];
            }else if ( $dateRangeType == '3rd Quarter' ){
                $period = [Carbon::parse($start_first_quarter_day)->addMonths(6), Carbon::parse($end_first_quarter_day)->addMonths(6)];
            }else if ( $dateRangeType == '2nd Quarter' ){
                $period = [Carbon::parse($start_first_quarter_day)->addMonths(3), Carbon::parse($end_first_quarter_day)->addMonths(3)];
            }else{
                $start = Carbon::now()->subYear()->startOfYear();
                $period = [$start_first_quarter_day, $end_first_quarter_day];
            }
        }
        $total_sale_stores = InfoOrder::whereBetween('created_at', $period)
                                    ->select(DB::raw('AVG(Total) as avg'), DB::raw('SUM(Total) as total'), 'TypeDelivery')
                                    ->groupBy('TypeDelivery')
                                    ->get();
        
        $stats = [];
        $statistique['mb'] = 0;
        $statistique['nh'] = 0;
        $statistique['sk'] = 0;
        $statistique['ch'] = 0;
        $total_sales_store = 0;
        $avg_store_order = 0;
        $avg_delivery_order = 0;
        foreach ($total_sale_stores as $key => $store) {
            if($store->TypeDelivery == 'MARYLEBONE') {
                $total_sales_store  +=  $store->total;
                $avg_store_order    +=  $store->avg;
                $statistique['mb'] = number_format($store->total, 2) ;
            }
            if($store->TypeDelivery == 'NOTTING HILL') {
                $total_sales_store  += $store->total;
                $avg_store_order    +=  $store->avg;
                $statistique['nh'] = number_format($store->total, 2);
            }
            if($store->TypeDelivery == 'SOUTH KEN') {
                $total_sales_store  += $store->total;
                $avg_store_order    +=  $store->avg;
                $statistique['sk'] = number_format($store->total, 2);
            }
            if($store->TypeDelivery == 'CHELSEA') {
                $total_sales_store  += $store->total;
                $avg_store_order    +=  $store->avg;
                $statistique['ch'] = number_format($store->total, 2);
            }
            if($store->TypeDelivery == 'DELIVERY') {
                $avg_delivery_order +=  $store->avg;
            }
        }
        // end total sales store
        // start total sales deliveries (B2B, B2C)
        $statistique['avg_store_order'] = number_format( $avg_store_order / 4, 2);
        $statistique['avg_delivery_order'] = number_format( $avg_delivery_order, 2);
        $total_sales_b2b = InfoOrder::join('infoCustomer', 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')
                                    ->whereBetween('infoOrder.created_at', $period)
                                    ->where('infoOrder.TypeDelivery', 'DELIVERY')
                                    ->where('infoCustomer.CustomerIDMaster', '!=', '')
                                    ->orWhere('infoCustomer.CustomerIDMasterAccount', '!=', '')
                                    ->orWhere('infoCustomer.IsMaster', 1)
                                    ->orWhere('infoCustomer.IsMasterAccount', 1)
                                    ->select(DB::raw('AVG(Total) as avg'), DB::raw('SUM(Total) as total'))
                                    ->first();
        $statistique['b2b'] = number_format( $total_sales_b2b->total, 2);
        $statistique['avg_b2b_order'] = number_format( $total_sales_b2b->avg, 2);
        $total_sales_b2c = InfoOrder::join('infoCustomer', 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')
                                    ->whereBetween('infoOrder.created_at', $period)
                                    ->where('infoOrder.TypeDelivery', 'DELIVERY')
                                    ->where('infoCustomer.CustomerIDMaster', '')
                                    ->orWhere('infoCustomer.CustomerIDMasterAccount', '')
                                    ->orWhere('infoCustomer.IsMaster', '!=',1)
                                    ->orWhere('infoCustomer.IsMasterAccount', '!=',1)
                                    ->select(DB::raw('AVG(Total) as avg'), DB::raw('SUM(Total) as total'))
                                    ->first();
        $statistique['b2c'] = number_format( $total_sales_b2c->total, 2);
        $statistique['avg_b2c_order'] = number_format( $total_sales_b2c->avg, 2);
        $statistique['avg_total_order'] = number_format( ($total_sales_b2c->avg + $total_sales_b2b->avg + ($avg_store_order / 4) + $avg_delivery_order ) / 4, 2);
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
                                    ->where('infoCustomer.CustomerIDMaster', '!=', '')
                                    ->orWhere('infoCustomer.CustomerIDMasterAccount', '!=', '')
                                    ->orWhere('infoCustomer.IsMaster', 1)
                                    ->orWhere('infoCustomer.IsMasterAccount', 1)
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
                                        ->where('CustomerIDMaster', '!=', '')
                                        ->orWhere('CustomerIDMasterAccount', '!=', '')
                                        ->orWhere('IsMaster', 1)
                                        ->orWhere('IsMasterAccount', 1)
                                        ->select(DB::raw('count(*) as count'))->first()->count;
        $b2c_new_signup = InfoCustomer::whereBetween('SignupDateOnline', $period)
                                        ->where('CustomerIDMaster', '')
                                        ->orWhere('CustomerIDMasterAccount', '')
                                        ->orWhere('IsMaster', '!=',1)
                                        ->orWhere('IsMasterAccount', '!=',1)
                                        ->select(DB::raw('count(*) as count'))->first()->count;
        $statistique['b2c_new_signup'] = $b2c_new_signup;
        $statistique['b2b_new_signup'] = $b2b_new_signup;
        $statistique['delivery_new_signup'] = $delivery_new_signup;
        $statistique['stores_new_signup'] = $stores_new_signup;
        // end new signup data


        $now = strtotime('now');
        $today = date('Y-m-d');
        $aday = 24 * 3600;

        $day_plus_2 = date("Y-m-d", $now + (2 * $aday));
        $tomorrow = date("Y-m-d", $now + $aday);

        $stats = Db::table('infoitems')
            ->select(DB::raw('count(distinct(infoitems.id_items)) as count,  SUM(infoitems.tailoring) AS count_tailoring'), 'infoitems.DepartmentName', DB::raw('infoitems.PromisedDate AS date_prod, DATE_FORMAT(infoitems.PromisedDate,"%W") AS day_prod'))
            ->whereDate('infoitems.PromisedDate', '>=', $today)
            ->whereDate('infoitems.PromisedDate', '<=', $day_plus_2)
            ->groupBy('infoitems.DepartmentName', DB::raw('infoitems.PromisedDate,DATE_FORMAT(infoitems.PromisedDate,"%W")'))
            ->get();

        $dept = Db::table('infoitems')->select('DepartmentName')->distinct()
            ->where('DepartmentName', '<>', 'NULL')
            ->orderByRaw("FIELD(DepartmentName, 'Garment Care','Shirts Hung','Shirts Folded','Shirts','Business','Laundry','Bedlinen','Household','Suede/Leather/Wax','Laundry Bag','Other')")
            ->get();

        $all_stats = [];
        $stats_today = [];
        $stats_plus_one_day = [];
        $stats_plus_two_day = [];

        $all_stats_tailor = [];
        $stats_today_tailor = [];
        $stats_plus_one_day_tailor = [];
        $stats_plus_two_day_tailor = [];


        if (!empty($dept)) {
            foreach ($dept as $k => $v) {
                $stats_today[$v->DepartmentName] = $k;
                $stats_plus_one_day[$v->DepartmentName] = 0;
                $stats_plus_two_day[$v->DepartmentName] = 0;
                $stats_today_tailor[$v->DepartmentName] = 0;
                $stats_plus_one_day_tailor[$v->DepartmentName] = 0;
                $stats_plus_two_day_tailor[$v->DepartmentName] = 0;
            }
        }


        if (!empty($stats)) {
            foreach ($stats as $id => $val) {
                if ($val->date_prod == $today) {
                    $stats_today[$val->DepartmentName] = (isset($stats_today[$val->DepartmentName]) ? $val->count : 0);
                    $stats_today_tailor[$val->DepartmentName] = (isset($stats_today[$val->DepartmentName]) ? $val->count_tailoring : 0);
                } elseif ($val->date_prod == $tomorrow) {
                    $stats_plus_one_day[$val->DepartmentName] = (isset($stats_plus_one_day[$val->DepartmentName]) ? $val->count : 0);
                    $stats_plus_two_day_tailor[$val->DepartmentName] = (isset($stats_plus_two_day_tailor[$val->DepartmentName]) ? $val->count_tailoring : 0);
                } elseif ($val->date_prod == $day_plus_2) {
                    $stats_plus_two_day[$val->DepartmentName] = (isset($stats_plus_two_day[$val->DepartmentName]) ? $val->count : 0);
                    $stats_plus_two_day_tailor[$val->DepartmentName] = (isset($stats_plus_two_day_tailor[$val->DepartmentName]) ? $val->count_tailoring : 0);
                }
            }
        }

        $stats_per_date = [];
        $stats_per_date_tailor = [];

        $stats_per_date[$today] = $stats_today;
        $stats_per_date[$tomorrow] = $stats_plus_one_day;
        $stats_per_date[$day_plus_2] = $stats_plus_two_day;

        $stats_per_date_tailor[$today] = $stats_today_tailor;
        $stats_per_date_tailor[$tomorrow] = $stats_plus_one_day_tailor;
        $stats_per_date_tailor[$day_plus_2] = $stats_plus_two_day_tailor;


        //SET stamp to less 2 days
        $stamp_2_day = $now + (2 * $aday);
        $i = $now;

        while ($i <= $stamp_2_day) {
            $date = date('Y-m-d', $i);
            $all_stats[date('l', $i)] = $stats_per_date[$date];
            $all_stats_tailor[date('l', $i)] = $stats_per_date_tailor[$date];
            $i += $aday; //For while loop to stop
        }


        $total_per_day = [];
        $total_per_day_tailor = [];

        foreach ($all_stats as $k => $v) {
            $total_per_day[$k] = 0;
            $num = 0;
            foreach ($v as $x) {
                $num += $x;
            }
            $total_per_day[$k] = $num;
        }

        foreach ($all_stats_tailor as $k => $v) {
            $total_per_day_tailor[$k] = 0;
            $num = 0;
            foreach ($v as $x) {
                $num += $x;
            }
            $total_per_day_tailor[$k] = $num;
        }


        $total_today = 0;
        $scale = 0;
        $col1 = 100 / 8;

        $nb_today = [];
        foreach ($stats_today as $k => $v) {
            $total_today += $v;
            $nb_today[] = $v;
        }

        rsort($nb_today);

        foreach ($nb_today as $k => $v) {
            if ($k > 2)
                unset($nb_today[$k]);
        }

        $top_3_today = [];

        foreach ($stats_today as $k => $v) {
            if (in_array($v, $nb_today)) {
                $top_3_today[$k] = $v;
            }
        }

        arsort($top_3_today);

        $colors = [
            '#565656', '#a0a0a0', '#b8b8b8'
        ];

        $top_3_colors = [];

        $i = 0;

        if (count($top_3_today) > 0) {
            foreach ($top_3_today as $k => $v) {
                if (isset($colors[$i])) {
                    $top_3_colors[$k] = $colors[$i];
                    $i += 1;
                }
            }
        }

        $max = $nb_today[0];
        if ($total_today > 0 && $max > 0) {
            $scale = (100 - $col1) / $total_today;
        }
        // total items by service

        $service_items = Infoitem::whereBetween('PromisedDate', $period)
                                ->select(DB::raw('SUM(priceTotal) as total'), 'DepartmentName')
                                ->groupBy('DepartmentName')
                                ->get();
        $statistique['service_items'] = $service_items;        
        $statistique['service_items_total'] = number_format($service_items->sum('total'), 2);        

        $statistique['dept'] = $dept;
        $statistique['stats_today'] = $stats_today;
        $statistique['all_stats'] = $all_stats;
        $statistique['all_stats_tailor'] = $all_stats_tailor;
        $statistique['total_today'] = $total_today;
        $statistique['scale'] = $scale;
        $statistique['total_per_day'] = $total_per_day;
        $statistique['total_per_day_tailor'] = $total_per_day_tailor;
        $statistique['top_3_today'] = $top_3_colors;
        return response()->json(
            [
                'statistique'=> $statistique
            ]
        );
    }
}
