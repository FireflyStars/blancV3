<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class ShippingController extends Controller{

    public function getPartnerList(Request $request){
        $partners_default = [];

        $obj = new stdClass();
        $obj->value = '';
        $obj->display = '--';
        $partners_default[] = $obj;

        $partners = DB::table('shipping_partner')->select('id as value','name as display')
            ->get()->toArray();


        return response()->json([
            'partners'=>array_merge($partners_default,$partners),
        ]);

    }


}


?>
