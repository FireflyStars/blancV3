<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return response()->json(
            ['user'=>Auth::user()]
        );

    }

    public function logout(Request $request){
        Auth::logout();
        return response()->json(
            ['ok'=>1]
        );
    }
}
