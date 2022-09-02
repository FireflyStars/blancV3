<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TailoringService extends Model{
    protected $table="tailoring_services";
    use SoftDeletes;
	use HasFactory;
}
