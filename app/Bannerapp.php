<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bannerapp extends Model
{

    protected $table = 'bannerapp';
    use SoftDeletes;

}
