<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\DetailingServices;
use App\Models\Tranche;
use DateTime;
use PDO;
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

        $count_has_invoices = 0;

        $detailingitemlist = DB::table('detailingitem')
            ->select( 'detailingitem.pricecleaning as price','detailingitem.id as item_number','detailingitem.*',)
            //->join('typeitem', 'typeitem.id', 'detailingitem.typeitem_id')
            //->join('infoitems', 'infoitems.ItemTrackingKey', '=', 'detailingitem.item_id')
            //->join('infoInvoice', 'infoInvoice.SubOrderID', '=', 'infoitems.SubOrderID')
            ->where('detailingitem.order_id', '=', $order_id)
            //->orderBy('infoitems.NoBag')
            //->orderBy('infoInvoice.NumInvoice')
            ->orderBy('detailingitem.id','ASC')
            ->get();
        //'typeitem.name as typeitem_name',

        $typeitem_map = [];
        $typeitems = DB::table('typeitem')->get();
        if(count($typeitems) > 0){
            foreach($typeitems as $k=>$v){
                $typeitem_map[$v->id] = $v->name;
            }
        }

        /*
        'infoitems.NoBag', 'infoInvoice.NumInvoice as sub_order', 'infoitems.ItemTrackingKey as item_number'
        */

        if(count($detailingitemlist) > 0){
            foreach($detailingitemlist as $k=>$v){
                $detailingitemlist[$k]->sub_order = "";
                $detailingitemlist[$k]->NoBag = 0;

                $detailingitemlist[$k]->typeitem_name = (isset($typeitem_map[$v->typeitem_id])?$typeitem_map[$v->typeitem_id]:"");

                if($v->etape==11){
                    $cleaning_price = $v->dry_cleaning_price + $v->cleaning_addon_price;
                    if($v->cleaning_price_type=='Quote'){
                        $cleaning_price = 0;
                    }

                    $tailoring_price = ($v->tailoring_price_type=='Quote'?0:$v->tailoring_price);

                    $price = $cleaning_price + $tailoring_price;

                    $detailingitemlist[$k]->price = $price;
                }

                if($v->InvoiceID !=''){
                    $count_has_invoices += 1;
                }
            }
        }

        echo json_encode(
            [
                'user' => $user,
                'detailing_list' => $detailingitemlist,
                'customer' => $customer,
                'count_has_invoices'=>$count_has_invoices,
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

        if(count($detailingitem) > 0){
            if(is_null($detailingitem['cleaning_price_type'])){
                $detailingitem['cleaning_price_type'] = "Standard";

                DB::table("detailingitem")->where('id',$detailingitem_id)->update(['cleaning_price_type'=>'Standard']);
            }

            if(is_null($detailingitem['tailoring_price_type'])){
                $detailingitem['tailoring_price_type'] = "Standard";

                DB::table("detailingitem")->where('id',$detailingitem_id)->update(['tailoring_price_type'=>'Standard']);
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
        }else{
            $sel_cleaning_services = [1,3]; //Cleaning & pressing
        }

        if(!empty($sel_cleaning_services)){
            foreach($cust_cleaning_services as $k=>$v){
                foreach($v as $i=>$x){
                    $v[$i]->selected_default = 0;
                    $v[$i]->cust_selected = (in_array($x->id,$sel_cleaning_services)?1:0);
                }
            }
            $cust_cleaning_services[$k] = $v;
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


                $cleaning_arr_to_update = [
                    'cleaning_services'=>$cleaning_services,
                    'dry_cleaning_price'=>(isset($cprices['Dry cleaning'])?$cprices['Dry cleaning']:0),
                    'cleaning_addon_price'=>(isset($cprices['Cleaning Add-on'])?$cprices['Cleaning Add-on']:0),
                ];

                if(!is_null($cleaning_price_type)){
                    $cleaning_arr_to_update['cleaning_price_type'] = $cleaning_price_type;
                }

                DB::table('detailingitem')->where('id',$detailingitem_id)->update($cleaning_arr_to_update);


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
            'brand_name' => isset($brand) && isset($brand['name']) ? $brand['name'] : '',
            'brand_coef_cleaning' => isset($brand) &&  isset($brand['coefcleaning'])? $brand['coefcleaning'] * 100 : '',
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

    public function checkDetailingTracking(Request $request){
        $tracking = $request->tracking;
        $customer_id = $request->customer_id;
        $order_id = $request->order_id;

        $cust = DB::table('infoCustomer')->where('CustomerID',$customer_id)->first();
        $duplicate_detailing_item = false;

        $item = DB::table('infoitems')->where('ItemTrackingKey',$tracking)->first();
        $has_detailing_order = false;
        $err = '';

        $previous_detailed_item = DB::table('detailingitem')
            ->where('tracking',$tracking)
            ->where('customer_id',$cust->id)
            ->where('status','Completed')
            ->latest('id')
            ->first();

        $detailingitem_id = 0;

        if($previous_detailed_item){
            $duplicate_detailing_item = (array) $previous_detailed_item;

            $duplicate_detailing_item['id'] = '';
            $duplicate_detailing_item['etape'] = 9;
            $duplicate_detailing_item['InvoiceID'] = '';
            $duplicate_detailing_item['order_id'] = $order_id;
            $duplicate_detailing_item['tailoring_services'] = '';
            $duplicate_detailing_item['status'] = 'In Process';

            $int_field_to_clear = ['dry_cleaning_price','cleaning_addon_price','tailoring_price'];
            $fields_to_clear = ['stains','damages','cleaning_services','cleaning_price_type','tailoring_price_type'];

            foreach($duplicate_detailing_item as $key=>$value){
                if(in_array($key,$int_field_to_clear)){
                    $duplicate_detailing_item[$key] = 0;
                }
                if(in_array($key,$fields_to_clear)){
                    $duplicate_detailing_item[$key] = null;
                }
            }

            $detailingitem_id = DB::table('detailingitem')->insertGetId($duplicate_detailing_item);

        }else{
            if($item){
                $inv = DB::table('infoInvoice')
                    ->where('InvoiceID',$item->InvoiceID)
                    ->where('CustomerID',$customer_id)
                    ->get();

                if(count($inv)==0){
                    $err = "HSL $tracking already linked with another customer.";
                }
            }

            $has_detailing_order = DB::table('detailingitem')->where('tracking',$tracking)
                ->where('status','In Process')
                ->latest('id')
                ->first();

            if($has_detailing_order){
                $err = "HSL $tracking is already being detailed.";
            }
        }

        return response()->json([
            'item'=>$item,
            'err'=>$err,
            'has_detailing_order'=>$has_detailing_order,
            'previous_detailed_item'=>$previous_detailed_item,
            'detailingitem_id'=>$detailingitem_id,

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

    public function getCheckoutItems(Request $request){
        $order_id = $request->order_id;

        $order = DB::table('infoOrder')->where('id',$order_id)->first();
        $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        $tranches = Tranche::getDeliveryPlanningTranchesForApi();

        $addr = null;
        $cust_card = null;

        $booking_details = [];
        $amount_to_pay = 0;
        $balance = 0;
        $amount_paid_card = [];
        $amount_paid_credit = [];
        $discount_perc = 0;

        if($order){

            $cust = DB::table('infoCustomer')->where('CustomerID',$order->CustomerID)->first();

            $cust->phone_num = [];
            if($cust){
                $cust->cust_type = "";
                $cust->pay_name = $cust->Name;

                $addr = Delivery::getAddressByCustomerUUID($order->CustomerID);

                $cust_id = $cust->CustomerID;
                if($cust->CustomerIDMaster !=''){
                    $cust_id = $cust->CustomerIDMaster;

                    $master_cust = DB::table('infoCustomer')->where('CustomerID',$cust->CustomerIDMaster)->first();
                    $cust->pay_name = $master_cust->Name;
                }

                $cust_card = DB::table('cards')->where('CustomerID',$cust_id)->where('Actif',1)->first();

                if($cust->Phone !='' && !is_null($cust->Phone) && is_array(@json_decode($cust->Phone))){
                    $phone_num = @json_decode($cust->Phone);

                    foreach($phone_num as $key=>$phone){
                        $cust->phone_num[] = str_replace("|"," ",$phone);
                    }
                }

                $cp = DB::table('InfoCustomerPreference')
                    ->where('CustomerID',$cust->CustomerID)
                    ->where('Titre','Type Customer')
                    ->where('Delete',0)
                    ->first();

                if($cp){
                    $cust->cust_type = $cp->Value;
                }
            }




            if($order->deliverymethod=='in_store_collection'){
                $booking = DB::table('booking_store')
                ->select('booking_store.*','users.name AS user')
                ->join('users','booking_store.user_id','users.id')
                ->where('booking_store.order_id',$order_id)->where('booking_store.status','NEW')
                ->latest('booking_store.id')
                ->first();

                if($booking){
                    $dropoff_stamp = strtotime($booking->dropoff);
                    $pickup_stamp = strtotime($booking->pickup_date);


                    $dropoff_day_index = date('w',$dropoff_stamp);
                    $pickup_day_index  = date('w',$pickup_stamp);

                    $booking_details = [
                        'user'=>$booking->user,
                        'dropoff_date'=>date("d/m",$dropoff_stamp),
                        'dropoff_time'=>date('h:i',$dropoff_stamp)." ".strtolower(date("A",$dropoff_stamp)),
                        'dropoff_day' => $days[$dropoff_day_index],
                        'pickup_date'=>date("d/m",$pickup_stamp),
                        'pickup_day'=>$days[$pickup_day_index],
                        'pickup_time'=>'6:00 pm',
                    ];
                }
            }elseif($order->deliverymethod=='home_delivery' || $order->deliverymethod=='delivery_only'){

                $delivery_ask = DB::table('deliveryask')->where('order_id',$order->id)->where('status','NEW')->first();
                $booking_hist = DB::table('booking_histories')
                    ->select('users.name as user','booking_histories.created_at')
                    ->join('users','booking_histories.user_id','users.id')
                    ->where('booking_histories.order_id',$order->id)->where('booking_histories.status','NEW')
                    ->latest('booking_histories.id')
                    ->first();

                if($delivery_ask && $booking_hist){

                    $delivery_stamp = strtotime($delivery_ask->date);

                    $delivery_day_index = date('w',$delivery_stamp);

                    $creation_stamp = strtotime($booking_hist->created_at);


                    $delivery_slot = Tranche::getSlotFromTranche($delivery_ask->trancheFrom,$delivery_ask->trancheto);

                    $booking_details = [
                        'user'=>$booking_hist->user,
                        'creation_date'=>date('d/m',$creation_stamp),
                        'creation_time'=>date('h:i',$creation_stamp)." ".strtolower(date("A",$creation_stamp)),
                        'delivery_date'=>date('d/m',$delivery_stamp),
                        'delivery_day'=>$days[$delivery_day_index],
                        'delivery_time'=>(isset($tranches[$delivery_slot])?$tranches[$delivery_slot]:""),
                    ];
                }


                if($order->deliverymethod=='home_delivery'){
                    $pickup = DB::table('pickup')->where('order_id',$order->id)->where('status','NEW')->first();

                    if($pickup){
                        $pickup_stamp = strtotime($pickup->date);
                        $pickup_day_index = date('w',$pickup_stamp);
                        $pickup_slot = Tranche::getSlotFromTranche($pickup->trancheFrom,$pickup->trancheto);
                    }


                    $booking_details['pickup_date'] = date('d/m',$pickup_stamp);
                    $booking_details['pickup_day'] = $days[$pickup_day_index];
                    $booking_details['pickup_time'] = $tranches[$pickup_slot];

                }
            }

            if($order->deliverymethod=='recurring'){
                $booking_hist = DB::table('booking_histories')
                ->select('users.name as user','booking_histories.created_at')
                ->join('users','booking_histories.user_id','users.id')
                ->where('booking_histories.order_id',$order->id)->where('booking_histories.status','NEW')
                ->latest('booking_histories.id')
                ->first();

                $booking_details = [
                    'user'=>'',
                    'creation_date'=>'',
                    'creation_time'=>'',
                ];

                if($booking_hist){
                    $creation_stamp = strtotime($booking_hist->created_at);

                    $booking_details['user'] = $booking_hist->user;
                    $booking_details['creation_date'] = date('d/m',$creation_stamp);
                    $booking_details['creation_time'] = date('h:i',$creation_stamp)." ".strtolower(date("A",$creation_stamp));
                }

                $recurring = [];

                $slot_day = ['Mon'=>'Monday','Tu'=>'Tuesday','Wed'=>'Wednesday','Th'=>'Thursday','Fri'=>'Friday','Sat'=>'Saturday'];


                $cust_arr = (array) $cust;

                foreach($slot_day as $k=>$v){
                    $slot = $cust_arr["Delivery$k"];

                    if(!is_null($slot) && is_array(@json_decode($slot)) && !empty(@json_decode($slot))){
                        $slot_arr = @json_decode($slot);


                        if(!empty($slot_arr)){
                            $day_index = 0;
                            foreach($slot_arr as $id=>$val){
                                $day_index = $val;
                            }

                            if(isset($tranches[$day_index])){
                                $recurring[] = [
                                    'day'=>$v,
                                    'tranche'=>$tranches[$day_index],
                                ];
                            }
                        }

                    }
                }

                $booking_details['recurring'] = $recurring;
            }
        }

        $items = DB::table('detailingitem')
            ->select('detailingitem.*','detailingitem.id AS detailingitem_id','typeitem.pricecleaning as baseprice')
            ->join('infoOrder','detailingitem.order_id','infoOrder.id')
            ->join('typeitem','detailingitem.typeitem_id','typeitem.id')
            ->where('infoOrder.id',$order_id)
            ->get();

        $cs = [];

        $cleaning_services = DB::table('cleaningservices')->get();
        if(count($cleaning_services) > 0){
            foreach($cleaning_services as $k=>$v){
                $cs[$v->id] = $v->name;
            }
        }

        $color_map = [];
        $colors = DB::table('colours')->get();
        if(count($colors) > 0){
            foreach($colors as $k=>$v){
                $color_map[$v->id] = $v->code;
            }
        }

        $typeitem_map = [];
        $typeitems = DB::table('typeitem')->get();
        if(count($typeitems) > 0){
            foreach($typeitems as $k=>$v){
                $typeitem_map[$v->id] = $v->name;
            }
        }


        $brand_coef_map = [];
        $brands_map = [];
        $brands = DB::table('brands')->get();
        if(count($brands) > 0){
            foreach($brands as $k=>$v){
                $brand_coef_map[$v->id] = $v->coefcleaning;
                $brands_map[$v->id] = $v->name;
            }
        }

        $sizes_map = [];
        $sizes = DB::table('sizes')->get();
        if(count($sizes) > 0){
            foreach($sizes as $k=>$v){
                $sizes_map[$v->id] = $v->name;
            }
        }


        $conditions_map = [];
        $conditions = DB::table('conditions')->get();
        if(count($conditions) > 0){
            foreach($conditions as $k=>$v){
                $conditions_map[$v->id] = $v->name;
            }
        }

        $fabrics_coef = [];
        $fabrics = DB::table('fabrics')->get();

        if(count($fabrics) > 0){
            foreach($fabrics as $k=>$v){
                $fabrics_coef[$v->id] = [
                    'name'=>$v->Name,
                    'cleaning'=>$v->coefcleaning,
                    'tailoring'=>$v->coeftailoring,
                ];
            }
        }

        $complexities_coef = [];
        $complexities = DB::table('complexities')->get();
        if(count($complexities) > 0){
            foreach($complexities as $k=>$v){
                $complexities_coef[$v->id] = [
                    'name'=>$v->name,
                    'cleaning'=>$v->coefcleaning,
                    'tailoring'=>$v->coeftailoring,
                ];
            }
        }


        $zone_names = [];
        $zones = DB::table('itemzones')->get();
        if(count($zones) > 0){
            foreach($zones as $k=>$v){
                $zone_names[$v->id] = str_replace('_',' ',$v->description);
            }
        }

        $issue_names = [];
        $issues = DB::table('issues_tag')->get();
        if(count($issues) > 0){
            foreach($issues as $k=>$v){
                $issue_names[$v->id][$v->type] = $v->name;
            }
        }


        $grouped_tailoring_services = [];
        $tailoringservices = DB::table('tailoring_services')
            ->select('tailoring_services.*','tailoring_type_services.name AS group_service')
            ->join('tailoring_type_services','tailoring_services.type_service_id','tailoring_type_services.id')
            ->get();

        foreach($tailoringservices as $k=>$v){
            $grouped_tailoring_services[$v->id] = [
                'group'=>$v->group_service,
                'price'=>$v->price,
            ];
        }

        $total_price = 0;

        if(count($items) > 0){
            foreach($items as $k=>$v){

                $services = [];
                //Tailoring
                if($v->tailoring_services !='' && !is_null($v->tailoring_services) && is_array(@json_decode($v->tailoring_services)) && !empty(@json_decode($v->tailoring_services))){
                    $services[] = 'Tailoring';
                }

                //Cleaning services
                if($v->cleaning_services!='' && !is_null($v->cleaning_services) && is_array(@json_decode($v->cleaning_services)) && !empty(@json_decode($v->cleaning_services))){
                    $cs_arr = @json_decode($v->cleaning_services);

                    foreach($cs_arr as $id=>$val){
                        if(isset($cs[$val])){
                            $services[] = $cs[$val];
                        }
                    }
                }


                $items[$k]->services = $services;

                $items[$k]->brand = (isset($brands_map[$v->brand_id])?$brands_map[$v->brand_id]:"");
                $items[$k]->typeitem = (isset($typeitem_map[$v->typeitem_id])?$typeitem_map[$v->typeitem_id]:"");

                $items[$k]->size = "";
                if(isset($size_map[$v->size_id])){
                    $items[$k]->size = $sizes_map[$v->size_id];
                }

                $item_total_price = $v->dry_cleaning_price+$v->cleaning_addon_price+$v->tailoring_price;
                $total_price += $item_total_price;

                $items[$k]->priceTotal = number_format($item_total_price,2);
                $items[$k]->generalState = (isset($conditions_map[$v->condition_id])?ucfirst($conditions_map[$v->condition_id]):"");


                //Afficher la premiere couleur
                $items[$k]->first_color = "";
                $items[$k]->all_colours = [];

                $all_colors = [];

                if($v->color_id !='' && !is_null($v->color_id) && is_array(@json_decode($v->color_id)) && !empty(@json_decode($v->color_id))){
                    $colors = @json_decode($v->color_id);

                    if(isset($color_map[$colors[0]])){
                        $items[$k]->first_color = $color_map[$colors[0]];
                    }

                    foreach($colors as $id=>$val){
                        $all_colors[] = $color_map[$val];
                    }
                }

                $items[$k]->all_colors = $all_colors;
                $items[$k]->brand_coef_perc = 0;

                if(!is_null($v->brand_id) && isset($brand_coef_map[$v->brand_id])){
                    $coef_brand = $brand_coef_map[$v->brand_id];

                    if($coef_brand > 1){
                        $diff = $coef_brand;
                        $perc = $diff*100;
                        $items[$k]->brand_coef_perc = ceil($perc);
                    }
                }

                $items[$k]->complexities_arr = [];

                if(!is_null($v->complexities_id) && is_array(@json_decode($v->complexities_id)) && !empty(@json_decode($v->complexities_id))){
                    $item_comp = @json_decode($v->complexities_id);

                    $complexities_arr = [];

                    foreach($item_comp as $id=>$val){
                        if(isset($complexities_coef[$val])){
                            $cur_comp = $complexities_coef[$val];

                            $comp_price = 0;

                            $comp_price = $cur_comp['cleaning'] * $v->baseprice;

                            $complexities_arr[$complexities_coef[$val]['name']] = $comp_price;
                        }
                    }

                    $items[$k]->complexities_arr  = $complexities_arr;

                }

                $items[$k]->fabrics_arr = [];
                if(!is_null($v->fabric_id) && is_array(@json_decode($v->fabric_id)) && !empty(@json_decode($v->fabric_id))){
                    $item_fabric = @json_decode($v->fabric_id);
                    $fabric_arr = [];

                    foreach($item_fabric as $id=>$val){
                        $cur_fabric = $fabrics_coef[$val];
                        $fabric_coef_perc = $cur_fabric['cleaning']*100;

                        $fabric_arr[$cur_fabric['name']] = $fabric_coef_perc;
                    }

                    $items[$k]->fabrics_arr = $fabric_arr;

                }



                $items[$k]->stains = @json_decode($v->stains);
                $items[$k]->damages = @json_decode($v->damages);


                $items[$k]->detailed_services = [];

                if($v->cleaning_services!='' && !is_null($v->cleaning_services) && is_array(@json_decode($v->cleaning_services)) && !empty($v->cleaning_services)){
                     $dc = [];
                    $dc_arr = @json_decode($v->cleaning_services);

                    foreach($dc_arr as $id=>$val){
                        $dc[] = $cs[$val];
                    }

                    $items[$k]->detailed_services[] = [
                        'name'=>"Dry cleaning (".implode(",",$dc).")",
                        'price'=>number_format($v->dry_cleaning_price + $v->cleaning_addon_price,2),
                    ];
                }

                //To add grouped tailoring prices

                if($v->tailoring_services !='' && !is_null($v->tailoring_services) && is_array(@json_decode($v->tailoring_services)) && !empty(@json_decode($v->tailoring_services))){
                    $group_by_tailoring_service = [];
                    $tailoing_services_price_by_group = [];

                    $ts = @json_decode($v->tailoring_services);

                    foreach($ts as $id=>$val){
                        if(isset($grouped_tailoring_services[$val])){
                            $gp = $grouped_tailoring_services[$val];
                            $group_by_tailoring_service[$gp['group']][] = $gp['price'];
                        }
                    }

                    if(!empty($group_by_tailoring_service)){
                        foreach($group_by_tailoring_service as $group=>$prices){
                            $t_arr = [
                                'name'=>$group,
                                'price'=>number_format(array_sum($prices),2),
                            ];

                            $items[$k]->detailed_services[] = $t_arr;
                        }
                    }
                }

            }

        }

        if($order->express==1){
            $total_price = $total_price * 1.4;
        }elseif($order->express==6){
            $total_price = $total_price * 1.2;
        }

        $total_with_discount = $total_price;

        if($order->OrderDiscount > 0){
            $total_with_discount = $total_price - $order->OrderDiscount;

            $discount_perc = ($order->OrderDiscount/$total_price) * 100;
        }


        $payments = DB::table('payments')->where('order_id',$order->id)->where('status','succeeded')->get();

        $balance = $total_with_discount;




        $amount_paid = 0;

        if(count($payments) > 0){
            foreach($payments as $k=>$v){
                $amount_paid += number_format($v->montant,2);

                if($v->type=='cust_credit'){
                    $amount_paid_credit[] = [
                        'montant'=> number_format($v->montant,2),
                        'date'=>date('F d, Y',strtotime($v->created_at))." at ".date('h:i A',strtotime($v->created_at)),
                    ];
                }else{
                    $amount_paid_card[] = [
                        'montant'=> number_format($v->montant,2),
                        'date'=>date('F d, Y',strtotime($v->created_at))." at ".date('h:i A',strtotime($v->created_at)),
                    ];
                }
            }

            $balance = $total_with_discount - $amount_paid;

        }


        $amount_to_pay = $balance;

        if($cust->credit >= $balance){
            $amount_to_pay = 0;
        }else{
            $amount_to_pay = $balance - $cust->credit;
        }

        if($cust->credit > 0){
            if($balance > $cust->credit){
                $balance = $balance - $cust->credit;
            }else{
                $balance = 0;
            }
        }


        $order->balance = $balance;

        $created_date = "";

        $has_new_inv = DB::table('NewInvoice')->where('order_id',$order_id)->latest('id')->first();

        if($has_new_inv){
            $created_date = $has_new_inv->created_at;
        }


        //Mise a jour montant commande
        DB::table('infoOrder')->where('id',$order_id)->update([
            'Subtotal'=>$total_price,
            'Total'=>$total_with_discount,
        ]);

        $vat = number_format(0.20*$total_with_discount,2);
        $total_exc_vat = number_format($total_with_discount-$vat,2);

        $stripe_security_key = 'STRIPE_LIVE_SECURITY_KEY';
        $stripe_public_key = 'STRIPE_LIVE_PUBLIC_KEY';

        $stripe_test = env('STRIPE_TEST');

        if($stripe_test){
            $stripe_security_key = 'STRIPE_TEST_SECURITY_KEY';
            $stripe_public_key = 'STRIPE_TEST_PUBLIC_KEY';
        }


        return response()->json([
            'post'=>$request->all(),
            'items'=>$items,
            'zones'=>$zone_names,
            'issues'=>$issue_names,
            'cust'=>$cust,
            'order'=>$order,
            'booking_details'=>$booking_details,
            'address'=>$addr,
            'sub_total'=>number_format($total_price,2),
            'total_with_discount'=>number_format($total_with_discount,2),
            'discount'=>number_format($order->OrderDiscount,2),
            'vat'=>$vat,
            'total_exc_vat'=>$total_exc_vat,
            'custcard'=>$cust_card,
            'stripe_public_key'=>env($stripe_public_key),
            'stripe_security_key'=>env($stripe_security_key),
            'cur_user'=>Auth::user(),
            'amount_to_pay'=>number_format($amount_to_pay,2),
            'amount_paid'=>$amount_paid,
            'amount_paid_card'=>$amount_paid_card,
            'amount_paid_credit'=>$amount_paid_credit,
            'discount_perc'=>$discount_perc,
            'created_date'=>$created_date,
        ]);
    }

    public function changeDetailingEtape(Request $request){
        $etape = $request->etape;
        $id = $request->detailingitem_id;

        $detailingitem = DB::table('detailingitem')->where('id',$id)->first();

        if($detailingitem){
            DB::table('detailingitem')->where('id',$id)->update([
                'etape'=>$etape,
            ]);
        }

        return response()->json([
            'detalingitem'=>$detailingitem,
            'updated'=>true,
        ]);
    }

    public function setCheckoutDiscount(Request $request){
        $discount = $request->discount;
        $order_id = $request->order_id;
        $sub_total = $request->sub_total;

        $discount_price = $sub_total;

        if($discount > 0){
            $discount_price = ($discount/100) * $sub_total;
        }else{
            $discount_price = 0;
        }

        DB::table('infoOrder')->where('id',$order_id)->update(['OrderDiscount'=>$discount_price]);

        return response()->json([
            'post'=>$request->all(),
        ]);
    }

    public function getTerminalToken(Request $request){
        $stripe = new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));
        $connectionToken = $stripe->terminal->connectionTokens->create();

        return response()->json([
            'token'=>$connectionToken,
        ]);

    }

    public function getStripeTerminal(Request $request){
        $reader = $request->reader;
        $amount = $request->amount;

        $terminal = null;
        $payment_intent = null;

        if(!isset($reader) && $reader==''){
            die('Reader not set');
        }

        $readers_id = [
            'ATELIER'=>'tmr_Eqz4ewJhXq5eu6',
        ];

        if(isset($readers_id[$reader])){
            $reader_id = $readers_id[$reader];

            $stripe =  new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));
            $terminal = $stripe->terminal->readers->retrieve($reader_id, []);

            $payment_intent = $stripe->paymentIntents->create([
                'amount' => $amount*100,
                'currency' => 'gbp',
                'payment_method_types' => ['card_present'],
                'capture_method' => 'manual',
            ]);

        }

        return response()->json([
            'terminal'=>$terminal,
            'payment_intent'=>$payment_intent,
        ]);
    }

}
