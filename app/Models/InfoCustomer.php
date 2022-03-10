<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InfoCustomer extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'infoCustomer';

    /**
     * allowing mass assignable for all fields.
     *
     * @var array
     */
    protected $guarded = [];


    public static function getPreviousOrders($customerid){
        return DB::table('infoOrder')->where('CustomerID',$customerid)->get()->toArray();
    }

}
