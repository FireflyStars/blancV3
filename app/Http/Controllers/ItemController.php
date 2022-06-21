<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller{

    public function getItemPicto(Request $request){
        $item_type = $request->post('item_type');

        $zones = [];

        $zones_text = $request->post('zones');

        $all_zones = $request->post('all_zones');
        $face = $request->post('face');

        $all_desc = [];

        if ($all_zones == 1) {
            $zones = Db::table('itemzones')
                ->where('zone_type', $item_type)
                ->get();
        }

        if ($all_zones == 1 && !empty($zones)) {

            foreach ($zones as $k => $v) {
                $desc = str_replace(' ', '_', $v->description);
                if ($desc != '' && !in_array($desc, $all_desc))
                    array_push($all_desc, $desc);
            }
        }

        $svg = Db::table('itemzones')
            ->where('zone_type', $item_type)
            //->where('face','front')
            ->orderBy('id', 'asc')
            ->get();

        $svg_details = Db::table('itemsvg')->where('name', $item_type)->first();

        $arr_config = [];
        if($svg_details){
            $arr_config = explode(" ",$svg_details->viewpoint);

            if($face=='front' && isset($arr_config[2])){
                $width = $arr_config[2]/2-10;
                $arr_config[2] = $width;

                $svg_details->viewpoint = implode(" ",$arr_config);

            }

        }

        if ($svg_details) {
            $type_item = str_replace('_', ' ', $item_type);
            $svg_details->type_item = ucfirst($type_item);
        }

        if(!empty($svg)){
            foreach($svg as $k=>$v){
                $active = 0;

                $svg[$k]->description = str_replace(' ','_',$v->description);
                $svg[$k]->active = $active;
                $svg[$k]->line_stroke = $v->stroke;
            }
        }

        $svg_filtered = [];
        $has_desc = [];
        $no_desc = [];


        foreach($svg as $k=>$v){
            if($v->description !='' && !in_array($v->description,['FRONT','BACK'])){
                $has_desc[] = $v;
            }else{
                $no_desc[] = $v;
            }
        }

        $svg_filtered = array_merge($no_desc,$has_desc);



        return response()->json([
            'svg'=>$svg_filtered,
            'svg_details'=>$svg_details,
            'arr_config'=>$arr_config,
        ]);

    }

    public function getPictoNames(){
        $details = DB::table('itemsvg')->get();

        return response()->json([
            'details'=>$details,
        ]);

    }

    public function updateZoneLabelPos(Request $request){
        $post = $request->all();
        $id = $post['id'];
        unset($post['id']);

        $updated = DB::table('itemzones')->where('id',$id)->update($post);

        return response()->json([
            'post'=>$post,
            'id'=>$id,
            'updated'=>$updated
        ]);
    }
}

?>
