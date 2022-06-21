<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class StripeTerminalController extends Controller{
    public function index(){
        return view('stripeterminal.index');
    }
}


?>
