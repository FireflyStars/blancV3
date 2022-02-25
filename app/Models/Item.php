<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    
    public static function getTypeService(){
        return array(
          0=>'STANDARD',
          1=>'EXPRESS',
          2=>'NO RUSH',
          3=>'FREE EXPRESS',
		  4=>'RUSH / MUST BE READY',
		  5=>'RUSH - 24h',
		  6=>'RUSH - 48h',
        );
    }    
}
