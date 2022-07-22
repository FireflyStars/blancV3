<?php

namespace App\Http\Controllers;

use App\CustomerPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerPreferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $cp = DB::table('customerpreferences')
            ->orderBy('sequence','ASC')
            ->get();

        $data = [];

        $categories = CustomerPreference::getCategories();

        if(count($cp) > 0){
            foreach($cp as $k=>$v){
                $v->category_name =  $categories[$v->category_id];
                $opt = [];

                if($v->preference_type=='radio'){
                    $opt = explode("\r\n",$v->dropdown_values);

                    $v->dropdown_values = implode(", ",$opt);
                }

                $data[] = (array) $v;
            }
        }

        return view('customerpreference.index',[
            'data'=>$data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CustomerPreference  $customerPreference
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerPreference $customerPreference,Request $request)
    {
        //
        $message = "test";
        //Toastr::warning($message,null,[]);

        $cp = false;

        $id_cp = $request->get('id');

        if(isset($id_cp)){
            $cp = CustomerPreference::find($id_cp);
        }


        return view('customerpreference.view',[
            'cp'=>$cp,
            'categories'=>CustomerPreference::getCategories(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomerPreference  $customerPreference
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerPreference $customerPreference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CustomerPreference  $customerPreference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerPreference $customerPreference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerPreference  $customerPreference
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerPreference $customerPreference)
    {
        //
    }


    public function upsert(Request $request){
        $POST = $request->all();

        if(!is_null($POST['id_cp'])){
            $cust_pref = CustomerPreference::find($POST['id_cp']);
        }else {
            $cust_pref = new CustomerPreference();
        }

        $cust_pref->title = $POST['pref_title'];
        $cust_pref->preference_type = $POST['type_pref'];
        $cust_pref->category_id = $POST['cat_pref'];
        $cust_pref->description = (!is_null($POST['desc_pref'])?$POST['desc_pref']:'');
        $cust_pref->dropdown_values = (!is_null($POST['dropdown_values'])?$POST['dropdown_values']:'');
        $cust_pref->no_title = (isset($POST['no_title']) && $POST['no_title']=='on'?1:0);
        $cust_pref->save();

        if(is_null($POST['id_cp'])){
            $cust_pref->sequence = $cust_pref->id;
            $cust_pref->save();
        }

        return redirect('/admin/customer-preference');
    }

    public function updateSequence(Request $request){
        $obj = json_decode($request->post('arr'));
        $arr = (array) $obj;

        $ids_cp = [];

        foreach($arr as $k=>$v){
            $ids_cp[] = $k;

            //DB::table('customerpreferences')->where('id',$k)->update(['sequence'=>$v]);
        }


        $cp = CustomerPreference::find($ids_cp[0]);


        //DB::table('customerpreferences')->where('category_id',$cp->category_id)->update(['sequence'=>0]);

        foreach($arr as $k=>$v){
            DB::table('customerpreferences')->whereIn('id',$ids_cp)->where('id',$k)->update(['sequence'=>$v]);
        }


        //$other_cps = DB::table('customer')


        echo json_encode([
            'arr'=>$arr,
            'cp'=>$ids_cp,
            //'post'=>$request->all(),
        ]);
    }

    public function setPrefDeleted(Request $request){
        $id_cust_pref = $request->get('id');

        $cp = CustomerPreference::find($id_cust_pref);
        $deleted = 1;
        if($cp->deleted==1){
            $deleted = 0;
        }

        $cp->deleted = $deleted;
        $cp->save();

        return redirect('/admin/customer-preference');

    }
}
