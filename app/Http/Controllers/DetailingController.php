<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\DetailingServices;
use stdClass;

class DetailingController extends Controller
{
    public function getDetailingList(Request $request)
    {
        $order_id = $request->post('order_id');
        $user = Auth::user();

        $customer = DB::table('infoOrder')
            ->select('infoCustomer.*')
            ->join('infoCustomer', 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')
            ->where('infoOrder.id', $order_id)
            ->get();

        $customer = (array) $customer->first();

        $detailingitemlist = DB::table('detailingitem')
            ->select('typeitem.name as typeitem_name', 'detailingitem.etape', 'detailingitem.pricecleaning as price','detailingitem.id as item_number','detailingitem.status','detailingitem.tracking')
            ->join('typeitem', 'typeitem.id', 'detailingitem.typeitem_id')
            //->join('infoitems', 'infoitems.ItemTrackingKey', '=', 'detailingitem.item_id')
            //->join('infoInvoice', 'infoInvoice.SubOrderID', '=', 'infoitems.SubOrderID')
            ->where('detailingitem.order_id', '=', $order_id)
            //->orderBy('infoitems.NoBag')
            //->orderBy('infoInvoice.NumInvoice')
            ->orderBy('detailingitem.id','ASC')
            ->get();


        /*
        'infoitems.NoBag', 'infoInvoice.NumInvoice as sub_order', 'infoitems.ItemTrackingKey as item_number'
        */

        if(count($detailingitemlist) > 0){
            foreach($detailingitemlist as $k=>$v){
                $detailingitemlist[$k]->sub_order = "";
                $detailingitemlist[$k]->NoBag = 0;
            }
        }

        echo json_encode(
            [
                'user' => $user,
                'detailing_list' => $detailingitemlist,
                'customer' => $customer
            ]
        );
    }

    public function initDetailing(Request $request)
    {
        $search = $request->post('search');
        $order_id = $request->post('order_id');
        $item_id = $request->post('item_id');
        $tracking = $request->post('tracking');
        $detailingitem_id = $request->post('detailingitem_id');
        $user = Auth::user();

        $detailingitem = DB::table('detailingitem')
            ->where('order_id', '=', $order_id)
            ->where('id', '=', $item_id)
            ->get();

        if (count($detailingitem) == 0) {
            $detailingitem = (array) $detailingitem->first();
            $customer = DB::table('infoOrder')
                ->select('infoCustomer.id')
                ->join('infoCustomer', 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')
                ->where('infoOrder.id', $order_id)
                ->get();
            if (count($customer) > 0) {
                $customer = (array) $customer->first();

                $detailing_arr = [
                    'order_id' => $order_id,
                    'item_id' => $item_id,
                    'customer_id' => $customer['id'],
                    'status' => 'In Process',
                    'etape' => 1,
                    'coeftailoringbrand' => 0,
                    'coefcleaningbrand' => 0,
                    'coeftailoringfabric' => 0,
                    'coefcleaningfabric' => 0,
                    'coefcleaningcomplexities' => 0,
                    'coeftailoringcomplexities' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if(isset($tracking)){
                    $detailing_arr['tracking'] = $tracking;
                }

                $detailingitem_id = DB::table('detailingitem')->insertGetId(
                    $detailing_arr
                );
            } else {
                return response()->json(['message' => 'Order not found.']);
            }
        }

        $searched_item = null;

        if ($search) {
            $searched_item = DB::table('typeitem')
                ->where('typeitem.name', 'LIKE', '%' . $search . '%')
                ->first();
            DB::table('detailingitem')->where('id', $detailingitem_id)->update([
                'department_id' => $searched_item->department_id,
                'category_id' => $searched_item->category_id,
                'typeitem_id' => $searched_item->id,
                'size_id' => null,
                'etape' => 2,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }


        $detailingitem = DB::table('detailingitem')
            ->where('order_id', '=', $order_id)
            ->where('id', '=', $item_id)
            ->get();

        $detailingitem = (array) $detailingitem->first();

        $item_description = $this->getItemDescription($detailingitem);

        $detailing_data = [];
        $cust_cleaning_services = [];

        if(!empty($detailingitem)){
            $detailing_data = $this->getDetailingData($detailingitem['department_id'], $detailingitem['typeitem_id']);
            $cust_cleaning_services = DetailingController::getCustCleaningServices($detailingitem);

        }

        $tailoring_services = [];

        if(isset($detailingitem['typeitem_id'])){
            $tailoring_services = DetailingServices::getTailoringServicesByTypeitem($detailingitem['typeitem_id']);

            $cust_tailoring_services = [];
            if(!is_null($detailingitem['tailoring_services'])){
             $cust_tailoring_services = @json_decode($detailingitem['tailoring_services']);
            }

            foreach($tailoring_services as $k=>$v){
                foreach($v as $i=>$x){
                   $tailoring_services[$k][$i]->cust_selected = (!is_null($cust_tailoring_services) && in_array($x->id,$cust_tailoring_services)?1:0);
                }
            }

        }

        echo json_encode(
            [
                'user' => $user,
                'detailingitem' => $detailingitem,
                'search' => $search,
                'searched_item' => $searched_item,
                'item_description' => $item_description,
                'detailing_data' => $detailing_data,
                'post'=>$request->all(),
                'detailingitem_id'=>$detailingitem_id,
                'cust_cleaning_services'=>$cust_cleaning_services,
                'main_services'=>DetailingServices::getMainServices(),
                'tailoring_services'=>$tailoring_services,
            ]
        );
    }

    public static function getCustCleaningServices($detailingitem){


        $cust_cleaning_services = DetailingController::getCustomerServices($detailingitem['customer_id']);

            if(!is_null($detailingitem['cleaning_services'])){
                $sel_cleaning_services = @json_decode($detailingitem['cleaning_services']);

                if(!empty($sel_cleaning_services)){
                    foreach($cust_cleaning_services as $k=>$v){
                        foreach($v as $i=>$x){
                            $v[$i]->selected_default = 0;
                            $v[$i]->cust_selected = (in_array($x->id,$sel_cleaning_services)?1:0);
                        }
                    }
                    $cust_cleaning_services[$k] = $v;
                }

            }

        return $cust_cleaning_services;
    }

    public function updateItemDetailing(Request $request)
    {
        $detailingitem_id = $request->post('detailingitem_id');
        $dept_id = $request->post('dept_id');
        $category_id = $request->post('category_id');
        $typeitem_id = $request->post('typeitem_id');
        $size_id = $request->post('size_id');
        $brand_id = $request->post('brand_id');
        $fabrics_id = $request->post('fabrics_id');
        $color_id = $request->post('color_id');
        $pattern_id = $request->post('pattern_id');
        $condition_id = $request->post('condition_id');
        $complexities_id = $request->post('complexities_id');
        $status = $request->post('status');
        $step = $request->post('step');
        $stains = $request->post('stains');
        $damages = $request->post('damages');
        $brand_name = $request->post('brand_name');
        $cleaning_services = $request->post('cleaning_services');
        $cleaning_price_type = $request->post('cleaning_price_type');
        $cleaning_prices = $request->post('cleaning_prices');
        $tailoring_services = $request->post('tailoring_services');
        $tailoring_price = $request->post('tailoring_price');
        $tailoring_price_type = $request->post('tailoring_price_type');

        if (isset($dept_id)) {
            $detailingitem = DB::table('detailingitem')->where('id', '=', $detailingitem_id)->get();
            $detailingitem = (array) $detailingitem->first();
            if( $detailingitem['department_id']!= $dept_id){
                DB::table('detailingitem')->where('id', $detailingitem_id)->update([
                    'department_id' => $dept_id,
                    'category_id' => null,
                    'typeitem_id' => null,
                    'etape' => 2,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }else{
                DB::table('detailingitem')->where('id', $detailingitem_id)->update([
                    'etape' => 2,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        if (isset($category_id) && isset($typeitem_id)||isset($category_id) && $typeitem_id == null) {
            $typeitem = DB::table('typeitem')->where('typeitem.id', $typeitem_id)->first();
            DB::table('detailingitem')->where('id', $detailingitem_id)->update([
                'category_id' => $category_id,
                'typeitem_id' => $typeitem_id,
                'size_id' => null,
                'pricecleaning' => isset($typeitem)?$typeitem->pricecleaning:0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        if (isset($size_id)) {
            DB::table('detailingitem')->where('id', $detailingitem_id)->update([
                'size_id' => $size_id,
                'etape' => 6,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        if (isset($brand_name)) {
            $brand_id = $this->addNewBrand($brand_name);
        }
        if (isset($brand_id)) {
            $brand = DB::table('brands')->where('id', $brand_id)->first();
            DB::table('detailingitem')->where('id', $detailingitem_id)->update([
                'brand_id' => $brand_id,
                'coefcleaningbrand' => $brand->coefcleaning,
                'coeftailoringbrand' => $brand->coeftailoring,
                'etape' => 3,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        if (isset($fabrics_id)) {
            $fabrics = DB::table('fabrics')->whereIn('id', json_decode($fabrics_id))->get();
            $coefcleaningfabrics = 0;
            $coeftailoringfabrics = 0;
            foreach ($fabrics as $fab) {
                $coefcleaningfabrics += $fab->coefcleaning;
                $coeftailoringfabrics += $fab->coeftailoring;
            }
            DB::table('detailingitem')->where('id', $detailingitem_id)->update([
                'fabric_id' => $fabrics_id,
                'coefcleaningfabric' => $coefcleaningfabrics,
                'coeftailoringfabric' => $coeftailoringfabrics,
                'etape' => 5,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        if (isset($color_id)) {
            DB::table('detailingitem')->where('id', $detailingitem_id)->update([
                'color_id' => $color_id,
                'etape' => 4,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        if (isset($pattern_id)) {
            DB::table('detailingitem')->where('id', $detailingitem_id)->update([
                'pattern_id' => $pattern_id,
                'etape' => 7,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        if (isset($condition_id)) {
            DB::table('detailingitem')->where('id', $detailingitem_id)->update([
                'condition_id' => $condition_id,
                'etape' => 8,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        if (isset($complexities_id)) {
            $complexities = DB::table('complexities')->whereIn('id', json_decode($complexities_id))->get();
            $coefcleaningcomplexities = 0;
            $coeftailoringcomplexities = 0;
            foreach ($complexities as $comp) {
                $coefcleaningcomplexities += $comp->coefcleaning;
                $coeftailoringcomplexities += $comp->coeftailoring;
            }
            DB::table('detailingitem')->where('id', $detailingitem_id)->update([
                'complexities_id' => $complexities_id,
                'coefcleaningcomplexities' => $coefcleaningcomplexities,
                'coeftailoringcomplexities' => $coeftailoringcomplexities,
                'etape' => 9,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }



        $detailingitem = DB::table('detailingitem')->where('id', '=', $detailingitem_id)->get();
        $detailingitem = (array) $detailingitem->first();

        if ($detailingitem['typeitem_id']) {
            $item_base_price = 0;
            $item_base_price = DB::table('detailingitem')
                ->join('typeitem', 'detailingitem.typeitem_id', 'typeitem.id')
                ->where('detailingitem.id', $detailingitem_id)
                ->value('typeitem.pricecleaning');
            $pricecleaning = $item_base_price
                + ($item_base_price * $detailingitem['coefcleaningbrand'])
                + ($item_base_price * $detailingitem['coefcleaningfabric'])
                + ($item_base_price * $detailingitem['coefcleaningcomplexities']);
            $detailingitem = DB::table('detailingitem')->where('id', '=', $detailingitem_id)->get();
            DB::table('detailingitem')->where('id', $detailingitem_id)->update([
                'pricecleaning' => $pricecleaning,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        if (isset($status)) {
            DB::table('detailingitem')->where('id', $detailingitem_id)->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        }
        if (isset($stains)) {
            DB::table('detailingitem')->where('id', $detailingitem_id)->update(['stains' => $stains, 'updated_at' => date('Y-m-d H:i:s')]);
        }
        if (isset($damages)) {
            DB::table('detailingitem')->where('id', $detailingitem_id)->update(['damages' => $damages, 'updated_at' => date('Y-m-d H:i:s')]);
        }
        if (isset($step)) {
            if ($step == 3 && isset($typeitem_id)) {
                $sizes = DB::table('sizes')->where('typeitem_id', $typeitem_id)->where('deleted_at', NULL)->get();
                $step= count($sizes) == 0 ? $step+1 : $step;
            }
            DB::table('detailingitem')->where('id', $detailingitem_id)->update(['etape' => $step, 'updated_at' => date('Y-m-d H:i:s')]);

            if($step==11){
                $cprices = [];
                if(isset($cleaning_prices)){
                    $cprices = $cleaning_prices;
                }

                DB::table('detailingitem')->where('id',$detailingitem_id)->update([
                    'cleaning_services'=>$cleaning_services,
                    'cleaning_price_type'=>$cleaning_price_type,
                    'dry_cleaning_price'=>(isset($cprices['Dry cleaning'])?$cprices['Dry cleaning']:0),
                    'cleaning_addon_price'=>(isset($cprices['Cleaning Add-on'])?$cprices['Cleaning Add-on']:0),
                ]);


                if($cleaning_price_type=='Quote'){
                    DB::table('detailingitem')->where('id',$detailingitem_id)->update([
                        'dry_cleaning_price'=>0,
                        'cleaning_addon_price'=>0,
                    ]);
                }


                if(isset($tailoring_services) && isset($tailoring_price)){
                    DB::table('detailingitem')->where('id',$detailingitem_id)->update([
                        'tailoring_services'=>$tailoring_services,
                        'tailoring_price'=>$tailoring_price,
                        'tailoring_price_type'=>$tailoring_price_type,
                    ]);
                }
            }
        }
        $detailingitem = DB::table('detailingitem')->where('id', '=', $detailingitem_id)->get();
        $detailingitem = (array) $detailingitem->first();

        $detailing_data = $this->getDetailingData($detailingitem['department_id'], $detailingitem['typeitem_id']);
        $item_description = $this->getItemDescription($detailingitem);



        /* CLEANING SERVICES */

        $cust_cleaning_services = DetailingController::getCustCleaningServices($detailingitem);

        return response()->json(
            [
                'detailingitem' => $detailingitem,
                'item_description' => $item_description,
                'detailing_data' => $detailing_data,
                'cust_cleaning_services'=>$cust_cleaning_services,
                'post'=>$request->all(),
                'cleaning_prices'=>$cleaning_prices,
            ]
        );
    }
    public function addNewBrand(string $brand_name)
    {
        $brand = DB::table('brands')->where('name', $brand_name)->first();
        if ($brand) {
            return $brand->id;
        }
        $brand_id = DB::table('brands')->insertGetId(
            [
                'name' => $brand_name,
                'TypeBrands' => 0,
                'coefcleaning' => 0,
                'coeftailoring' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );
        return $brand_id;
    }
    public function getDetailingData($department_id = 0, $typeitem_id = 0)
    {
        $detailing_data = null;
        $query = DB::table('etape')->where('Actif', 1)->where('deleted_at', NULL);
        $detailing_data['steps'] = $query->select('id', 'name')->get();
        $detailing_data['departements'] = DB::table('departments')->where('deleted_at', NULL)->get();
        $detailing_data['categories'] = DB::table('categories')->where('department_id', $department_id)->where('deleted_at', NULL)->get();
        $detailing_data['typeitems'] = DB::table('typeitem')->where('department_id', $department_id)->where('deleted_at', NULL)->get();

        $steps = $query->get()->pluck('id')->toArray();
        if (in_array(6, $steps)) {
            $detailing_data['sizes'] = DB::table('sizes')->where('typeitem_id', $typeitem_id)->where('deleted_at', NULL)->get();
        } else {
            $detailing_data['sizes'] = [];
        }
        if (in_array(3, $steps)) {
            $detailing_data['brands'] = DB::table('brands')->where('deleted_at', NULL)->get();
        } else {
            $detailing_data['brands'] = [];
        }
        if (in_array(5, $steps)) {
            $detailing_data['fabrics'] = DB::table('fabrics')->where('deleted_at', NULL)->get();
        } else {
            $detailing_data['fabrics'] = [];
        }
        if (in_array(4, $steps)) {
            $detailing_data['colours'] = DB::table('colours')->where('code', '!=', '')->where('deleted_at', NULL)->get();
        } else {
            $detailing_data['colours'] = [];
        }
        if (in_array(7, $steps)) {
            $detailing_data['patterns'] = DB::table('patterns')->where('deleted_at', NULL)->get();
        } else {
            $detailing_data['patterns'] = [];
        }
        if (in_array(8, $steps)) {
            $detailing_data['conditions'] = DB::table('conditions')->where('deleted_at', NULL)->get();
        } else {
            $detailing_data['conditions'] = [];
        }
        $detailing_data['complexities'] = DB::table('complexities')->where('deleted_at', NULL)->get();
        $detailing_data['issues_tags'] = DB::table('issues_tag')->get();
        return $detailing_data;
    }
    public function getItemDescription(array $detailingitem)
    {
        if (isset($detailingitem['typeitem_id']) && $detailingitem['typeitem_id'] != null) {
            $typeitem = DB::table('typeitem')->where('id', '=', $detailingitem['typeitem_id'])->get();
            $typeitem = (array) $typeitem->first();
        }
        if (isset($detailingitem['size_id']) && $detailingitem['size_id'] != null) {
            $size = DB::table('sizes')->where('id', '=', $detailingitem['size_id'])->get();
            $size = (array) $size->first();
        }
        if (isset($detailingitem['brand_id']) && $detailingitem['brand_id'] != null) {
            $brand = DB::table('brands')->where('id', '=', $detailingitem['brand_id'])->get();
            $brand = (array) $brand->first();
        }
        if (isset($detailingitem['fabric_id']) && $detailingitem['fabric_id'] != null) {
            $fabrics_arr = [];
            $fabrics = [];

            if(is_array(json_decode($detailingitem['fabric_id']))){
                $fabrics_arr = json_decode($detailingitem['fabric_id']);
            }

            if(!empty($fabrics_arr)){
                $fabrics = DB::table('fabrics')->select('Name as name', 'coefcleaning', 'coeftailoring')->whereIn('id',$fabrics_arr)->get();
                // $fabric = (array) $fabric->first();
            }
        }
        if (isset($detailingitem['color_id']) && $detailingitem['color_id'] != null) {
            $colors = DB::table('colours')->select('name', 'code')->whereIn('id', json_decode($detailingitem['color_id']))->get();
        }
        if (isset($detailingitem['pattern_id']) && $detailingitem['pattern_id'] != null) {
            $pattern = DB::table('patterns')->where('id', '=', $detailingitem['pattern_id'])->get();
            $pattern = (array) $pattern->first();
        }
        if (isset($detailingitem['condition_id']) && $detailingitem['condition_id'] != null) {
            $condition = DB::table('conditions')->where('id', '=', $detailingitem['condition_id'])->get();
            $condition = (array) $condition->first();
        }
        if (isset($detailingitem['complexities_id']) && $detailingitem['complexities_id'] != null) {
            $complexities = DB::table('complexities')->select('name', 'coefcleaning', 'coeftailoring')->whereIn('id', json_decode($detailingitem['complexities_id']))->get();
        }
        $stains_tags=collect();
        $damages_tags=collect();
        $stains_zones=collect();
        $damages_zones=collect();
        if (isset($detailingitem['stains']) && $detailingitem['stains'] != null) {
            $stains=json_decode($detailingitem['stains'],true);
            $stains_tags = DB::table('issues_tag')->select('id','name')->whereIn('id', array_column($stains, 'id_issue'))->get();
            $stains_zones = DB::table('itemzones')->whereIn('id', array_column($stains, 'id_zone'))->get();
        }
        if (isset($detailingitem['damages']) && $detailingitem['damages'] != null) {
            $damages=json_decode($detailingitem['damages'],true);
            $damages_tags = DB::table('issues_tag')->select('id','name')->whereIn('id', array_column($damages, 'id_issue'))->get();
            $damages_zones = DB::table('itemzones')->whereIn('id', array_column($damages, 'id_zone'))->get();

        }
        return  array(
            'typeitem_name' => isset($typeitem) ? $typeitem['name'] : '',
            'typeitem_picto' => isset($typeitem) ? $typeitem['draw1'] : '',
            'base_price' => isset($typeitem) ? $typeitem['pricecleaning'] : '',
            'size' => isset($size) ? $size['name'] : '',
            'brand_name' => isset($brand) ? $brand['name'] : '',
            'brand_coef_cleaning' => isset($brand) ? $brand['coefcleaning'] * 100 : '',
            'fabrics_name' => isset($fabrics) ? $fabrics : '',
           // 'fabric_coef_cleaning' => isset($fabric) ? $fabric['coefcleaning'] * $typeitem['pricecleaning'] : '',
            'colors_name' => isset($colors) ? $colors : '',
            'pattern_name' => isset($pattern) ? $pattern['name'] : '',
            'condition_name' => isset($condition) ? $condition['name'] : '',
            'complexities_name' => isset($complexities) ? $complexities : array(),
            'stains' => isset($detailingitem['stains']) ? json_decode($detailingitem['stains']) : array(),
            'damages' => isset($detailingitem['damages']) ? json_decode($detailingitem['damages']) : array(),
            'issues_zones' => $stains_zones->merge($damages_zones) ,
            'issues_tags' => $stains_tags->merge($damages_tags) ,
        );
    }

    public static function getCustomerServices($customerid){

        $cust = DB::table('infoCustomer')->where('id',$customerid)->first();


        $cust_pref_value = [];
        $grouped_services = [];

        $group_names = [
            1=>'Dry cleaning',
            2=>'Cleaning Add-on'
        ];

        if($cust){

            $preferences = DB::table('InfoCustomerPreference')
                ->where('CustomerID',$cust->CustomerID)
                ->where('Delete',0)
                ->get();



            foreach($preferences as $k=>$v){
                $cust_pref_value[$v->id_preference] = $v->Value;
            }

            $services = DB::table('cleaningservices')->where('id','!=',2)->get();

            foreach($services as $k=>$v){
                $services[$k]->group_name = $group_names[$v->cleaning_group];
                $services[$k]->isPrefActive = 0;
                if($v->cleaning_group==2 && isset($cust_pref_value[$v->id_preference])){
                    $services[$k]->isPrefActive = $cust_pref_value[$v->id_preference];
                }
            }


            foreach($services as $k=>$v){
                if(isset($group_names[$v->cleaning_group])){
                    $groupname = $group_names[$v->cleaning_group];
                    $grouped_services[$groupname][] = $v;
                }
            }
        }

        return $grouped_services;

    }

    public function getServices(Request $request){

        $customerid = $request->customer_id;
        $grouped_services = DetailingController::getCustomerServices($customerid);

        return response()->json([
            'main_services'=> DetailingServices::getMainServices(),
            'grouped_services'=>$grouped_services,

        ]);
    }

    public function completeDetailing(Request $request){
        $id = $request->detailingitem_id;
        $updated = false;

        $updated = DB::table('detailingitem')->where('id',$id)->update([
            'status'=>'Completed',
        ]);

        return response()->json([
            'updated'=>$updated,
        ]);
    }

    public function createOrderItems(Request $request){

        return response()->json([
            'post'=>$request->all(),
        ]);
    }

    public function checkDetailingTracking(Request $request){
        $tracking = $request->tracking;
        $customer_id = $request->customer_id;


        $item = DB::table('infoitems')->where('ItemTrackingKey',$tracking)->first();

        $err = '';

        if($item){
            $inv = DB::table('infoInvoice')
                ->where('InvoiceID',$item->InvoiceID)
                ->where('CustomerID',$customer_id)
                ->get();

            if(count($inv)==0){
                $err = "HSL $tracking not linked with current customer";
            }
        }


        return response()->json([
            'item'=>$item,
            'err'=>$err,
            //'post'=>$request->all(),
        ]);
    }

    public function updateCustomerServicePref(Request $request){
        $service_id = $request->service_id;
        $customer_id = $request->customer_id;

        $cust = DB::table('infoCustomer')->where('id',$customer_id)->first();

        $service_line = DB::table('cleaningservices')->where('id',$service_id)->first();

        $updated = false;

        if($service_line){
            $id_pref = $service_line->id_preference;

            if($id_pref !=0){
                $updated = DB::table('InfoCustomerPreference')
                    ->where('CustomerID',$cust->CustomerID)
                    ->where('id_preference',$id_pref)
                    ->where('Delete',0)
                    ->update([
                        'Value'=>1
                    ]);
            }
        }

        return response()->json([
            'post'=>$request->all(),
            'id_pref'=>$id_pref,
            'updated'=>$updated,
        ]);
    }

    public function removeDetailingItem(Request $request){
        $id = $request->id;

        $deleted = DB::table('detailingitem')->where('id',$id)->delete();

        return response()->json([
            'post'=>$request->all(),
            'deleted'=>$deleted,
        ]);
    }

    public static function calculateNextPost($detailing_item_id){
        $nextpost = 0;

        $detailing_item = DB::table('detailingitem')
            ->select('detailingitem.*','typeitem.process','typeitem.firstpost')
            ->join('typeitem','detailingitem.typeitem_id','typeitem.id')
            ->where('detailingitem.id',$detailing_item_id)
            ->first();

        $process = @json_decode($detailing_item->process);

        //Tailoring
        if($detailing_item->tailoring_services !='' && !is_null($detailing_item->tailoring_services)){
            $tailoring_arr = @json_decode($detailing_item->tailoring_services);

            if(!empty($tailoring_arr)){
                $nextpost = 1;

                $process_to_add = new stdClass;
                $process_to_add->idpost = 1;
                $process_to_add->need = 1;

                $to_add_arr = [$process_to_add];

                $process = array_merge($to_add_arr,$process);
            }else{
                foreach($process as $k=>$v){
                    if($v->idpost==1){
                        unset($process[$k]);
                    }
                }
            }
        }


        if($detailing_item->cleaning_services !='' && !is_null($detailing_item->cleaning_services)){
            $cleaning_arr = @json_decode($detailing_item->cleaning_services);

            if(!empty($cleaning_arr)){
                $postes_cleaning = [4,5];
                $postes_pressing = [8,9,10,11];

                //table cleaningservices: 1=>cleaning, 3=>pressing, 4=>Ozonation / CO2
                //Cleaning
                if(in_array(1,$cleaning_arr)){
                    if($nextpost==0){
                        foreach($process as $k=>$v){
                            if(in_array($v->idpost,$postes_cleaning)){
                                $nextpost = $v->idpost;
                            }
                        }
                    }
                }else{
                    foreach($process as $k=>$v){
                        if(in_array($v->idpost,$postes_cleaning)){
                            unset($process[$k]);
                        }
                    }

                }

                //Pressing
                if(in_array(3,$cleaning_arr)){
                    if($nextpost==0){
                        foreach($process as $k=>$v){
                            if(in_array($v->idpost,$postes_pressing)){
                                $nextpost = $v->idpost;
                            }
                        }
                    }
                }else{
                    foreach($process as $k=>$v){
                        if(in_array($v->idpost,$postes_pressing)){
                            unset($process[$k]);
                        }
                    }

                }


                //Ozonation
                if(in_array(4,$cleaning_arr)){
                    /*
                    if($nextpost==0){
                        foreach($process as $k=>$v){
                            if(in_array($v->idpost,$postes_pressing)){
                                $nextpost = $v->idpost;
                            }
                        }
                    }
                    */
                }else{
                    foreach($process as $k=>$v){
                        if($v->idpost==24){
                            unset($process[$k]);
                        }
                    }

                }

            }
        }

        return [
            'nextpost'=>$nextpost,
            'process'=>$process,
        ];
    }
}
