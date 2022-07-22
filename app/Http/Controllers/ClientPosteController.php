<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use TCG\Voyager\Voyager;

//use TCG\Voyager\Facades\Voyager;

class ClientPosteController extends \TCG\Voyager\Http\Controllers\Controller
{

    public function index(){
        $voyager = new Voyager();
        return $voyager->view('vendor.voyager.clientposte.index');
    }

}
