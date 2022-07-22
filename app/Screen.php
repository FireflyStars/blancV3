<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Screen extends Model
{
    //
    public static function getGroupedScreens(){
        $gp = DB::table('screens')
            ->select('screens.*')
            ->join('groups','screens.group_id','groups.id')
            ->whereNull('groups.deleted_at')
            ->orderBy('screens.group_id','ASC')
            ->get();

        $grouped_screens = [];

        if(count($gp) > 0){
            foreach($gp as $k=>$v){
                $grouped_screens[$v->group_id][] = [
                    'screen_id'=>$v->id,
                    'screen_name'=>$v->name,
                ];
            }
        }

        return $grouped_screens;
    }

    public static function getCountryPhoneCodes(){
        $file = storage_path('uploads/country_codes.csv');

        $data = [];
        $codes = [];
        $codes_with_commas = [];
        $all_codes = [];

        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($row = fgetcsv($handle, 0, ",")) !== FALSE) {
                $data[] = $row;
            }
            fclose($handle);
        }
        unset($data[0]);

        foreach($data as $k=>$v){

            if(trim($v[1]) !='' && !in_array(trim($v[1]),$codes)){
                array_push($codes,trim($v[1]));
            }
        }

        foreach($codes as $k=>$v){

            if(strpos($v,",")){
                $arr = explode(",",$v);
                foreach($arr as $i=>$x){
                    $codes_with_commas[] = $x;
                }
            }else{
                $all_codes[] = $v;
            }
        }

        $all_codes = array_merge($all_codes,$codes_with_commas);

        sort($all_codes);

        return $all_codes;
    }
}
