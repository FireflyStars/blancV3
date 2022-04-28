<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TailoringTypeService extends Model{
    protected $table="tailoring_type_services";
    use SoftDeletes;
}
