<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    //
    public static function getScreens($group_id){
        return DB::table('screens')->where('group_id',$group_id)->get();
    }
}
