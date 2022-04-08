<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    //

    public static function getHolidays(){
        $holidays_dates=Holiday::where('date','>=',date('Y-m-d',strtotime('Yesterday')))->get('date');
        $holidays=[];
        foreach ($holidays_dates as $dates){
            $holidays[]=$dates->date;
        }
        sort($holidays);
        return $holidays;
    }
}
