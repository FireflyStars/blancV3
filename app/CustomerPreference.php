<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerPreference extends Model
{
    //
    protected $table = 'customerpreferences';
    protected $primaryKey = 'id';
    //protected $fillable = ['nextpost'];
    public $timestamps = false;

    public static function getCategories(){
        return [
            1=>'Preferences',
            2=>'Authorisations',
            3=>'Status & Tags'
        ];
    }
}
