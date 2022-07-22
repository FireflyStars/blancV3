<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    //
    private $rules = array(
        'StartDate' => 'required|date',
        'StartDate'  => 'required|date|after_or_equal:StartDate',
        // .. more rules here ..
    );


}
