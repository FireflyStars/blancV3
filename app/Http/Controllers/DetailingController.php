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
            ->select( 'detailingitem.pricecleaning as price','detailingitem.id as item_number','detailingitem.*')
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

                /*
                if($v->InvoiceID !=''){
                    $count_has_invoices += 1;
                }
                */
            }

            DetailingController::getVoucherAmount($order_id);
        }

        $order = DB::table('infoOrder')->where('id',$order_id)->first();

        $invoices = DB::table('infoInvoice')->where('OrderID',$order->OrderID)->get();

        $count_has_invoices = count($invoices);

        echo json_encode(
            [
                'user' => $user,
                'detailing_list' => $detailingitemlist,
                'customer' => $customer,
                'count_has_invoices'=>$count_has_invoices,
                'order_status' => $order->Status,
            ]
        );
    }

    public function recalculateDryCleaningPrice($order_id){
        //dry_cleaning_price = typeitem.pricecleaning +  typeitem.pricecleaning*brands.coefcleaning
        //+  SUM (typeitem.pricecleaning*complexities.coefcleaning) + SUM(typeitem.pricecleaning*fabrics.coefcleaning )
        $order = DB::table('infoOrder')->where('id',$order_id)->first();

        if(in_array($order->Status,["FULFILLED",'DELETE','VOID','CANCEL','CANCELLED'])){
            return;
        }

            $detailingitemlist = DB::table('detailingitem')->select(['detailingitem.id','detailingitem.pricecleaning','brands.coefcleaning','detailingitem.complexities_id','detailingitem.fabric_id','detailingitem.cleaning_services','detailingitem.etape','detailingitem.status','detailingitem.cleaning_price_type','detailingitem.dry_cleaning_price','detailingitem.cleaning_addon_price','detailingitem.etape'])
                ->join('typeitem', 'detailingitem.typeitem_id', 'typeitem.id')
                ->join('brands', 'detailingitem.brand_id', 'brands.id')
                ->where('detailingitem.order_id', '=', $order_id)
                ->get();
            foreach($detailingitemlist as $detailingitem){

                $sum_complexites=0;
                if($detailingitem->complexities_id!=null&&$detailingitem->complexities_id!=''){
                    $complexitiesids=json_decode($detailingitem->complexities_id);
                    foreach($complexitiesids as $complexity_id){
                        $complexities_coefcleaning=DB::table('complexities')->where('id','=',$complexity_id)->value('coefcleaning');
                        //$sum_complexites+=$detailingitem->pricecleaning*$complexities_coefcleaning;
                    }
                }
                $sum_fabrics=0;
                if($detailingitem->fabric_id!=null&&$detailingitem->fabric_id!=''){
                    $fabicsids=json_decode($detailingitem->fabric_id);
                    foreach($fabicsids as $fabric_id){
                        $fabrics_coefcleaning=DB::table('fabrics')->where('id','=',$fabric_id)->value('coefcleaning');
                        //$sum_fabrics+=$detailingitem->pricecleaning*$fabrics_coefcleaning;
                    }
                }

                //Quote
                $dry_cleaning_price=0;
                $cleaning_addon_price=0;

                if($detailingitem->cleaning_price_type=='Standard'){
                    //$dry_cleaning_price=$detailingitem->pricecleaning + $detailingitem->pricecleaning*$detailingitem->coefcleaning+ $sum_complexites + $sum_fabrics;
                    $dry_cleaning_price = $detailingitem->pricecleaning;

                    if($detailingitem->etape==11){
                        $cs = @json_decode($detailingitem->cleaning_services);

                        if(is_array($cs)){
                            foreach($cs as $keycs=>$idcs){
                                /*
                                if(!in_array($idcs,[1,2,3])){
                                    unset($cs[$keycs]);
                                }
                                */
                            }
                        }

                        $dry_cleaning_price = DetailingController::calculateCleaningPrice($cs,$dry_cleaning_price);

                        $cleaning_addon_price = $detailingitem->cleaning_addon_price;
                    }
                }else if($detailingitem->cleaning_price_type=='PriceNow'){
                    $dry_cleaning_price=$detailingitem->dry_cleaning_price;
                    $cleaning_addon_price = 0;
                }

                if(($detailingitem->cleaning_services==null||$detailingitem->cleaning_services==''||$detailingitem->cleaning_services=='[]')&&$detailingitem->etape==11)
                $dry_cleaning_price=0;


                DB::table('detailingitem')->where('id', $detailingitem->id)->update([
                    'dry_cleaning_price'=>$dry_cleaning_price,
                    'cleaning_addon_price'=>$cleaning_addon_price,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

    }

    public function savePriceDeliveryNow(Request $request){
        $order_id=$request->post('order_id');
        $price=$request->post('price');

        $updated=DB::table('infoOrder')->where('id','=',$order_id)->where('TypeDelivery','DELIVERY')->where('Status','<>','FULFILLED')->update([
            'DeliveryNowFee'=>$price,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return response()->json(['updated'=>$updated]);
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
                ->where('deleted_at' , '=' , NULL)
                ->first();
                if($searched_item){
                    DB::table('detailingitem')->where('id', $detailingitem_id)->update([
                        'department_id' => $searched_item->department_id,
                        'category_id' => $searched_item->category_id,
                        'typeitem_id' => $searched_item->id,
                        'size_id' => null,
                        'etape' => 2,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
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

        $customer_instructions = [];
        $detailingitem['voucher'] = "";

        if(!empty($detailingitem)){
            $order = DB::table('infoOrder')->where('id',$order_id)->first();

            if($order && $order->PickupID !=''){
                $item = DetailingController::getInstructionsFromPickup($order->PickupID);

                if(isset($item->instructions)){
                    $customer_instructions = $item->instructions;
                }

                if(isset($item->voucher)){
                    $detailingitem['voucher'] = $item->voucher;
                }
            }
        }

        $detailingitem['customer_instructions'] = $customer_instructions;

        $categories = [
            'Garment'=>'Garment Care',
            'Tailor'=>'Tailoring',
            'Home'=>'Home & Bedding',
            'Wash'=>'Wash & Fold',
            'Leather'=>'Leather, Suede & fur',
            'DonateBags'=>'Donations',
        ];

        $detailingitem['instruction_categories'] = $categories;

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
        $stainisssue = $request->post('stainisssue');
        $damages = $request->post('damages');
        $damagesissues = $request->post('damagesissues');
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
        if (isset($stainisssue)) {
            DB::table('detailingitem')->where('id', $detailingitem_id)->update(['stainsissue' => $stainisssue, 'updated_at' => date('Y-m-d H:i:s')]);
        }
        if (isset($damages)) {
            DB::table('detailingitem')->where('id', $detailingitem_id)->update(['damages' => $damages, 'updated_at' => date('Y-m-d H:i:s')]);
        }
        if (isset($damagesissues)) {
            DB::table('detailingitem')->where('id', $detailingitem_id)->update(['damagesissues' => $damagesissues, 'updated_at' => date('Y-m-d H:i:s')]);
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

                if($cleaning_price_type=='PriceNow'){
                    DB::table('detailingitem')->where('id',$detailingitem_id)->update([
                        'dry_cleaning_price'=>$request->montant,
                        'cleaning_addon_price'=>0,
                    ]);
                }else{
                    DB::table('detailingitem')->where('id',$detailingitem_id)->update([
                        'describeprixnow' => null
                    ]);
                }


                if($cleaning_price_type=='Quote'){
                    DB::table('detailingitem')->where('id',$detailingitem_id)->update([
                        'dry_cleaning_price'=>0,
                        'cleaning_addon_price'=>0,
                        'describeprixnow' => null
                    ]);
                }


                if(isset($tailoring_services) && isset($tailoring_price)){
                    DB::table('detailingitem')->where('id',$detailingitem_id)->update([
                        'tailoring_services'=>$tailoring_services,
                        'tailoring_price'=>$tailoring_price,
                        'tailoring_price_type'=>$tailoring_price_type,
                    ]);
                }
                if($tailoring_price_type!='PriceNow'){
                    DB::table('detailingitem')->where('id',$detailingitem_id)->update([
                        'describeprixnowtailoring' => null
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
        $detailing_data['typeitems'] = DB::table('typeitem')->where('department_id', $department_id)->where('deleted_at', NULL)->orderBy('typeitem.Order' , 'ASC')->get();

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
            $stains_tags_decode=json_decode($detailingitem['stainsissue'],true);
            if(!empty($stains_tags_decode)){
                $stains_tags = DB::table('issues_tag')->select('id','name')->whereIn('id',$stains_tags_decode)->get();
            }
            $stains_zones = DB::table('itemzones')->whereIn('id', array_column($stains, 'id_zone'))->get();
        }
        if (isset($detailingitem['damages']) && $detailingitem['damages'] != null) {
            $damages=json_decode($detailingitem['damages'],true);
            $damages_tags_decode=json_decode($detailingitem['damagesissues'],true);
            if(!empty($damages_tags_decode)){
               $damages_tags = DB::table('issues_tag')->select('id','name')->whereIn('id', $damages_tags_decode)->get();
            }
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
            'stains_tags' => $stains_tags,
            'damages_tags' => $damages_tags
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

        $detailingitem_id = 0;

        if($item){
            $inv = DB::table('infoInvoice')
                ->where('InvoiceID',$item->InvoiceID)
                ->latest('id')
                ->first();

            $is_cust_inv = true;
            if($inv){

                if($cust->CustomerID != $inv->CustomerID){
                    $err = "HSL $tracking already linked with another customer.";
                    $is_cust_inv = false;
                }
            }

            if($is_cust_inv && !in_array($item->nextpost,[28,34,39,47,43,44,46])){
                $err = "HSL $tracking is active. Please change its station";
            }

        }
    $previous_detailed_item = false;

    if($err==''){
        $previous_detailed_item = DB::table('detailingitem')
            ->where('tracking',$tracking)
            ->where('customer_id',$cust->id)
            ->where('status','Completed')
            ->latest('id')
            ->first();



        if($previous_detailed_item){
            $duplicate_detailing_item = (array) $previous_detailed_item;

            $duplicate_detailing_item['id'] = '';
            $duplicate_detailing_item['etape'] = 11;
            $duplicate_detailing_item['InvoiceID'] = '';
            $duplicate_detailing_item['order_id'] = $order_id;
            $duplicate_detailing_item['tailoring_services'] = '';
            $duplicate_detailing_item['status'] = 'In Process';

            $int_field_to_clear = ['dry_cleaning_price','cleaning_addon_price','tailoring_price'];
            $fields_to_clear = ['stains','damages','cleaning_price_type','tailoring_price_type'];

            foreach($duplicate_detailing_item as $key=>$value){
                if(in_array($key,$int_field_to_clear)){
                    $duplicate_detailing_item[$key] = 0;
                }
                if(in_array($key,$fields_to_clear)){
                    $duplicate_detailing_item[$key] = null;
                }
            }

            $item_base_price = 0;

            $prev_type_item = DB::table('typeitem')->where('id',$previous_detailed_item->typeitem_id)->first();

            if($prev_type_item){
                $item_base_price = $prev_type_item->pricecleaning;
            }

            $duplicate_detailing_item['pricecleaning'] = $item_base_price;

            $pricecleaning = $item_base_price
                + ($item_base_price * $previous_detailed_item->coefcleaningbrand)
                + ($item_base_price * $previous_detailed_item->coefcleaningfabric)
                + ($item_base_price * $previous_detailed_item->coefcleaningcomplexities);

            $cs = @json_decode($previous_detailed_item->cleaning_services);

            if(is_array($cs) && !empty($cs)){
                $pricecleaning = DetailingController::calculateCleaningPrice($cs,$pricecleaning);
            }

            $duplicate_detailing_item['dry_cleaning_price'] = $pricecleaning;
            $duplicate_detailing_item['tailoring_services'] = "[]";


            $detailingitem_id = DB::table('detailingitem')->insertGetId($duplicate_detailing_item);

        }else{

            $has_detailing_order = DB::table('detailingitem')->where('tracking',$tracking)
                ->where('status','In Process')
                ->latest('id')
                ->first();

            if($has_detailing_order){
                $err = "HSL $tracking is already being detailed.";
            }
        }
    }

        $current_detailing_item = DB::table('detailingitem')->where('tracking',$tracking)
            ->where('order_id',$order_id)
            ->first();

        if(!$previous_detailed_item && $current_detailing_item){
            $err = "HSL $tracking is already being detailed.";
        }

        if($err==''){
            DB::table('infoOrder')->where('id',$order_id)->update(['Status'=>'IN DETAILING']);
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

        $tailoring = @json_decode($detailing_item->tailoring_services);
        $cleaning = @json_decode($detailing_item->cleaning_services);
        $process = @json_decode($detailing_item->process);

        if( empty($tailoring) && empty($cleaning) ){
            $nextpost = 5;
        }

       // dump(!empty($tailoring) , $detailing_item->describeprixnow , $detailing_item->describeprixnowtailoring );

        if(!empty($tailoring) || ($detailing_item->describeprixnow != null ||$detailing_item->describeprixnowtailoring != null )){
            $nextpost = 1;
        }

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

    public function calculateCheckout($order_id){
        $order = DB::table('infoOrder')->where('id',$order_id)->first();
        if(in_array($order->Status,["FULFILLED",'DELETE','VOID','CANCEL','CANCELLED']))
        return;

        $cust = DB::table('infoCustomer')->where('CustomerID',$order->CustomerID)->first();
        $_SUBTOTAL=0;
        $_SUBTOTAL_WITH_DISCOUNT=0;
        $_ITEMS_TOTAL = 0;

        $all_typeitems = [];
        $count_by_typeitem = [];

        $all_services = [];
        $count_by_service = [];

        $items = DB::table('detailingitem')
        ->select('detailingitem.*','detailingitem.id AS detailingitem_id','typeitem.pricecleaning as baseprice')
        ->join('infoOrder','detailingitem.order_id','infoOrder.id')
        ->join('typeitem','detailingitem.typeitem_id','typeitem.id')
        ->where('infoOrder.id',$order_id)
        ->get();

        if(count($items) > 0){
            foreach($items as $k=>$v){
                $_ITEMS_TOTAL += $v->dry_cleaning_price+$v->cleaning_addon_price+$v->tailoring_price;
                $all_typeitems[] = $v->typeitem_id;

                $services = @json_decode($v->cleaning_services);

                if(is_array($services) && !empty($services)){
                    foreach($services as $index=>$service){
                        $all_services[] = $service;
                    }
                }
            }

            $count_by_typeitem = array_count_values($all_typeitems);
            $count_by_service = array_count_values($all_services);
        }



        $_SUBTOTAL = $_ITEMS_TOTAL;

        $_BUNDLES_DISCOUNT=0;

        $bundles_id = DetailingController::checkOrderBundles($order_id);

        if(!empty($bundles_id)){
            $bundles = DB::table('bundles')->whereIn('id',$bundles_id)->get();

            foreach($bundles as $k=>$v){
                $bundle_type = $v->type;
                if(isset(${"count_by_".$bundle_type}[$v->bundles_id]) && ${"count_by_".$bundle_type}[$v->bundles_id]>=$v->qty)
                    $_BUNDLES_DISCOUNT += $v->discountbyitem*${"count_by_".$bundle_type}[$v->bundles_id];

               /* if($v->type=='service' && isset($count_by_service[$v->bundles_id]) && $count_by_service[$v->bundles_id]>=$v->qty)
                    $_BUNDLES_DISCOUNT += $v->discountbyitem*$count_by_service[$v->bundles_id];
                    */
            }
        }

        $_VOUCHER_DISCOUNT=0;
        $voucher_codes = [];

        $order_vouchers = DB::table('vouchers_histories')->where('order_id',$order_id)->get();

        if(count($order_vouchers) > 0){
            foreach($order_vouchers as $k=>$v){
                $voucher_codes[$v->id] = $v->code;
                $_VOUCHER_DISCOUNT+= $v->amount;
            }

            /*
            //Recalculate vouchers

            foreach($voucher_codes as $k=>$v){
                $arr = DetailingController::getVoucherAmount($order_id,$v,false);
                $new_voucher_amount = $arr['montant'];
                $_VOUCHER_DISCOUNT += $new_voucher_amount;
                DB::table('vouchers_histories')->where('id',$k)->update(['amount'=>$new_voucher_amount]);
            }
            */

        }

        $_EXPRESS_CHARGES_PRICE = 0;
        $_FAILED_DELIVERY_PRICE = 0;
        $_DELIVERY_NOW_FEE=$order->DeliveryNowFee;
        $_AUTO_DELIVERY_FEE=0;

        if($order->TypeDelivery=='DELIVERY' && $order->FailedDelivery===1)
            $_FAILED_DELIVERY_PRICE = 5;

        $upcharges = DB::table('order_upcharges')->where('order_id',$order_id)->get();
        if(count($upcharges) > 0){
            foreach($upcharges as $k=>$v){
                if($v->upcharges_id==1||$v->upcharges_id==2){
                    $_EXPRESS_CHARGES_PRICE += $v->amount;
                }
            }
        }


        $_SUBTOTAL = $_SUBTOTAL - $_BUNDLES_DISCOUNT-$_VOUCHER_DISCOUNT+$_EXPRESS_CHARGES_PRICE;

        $_ACCOUNT_DISCOUNT  =  $order->AccountDiscount;
        $_ORDER_DISCOUNT = 0;

        if($order->detailed_at=='0000-00-00 00:00:00'){
            $_ACCOUNT_DISCOUNT=($cust->discount/100) * $_SUBTOTAL;
        }

        if($_SUBTOTAL==0){
            $_ACCOUNT_DISCOUNT = 0;
        }


        $_ORDER_DISCOUNT=($order->DiscountPerc/100) * $_SUBTOTAL;


        $payments = DB::table('payments')->where('order_id',$order->id)->where('status','succeeded')->get();
        $_AMOUNT_PAID=0;

        if(count($payments) > 0)
        foreach($payments as $k=>$v){
            $_AMOUNT_PAID += $v->montant;
        }

       //SubTotal inc Discount = SubTotal (excl Discount) - Account Discount - Order Discount - Bundles + Express Charge - voucher

        $_SUBTOTAL_WITH_DISCOUNT=$_SUBTOTAL-$_ACCOUNT_DISCOUNT-$_ORDER_DISCOUNT;



        if($order->TypeDelivery=='DELIVERY'&&$_SUBTOTAL_WITH_DISCOUNT<25){
            if(is_null($order->DeliveryNowFee)){
                $_AUTO_DELIVERY_FEE=25-$_SUBTOTAL_WITH_DISCOUNT;
            }
        }


        if($_DELIVERY_NOW_FEE>=0&&$_DELIVERY_NOW_FEE!==NULL)
            $_AUTO_DELIVERY_FEE=0;// Si on a un price Delivery now >> cela efface le Auto Delivery




        //Total = SubTotal inc Discount + Failed delivery + DeliveryNowFee + AutoDeliveryFee


        $_TOTAL=$_SUBTOTAL_WITH_DISCOUNT+$_FAILED_DELIVERY_PRICE+(!is_null($_DELIVERY_NOW_FEE)?$_DELIVERY_NOW_FEE:0)+$_AUTO_DELIVERY_FEE;

          //TotalDue = Total - SUM(payements)
        $_TOTAL_DUE=$_TOTAL-$_AMOUNT_PAID;

        $_TOTAL_EXC_VAT=$_TOTAL/1.2;

        $_TAX_AMOUNT=$_TOTAL-$_TOTAL_EXC_VAT;


        if($cust->credit > 0){
            if($cust->credit >= $_TOTAL_DUE){
                $_TOTAL_DUE = 0;
            }else{
                $_TOTAL_DUE = $_TOTAL_DUE - $cust->credit;
            }
        }


        $values=array(
            'Subtotal'=>number_format($_SUBTOTAL,2),
            'SubtotalWithDiscount'=>number_format($_SUBTOTAL_WITH_DISCOUNT,2),//
            'bundles'=>number_format($_BUNDLES_DISCOUNT,2),
            'itemsTotal'=>number_format($_ITEMS_TOTAL,2),
            'Total'=>number_format($_TOTAL,2),
            'TotalDue'=>number_format($_TOTAL_DUE,2),
            'AutoDeliveryFee'=>number_format($_AUTO_DELIVERY_FEE,2),//
            'ExpressCharge'=>number_format($_EXPRESS_CHARGES_PRICE,2),
            'FailedDeliveryCharge'=>number_format($_FAILED_DELIVERY_PRICE,2),//
            'TotalExcVat'=>number_format($_TOTAL_EXC_VAT,2),//
            'TaxAmount'=> number_format($_TAX_AMOUNT,2),//
            'OrderDiscount' => number_format($_ORDER_DISCOUNT,2),
            'VoucherDiscount' => number_format($_VOUCHER_DISCOUNT,2),
        );



        if($order->detailed_at=='0000-00-00 00:00:00' || $_SUBTOTAL==0){
            $values['AccountDiscount'] = number_format($_ACCOUNT_DISCOUNT,2);
            $values['AccountDiscountPerc'] = $cust->discount;
        }
/*
        echo "<pre>";
        print_r($values);
        die();
*/
        DB::table('infoOrder')->where('id',$order_id)->update($values);

       return $values;
    }

    public function getCheckoutItems(Request $request){
        $order_id = $request->order_id;
        $order = DB::table('infoOrder')->where('id',$order_id)->first();

        /*
        if($order->PickupID !=''){
            $this->getVoucherAmount($order_id,false,true,true);
        }



        $this->calculateCheckout($order_id);
        $this->recalculateDryCleaningPrice($order_id);
        */

        $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        $tranches = Tranche::getDeliveryPlanningTranchesForApi();

        $addr = null;
        $cust_card = null;

        $booking_details = [];
        $amount_to_pay = 0;
        $balance = 0;
        $amount_paid_card = [];
        $amount_paid_credit = [];
        $amount_paid_other = [];
        $discount_perc = 0;
        $credit_to_deduct = 0;
        $cust_discount = 0;
        $order_addons = 0;
        $order_without_upcharges = 0;
        $amount_without_credit = 0;
        $discount_from_voucher = 0;
        $order_vouchers = [];

        if($order){

            $master_account = false;

            $cust = DB::table('infoCustomer')->where('CustomerID',$order->CustomerID)->first();
            if($cust->CustomerIDMaster!=''){
                $master_account = DB::table('infoCustomer')->where('CustomerID',$cust->CustomerIDMaster)->first();
            }

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
                    $cust->OnAccount = $master_cust->OnAccount;
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
                    ->select('users.name as user','booking_histories.created_at','booking_histories.booking_id')
                    ->join('users','booking_histories.user_id','users.id')
                    ->where('booking_histories.order_id',$order->id)->where('booking_histories.status','NEW')
                    ->latest('booking_histories.id')
                    ->first();

                if($master_account && $order->deliverymethod=='delivery_only'){
                    if($order->DeliveryaskID!=''){
                        $delivery_ask = DB::table('deliveryask')->where('DeliveryaskID',$order->DeliveryaskID)->first();
                    }

                    if($order->PickupID !=''){
                        $delivery_ask = DB::table('pickup')->where('PickupID',$order->PickupID)->first();
                    }

                }

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

                    $booking_details['pickup_date'] = '';
                    $booking_details['pickup_day'] = '';
                    $booking_details['pickup_time'] = '';

                    if($pickup){
                        $pickup_stamp = strtotime($pickup->date);
                        $pickup_day_index = date('w',$pickup_stamp);
                        $pickup_slot = Tranche::getSlotFromTranche($pickup->trancheFrom,$pickup->trancheto);

                        $booking_details['pickup_date'] = date('d/m',$pickup_stamp);
                        $booking_details['pickup_day'] = $days[$pickup_day_index];
                        $booking_details['pickup_time'] = $tranches[$pickup_slot];
                    }

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

            $order_vouchers = DB::table('vouchers_histories')->where('order_id',$order_id)->get();

            if(count($order_vouchers) > 0){
                foreach($order_vouchers as $k=>$v){
                    $discount_from_voucher+= $v->amount;
                }
            }

        }

        $all_typeitem = [];
        $count_by_typeitem = [];
        $all_services = [];
        $count_by_service = [];

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
                $issue_names[$v->id] = $v->name;
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

                $all_typeitem[] = $v->typeitem_id;

                $allservices = @json_decode($v->cleaning_services);

                if(is_array($allservices) && !empty($allservices)){
                    foreach($allservices as $id=>$serv){
                        $all_services[] = $serv;
                    }
                }

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
                $items[$k]->damagesissues = @json_decode($v->damagesissues);
                $items[$k]->stainsissue = @json_decode($v->stainsissue);



                $items[$k]->detailed_services = [];

                if($v->cleaning_services!='' && !is_null($v->cleaning_services) && is_array(@json_decode($v->cleaning_services)) && !empty($v->cleaning_services)){
                     $dc = [];
                    $dc_arr = @json_decode($v->cleaning_services);

                    foreach($dc_arr as $id=>$val){
                        $dc[] = $cs[$val];
                    }

                    $c_price = number_format($v->dry_cleaning_price + $v->cleaning_addon_price,2);

                    if($v->cleaning_price_type=='PriceNow'){
                        $c_price = "Price now";
                    }elseif($v->cleaning_price_type=='Quote'){
                        $c_price = 'Quote';
                    }

                    if(!empty($dc)){
                        $items[$k]->detailed_services[] = [
                            'name'=>"Dry cleaning (".implode(",",$dc).")",
                            'price'=>$c_price,
                        ];
                    }
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
                            $t_price = number_format(array_sum($prices),2);
                            if($v->tailoring_price_type=='PriceNow'){
                                $t_price = 'Price now';
                            }else if($v->tailoring_price_type=='Quote'){
                                $t_price = 'Quote';
                            }


                            $t_arr = [
                                'name'=>$group,
                                'price'=>$t_price,
                            ];

                            $items[$k]->detailed_services[] = $t_arr;
                        }
                    }
                }

            }

            $count_by_typeitem = array_count_values($all_typeitem);
            $count_by_service = array_count_values($all_services);
        }

        $order_without_upcharges = $total_price;
        $order_addon = 0;
        $failed_delivery_price = 0;

        $upcharges = DB::table('order_upcharges')->where('order_id',$order_id)->get();
        if(count($upcharges) > 0){
            foreach($upcharges as $k=>$v){
                if($v->upcharges_id==3){
                    $failed_delivery_price = $v->amount;
                }else{
                    $order_addon += $v->amount;
                }
            }
        }

        /*
        if($order->express==1){
            $express_addon = $total_price * 0.4;
        }elseif($order->express==6){
            $express_addon = $total_price * 0.2;
        }
        */

        /*
        $total_price = $total_price + $order_addon;

        $total_with_discount = $total_price;

        if($cust->discount > 0){
            $cust_discount = ($cust->discount/100) * $total_price;
            $total_with_discount = $total_with_discount - $cust_discount;
        }

        if($discount_from_voucher > 0){
            $total_with_discount = $total_with_discount - $discount_from_voucher;
        }

        $order_discount = ($order->DiscountPerc/100) * $total_price;
        $discount_perc = $order->DiscountPerc;

        if($order_discount > 0){
            $total_with_discount = $total_with_discount - $order_discount;
        }

        $total_with_discount = $order->Total;

*/

        //Bundles
        $order_bundles = [];
        $bundles_discount = 0;
        $bundles_id = DetailingController::checkOrderBundles($order_id);

        if(!empty($bundles_id)){
            $bundles = DB::table('bundles')->whereIn('id',$bundles_id)->get();

            foreach($bundles as $k=>$v){
                $bundle_type =  $v->type;

                if(isset(${"count_by_".$bundle_type}[$v->bundles_id]) && ${"count_by_".$bundle_type}[$v->bundles_id]>=$v->qty){
                    $order_bundles[$v->name] = number_format($v->discountbyitem*${"count_by_".$bundle_type}[$v->bundles_id],2);
                    $bundles_discount += $v->discountbyitem*${"count_by_".$bundle_type}[$v->bundles_id];
                }

                /*
                if($v->type=='service' && isset($count_by_service[$v->bundles_id]) && $count_by_service[$v->bundles_id]>=$v->qty){
                    $order_bundles[$v->name] = number_format($v->discountbyitem,2);
                    $bundles_discount += $v->discountbyitem;
                }
                */

                //To add for service
            }
        }

/*
        $total_with_discount = $total_with_discount - $bundles_discount;

        $total_inc_vat = $total_with_discount;

        $total_exc_vat = number_format(($total_with_discount/1.2),2);
        $vat = $total_inc_vat - $total_exc_vat;



        $total_with_discount = $total_inc_vat;
        $price_plus_delivery = $total_inc_vat + $failed_delivery_price;
*/
        $payments = DB::table('payments')->where('order_id',$order->id)->where('status','succeeded')->get();

        $amount_paid = 0;

        if(count($payments) > 0){
            foreach($payments as $k=>$v){
                $amount_paid += number_format($v->montant,2);

                if($v->type=='cust_credit'){
                    $amount_paid_credit[] = [
                        'montant'=> number_format($v->montant,2),
                        'date'=>date('F d, Y',strtotime($v->created_at))." at ".date('g:i A',strtotime($v->created_at)),
                    ];
                }else{
                    if($v->card_id != 0){
                        $card = DB::table('cards')->where('id',$v->card_id)->whereNotNull('cards.type')->first();
                        if($card){
                            $amount_paid_card[] = [
                                'montant'=> number_format($v->montant,2),
                                'date'=>date('F d, Y',strtotime($v->created_at))." at ".date('g:i A',strtotime($v->created_at)),
                                'cardNumber'=>$card->cardNumber,
                                'type'=>$card->type,
                                'card_id'=>$v->card_id
                            ];
                        }
                    }else{
                        $amount_paid_other[] = [
                            'montant'=> number_format($v->montant,2),
                            'date'=>date('F d, Y',strtotime($v->created_at))." at ".date('g:i A',strtotime($v->created_at)),
                            'name'=>ucfirst($v->type),
                        ];
                    }
                }
            }

            //$balance = $order->Total;//number_format($total_with_discount,2) - number_format($amount_paid,2);

        }


        $balance = $order->Total - $amount_paid;

        $amount_without_credit = $balance;
        $amount_to_pay = $balance;

        if($cust->credit >= $balance){
            $amount_to_pay = 0;
            $credit_to_deduct = $balance;
        }else{
            $amount_to_pay = $balance - $cust->credit;
            $credit_to_deduct = $cust->credit;
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
            $created_date = date('F d, Y',strtotime($has_new_inv->created_at))." at ".date('g:i A',strtotime($has_new_inv->created_at));
        }


        $stripe_security_key = 'STRIPE_LIVE_SECURITY_KEY';
        $stripe_public_key = 'STRIPE_LIVE_PUBLIC_KEY';

        $stripe_test = env('STRIPE_TEST');

        if($stripe_test){
            $stripe_security_key = 'STRIPE_TEST_SECURITY_KEY';
            $stripe_public_key = 'STRIPE_TEST_PUBLIC_KEY';
        }

        $grouped_addons = [];
        $addons = DB::table('upcharges')
            ->select('upcharges.*','upcharges_category.name as category')
            ->join('upcharges_category','upcharges.category_id','upcharges_category.id')
            ->where('deleted',0)
            ->get();

        if(count($addons) > 0){
            foreach($addons as $k=>$v){
                $grouped_addons[$v->category][] = $v;
            }
        }

        $order_upcharges = [];
        $ou = DB::table('order_upcharges')->where('order_id',$order_id)->get();
        if(count($ou) > 0){
            foreach($ou as $k=>$v){
                $order_upcharges[] = $v->upcharges_id;
            }
        }

        if($cust){
            $cust->credit_to_deduct = number_format($credit_to_deduct,2);
            $cust_amount_diff =  DetailingController::getAmountToPay($order_id);
            $cust->amount_diff =number_format($cust_amount_diff,2);
        }

        $has_invoices = [];

        if($order){
            $has_invoices = DB::table('infoInvoice')->where('OrderID',$order->OrderID)->get();
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
            //'sub_total'=>number_format($total_price,2),
            'bundles'=>$order->bundles,
            //'total_with_discount'=>number_format($total_with_discount,2),
            //'discount'=>number_format($order_discount,2),
            //'vat'=>number_format($vat,2),
            //'total_inc_vat'=>number_format($total_inc_vat,2),
            //'total_exc_vat'=>number_format($total_exc_vat,2),
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
            'credit_to_deduct'=>number_format($credit_to_deduct,2),
            'cust_discount'=>$cust_discount,
            'order_addon'=>$order_addon,
            'order_without_upcharges'=>$order_without_upcharges,
            'amount_without_credit'=>number_format($amount_without_credit,2),
            'amount_diff'=>DetailingController::getAmountToPay($order_id),
            'addons'=>$grouped_addons,
            'order_upcharges'=>$order_upcharges,
            'order_vouchers'=>$order_vouchers,
            'discount_from_voucher'=>$discount_from_voucher,
            'master_account'=>$master_account,
            'failed_delivery_price'=>number_format($failed_delivery_price,2),
            //'price_plus_delivery'=>number_format($price_plus_delivery,2),
            'order_bundles'=>$order_bundles,
            'has_invoices'=>$has_invoices,
            'amount_paid_other'=>$amount_paid_other,
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

        DB::table('infoOrder')->where('id',$order_id)->where('Status','<>','FULFILLED')->update(['DiscountPerc'=>$discount]);

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

    public function setPriceNow(Request $request){
        $type = $request->type;
        $id = $request->id;
        $montant = $request->montant;

        $updated = false;

        if($type=='tailoring'){
            $updated = DB::table('detailingitem')->where('id',$id)->update([
                'tailoring_price_type'=>'PriceNow',
                'tailoring_price'=>$montant,
                'updated_at'=>date('Y-m-d H:i:s'),
                'describeprixnowtailoring'=> null
            ]);
        }
        if($type=='cleaning'){
            $updated = DB::table('detailingitem')->where('id',$id)->update([
                'cleaning_price_type'=>'PriceNow',
                'dry_cleaning_price'=>$montant,
                'cleaning_addon_price'=>0,
                'updated_at'=>date('Y-m-d H:i:s'),
                'describeprixnow'=> null
            ]);
        }

        return response()->json([
            'updated'=>$updated,
        ]);
    }

    public function setDescribeAndPriceNow(Request $request){
        $type = $request->type;
        $id = $request->id;
        $montant = $request->montant;
        $describeprixnow = $request->describeprixnow;

        $updated = false;

        if($type=='tailoring'){
            $updated = DB::table('detailingitem')->where('id',$id)->update([
                'tailoring_price_type'=>'PriceNow',
                'tailoring_price'=>$montant,
                'describeprixnowtailoring'=>$describeprixnow,
                'updated_at'=>date('Y-m-d H:i:s')
            ]);
        }
        if($type=='cleaning'){
            $updated = DB::table('detailingitem')->where('id',$id)->update([
                'cleaning_price_type'=>'PriceNow',
                'dry_cleaning_price'=>$montant,
                'describeprixnow'=>$describeprixnow,
                'cleaning_addon_price'=>0,
                'updated_at'=>date('Y-m-d H:i:s')
            ]);
        }

        return response()->json([
            'updated'=>$updated,
        ]);
    }

    public static function getAmountToPay($order_id){
        $amount_to_pay = 0;

        $amount_paid = 0;
        $order = DB::table('infoOrder')->where('id',$order_id)->first();
        $payments = DB::table("payments")->where('order_id',$order_id)->where('status','succeeded')->get();
        if(count($payments) > 0){
            foreach($payments as $k=>$v){
                $amount_paid += $v->montant;
            }
        }

        $amount_to_pay = $order->Total - $amount_paid;

        return number_format($amount_to_pay,2);
    }

    public function setCheckoutAddon(Request $request){
        $order_id = $request->order_id;
        $addon_id = $request->addon_id;
        $selected = $request->selected;
        $exp_addons_to_remove = json_decode($request->exp_addons_to_remove);

        $updated = false;

        if($selected){
            $order_total = 0;
            $items = DB::table('detailingitem')->where('order_id',$order_id)->get();
            if(count($items) > 0){
                foreach($items as $k=>$v){
                    $item_total = $v->dry_cleaning_price+$v->cleaning_addon_price+$v->tailoring_price;
                    $order_total += $item_total;
                }
            }

            $amount = 0;

            $addon = DB::table('upcharges')->where('id',$addon_id)->first();
            if($addon){
                if($addon->type=='fixed'){
                    $amount = $addon->amount;
                }
                if($addon->type=='perc'){
                    $amount = ($addon->amount/100)*$order_total;
                }
            }

            if(!empty($exp_addons_to_remove)){
                DB::table('order_upcharges')->whereIn('upcharges_id',$exp_addons_to_remove)->delete();
            }

            $updated = DB::table('order_upcharges')->updateOrInsert(
                ['order_id'=>$order_id,'upcharges_id'=>$addon_id],
                [
                'order_id'=>$order_id,
                'upcharges_id'=>$addon_id,
                'user_id'=>Auth::user()->id,
                'amount'=>number_format($amount,2),
                'created_at'=>date('Y-m-d H:i:s'),

            ]);
            if($addon_id==3)
                DB::table('infoOrder')->where('id',$order_id)->where('Status','<>','FULFILLED')->update(['FailedDelivery'=>1]);

            if($addon_id !=4){
                DB::table('infoOrder')->where('id',$order_id)->update(['DeliveryNowFee'=>null]);
            }
        }else{
            $updated = DB::table('order_upcharges')->where('order_id',$order_id)->where('upcharges_id',$addon_id)->delete();
            if($addon_id==3)
            DB::table('infoOrder')->where('id',$order_id)->where('Status','<>','FULFILLED')->update(['FailedDelivery'=>0]);
        }

        return response()->json([
            'post'=>$request->all(),
            'updated'=>$updated,
        ]);
    }

    public function updateIssuesText(Request $request){
        $detailingitem_id = $request->detailingitem_id;
        $stains_text = $request->stains_text;
        $damages_text = $request->damages_text;
        $updated = false;

        if(isset($stains_text)){
            $updated = DB::table('detailingitem')
                ->where('id',$detailingitem_id)
                ->update([
                    'stainstext'=>$stains_text,
                    'updated_at'=>date('Y-m-d H:i:s')
                ]);
        }

        if(isset($damages_text)){
            $updated = DB::table('detailingitem')
                ->where('id',$detailingitem_id)
                ->update([
                    'damagestext'=>$damages_text,
                    'updated_at'=>date('Y-m-d H:i:s')
                ]);
        }

        return response()->json([
            'post'=>$request->all(),
            'updated'=>$updated,
        ]);
    }

    public static function getVoucherAmount($order_id,$input_code=false,$insert=true,$pre_checkout=false){
        $user = Auth::user();
        $code = "";
        $voucher = false;
        $voucher_valid = false;
        $voucher_already_used = [];
        $today = date('Y-m-d');
        $err = "";
        $montant = 0;
        $inserted = 0;
        $is_booking_voucher = false;

        $order = DB::table('infoOrder')->where('id',$order_id)->first();

        if($input_code){
            $code = $input_code;
        }else{
            if(isset($order->PickupID) && $order->PickupID !=''){
                $pickup = DB::table('pickup')->where('PickupID',$order->PickupID)->first();

                if($pickup){

                    $gi = $pickup->GarmentInstruction;

                    if(@json_decode($gi) && is_object(@json_decode($gi))){
                        $garment_instruction = @json_decode($gi);

                        if(isset($garment_instruction->Voucher)){
                            $code = strtoupper($garment_instruction->Voucher);
                            $is_booking_voucher = true;
                        }
                    }
                }
            }
        }

        if($order->Total==0){
            $err = "Order amount is 0";
        }

        if($code !=''){
            $voucher = DB::table('vouchers')->where('Actif',1)->where('CodeCustomer',$code)->first();

            if(!$voucher){
                $err = "Invalid voucher code";
            }
        }

        if($err =="" && $voucher){
            if($voucher->StartDate <= $today && $voucher->EndDate >= $today){

                if($voucher->VoucherValidOnce==1){
                    $voucher_already_used = DB::table('vouchers_histories')
                        ->where('CustomerID',$order->CustomerID)
                        ->where('code',$voucher->CodeCustomer)
                        ->get();

                    if(count($voucher_already_used) > 0){
                        //Do nothing, Voucher already used
                        $err = "Voucher already used";
                    }else{
                        //Can use voucher once
                        $voucher_valid = true;
                    }
                }else{
                    //Can use voucher many times
                    $voucher_valid = true;
                }
            }else{
                //Date range error
                $err = "Voucher date not valid";
            }


            if($voucher_valid){
                if(!is_null($voucher->typeitem) && is_array(@json_decode($voucher->typeitem))){
                    $items = DB::table('detailingitem')
                        ->where('order_id',$order_id)
                        ->whereIn('typeitem_id',@json_decode($voucher->typeitem))
                        ->get();

                    if(count($items) > 0){
                        foreach($items as $k=>$v){
                            $price = ($v->etape==11?$v->dry_cleaning_price:$v->pricecleaning)+$v->cleaning_addon_price+$v->tailoring_price;
                            $montant += ($voucher->pourcentrage/100)*$price;
                        }
                    }
                }
                else if(!is_null($voucher->brand) && is_array(@json_decode($voucher->brand))){
                    $items = DB::table('detailingitem')
                    ->where('order_id',$order_id)
                    ->whereIn('brand_id',@json_decode($voucher->brand))
                    ->get();

                if(count($items) > 0){
                    foreach($items as $k=>$v){
                        $price = ($v->etape==11?$v->dry_cleaning_price:$v->pricecleaning)+$v->cleaning_addon_price+$v->tailoring_price;
                        $montant += ($voucher->pourcentrage/100)*$price;
                    }
                }
                }
                else{
                    $items = DB::table('detailingitem')
                        ->where('order_id',$order_id)
                        ->get();

                    if(count($items) > 0){
                        foreach($items as $k=>$v){
                            $price = ($v->etape==11?$v->dry_cleaning_price:$v->pricecleaning)+$v->cleaning_addon_price+$v->tailoring_price;
                            $montant += ($voucher->pourcentrage/100)*$price;
                        }
                    }
                }

            //if($user){
                    if($montant > 0 && $order->Total > $montant){

                        if($insert){
                            $has_voucher = DB::table('vouchers_histories')
                                ->where('vouchers_id',$voucher->id)
                                ->where('CustomerID',$order->CustomerID)
                                ->where('order_id',$order->id)
                                ->where('code',$code)
                                ->first();

                            if(!$has_voucher){
                                $inserted = DB::table('vouchers_histories')->insertGetId([
                                    'vouchers_id'=>$voucher->id,
                                    'CustomerID'=>$order->CustomerID,
                                    'order_id'=>$order_id,
                                    'code'=>$code,
                                    'amount'=>number_format($montant,2),
                                    'user_id'=>($user?$user->id:0),
                                    'created_at'=>date('Y-m-d H:i:s')
                                ]);
                            }

                        }
                    }else{
                        $err = "Voucher not applicable for this order";
                    }
             /*   }else{
                    $err = "No user logged";
                }*/

            }

        }

        $arr = [
            'err'=>$err,
            'montant'=>number_format($montant,2),
            'voucher_valid'=>$voucher_valid,
            'code'=>$code,
            'inserted'=>$inserted,
            'voucher'=>$voucher,
        ];

        if(!$pre_checkout)
            return $arr;

    }

    public function removeCheckoutVoucher(Request $request){
        $code = $request->code;
        $order_id = $request->order_id;
        $voucher_id = $request->voucher_id;

        $deleted = DB::table('vouchers_histories')->where('order_id',$order_id)->where('vouchers_id',$voucher_id)->delete();

        $this->calculateCheckout($order_id);

        return response()->json([
            'deleted'=>$deleted,
        ]);
    }

    public function addCheckoutVoucher(Request $request){
        $response = DetailingController::getVoucherAmount($request->order_id,$request->voucher);

        return response()->json([
            //'post'=>$request->all(),
            'output'=>$response,
        ]);
    }

    public static function getInstructionsFromPickup($pickupid){
        $categories = [
            'Garment'=>'Garment Care',
            'Tailor'=>'Tailoring',
            'Home'=>'Home & Bedding',
            'Wash'=>'Wash & Fold',
            'Leather'=>'Leather, Suede & fur',
            'DonateBags'=>'Donations',
        ];


        $formatted_instructions = [];
        $arranged_arr = [];
        $flat_arr = [];

        $item = false;

        $item = DB::table('pickup')
            ->select('GarmentInstruction as bag_instruction')
            ->where('PickupID',$pickupid)
            ->first();


        if($item) {

            $bag_instruction = mb_ereg_replace("\r?\n|\r", "", $item->bag_instruction);

            if(is_object(@json_decode($bag_instruction))){

            $instructions = (array)json_decode($bag_instruction);

            $donate_bags = '';
            $voucher = '';
            foreach ($instructions as $k => $v) {
                if ($k == 'DonateBags') {
                    $donate_bags = $v;
                }
                if ($k == 'Voucher') {
                    $voucher = $v;
                }
            }


            foreach ($instructions as $k => $v) {
                $key = trim($k);
                $formatted_instructions[$key] = $v;

                if($k!='Voucher') {
                    $key = trim($k);
                    $formatted_instructions[$key] = $v;

                    if($k=='DonateBags'){
                        $formatted_instructions[$key] = [];
                        $obj = new stdClass();
                        $obj->Item = $donate_bags;
                        $obj->Brand = '';
                        $obj->Instructions = [];
                        $obj->Category = $key;
                        $formatted_instructions[$key][0] = $obj;
                    }else {
                        foreach ($v as $i => $x) {
                            $formatted_instructions[$key][$i]->Category = $key;

                            if (!isset($formatted_instructions[$key][$i]->Item)) {
                                $formatted_instructions[$key][$i]->Item = '';
                            }

                            if (!isset($formatted_instructions[$key][$i]->Instructions)) {
                                $formatted_instructions[$key][$i]->Instructions = [];
                            }

                            if (!isset($formatted_instructions[$key][$i]->Brand)) {
                                $formatted_instructions[$key][$i]->Brand = '';
                            }

                            if (!isset($formatted_instructions[$key][$i]->Comment)) {
                                $formatted_instructions[$key][$i]->Comment = '';
                            }


                        }
                    }
                }
            }

            foreach($categories as $id=>$val){
                if(isset($formatted_instructions[$id])){
                    $arranged_arr[$id] = $formatted_instructions[$id];
                }else{
                    $obj = new stdClass();
                    $obj->Item = "";
                    $obj->Brand = "";
                    $obj->Instructions = [];
                    $obj->Comment = "";
                    $obj->Category = $id;

                    $arranged_arr[$id][] = $obj;
                }
            }

            $already_logged_cat = [];

            foreach($arranged_arr as $k=>$v){
                foreach($v as $i=>$x){
                    $x->Actions = "";
                    if(is_array($x->Instructions)){
                        if(!empty($x->Instructions)){
                            $x->Actions = implode(', ',$x->Instructions);
                        }
                    }else{
                        $x->Actions = $x->Instructions;
                    }

                    $flat_arr[] = $x;
                }
            }

            foreach($flat_arr as $k=>$v){
                if(!in_array($v->Category,$already_logged_cat)){
                    array_push($already_logged_cat,$v->Category);
                }else{
                    $flat_arr[$k]->Category = "";
                }
            }

            $item->instructions = $flat_arr;
            $item->voucher = $voucher;
            }

        }

        return $item;
    }

    public static function checkOrderBundles($order_id){
        $items = DB::table('detailingitem')->where('order_id',$order_id)->get();


        $bundles = DB::table('bundles')
            ->where('active',1)
            ->where('datefrom','<=',date('Y-m-d'))
            ->where('dateto','>=',date('Y-m-d'))
            ->get();

        $test_arr = [];
        $sel_bundle_id = [];

        foreach($bundles as $k=>$v){
            $test_arr[] = [
                'type'=>$v->type,
                'typeservice'=>$v->typeservice,
                'bundles_id'=>$v->bundles_id,
                'qty'=>$v->qty,
                'id'=>$v->id,
            ];
        }


        $grouped_by_typeitem = [];
        $count_per_typeitem = [];

        $grouped_by_cleaning_service = [];
        $count_by_cleaning_service = [];

        $grouped_by_tailoring_service = [];
        $count_by_tailoring_service = [];

        foreach($items as $k=>$v){
            $grouped_by_typeitem[$v->typeitem_id][] = $v->id;

            $cs = @json_decode($v->cleaning_services);
            foreach($cs as $id=>$idservice){
                $grouped_by_cleaning_service[$idservice][] = $v->id;
            }

            $ts = @json_decode($v->tailoring_services);
            foreach($ts as $id=>$idservice){
                $grouped_by_tailoring_service[$idservice][] = $v->id;
            }
        }

        foreach($grouped_by_typeitem as $k=>$v){
            $count_per_typeitem[$k] = count($v);
        }

        foreach($grouped_by_cleaning_service as $k=>$v){
            $count_by_cleaning_service[$k] = count($v);
        }

        foreach($grouped_by_tailoring_service as $k=>$v){
            $count_by_tailoring_service[$k] = count($v);
        }

        //1st Case - Count by typeitem
        foreach($count_per_typeitem as $typeitem=>$count){
            foreach($test_arr as $k=>$v){
                if($v['type']=='typeitem' && $v['bundles_id']==$typeitem && $count >= $v['qty']){
                    $sel_bundle_id[] = $v['id'];
                }
            }
        }

        //2nd Case - Count by cleaning services
        foreach($count_by_cleaning_service as $service=>$count){
            foreach($test_arr as $k=>$v){
                if($v['type']=='service' && $v['typeservice']=='cleaning' && $v['bundles_id']==$service && $count >= $v['qty']){
                    $sel_bundle_id[] = $v['id'];
                }
            }
        }

        //3rd case - Count by tailoring services
        foreach($count_by_tailoring_service as $service=>$count){
            foreach($test_arr as $k=>$v){
                if($v['type']=='service' && $v['typeservice']=='tailoring' && $v['bundles_id']==$service && $count >= $v['qty']){
                    $sel_bundle_id[] = $v['id'];
                }
            }
        }

        return $sel_bundle_id;
    }

    public function getPreferenceCustomer(Request $request){

        $prefrenceActive= null;
        $cust = DB::table('infoCustomer')->where('infoCustomer.id' , '=' , $request->Customer_id)->first();

        $prferenceIds = DB::table('InfoCustomerPreference')->select('InfoCustomerPreference.id_preference')
        ->where('InfoCustomerPreference.CustomerID' , '=' , $cust->CustomerID)
        ->where('InfoCustomerPreference.Value' , '=' , 1)
        ->get();

        if(!empty($prferenceIds)){

            foreach($prferenceIds as $key=>$value){
                $prference = DB::table('preferencetypitem')
                                   ->where('preferencetypitem.customerpreferences_id','=' ,$value->id_preference )
                                   ->where('preferencetypitem.typitem_id','=' , $request->typeitem_id )->first();
                if($prference != null){
                    $pref = DB::table('cleaningservices')->select("cleaningservices.id")->where('id_preference', $prference->customerpreferences_id)->first();
                    $prefrenceActive [] =(string)$pref->id ;
                }
            }
        }
        return response()->json([
            "prferenceIds" => $prferenceIds,
            "prefrenceActive" =>$prefrenceActive
        ]);

    }

    public static function calculateCleaningPrice($cs,$baseprice){
        $cleaning_services = DB::table('cleaningservices')->whereIn('id',$cs)->get();
        $services = [];

        foreach($cleaning_services as $k=>$v){
            $services[$v->id] = [
                'perc'=>$v->perc,
                'fixed_price'=>$v->fixed_price
            ];
        }

        $price_std = 0;
        $price_ozone = 0;


        if(in_array(1,$cs) || in_array(2,$cs) || in_array(3,$cs)){

            if(in_array(1,$cs) && in_array(3,$cs)){
                //100%
                $price_std = $baseprice;
            }

            if(in_array(1,$cs) && !in_array(3,$cs) && !in_array(2,$cs)){
                $price_std = $baseprice*($services[1]['perc']/100);
            }
            if(!in_array(1,$cs) && !in_array(3,$cs) && in_array(2,$cs)){
                $price_std = $baseprice*($services[2]['perc']/100);
            }
            if(!in_array(1,$cs) && in_array(3,$cs) && !in_array(2,$cs)){
                $price_std = $baseprice*($services[3]['perc']/100);
            }

        }else{
            foreach($services as $k=>$v){
                if(isset($services[$k])){
                    if($v['perc']==0){
                        $price_ozone += $v['fixed_price'];
                    }
                }
            }
        }



        $total = $price_std + $price_ozone;

        return $total;
    }

    public function preCalculateCheckout(Request $request){
        $order_id = $request->order_id;
        $order = DB::table('infoOrder')->where('id',$order_id)->first();


        if($order->PickupID !=''){
            $this->getVoucherAmount($order_id);
        }

        $this->calculateCheckout($order_id);
        $this->recalculateDryCleaningPrice($order_id);


        return response()->json([
            'post'=>$request->all(),
        ]);
    }

}
