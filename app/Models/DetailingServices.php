<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailingServices extends Model
{
    public static function getMainServices(){
        return [
           1=>'Cleaning',
           2=>'Tailoring & Alterations',
           3=>'Restoration',
           4=>'Storage',
           5=>'Sale'
        ];
    }

    public static function getTailoringServicesByTypeitem($typeitem_id){
        $group_services = [];

        $services = DB::table('tailoring_services')
            ->select('tailoring_services.*','tailoring_type_services.name as group_name')
            ->join('typeitem','tailoring_services.category_id','typeitem.tailoring_category_id')
            ->join('tailoring_type_services','tailoring_services.type_service_id','tailoring_type_services.id')
            ->where('typeitem.id',$typeitem_id)
            ->get();

        if(count($services) > 0){
            foreach($services as $k=>$v){
                $group_services[$v->group_name][] = $v;
            }
        }

        return $group_services;
    }

}
