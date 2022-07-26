<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeItem extends Model{
    protected $table="typeitem";
	
	use HasFactory;
    use SoftDeletes;

}
