<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
