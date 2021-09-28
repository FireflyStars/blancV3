<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authorization extends Model
{
    use HasFactory;

    public function group(){
        return $this->hasOne(AuthorizationGroup::class,'id','authorization_group_id');
    }
}
