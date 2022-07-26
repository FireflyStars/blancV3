<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryTailoringController extends Controller{
    public function index(Request $request){
        return view('categorytailoring.index');
    }
}
