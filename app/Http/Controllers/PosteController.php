<?php

namespace App\Http\Controllers;
use App\Poste;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Conveyor;

use Illuminate\Http\Request;

class PosteController extends Controller
{
    /**
     * get cert and private_key
     *
     */
    public function getSiteKeys(Request $request){
        $cert = DB::table('settings')->where('key', 'site.site_certificate')->value('value');
        $private_key = DB::table('settings')->where('key', 'site.site_private_key')->value('value');;

        return response()->json([
            'cert'          =>  str_replace('\r\n','\\n" +\n"', $cert),
            'private_key'   =>  str_replace('\r\n','\\n" +\n"', $private_key)
        ]);
    }

    /**
     *
     */

    public function getSubOrderToPrint(Request $request){
        $invoice_id = $request->post('invoice_id');
        $route_name = $request->post('route_name');
        $poste_id = $request->post('poste_id');

        $inv = PosteController::getSubOrderDataToPrint($invoice_id,$route_name,$poste_id);

        return response()->json([
            'inv'   =>  $inv,
        ]);
    }

    public static function getSubOrderDataToPrint($invoice_id,$route_name,$poste_id,$print_user=false){

        $date_less_six_month = date('Y-m-d',strtotime('-6month'));

        if($route_name=='item-qc'){
            $inv = DB::table('infoInvoice')
                    ->select('infoInvoice.Client','infoCustomer.Name as customer_name' , 'infoInvoice.NumInvoice','infoCustomer.Phone','infoInvoice.SubOrderID','infoInvoice.OrderID', 'infoInvoice.StoreName','infoInvoice.InvoiceID','infoInvoice.CustomerID')
                    ->join('infoCustomer','infoInvoice.CustomerID','infoCustomer.CustomerID')
                    ->where('infoInvoice.id', $invoice_id)
                    ->first();
        }
        if($route_name == 'CustomerDetail'){
            $inv = DB::table('infoInvoice')
                    ->select('infoInvoice.Client', 'infoCustomer.Name as customer_name' ,'infoInvoice.NumInvoice','infoCustomer.Phone','infoInvoice.SubOrderID','infoInvoice.OrderID', 'infoInvoice.StoreName','infoInvoice.InvoiceID','infoInvoice.CustomerID')
                    ->join('infoCustomer','infoInvoice.CustomerID','infoCustomer.CustomerID')
                    ->where('infoInvoice.id',$invoice_id)
                    ->first();
        }
        if($route_name == 'OrderDetails'){
            $inv = DB::table('infoInvoice')
                    ->select('infoInvoice.Client','infoCustomer.Name as customer_name' ,  'infoInvoice.NumInvoice','infoCustomer.Phone','infoInvoice.SubOrderID','infoInvoice.OrderID', 'infoInvoice.StoreName','infoInvoice.InvoiceID','infoInvoice.CustomerID')
                    ->join('infoCustomer','infoInvoice.CustomerID','infoCustomer.CustomerID')
                    ->where('infoInvoice.InvoiceID',$invoice_id)
                    ->first();
        }
        if($route_name == 'ItemDetails'){
            $inv = DB::table('infoInvoice')
                    ->select('infoInvoice.Client','infoCustomer.Name as customer_name' , 'infoInvoice.NumInvoice','infoCustomer.Phone','infoInvoice.SubOrderID','infoInvoice.OrderID', 'infoInvoice.StoreName','infoInvoice.InvoiceID','infoInvoice.CustomerID')
                    ->join('infoCustomer','infoInvoice.CustomerID','infoCustomer.CustomerID')
                    ->where('infoInvoice.InvoiceID',$invoice_id)
                    ->first();
        }
        if($inv){

            $user = Auth::user();

            if($print_user){
                $user = DB::table('users')->where('id',$print_user)->first();
            }
            $inv->user_initials = "";
            if($user){
                $inv->user_initials = (!is_null($user->UserInitials)?$user->UserInitials:$user->name);
            //Customer preferences
            }
            $preferences = [];

            $cust_pref = DB::table('InfoCustomerPreference')
                ->select('InfoCustomerPreference.*','customerpreferences.preference_type')
                ->join('customerpreferences','InfoCustomerPreference.id_preference','customerpreferences.id')
                ->where('InfoCustomerPreference.Delete',0)
                ->where('InfoCustomerPreference.CustomerID', "=" ,$inv->CustomerID)
                ->get();


            if(count($cust_pref) > 0){
                foreach($cust_pref as $k=>$v){
                    if($v->preference_type=='switch' && $v->Value !=0){
                        $preferences[$v->Titre] = 'Yes';
                    }
                    if($v->preference_type=='radio'){
                        if((in_array($v->id_preference,[2,7]) && $v->Value !='Always') || $v->id_preference==1 || ($v->id_preference==9 && $v->Value !='Standard')){
                            $preferences[$v->Titre] = $v->Value;
                        }
                    }

                }
            }

            $inv->customer_preferences = $preferences;



            $inv->customer_preferences = $preferences;

            $inv->poste_details = "";
            if($route_name=='item-qc'){
                $poste = Poste::find($poste_id);
                $inv->poste_details = $poste->nominterface." ";
            }

            $inv->poste_details .= date('D d/m/y H:i');

            $inv->phone_num = json_decode($inv->Phone);

            $items = DB::table('infoitems')->where('InvoiceID',$inv->InvoiceID)->get();

            $storecode = "";

            $inv_items = [];
            $has_six_month_items = [];

            if(count($items) > 0){
                foreach($items as $k=>$v){
                    $itempost = DB::table('infoitemspost')->where('id_items',$v->id_items)->first();
                    $comment = "";

                    if($v->PromisedDate <= $date_less_six_month){
                        $has_six_month_items[] = $v->ItemTrackingKey;
                    }

                    if($itempost) {
                        $comment = $itempost->comment;
                    }

                    $v->item_comment = ($comment!=''?json_decode($comment):[]);

                    $v->totalPrice = number_format($v->priceTotal,2);
                    $inv_items[] = $v;

                    $storecode = Conveyor::getStoreCodeByName($v->StoreName);
                }
            }

            $inv->items = $inv_items;
            $inv->count_six_month_items = count($has_six_month_items);

            $order = DB::table('infoOrder')->where('OrderID',$inv->OrderID)->first();
            $inv->order_total_due = 0;
            $inv->order_discount = 0;

            if($order){
                $date_add = date('d/m/Y H:i:s',strtotime($order->created_at));
                $order->date_add = $date_add;
                $order->date_due = "";

                //if($order->DateDeliveryAsk != '01-01-2020')

                $due_date_arr = explode("-",$order->DateDeliveryAsk);
                $date_due = $due_date_arr[2]."-".$due_date_arr[1];

                $order->date_due = $date_due;

                $inv->order_total_due = $order->Total;
                $inv->order_discount = $order->OrderDiscount;

            }

            $inv->order = $order;
            $inv->storecode = $storecode;
        }

        return $inv;
    }

    public static function getOrderToPrint(Request $request){
        $order_id = $request->order_id;
        $print_user = $request->userid;

        $order = DB::table('infoOrder')->where('id',$order_id)->first();

        $discount_perc = 0;
        $cust_discount = 0;
        $order_without_upcharges = 0;
        $discount_from_voucher = 0;
        $codes_voucher = [];
        $order_vouchers = [];

        if($order){



            $cust = DB::table('infoCustomer')->where('CustomerID',$order->CustomerID)->first();

            if($cust){
                $cust->cust_type = "";
                $cust->pay_name = $cust->Name;
                $cust->Phone = json_decode($cust->Phone );
                $cust_id = $cust->CustomerID;
                if($cust->CustomerIDMaster !=''){
                    $cust_id = $cust->CustomerIDMaster;
                    $master_cust = DB::table('infoCustomer')->where('CustomerID',$cust->CustomerIDMaster)->first();
                    $cust->pay_name = $master_cust->Name;
                    $cust->OnAccount = $master_cust->OnAccount;
                }


                        //Customer preferences
                    $preferences = [];

                    $cust_pref = DB::table('InfoCustomerPreference')
                        ->select('InfoCustomerPreference.*','customerpreferences.preference_type')
                        ->join('customerpreferences','InfoCustomerPreference.id_preference','customerpreferences.id')
                        ->where('InfoCustomerPreference.Delete',0)
                        ->where('InfoCustomerPreference.CustomerID', "=" ,$order->CustomerID)
                        ->get();


                    if(count($cust_pref) > 0){
                        foreach($cust_pref as $k=>$v){
                            if($v->preference_type=='switch' && $v->Value !=0){
                                $preferences[$v->Titre] = 'Yes';
                            }
                            if($v->preference_type=='radio'){
                                if((in_array($v->id_preference,[2,7]) && $v->Value !='Always') || $v->id_preference==1 || ($v->id_preference==9 && $v->Value !='Standard')){
                                    $preferences[$v->Titre] = $v->Value;
                                }
                            }

                        }
                    }
                $cust->customer_preferences = $preferences;
            }


            $order_vouchers = DB::table('vouchers_histories')->where('order_id',$order_id)->get();

            if(count($order_vouchers) > 0){
                foreach($order_vouchers as $k=>$v){
                    $discount_from_voucher+= $v->amount;
                    $codes_voucher[] = $v->code;
                }
            }

        }

        $items = DB::table('detailingitem')
            ->select('detailingitem.*','detailingitem.id AS detailingitem_id','typeitem.pricecleaning as baseprice')
            ->join('infoOrder','detailingitem.order_id','infoOrder.id')
            ->join('typeitem','detailingitem.typeitem_id','typeitem.id')
            ->where('infoOrder.id',$order_id)
            ->get();

        $color_map = [];
        $colors = DB::table('colours')->get();
        if(count($colors) > 0){
            foreach($colors as $k=>$v){
                $color_map[$v->id] = $v->name;
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

        $cs = [];
        $cleaning_services = DB::table('cleaningservices')->get();
        if(count($cleaning_services) > 0){
            foreach($cleaning_services as $k=>$v){
                $cs[$v->id] = $v->name;
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
                'name' =>$v->name,
            ];
        }

        $total_price = 0;
        $total_price_item = 0;

        if(count($items) > 0){
            foreach($items as $k=>$v){

                $services = [];
                //Tailoring
                if($v->tailoring_services !='' && !is_null($v->tailoring_services) && is_array(@json_decode($v->tailoring_services)) && !empty(@json_decode($v->tailoring_services))){
                    $services[] = 'Tailoring';
                    $ts_arr = @json_decode($v->tailoring_services);
                    foreach($ts_arr as $id=>$val){
                        if(isset($cs[$val])){
                            $services[] = $cs[$val];
                        }
                    }
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
                $total_price_item += $item_total_price;

                $items[$k]->priceCleaningTotal = number_format(($v->dry_cleaning_price+$v->cleaning_addon_price),2);
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


                $items[$k]->clean_services = [];
                $items[$k]->tail_services = [];

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
                        $items[$k]->clean_services[] = [
                            'name'=>"".implode(",",$dc)."",
                            'price'=>$c_price,
                        ];
                    }
                }

                //To add grouped tailoring prices
                if($v->tailoring_services !='' && !is_null($v->tailoring_services) && is_array(@json_decode($v->tailoring_services)) && !empty(@json_decode($v->tailoring_services))){
                    $group_by_tailoring_service = [];
                    $tailoing_services_price_by_group = [];

                    $ts = @json_decode($v->tailoring_services);
                    $tail_services = [];
                    foreach($ts as $id=>$val){
                        $tail_services[] = $grouped_tailoring_services[$val]['name'];
                        if(isset($grouped_tailoring_services[$val])){
                            $gp = $grouped_tailoring_services[$val];
                            $group_by_tailoring_service[$gp['group']][] = $gp['price'];
                        }
                    }
                    $items[$k]->tail_services[] = "".implode(",",$tail_services)."";
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
                        }
                    }
                }else{
                    $items[$k]->tailoring_services = @json_decode($v->tailoring_services);

                }

            }

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



        //Bundles
        $order_bundles = [];
        $bundles_discount = 0;
        $bundles_id = DetailingController::checkOrderBundles($order_id);

        if(!empty($bundles_id)){
            $bundles = DB::table('bundles')->whereIn('id',$bundles_id)->get();

            foreach($bundles as $k=>$v){
                $order_bundles[$v->name] = number_format($v->discount,2);
                $bundles_discount += $v->discount;
            }
        }

        $total_with_discount = $total_with_discount - $bundles_discount;

        $total_inc_vat = $total_with_discount;

        $total_exc_vat = number_format(($total_with_discount/1.2),2);
        $vat = $total_inc_vat - $total_exc_vat;



        $total_with_discount = $total_inc_vat;
        $price_plus_delivery = $total_inc_vat + $failed_delivery_price;

        $created_date = "";

        $has_new_inv = DB::table('NewInvoice')->where('order_id',$order_id)->latest('id')->first();

        if($has_new_inv){
            $created_date = date('F d, Y',strtotime($has_new_inv->created_at))." at ".date('g:i A',strtotime($has_new_inv->created_at));
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

        $top_ticket = DB::table('settings')->where('key', 'admin.topticket')->value('value');
        $Footer_ticket = DB::table('settings')->where('key', 'admin.footerticket')->value('value');

        $cur_user = Auth::user();
        if(isset($print_user)){
            $cur_user = DB::table('users')->where('id',$print_user)->first();
        }

        return response()->json([
            'items'=>$items,
            'cust'=>$cust,
            'order'=>$order,
            'sub_total'=>number_format($total_price,2),
            'bundles'=>$order->bundles,
            'total_with_discount'=>number_format($total_with_discount,2),
            'discount'=>number_format($order_discount,2),
            'vat'=>number_format($vat,2),
            'total_inc_vat'=>number_format($total_inc_vat,2),
            'total_exc_vat'=>number_format($total_exc_vat,2),
            'cur_user'=>$cur_user,
            'discount_perc'=>$discount_perc,
            'created_date'=>$created_date,
            'cust_discount'=>$cust_discount,
            'order_addon'=>$order_addon,
            'order_without_upcharges'=>$order_without_upcharges,
            'amount_diff'=>DetailingController::getAmountToPay($order_id),
            'addons'=>$grouped_addons,
            'order_upcharges'=>$order_upcharges,
            'order_vouchers'=>$order_vouchers,
            'discount_from_voucher'=>$discount_from_voucher,
            'failed_delivery_price'=>number_format($failed_delivery_price,2),
            'price_plus_delivery'=>number_format($price_plus_delivery,2),
            'order_bundles'=>$order_bundles,
            'total_price_item'=>number_format($total_price_item,2),
            'codes_voucher'=>implode(",",$codes_voucher),
            'TopTicket' => $top_ticket,
            'FooterTicket' => $Footer_ticket,
        ]);
    }

    /**
     * Get Customer info to print ticket
     */
    public function getCustomerToPrint(Request $request){
        $customer = DB::table('infoCustomer')->where('CustomerID', $request->CustomerID)
                    ->select('Name', 'Phone', 'commentDelivery', 'TypeDelivery','CompanyName', 'EmailAddress')
                    ->first();
        $customer->address = DB::table('address')->where('CustomerID', $request->CustomerID)
                    ->select('County', 'Town', 'postcode', 'address1', 'address2', 'name')->first();
        $customer->deliveryPreference = DB::table('DeliveryPreference')->where('CustomerID', $request->CustomerID)
                    ->select('TypeDelivery', 'CodeCountry', 'PhoneNumber', 'OtherInstruction', 'Name')->first();
        $customer->user = auth()->user()->name;
        $customer->date = now()->format('d/m/Y H:i');
        return response()->json($customer);
    }
}
