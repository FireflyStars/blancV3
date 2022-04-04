<?php

namespace App\Http\Controllers;
use App\Models\Poste;
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

        $date_less_six_month = date('Y-m-d',strtotime('-6month'));

        // if($route_name=='item-qc'){
        //     $inv = DB::table('infoInvoice')
        //         ->select('infoInvoice.Client', 'infoInvoice.NumInvoice','infoCustomer.Phone','infoInvoice.SubOrderID','infoInvoice.OrderID','infoInvoice.StoreName','infoInvoice.InvoiceID','infoInvoice.CustomerID')
        //         ->join('infoitems','infoitems.InvoiceID','infoInvoice.InvoiceID')
        //         ->join('infoCustomer','infoInvoice.CustomerID','infoCustomer.CustomerID')
        //         ->where('infoInvoice.InvoiceID','!=','')
        //         ->where('infoitems.id_items',$invoice_id)
        //         ->first();
        // }elseif($route_name=='invoice-item' || $route_name=='assembly-invoice'){
        if($route_name == 'ItemDetails'){
            $inv = DB::table('infoInvoice')
                    ->select('infoInvoice.Client', 'infoInvoice.NumInvoice','infoCustomer.Phone','infoInvoice.SubOrderID','infoInvoice.OrderID', 'infoInvoice.StoreName','infoInvoice.InvoiceID','infoInvoice.CustomerID')
                    ->join('infoCustomer','infoInvoice.CustomerID','infoCustomer.CustomerID')
                    ->where('infoInvoice.id', $invoice_id)
                    ->first();
        }
        if($route_name == 'CustomerDetail'){
            $inv = DB::table('infoInvoice')
                    ->select('infoInvoice.Client', 'infoInvoice.NumInvoice','infoCustomer.Phone','infoInvoice.SubOrderID','infoInvoice.OrderID', 'infoInvoice.StoreName','infoInvoice.InvoiceID','infoInvoice.CustomerID')
                    ->join('infoCustomer','infoInvoice.CustomerID','infoCustomer.CustomerID')
                    ->whereIn('infoInvoice.id',$invoice_id)
                    ->get();
        }
        // }else{
        //     $inv = DB::table('infoInvoice')
        //     ->select('infoInvoice.Client', 'infoInvoice.NumInvoice','infoCustomer.Phone','infoInvoice.SubOrderID','infoInvoice.InvoiceID', 'infoInvoice.StoreName', 'infoInvoice.OrderID','infoInvoice.CustomerID')
        //     ->join('infoCustomer','infoInvoice.CustomerID','infoCustomer.CustomerID')
        //     ->where('infoInvoice.NumInvoice',$invoice_id)
        //     ->orderBy('infoInvoice.id','DESC')
        //     ->first();
        // }

        if($inv){
            $user = Auth::user();

            $inv->user_initials = (!is_null($user->UserInitials)?$user->UserInitials:$user->name);

            //Customer preferences
            $preferences = [];

            $cust_pref = DB::table('InfoCustomerPreference')
                ->select('InfoCustomerPreference.*','customerpreferences.preference_type')
                ->join('customerpreferences','InfoCustomerPreference.id_preference','customerpreferences.id')
                ->where('InfoCustomerPreference.CustomerID',$inv->CustomerID)
                ->where('InfoCustomerPreference.Delete',0)
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

            $inv->poste_details = "";
            // if($route_name=='item-qc'){
            //     $poste = Poste::find($poste_id);
            //     $inv->poste_details = $poste->nominterface." ";
            // }

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

            if($order){
                $date_add = date('d/m/Y H:i:s',strtotime($order->created_at));
                $order->date_add = $date_add;
                $order->date_due = "";

                //if($order->DateDeliveryAsk != '01-01-2020')

                $due_date_arr = explode("-",$order->DateDeliveryAsk);
                $date_due = $due_date_arr[2]."-".$due_date_arr[1];

                $order->date_due = $date_due;

            }

            $inv->order = $order;
            $inv->storecode = $storecode;
        }

        return response()->json([
            'inv'   =>  $inv,
        ]);
    }
}
