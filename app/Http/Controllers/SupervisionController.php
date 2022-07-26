<?php

namespace App\Http\Controllers;

use App\Conveyor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SupervisionController extends Controller
{
    //
    public function index(Request $request){

        $data=[
            'stores'=>Conveyor::getStoreConveyorNames(),
        ];
        return view('supervision.index',$data);
    }

    public function supervisionData(Request $request){
        //$numitems=DB::table('conveyor_op_in')->select('id')->where('sent','=',0)->get()->count();

       /*
        return response()->json(array(
            'MAPEXC_IN'=>count(Storage::disk('at_mapexc')->files('/IN')),
            'MAPEXC_OUT'=>count(Storage::disk('at_mapexc')->files('/OUT')),
            'BAMEXC_IN'=>count(Storage::disk('at_bamexc')->files('/IN')),
            'BAMEXC_OUT'=>count(Storage::disk('at_bamexc')->files('/OUT')),
            'NUM_ITEM'=>$numitems,
            'conveyor_file_log'=>DB::table('conveyor_file_log')->orderBy('id','desc')->take(20)->get()
        ));
        */

        $store_codes = Conveyor::getStoreWithOtherFormats();
        $store_codes[] = "AT";

        $map_out_files = glob(storage_path('AT/mapexc/OUT/*.*'));
        $map_out_count['AT'] = count($map_out_files);
        $map_in_files = glob(storage_path('AT/mapexc/IN/*.*'));
        $map_in_count['AT'] = count($map_in_files);

        $bam_out_count = [];
        $bam_in_count = [];

        foreach($store_codes as $k=>$v){
            $bam_out_files = glob(storage_path($v.'/bamexc/OUT/*.*'));
            $bam_out_count[$v] = count($bam_out_files);
        }


        foreach($store_codes as $k=>$v){
            $bam_in_files = glob(storage_path($v.'/bamexc/IN/*.*'));
            $bam_in_count[$v] = count($bam_in_files);
        }

        $in_not_sent = [];

        $all_in_not_sent = DB::table('conveyor_op_in')->where('sent',0)->get();

        if(count($all_in_not_sent)){
            foreach($all_in_not_sent as $k=>$v){
                $in_not_sent[$v->ConvStoreCode][] = $v;
            }
        }

        $store_in_not_sent = [];

        foreach($store_codes as $k=>$v){
            $store_in_not_sent[$v] = 0;
        }

        foreach($store_in_not_sent as $k=>$v){
            $store_in_not_sent[$k] = (isset($in_not_sent[$k])?count($in_not_sent[$k]):0);
        }


        return response()->json([
            'MAPEXC_IN'=>$map_in_count['AT'],
            'MAPEXC_OUT'=>$map_out_count['AT'],
            'BAMEXC_IN'=>$bam_in_count,
            'BAMEXC_OUT'=>$bam_out_count,
            'NUM_ITEM'=>$store_in_not_sent,
            'conveyor_file_log'=>DB::table('conveyor_file_log')->orderBy('id','desc')->take(20)->get()
        ]);




    }
}
