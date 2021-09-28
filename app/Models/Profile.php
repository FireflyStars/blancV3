<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;



    public function authorizations(){
        return $this->belongsToMany(Authorization::class,'profile_authorizations','profile_id','authorization_id')->withPivot(['allow']);
    }
}
