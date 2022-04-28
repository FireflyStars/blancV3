<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TailoringService extends Model{
    protected $table="tailoring_services";
    use SoftDeletes;
}
