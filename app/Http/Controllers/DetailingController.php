<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            ->select('infoitems.NoBag', 'infoInvoice.NumInvoice as sub_order', 'typeitem.name as typeitem_name', 'infoitems.ItemTrackingKey as item_number', 'detailingitem.etape', 'detailingitem.pricecleaning as price')
            ->join('typeitem', 'typeitem.id', 'detailingitem.typeitem_id')
            ->join('infoitems', 'infoitems.ItemTrackingKey', '=', 'detailingitem.item_id')
            ->join('infoInvoice', 'infoInvoice.SubOrderID', '=', 'infoitems.SubOrderID')
            ->where('detailingitem.order_id', '=', $order_id)
            ->orderBy('infoitems.NoBag')
            ->orderBy('infoInvoice.NumInvoice')
            ->get();

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
        $detailingitem_id = $request->post('detailingitem_id');
        $user = Auth::user();

        $detailingitem = DB::table('detailingitem')
            ->where('order_id', '=', $order_id)
            ->where('item_id', '=', $item_id)
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
                $detailingitem_id = DB::table('detailingitem')->insertGetId(
                    [
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
                    ]
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
            ->where('item_id', '=', $item_id)
            ->get();
        $detailingitem = (array) $detailingitem->first();

        $item_description = $this->getItemDescription($detailingitem);
        $detailing_data = $this->getDetailingData($detailingitem['department_id'], $detailingitem['typeitem_id']);

        echo json_encode(
            [
                'user' => $user,
                'detailingitem' => $detailingitem,
                'search' => $search,
                'searched_item' => $searched_item,
                'item_description' => $item_description,
                'detailing_data' => $detailing_data
            ]
        );
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
        $brand_name = $request->post('brand_name');

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
        if (isset($step)) {
            if ($step == 3 && isset($typeitem_id)) {
                $sizes = DB::table('sizes')->where('typeitem_id', $typeitem_id)->where('deleted_at', NULL)->get();
                $step= count($sizes) == 0 ? $step+1 : $step;
            }
            DB::table('detailingitem')->where('id', $detailingitem_id)->update(['etape' => $step, 'updated_at' => date('Y-m-d H:i:s')]);
        }
        $detailingitem = DB::table('detailingitem')->where('id', '=', $detailingitem_id)->get();
        $detailingitem = (array) $detailingitem->first();

        $detailing_data = $this->getDetailingData($detailingitem['department_id'], $detailingitem['typeitem_id']);
        $item_description = $this->getItemDescription($detailingitem);

        echo json_encode(
            [
                'detailingitem' => $detailingitem,
                'item_description' => $item_description,
                'detailing_data' => $detailing_data
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
        return $detailing_data;
    }
    public function getItemDescription(array $detailingitem)
    {
        if ($detailingitem['typeitem_id'] != null) {
            $typeitem = DB::table('typeitem')->where('id', '=', $detailingitem['typeitem_id'])->get();
            $typeitem = (array) $typeitem->first();
        }
        if ($detailingitem['size_id'] != null) {
            $size = DB::table('sizes')->where('id', '=', $detailingitem['size_id'])->get();
            $size = (array) $size->first();
        }
        if ($detailingitem['brand_id'] != null) {
            $brand = DB::table('brands')->where('id', '=', $detailingitem['brand_id'])->get();
            $brand = (array) $brand->first();
        }
        if ($detailingitem['fabric_id'] != null) {
            $fabrics = DB::table('fabrics')->select('Name as name', 'coefcleaning', 'coeftailoring')->whereIn('id', json_decode($detailingitem['fabric_id']))->get();
            // $fabric = (array) $fabric->first();
        }
        if ($detailingitem['color_id'] != null) {
            $colors = DB::table('colours')->select('name', 'code')->whereIn('id', json_decode($detailingitem['color_id']))->get();
        }
        if ($detailingitem['pattern_id'] != null) {
            $pattern = DB::table('patterns')->where('id', '=', $detailingitem['pattern_id'])->get();
            $pattern = (array) $pattern->first();
        }
        if ($detailingitem['condition_id'] != null) {
            $condition = DB::table('conditions')->where('id', '=', $detailingitem['condition_id'])->get();
            $condition = (array) $condition->first();
        }
        if ($detailingitem['complexities_id'] != null) {
            $complexities = DB::table('complexities')->select('name', 'coefcleaning', 'coeftailoring')->whereIn('id', json_decode($detailingitem['complexities_id']))->get();
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
        );
    }
}
