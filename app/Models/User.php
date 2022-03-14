<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getRoles(){
        $roles_ids=[];
        $roles_ids[]=$this->role_id;
        $user_roles=DB::table('user_roles')->select('role_id')->where('user_id','=',$this->id)->get();
        foreach ($user_roles as $role){
            $roles_ids[]= $role->role_id;
        }
        $roles=DB::table('roles')->select('name')->whereIn('id',$roles_ids)->get();
        return $roles;
    }

    public function hasRoles(Array $roles){
        $user_roles=$this->getRoles();
        $user_roles2=[];
        foreach ($user_roles as $r){
            $user_roles2[]=$r->name;
        }
        foreach ($roles as $role)
            if(in_array($role,$user_roles2))
                return true;

            return false;
    }

    public function profiles(){
        return $this->belongsToMany(Profile::class,'profile_users','user_id','profile_id');
    }
}
