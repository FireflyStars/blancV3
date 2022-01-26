<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function getStatistics(Request $request)
    {
        $customFilter=$request->post('customFilter');
        $startDate=$request->post('startDate');
        $endDate=$request->post('endDate');
        $dateRangeType=$request->post('dateRangeType');

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

        echo json_encode(
            [
                'dept' => $dept,
                'stats_today' => $stats_today,
                'all_stats' => $all_stats,
                'all_stats_tailor' => $all_stats_tailor,
                'total_today' => $total_today,
                'scale' => $scale,
                'total_per_day' => $total_per_day,
                'total_per_day_tailor' => $total_per_day_tailor,
                'top_3_today' => $top_3_colors,
            ]
        );
    }
}
