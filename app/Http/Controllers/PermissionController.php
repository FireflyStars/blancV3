<?php

namespace App\Http\Controllers;

use App\Models\Authorization;
use App\Models\AuthorizationGroup;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    //

    public function getPermissions(Request $request){

        $users=User::all();
        $profileUsers=[];
        foreach ($users as $user){
            if(!$user->hasRoles(['admin'])){
                $profileUsers[]=array(
                    'id'=>$user->id,
                    'name'=>$user->name,
                    'email'=>$user->email
                );
            }
        }

        return response()->json([
            'profiles'=>Profile::all(),
            'authorizations'=>Authorization::all(),
            'authorization_groups'=>AuthorizationGroup::all(),
            'profile_authorizations'=>DB::table('profile_authorizations')->get(),
            'users'=>$profileUsers,
            'user_profiles'=>DB::table('profile_users')->get()


        ]);
    }


    public function setPermission(Request $request)
    {
        $authorization_id=$request->post('authorization_id');
        $profile_id=$request->post('profile_id');
        $allow=$request->post('allow');
       $profile_authorization=DB::table('profile_authorizations')->where('authorization_id','=',$authorization_id)->where('profile_id','=',$profile_id)->first();
       if($profile_authorization==null){
           DB::table('profile_authorizations')->insert([
               'authorization_id'=>$authorization_id,
               'profile_id'=>$profile_id,
               'allow'=>1,
                'created_at'=>date('Y-m-d H:i:s')
           ]);
       }else{
           DB::table('profile_authorizations')->where('authorization_id','=',$authorization_id)->where('profile_id','=',$profile_id)->update([
               'allow'=>$allow,
               'updated_at'=>date('Y-m-d H:i:s')
           ]);
       }
       return response()->json(['ok'=>1]);
    }

    public function setProfile(Request $request)
    {
        $user_id=$request->post('user_id');
        $profile_id=$request->post('profile_id');

        $profile_user=DB::table('profile_users')->where('user_id','=',$user_id)->where('profile_id','=',$profile_id)->first();
        if($profile_user==null){
            DB::table('profile_users')->insert([
                'user_id'=>$user_id,
                'profile_id'=>$profile_id,
                'created_at'=>date('Y-m-d H:i:s')
            ]);
        }else{
            DB::table('profile_users')->where('user_id','=',$user_id)->where('profile_id','=',$profile_id)->delete();
        }
        return response()->json(['ok'=>1]);
    }
}
