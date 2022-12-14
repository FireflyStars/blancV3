<?php

namespace App\Http\Controllers\Voyager;

use App\Http\Controllers\Controller;
use Hamcrest\Arrays\IsArray;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoyagerPostcodesController extends Controller
{
    //
    public static  $DAYS=['MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY'];

    public function index(Request $request){
        //$this->authorize('read_configure-postcodes');
        $postcodes = DB::table('tranchepostcode')->orderBy('Postcode')->get();
        $preroutes =[];
        $pre_routes = DB::table('preroute')->select(['id','name','number','day'])->whereNull('deleted_at')->get();

        foreach($pre_routes as $preroute){
            $preroutes[$preroute->day][]=$preroute;
        }

        foreach ($postcodes as $postcode) {
            $group_postcodes[$postcode->Postcode][$postcode->day] = (array)json_decode($postcode->preroute);

        }
        foreach ($postcodes as $postcode) {
            $group_postcodes2[$postcode->Postcode][$postcode->day] = json_decode($postcode->tranche);

        }
        $DAYS=self::$DAYS;
        return view('admin.postcodes',compact('group_postcodes','DAYS','preroutes','group_postcodes2'));
    }

    public function postprocess(Request $request)
    {

        if (isset($_POST['newpostcodes'])) {
            //$this->authorize('add_configure-postcodes');
            $postcodes = $request->get('newpostcodes');
            $postcodes = explode(',', $postcodes);
            $existing_postcodes = [];
            $postcode_count = 0;
            if (!empty($postcodes))
                foreach ($postcodes as $postcode) {
                    $existing_postcode = DB::table('tranchepostcode')->where('Postcode', $postcode)->value('Postcode');
                    if ($existing_postcode == $postcode) {
                        $existing_postcodes[] = $postcode;
                        continue;
                    }
                    foreach (self::$DAYS as $d)
                        DB::table('tranchepostcode')->insert(array(
                            'Postcode' => $postcode,
                            'day' => $d,
                            'tranche' => '[]'
                        ));
                    $postcode_count++;
                }
            return redirect()->route('configure-postcodes', ['existing_postcodes' => implode(',', $existing_postcodes), 'postcode_count' => $postcode_count]);
        }



        if (isset($_POST['bulkdelete'])) {
            //$this->authorize('delete_configure-postcodes');
            if (!isset($_POST['sel_postcode'])) {

                return redirect()->route('configure-postcodes', ['delete' => 0]);
            } else {
                $selPostcodes = $request->post('sel_postcode');

                foreach ($selPostcodes as $postcode) {
                    DB::table('tranchepostcode')->where('Postcode', $postcode)->delete();
                }
                return redirect()->route('configure-postcodes', ['delete' => 1]);
            }
        }

        if (isset($_POST['bulksave'])) {
            $t_=[];
            $postcodes = [];
            foreach($_POST as  $postkey=>$postvalue){
                if(substr($postkey,0,1)=='#'&&$postvalue!=""){
                    $t_[]= substr($postkey,1);
                }
            }

            foreach ($t_ as $t) {
                $tranche = explode('_', $t);
                $postcodes[$tranche['0']][$tranche[1]] [$tranche[2]] =$_POST['#'.$t];
            }
      
            $existingPostcodes=DB::table('tranchepostcode')->select(['Postcode'])->distinct()->get();
            foreach($existingPostcodes as $pcode){
                if(!key_exists($pcode->Postcode,$postcodes)){
                   DB::table('tranchepostcode')->where('Postcode', $pcode->Postcode)->update(['tranche' => '[]','preroute'=>'']);
                }
            }
       


            foreach ($postcodes as $postcode => $days) {

                foreach ($days as $day => $tranche_preroute) {
                    $tranche=[];
                    foreach($tranche_preroute as $_tranche=>$preroute)
                    $tranche[]=$_tranche;

                    $affected = DB::table('tranchepostcode')
                        ->where('Postcode', $postcode)
                        ->where('day', $day)
                        ->update(['tranche' => json_encode($tranche),'preroute'=>json_encode($tranche_preroute)]);

                    if (!$affected) {
                        $existing_postcode = DB::table('tranchepostcode')->where('Postcode', $postcode)->where('day', $day)->value('Postcode');
                        if ($existing_postcode == null) {
                            DB::table('tranchepostcode')->insert(array(
                                'Postcode' => $postcode,
                                'day' => $day,
                                'tranche' => json_encode($tranche),
                                'preroute'=>json_encode($tranche_preroute)
                            ));
                        }
                    }
                }
                foreach (['MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY'] as $dayname)
                    if(!key_exists($dayname,$days))
                        DB::table('tranchepostcode') ->where('Postcode', $postcode)
                            ->where('day', $dayname)->delete();
            }
            return redirect()->route('configure-postcodes', ['updateok' => 1]);
        }
    }

    protected function guard()
    {
        return Auth::guard(app('VoyagerGuard'));
    }
}
