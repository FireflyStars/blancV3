<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryTailoring extends Model{
    protected $table="category_tailorings";
    use SoftDeletes;
}
