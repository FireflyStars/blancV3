<?php

namespace App\Models;

use App\Models\Tax;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Colour extends Model
{
	 protected $table="colours";
    use HasFactory;
    use SoftDeletes;


}
