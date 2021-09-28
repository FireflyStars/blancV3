<?php

namespace App\Http\Controllers;

use App\Models\Authorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        Auth::logout();
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
        }
        $user=Auth::user();
        $roles = [];

        if($user) {
            $roles = $user->getRoles();
        }
        $permissions=[];
        foreach ($user->profiles as $profile) {
            foreach ($profile->authorizations as $authorization) {
                $_authorization = Authorization::find($authorization->pivot->authorization_id);
                $permissions[$profile->name][] = array(
                    'name' => $_authorization->description,
                    'allow' => $authorization->pivot->allow
                );
            }
        }

        return response()->json(
            ['user'=>$user,'roles'=>$roles,'profile_permissions'=>$permissions]
        );

    }

    public function logout(Request $request){
        Auth::logout();
        return response()->json(
            ['ok'=>1]
        );
    }
}
