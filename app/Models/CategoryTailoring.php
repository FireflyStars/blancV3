<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryTailoring extends Model{
    protected $table="category_tailorings";
    use SoftDeletes;
	use HasFactory;
}
