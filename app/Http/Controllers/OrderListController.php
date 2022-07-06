<?php

namespace App\Http\Controllers;

use App\Models\Conveyor;
use App\Models\Tranche;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Holiday;

class OrderListController extends Controller
{
    public $holidays;

    public function isDateHoliday($date){
        return in_array($date,$this->holidays);
    }
    public function isDateFuture($date){
        $d1 = Carbon::createFromFormat('Y-m-d', $date);
        return !$d1->isPast();
    }

    public function getorderlist(Request $request){
        $skip=$request->get('skip');
        $take=$request->get('take');
        $current_tab=$request->get('current_tab');
        $sort=$request->get('sort');
        $filters=$request->get('filters');
        if($current_tab != 'customer_care'){
            $orderlist=DB::table('infoOrder')
              
                ->select( [
                    'infoOrder.id','infoOrder.Status','infoOrder.Total', 'infoitems.id as item_id',
                    'infoCustomer.Name','infoCustomer.TypeDelivery', 'infoitems.PromisedDate',
                    'infoOrder.DateDeliveryAsk','infoInvoice.datesold','infoOrder.DatePickup', 'infoCustomer.DeliverybyDay',
                DB::raw('count(distinct(infoInvoice.id)) as subOrderCount'),
                DB::raw('GROUP_CONCAT(infoitems.express) as express'),
                DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Prod'),
                DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Deliv'),
                DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),
            ])
            ->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')
            ->leftJoin('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
            ->leftJoin('infoitems',function($join){
                $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                    ->where('infoitems.InvoiceID','!=','')
                    ->whereNotIn('infoitems.Status',['DELETE','VOID']);
            })
            ->where('infoOrder.OrderID','!=','')
            ->whereNotIn('infoOrder.Status',['VOID', 'DELETE']);
        }else{
            $orderlist=DB::table('infoOrder')
                ->select( [
                    'infoOrder.id','infoOrder.Status','infoOrder.Total', 'infoitems.id as item_id','infoitems.PromisedDate',
                    'infoCustomer.Name as Customer','infoCustomer.TypeDelivery', 'infoInvoice.datesold',
                    DB::raw('GROUP_CONCAT(infoitems.express) as express'),
                    DB::raw('IF(infoOrder.Paid = 0, "unpaid", "paid") as paid'),
                    'infoitems.CCStatus as Action',
                    DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Prod'),
                    DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Deliv'),
                ])
                ->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')
                ->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
                ->where('infoOrder.OrderID','!=','')
                ->where('infoitems.CCStatus','!=','')
                ->join('infoitems',function($join){
                    $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                        ->where('infoitems.InvoiceID','!=','')
                        ->distinct('infoitems.InvoiceID')
                        ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                })
                ->where(
                    function($query) {
                        $query->where(function($query) {
                            $query->whereDate('infoOrder.DateDeliveryAsk', '<=', date('Y-m-d'));
                            $query->whereNotIn('infoOrder.Status', ['DELIVERED', 'DELIVERD TO STORE', 'SOLD', 'DONATED', 'DONATED TO CHARITY', 'COLLECTED', 'VOIDED', 'FULFILLED', 'VOID', 'DELETE', 'SOLD']);
                        })->orWhere(function($query){
                            $query->where('infoOrder.Paid', 0)->where('infoCustomer.TypeDelivery','=','DELIVERY');
                        })->orWhere(function($query){
                            $query->whereIn('infoOrder.Status',['LATE','LATE DELIVERY','OVERDUE FOR COLLECTION','MISSED PICKUP','OVERDUE STORE','FAILED DELIVERY','FAILED PAYMENT','PART ON HOLD','PART PENDING'])
                                ->where('infoOrder.DateDeliveryAsk','!=','2020-01-01');
                        });
                    });
        }

        if($current_tab=='with_partner')
        $orderlist=$orderlist->where('infoitems.idPartner','!=','0')
            ->where('infoitems.PartnerINOUT','=','1');

        if($current_tab=='due_today')
            $orderlist=$orderlist->whereDate('infoOrder.DateDeliveryAsk','=',date('Y-m-d'));

        if($current_tab=='due_tomorrow')
            $orderlist=$orderlist->whereDate('infoOrder.DateDeliveryAsk','=',date('Y-m-d',strtotime('tomorrow')));

        if($current_tab=='unfulfilled'){
            $orderlist=$orderlist->whereNotIn('infoOrder.Status',['FULFILLED','DELETE','DELIVERED','SOLD','VOID','CANCEL']);
        }
        if($current_tab=='without_delivery_date'){
            $orderlist=$orderlist->whereNotIn('infoOrder.status',['DELIVERED', 'DELIVERD TO STORE', 'SOLD', 'DONATED', 'DONATED TO CHARITY', 'COLLECTED','VOIDED','FULFILLED'])
                ->where('infoOrder.DateDeliveryAsk','<=',date('Y-m-d'))
                ->where('infoitems.ItemTrackingKey','<>','');
        }
        if($current_tab!='all_orders')
            $orderlist=$orderlist->whereNotIn('infoOrder.Status',['VOID', 'DELETE']);
        //filters


        if(!empty($filters))
            foreach($filters as $colname => $values){
                if($colname =='Customername'){ 
                  $orderlist=$orderlist->where('Name','LIKE','%'.$values['value'].'%' );
                } else 
                if($colname =='infoitems.express'){
                        $express=[];
                        if(in_array('standard',$values)){
                            $express=array_merge($express,[0,2,3]);
                        }
                    if(in_array('exp24',$values)){
                        $express=array_merge($express,[1,4,5]);
                    }
                    if(in_array('exp48',$values)){
                        $express=array_merge($express,[6]);
                    }
                    if(!empty($express))
                        $orderlist=$orderlist->whereIn($colname,$express);
                }else if( $colname !='infoitems.express' && $colname != 'infoitems.ProdDate' && $colname != 'infoitems.DelivDate'){
                    if(!empty($values))
                    $orderlist=$orderlist->whereIn($colname, $values);
                }else if($colname == 'infoitems.ProdDate' && !empty($values)){
                    $orderlist=$orderlist->whereBetween('infoitems.PromisedDate', [ $values[0], $values[1]]);
                }else if($colname == 'infoitems.DelivDate' && !empty($values)){
                    $orderlist=$orderlist->whereBetween('infoitems.PromisedDate', [ $values[0], $values[1]]);
                }else{

                }
            }

        $orderlist=$orderlist->groupBy('infoOrder.id');

        //sort
        if(!empty($sort)) {
            foreach ($sort as $columns)
                foreach ($columns as $column => $direction)
                    $orderlist = $orderlist->orderBy($column, $direction);
        }else{
            $orderlist = $orderlist->orderByDesc('infoOrder.id');
        }


        $orderlist=$orderlist->skip($skip)->take($take);
        $orderlist=$orderlist->get();
        // adding ready_sub_orders and deliv date
        foreach ($orderlist as $order) {
            // if(
            //     (Carbon::parse($order->PromisedDate)->gt(Carbon::now()) || Carbon::parse($order->PromisedDate)->gt(Carbon::now()->subMonth())) &&
            //     ($order->datesold == '' || $order->datesold == '2019-01-01 00:00:00')
            // ){

                // $order->Deliv = DB::table('infoOrder')
                //     ->leftJoin('pickup', 'pickup.CustomerID', '=', 'infoOrder.CustomerID')
                //     ->leftJoin('deliveryask', 'deliveryask.CustomerID', '=', 'infoOrder.CustomerID')
                //     ->where('infoOrder.id', $order->id)
                //     ->where('pickup.status', 'not like', '%DEL%')
                //     ->where('deliveryask.status', 'not like', '%DEL%')
                //     ->select(DB::raw('DATE_FORMAT(IF( MIN(pickup.date) > MIN(deliveryask.date), IF (MIN(deliveryask.date) <> "2020-01-01", MIN(deliveryask.date), MIN(pickup.date)), IF (MIN(pickup.date) <> "2020-01-01", MIN(pickup.date), MIN(deliveryask.date))), "%d/%m/%Y") AS Deliv'))
                //     ->value('Deliv');
                   
            
                // cas not Recurring 
                if($order->Status != "RECURRING"  && $order->datesold == '' &&  $order->DeliverybyDay == 0  &&  $order->Status != "FULFILLED" && $order->TypeDelivery == "DELIVERY"){
                    $order->Deliv = $order->DateDeliveryAsk;
                    $DateDeliveryAsk = Carbon::createFromFormat('Y-m-d', $order->DateDeliveryAsk)->format('Y-m-d'); 
                    $date = date_create($order->DateDeliveryAsk);
                    $lastDate = date_sub($date,date_interval_create_from_date_string("1 days"))->format('Y-m-d');
                    $this->holidays=Holiday::getHolidays();
                    if(Carbon::createFromFormat('Y-m-d', $lastDate)->format('l') != "Saturday" && Carbon::createFromFormat('Y-m-d', $lastDate)->format('l') != "Sunday" && $this->isDateHoliday($DateDeliveryAsk) == false){
                    $order->Prod = $order->DateDeliveryAsk;
                    } else {
                        $order->Prod = $lastDate;
                    }
                }
                // cas  Recurring 
                if($order->Status == "RECURRING"  &&  $order->DeliverybyDay = 1  &&  $order->Status != "FULFILLED" && $order->TypeDelivery == "DELIVERY"){

                        $pickupDate = strtotime(date('Y-m-d', strtotime($order->DatePickup) ) );
                        $DeliveryDate = strtotime(date('Y-m-d', strtotime($order->DateDeliveryAsk) ) );
                        $DateDeliveryAsk = Carbon::createFromFormat('Y-m-d', $order->DateDeliveryAsk)->format('Y-m-d'); 

                        if($pickupDate <  $DeliveryDate){
                            $order->Deliv = $order->DateDeliveryAsk;
                        } else {
                            $order->Deliv =$order->DatePickup;
                        }

                    $date = date_create($order->DateDeliveryAsk);
                    $lastDate = date_sub($date,date_interval_create_from_date_string("1 days"))->format('Y-m-d');
                    $this->holidays=Holiday::getHolidays();
                    if(Carbon::createFromFormat('Y-m-d', $lastDate)->format('l') != "Saturday" && Carbon::createFromFormat('Y-m-d', $lastDate)->format('l') != "Sunday" && $this->isDateHoliday($DateDeliveryAsk) == false){
                    $order->Prod = $order->DateDeliveryAsk;
                    } else {
                        $order->Prod = $lastDate;
                    }
                }
                // cas not Recurring AND Not Delivery
                if($order->Status != "RECURRING"  && $order->datesold == '' &&  $order->DeliverybyDay == 0  &&  $order->Status != "FULFILLED" && $order->TypeDelivery != "DELIVERY"){
                    $order->Deliv = $order->DateDeliveryAsk;
                    $order->Prod = $order->DateDeliveryAsk;
                }

                if($order->Status == "FULFILLED"  ){
                    $order->Deliv = $order->datesold;
                    $order->Prod = $order->datesold;
                }
            // }
        
            if( $order->Deliv =='' || $order->Deliv =='2019-01-01' || $order->Deliv =='2020-01-01' ){
                $order->Deliv = "--";
            }
            
            if( $order->Prod =='' || $order->Prod =='2019-01-01' || $order->Prod =='2020-01-01' ){
                $order->Prod = "--";
            }
           
            if($current_tab != 'customer_care'){
                $order->ready_sub_orders = DB::table('infoOrder')
                    ->join('infoInvoice', 'infoOrder.OrderID','=', 'infoInvoice.OrderID')
                    ->distinct('infoInvoice.InvoiceID')
                    ->where('infoOrder.id', $order->id)
                    ->where('infoInvoice.Status', 'READY')->count();
            }
        }
        return response()->json($orderlist);
    }

    public function cancelorders(Request $request){

        $orderids=$request->post('orderids');

        //PERFORM DELETE QUERY HERE
        //On passe L order infoOrder.status DELETE, on passe infoInvoice.status et infoItem.status en DELETE, et infoitem.nextpost=34
        if(!empty($orderids)){

            $infoOrders=DB::table('infoOrder')->select('OrderID')->whereIn('infoOrder.id',$orderids)->get();
            $infoOrdersIds=[];

            $infoOrders->each(function ($item, $key) use(&$infoOrdersIds){
                $infoOrdersIds[]=$item->OrderID;
            });

            $infoitems=DB::table('infoitems')->select('infoitems.id')->join('infoInvoice',function($join) use ($infoOrdersIds){

                        $join->on('infoitems.SubOrderID','=','infoInvoice.SubOrderID')
                            ->whereIn('infoInvoice.OrderID',$infoOrdersIds);
            });
            $infoitems=$infoitems->get();

            $infoitemsIds=[];
            $infoitems->each(function ($item, $key) use(&$infoitemsIds){
                $infoitemsIds[]=$item->id;
            });


            DB::table('infoitems')->whereIn('id',$infoitemsIds)->update([
                'Status'=>'DELETE',
                'nextpost'=>34
            ]);
            DB::table('infoInvoice')->whereIn('infoInvoice.OrderID',$infoOrdersIds)->update([
                'Status'=>'DELETE'
            ]);
            DB::table('infoOrder')->whereIn('infoOrder.OrderID',$infoOrdersIds)->update([
                'Status'=>'DELETE'
            ]);
        }
        return response()->json(['done'=>'ok']);
    }


    public  function  markaslate(Request $request){

        $orderids=$request->post('orderids');
        if(!empty($orderids)){
            DB::table('infoOrder')->whereIn('infoOrder.id',$orderids)->update([
                'Status'=>'LATE',
                'DateDeliveryAsk'=>'2020-01-01'
            ]);
        }
        return response()->json(['done'=>'ok']);
    }

    public function getorderdetail(Request $request){
        $infoOrder_id=$request->post('infoOrder_id');
        $order=DB::table('infoOrder')
            ->select(['infoOrder.id AS order_id','infoOrder.Status','infoOrder.Total','infoCustomer.Name','infoCustomer.TypeDelivery','infoCustomer.id' , 'infoCustomer.Phone','infoCustomer.CustomerID',DB::raw('IF(infoOrder.DateDeliveryAsk="2020-01-01" OR infoOrder.DateDeliveryAsk="2000-01-01" OR infoOrder.DateDeliveryAsk="","--",DATE_FORMAT(infoOrder.DateDeliveryAsk, "%a %d/%m")) as PromisedDate'),DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),'infoOrder.OrderID','infoOrder.suggestedDeliveryDate'])
            ->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')

            ->where('infoOrder.id','=',$infoOrder_id)->first();

            $billing_add=DB::table('address')->where('CustomerID','=',$order->CustomerID)->where('status','=','BILLING')->first();
            $delivery_add=DB::table('address')->where('CustomerID','=',$order->CustomerID)->where('status','=',$order->TypeDelivery)->first();

            $infoitems=DB::table('infoitems')->select(['infoInvoice.NumInvoice','infoInvoice.InvoiceID' , 'infoitems.id as infoitems_id','infoitems.brand','infoitems.ItemTrackingKey','infoitems.colors','infoitems.typeitem','infoitems.priceTotal','infoitems.status','postes.nominterface as station',])->join('infoInvoice',function($join) use($order){
               $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
               ->where('infoInvoice.OrderID','=',$order->OrderID);
            })->leftJoin('postes','postes.id','=','infoitems.nextpost')
                ->leftJoin('TypePost','TypePost.id','=','postes.TypePost')
                ->whereNotIn('infoitems.Status',['DELETE','VOID'])
                ->orderBy('infoInvoice.NumInvoice')->get();
        $items=[];
        $infoitems->each(function ($item) use(&$items) {
            $items[$item->NumInvoice][]=$item;//suborder grouping
        });

        if($order->Phone!=""){
            $order->Phone=json_decode($order->Phone);

        }
        $available_slots=[];
        if($order->TypeDelivery=='DELIVERY'&&$delivery_add!=null&&trim($delivery_add->postcode)!=''){
            $postcode = $delivery_add->postcode;

            $sel_postcode = "";

            $allpostcodes = DB::table('tranchepostcode')
                ->select(DB::raw('DISTINCT(Postcode) AS post_code'))
                ->get();

            $postcode = str_replace(' ','',$postcode);
            $postcode = substr($postcode,0,-3);

            if (count($allpostcodes) > 0) {
                foreach ($allpostcodes as $k=>$v){
                    $cur_postcode = $v->post_code;
                    if($postcode==$cur_postcode){
                        $sel_postcode = $cur_postcode;
                    }
                }
            }

            $tranche_details = [];
            $formatted_tranche = [];

            if($sel_postcode !=''){
                $tranche_details = DB::table('tranchepostcode')
                    ->where('Postcode',$sel_postcode)
                    ->get();
            }
            if(count($tranche_details) > 0){
                foreach($tranche_details as $k=>$v){
                    $details = json_decode($v->tranche);
                    $formatted_tranche[$v->day] = $details;
                    $slot_dates=Tranche::getAvailableSlotAndDates($v->day,$details,true,3);
                    foreach ($slot_dates as $slot_key=>$dates){
                        foreach ($dates as $date)
                            $available_slots[$date][]=$slot_key;
                    }

                }
            }
        }
        $detailingitemlist = DB::table('detailingitem')
        ->where('detailingitem.order_id', '=', $infoOrder_id)
        ->orderBy('detailingitem.id','ASC')
        ->get();

        return response()->json(['order'=>['detail'=>$order,'billing'=>$billing_add,'delivery'=>$delivery_add,'items'=>$items,'available_slots'=>$available_slots ,'detailingitemlist' => $detailingitemlist]] );
    }

     public function setInvoiceFulfilled(Request $request){

        $invoice_id = $request->post('suborderid');
        $endpoint = "http://blancspot.vpc-direct-service.com/fulfiled-v2.php";
        $client = new \GuzzleHttp\Client();
        $content = "";

        $user = Auth::user();

        $params = [
            'token'=>'GhtfvbbG4489hGtyEfgARRGht3',
            'invoiceid'=>$invoice_id,
            'userid'=>$user->id,
        ];

        $response = $client->request('GET', $endpoint, ['query' => $params]);

        $statusCode = $response->getStatusCode();
        @$content = $response->getBody();

        $content = str_replace('\"', '', $content);

        $output = @json_decode($content);

        if($output && isset($output->result) && $output->result=='ok'){
            $items = DB::table('infoitems')->where('InvoiceID',$invoice_id)->get();

            if(count($items) > 0){
                foreach($items as $k=>$v){

                    DB::table('production')->insert([
                                            'user_id'=>$user->id,
                                            'ID_item'=>$v->id_items,
                                            'item_id'=>$v->id,
                                            'poste_id'=> 28,
                                            'date_add'=>date('Y-m-d H:i:s')
                                         ]);
            }
        }
    }

        return \response()->json([
            'url'=>$endpoint."?token=GhtfvbbG4489hGtyEfgARRGht3&invoiceid=$invoice_id&userid=".$user->id,
            'output'=>$output,
            'status_code'=>$statusCode,
        ]);
    }

    public function SplitSubOrder(Request $request){

        $invoice_id = $request->post('suborderid');
        $items = $request->post('items');
        
        $endpoint = "http://blancspot.vpc-direct-service.com/split-v1.php";
        $arr = http_build_query($items,"item[");
        $array_item = serialize(preg_replace('/\[\d/', '\\0]', $arr));
        
        $client = new \GuzzleHttp\Client();
        $content = "";

        $user = Auth::user();

        $params = [
            'token'=>'GhtfvbbG44$hhGtyEfgARRGht3',
            'invoiceid'=>$invoice_id,
            $array_item

        ];

        $response = $client->request('GET', $endpoint, ['query' => $params]);
        
        $statusCode = $response->getStatusCode();


        @$content = $response->getBody();

        $content = str_replace('\"', '', $content);

        $output = @json_decode($content);


        return \response()->json([
            'url'=>$endpoint."?token=GhtfvbbG4489hGtyEfgARRGht3&invoiceid=.$invoice_id&".$array_item,
            'output'=>$output,
            'status_code'=>$statusCode,
        ]);
    }

    public function getitemdetail(Request $request){
        $itemInfo = DB::table('infoitems')
                      ->join('infoInvoice', 'infoitems.SubOrderID', '=', 'infoInvoice.SubOrderID')
                      ->join('infoCustomer', 'infoInvoice.CustomerID', '=', 'infoCustomer.CustomerID')
                      ->join('infoOrder','infoOrder.CustomerID','=','infoCustomer.CustomerID')
                      ->join('postes', 'infoitems.nextpost', '=', 'postes.id')
                      ->join('TypePost', 'TypePost.id', '=', 'postes.TypePost')
                      ->where('infoitems.id', $request->item_id)
                      ->select(
                          'infoitems.id', 'infoitems.ItemTrackingKey as item_key', 'infoInvoice.id as sub_order_id', 'infoInvoice.NumInvoice as sub_order', 'infoitems.Colors as colors', 'infoInvoice.id as invoice_id',
                          'infoitems.Fabrics as fabrics', 'infoitems.Patterns as patterns', 'infoitems.Size as size',
                          'infoitems.StoreName as store_name', 'infoitems.store', 'infoitems.damage', 'infoitems.id_items',
                          'infoitems.typeitem as item_name', 'TypePost.bg_color as location_color', 'postes.nom as location', 'TypePost.circle_color', 'TypePost.process',
                          'infoCustomer.Name as customer_name', 'infoCustomer.CustomerIDMaster', 'infoCustomer.CustomerIDMasterAccount',
                          'infoCustomer.IsMaster', 'infoCustomer.IsMasterAccount', 'postes.id as poste_id', 'infoOrder.id as order_id'
                          )->first();

        $location_history = DB::table('production')
                              ->join('postes', 'production.poste_id', '=', 'postes.id')
                              ->join('TypePost', 'TypePost.id', '=', 'postes.TypePost')
                              ->join('users', 'production.user_id', '=', 'users.id')
                              ->select(
                                    'TypePost.bg_color as location_color', 'postes.nom as location',
                                    'TypePost.process', 'TypePost.circle_color',
                                    // DB::raw('DATE_FORMAT(production.date_add,"%a") as day'),
                                    DB::raw('DATE_FORMAT(production.date_add,"%a %m/%d/%Y %H:%i") as date'),
                                    // DB::raw('DATE_FORMAT(production.date_add,"%H:%i") as time'),
                                    'users.name'
                                )
                              ->where('production.item_id', $request->item_id)
                              ->orderByDesc('production.date_add')
                              ->get();
        return response()->json([
            'item_detail'=>[
                'breif_info'        => $itemInfo,
                'location_history'  => $location_history
            ]
        ]);
    }


    public function suggestdate(Request $request){
            $infoOrder_id=$request->post('infoOrder_id');
            $suggested_delivery_date=$request->post('suggested_delivery_date');
            $user=Auth::user();
            $update=false;
            if($user->hasRoles(['admin','Blanc Admin','user'])){ // cc cannot suggest a date
                $update= DB::table('infoOrder')->where('id','=',$infoOrder_id)->update(
                    [
                        'suggestedDeliveryDate'=>$suggested_delivery_date
                    ]
                );
            }
        return response()->json(['updated'=>$update]);
    }

    public function newdeliverydate(Request $request){
        $infoOrder_id=$request->post('infoOrder_id');
        $PromisedDate=$request->post('PromisedDate');
        $timeslot=$request->post('timeslot');
        $user=Auth::user();
        $update=false;
        if($user->hasRoles(['admin','Blanc Admin','cc'])){ // Production operator cannot set a new delivery date

                $infoOrder=DB::table('infoOrder')->select(['CustomerID','DeliveryaskID'])->where('id','=',$infoOrder_id)->first();

                if($infoOrder==null)
                    return response()->json(['updated'=>$update,'message'=>'Order not found.']);


                $cust_details =  DB::table('infoCustomer')
                    ->select('infoCustomer.id AS customer_id','infoCustomer.*','address.*')
                    ->join('address','infoCustomer.CustomerID','address.CustomerID')
                    ->where('infoCustomer.CustomerID',$infoOrder->CustomerID)
                    ->whereColumn('address.status','=','infoCustomer.TypeDelivery')
                    ->first();
                if($cust_details==null)
                    return response()->json(['updated'=>$update,'message'=>'Customer or address not found.']);


                //retrieve comment on previous delivery ask
                $previous_delivery_ask=DB::table('deliveryask')->where('DeliveryaskID','=',$infoOrder->DeliveryaskID)->first();




                if($PromisedDate!='' && $timeslot!= '') {

                    $deliveryTimeTranche=Tranche::getFormattedTranche($timeslot);
                    $inserteddelivery= DB::table('deliveryask')
                        ->insertGetId(
                            [
                                'id_customer'=>$cust_details->customer_id,
                                'CustomerID'=>$cust_details->CustomerID,
                                'AddressID'=>$cust_details->AddressID,
                                'created_at'=>date('Y-m-d H:i:s'),
                                'updated_at'=>date('Y-m-d H:i:s'),
                                'comment'=>($previous_delivery_ask!=null?$previous_delivery_ask->comment:''),
                                'trancheto'=>$deliveryTimeTranche['trancheto'],
                                'trancheFrom'=>$deliveryTimeTranche['tranchefrom'],
                                'status'=>'PMS',
                                'date'=>$PromisedDate
                            ]
                        );
                    $deliveryAsk=DB::table('deliveryask')->where('id',$inserteddelivery)->first();
                    DB::table('infoOrder')->where('id',$infoOrder_id)->update(
                        [
                            'DeliveryaskID'=>$deliveryAsk->DeliveryaskID,
                            'DateDeliveryAsk'=>$deliveryAsk->date,
                            'Status'=>'IN PROCESS'
                        ]
                    );

                    $infoInvoices=DB::table('infoInvoice')
                        ->join('infoOrder',function ($join) {
                            $join->on('infoInvoice.OrderID', '=', 'infoOrder.OrderID')->where('infoOrder.OrderID','<>','');
                        })->where('infoOrder.id','=',$infoOrder_id)->select(['infoInvoice.InvoiceID'])->get();

                    $InvoiceIDs=[];
                    foreach ($infoInvoices as $infoInvoice) {
                        $InvoiceIDs[]=$infoInvoice->InvoiceID;
                    }
                    DB::table('infoitems')->whereIn('InvoiceID',$InvoiceIDs)->update([
                        'PromisedDate'=>$deliveryAsk->date,
                        'dateJour'=>date('l',strtotime($deliveryAsk->date))
                    ]);
                    $update=true;
                }






        }
        return response()->json(['updated'=>$update,'message'=>'']);
    }

    public function splititems(Request $request){
        $suborder_items= $request->post('items');
        $infoOrder_id= $request->post('infoOrder_id');
        $upsert = false;
        $infoOrder_idS=[];
        foreach ($suborder_items as $suborder=>$items) {
            //copied from blancpos
            foreach ($items as $item_id) {


                $item = DB::table('infoitems')
                    ->select('infoitems.id_items', 'infoOrder.id AS order_id', 'infoitems.PromisedDate', 'infoOrder.CustomerID', 'infoInvoice.*', 'infoitems.nextpost', 'infoitems.ItemTrackingKey', 'infoInvoice.Client')
                    ->join('infoInvoice', 'infoitems.SubOrderID', 'infoInvoice.SubOrderID')
                    ->join('infoOrder', 'infoInvoice.OrderID', 'infoOrder.orderID')
                    ->where('infoitems.id', $item_id)
                    ->first();

                if ($item) {

                    Conveyor::addLineIn($item->ItemTrackingKey, $item->NumInvoice, 'Split');


                    $other_items = DB::table('infoitems')
                        ->join('infoInvoice', 'infoitems.SubOrderID', 'infoInvoice.SubOrderID')
                        ->join('infoOrder', 'infoInvoice.OrderID', 'infoOrder.OrderID')
                        ->where('infoOrder.id', '=', $item->order_id)
                        ->where('infoitems.id', '!=', $item_id)
                        ->get();


                    if (count($other_items) > 0) { //si on a > 1 item pour le sub-order

                        //Create new order
                        $order = DB::table('infoOrder')->where('id', $item->order_id)->first();
                        $new_order = (array)$order;

                        foreach ($new_order as $k => $v) {
                            if (in_array($k, ['id', 'DatePickup', 'DateDeliveryAsk', 'PickupID', 'DeliveryAskID'])) {
                                unset($new_order[$k]);
                            }
                        }

                        $new_order_uuid = Str::uuid()->toString();

                        $new_order['created_at'] = date('Y-m-d H:i:s');
                        $new_order['updated_at'] = date('Y-m-d H:i:s');
                        $new_order['OrderID'] = $new_order_uuid;

                        $last_id_order_inserted = DB::table('infoOrder')->insertGetId($new_order);
                        $infoOrder_idS[]=$last_id_order_inserted;

                        //Create new suborder

                        $new_invoice = (array)$item;

                        foreach ($new_invoice as $k => $v) {
                            if (in_array($k, ['id_items', 'order_id', 'id', 'PromisedDate', 'InvoiceID', 'nextpost', 'ItemTrackingKey'])) {
                                unset($new_invoice[$k]);
                            }
                        }


                        $inv_part1 = date('m');


                        $last_id_split = 0;
                        $last_split = DB::table('split')->latest('id')->first();

                        if ($last_split) {
                            $last_id_split = $last_split->id;
                        }

                        $inv_part2 = 900000 + $last_id_split + 1;

                        $new_invoice_uuid = Str::uuid()->toString();
                        $new_invoice['NumInvoice'] = $inv_part1 . "-" . $inv_part2;
                        $new_invoice['date_add'] = date('Y-m-d H:i:s');
                        $new_invoice['OrderID'] = $new_order_uuid;
                        $new_invoice['id_invoice'] = 0;
                        $new_invoice['InvoiceID'] = $new_invoice_uuid;
                        $new_invoice['SubOrderID'] = '';

                        $last_id_invoice_inserted = DB::table('infoInvoice')->insertGetId($new_invoice);


                        $new_inv = DB::table('infoInvoice')->where('id', $last_id_invoice_inserted)->first();


                        if ($new_inv) {
                            $upsert = DB::table('infoitems')->where('id_items', $item->id_items)
                                ->update([
                                    'PromisedDate' => '2000-01-01',
                                    'InvoiceID' => $new_invoice_uuid,
                                    'id_invoice' => 0,
                                    'SubOrderID' => $new_inv->SubOrderID,
                                ]);

                            DB::table('infoOrder')->where('id', $item->order_id)->update(['Split' => 1]);
                        }

                        $upsert = DB::table('split')->insert([
                            'item_id' => $item_id,
                            'old_order_id' => $item->order_id,
                            'order_id' => $last_id_order_inserted,
                            'old_suborder_id' => $item->id_invoice,
                            'suborder_id' => $last_id_invoice_inserted,
                            'old_promised_date' => $item->PromisedDate,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);

                        Conveyor::addLineIn($item->ItemTrackingKey, $new_inv->NumInvoice, 'New Record');


                    } else {
                        //Mettre date item, order a 2000-01-01
                        $upsert = DB::table('infoitems')->where('id_items', $item->id_items)->update(['PromisedDate' => '2000-01-01']);
                    }


                }
            }
        }

        $order=DB::table('infoOrder')->where('id','=',$infoOrder_id)->first();

        $infoitems=DB::table('infoitems')->select(['infoInvoice.NumInvoice','infoitems.id as infoitems_id','infoitems.brand','infoitems.ItemTrackingKey','infoitems.colors','infoitems.typeitem','infoitems.priceTotal','infoitems.status','TypePost.Name as station',])->join('infoInvoice',function($join) use($order){
            $join->on('infoInvoice.SubOrderID','=','infoitems.SubOrderID')
                ->where('infoInvoice.OrderID','=',$order->OrderID);
        })->leftJoin('postes','postes.id','=','infoitems.nextpost')
            ->leftJoin('TypePost','TypePost.id','=','postes.TypePost')
            ->whereNotIn('infoitems.Status',['DELETE','VOID'])
            ->orderBy('infoInvoice.NumInvoice')->get();
        $items=[];
        $infoitems->each(function ($item) use(&$items) {
            $items[$item->NumInvoice][]=$item;//suborder grouping
        });

        return response()->json([
            'updated'=>$upsert,
            'items'=>$items,
            'add_order_to_list'=>$this->getOrders($infoOrder_idS)
        ]);
    }


    public function getOrders($infoOrder_idS=[]){

        $orderlist=DB::table('infoOrder')
            ->select(['infoOrder.id','infoOrder.Status','infoOrder.Total','infoCustomer.Name','infoCustomer.TypeDelivery',DB::raw('IF(infoOrder.DateDeliveryAsk="2020-01-01" OR infoOrder.DateDeliveryAsk="2000-01-01" OR infoOrder.DateDeliveryAsk="","--",DATE_FORMAT(infoOrder.DateDeliveryAsk, "%d/%m")) as PromisedDate'),DB::raw('count(infoitems.id) as numitems'),DB::raw('GROUP_CONCAT(infoitems.express) as express'),DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),'infoOrder.suggestedDeliveryDate'])
            ->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')
            ->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
            ->leftJoin('infoitems',function($join){
                $join->on('infoInvoice.SubOrderID','=','infoitems.SubOrderID')
                    ->where('infoitems.SubOrderID','!=','')
                    ->whereNotIn('infoitems.Status',['DELETE','VOID']);
            })
            ->whereIn('infoOrder.id',$infoOrder_idS)
            ->where('infoInvoice.OrderID','!=','');

            return $orderlist;



    }
    public function getOrdersByCustomerId(Request $request){
        $skip=$request->get('skip');
        $take=$request->get('take');
        $current_tab=$request->get('current_tab');
        $sort=$request->get('sort');
        $filters=$request->get('filters');
        $customerId = $request->get('customerID');

        if($current_tab != 'customer_care'){
            $orderlist=DB::table('infoOrder')
            ->select( [
                    'infoOrder.id','infoOrder.Status','infoOrder.Total', 'infoitems.id as item_id',
                    'infoCustomer.Name','infoCustomer.TypeDelivery', 'infoInvoice.datesold', 'infoitems.PromisedDate', 'infoCustomer.CustomerID',
                DB::raw('count(distinct(infoInvoice.id)) as subOrderCount'),
                DB::raw('GROUP_CONCAT(infoitems.express) as express'),
                DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Prod'),
                DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Deliv'),
                DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),
            ])
            ->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')
            ->leftJoin('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
            ->leftJoin('infoitems',function($join){
                $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                    ->where('infoitems.InvoiceID','!=','')
                    ->whereNotIn('infoitems.Status',['DELETE','VOID']);
            })
            ->where('infoOrder.OrderID','!=','')
            ->where('infoCustomer.CustomerID','=',$customerId)
            ->whereNotIn('infoOrder.Status',['VOID', 'DELETE']);
    

        }else{
            $orderlist=DB::table('infoOrder')
                ->select( [
                    'infoOrder.id','infoOrder.Status','infoOrder.Total', 'infoitems.id as item_id','infoitems.PromisedDate',
                    'infoCustomer.Name as Customer','infoCustomer.TypeDelivery', 'infoInvoice.datesold', 'infoCustomer.CustomerID',
                    DB::raw('GROUP_CONCAT(infoitems.express) as express'),
                    DB::raw('IF(infoOrder.Paid = 0, "unpaid", "paid") as paid'),
                    'infoitems.CCStatus as Action',
                    DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Prod'),
                    DB::raw('DATE_FORMAT(infoitems.PromisedDate, "%d/%m/%Y") as Deliv'),
                ])
                ->join('infoCustomer','infoOrder.CustomerID','=','infoCustomer.CustomerID')
                ->join('infoInvoice','infoOrder.OrderID','infoInvoice.OrderID')
                ->where('infoOrder.OrderID','!=','')
                ->where('infoitems.CCStatus','!=','')
                ->join('infoitems',function($join){
                    $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                        ->where('infoitems.InvoiceID','!=','')
                        ->distinct('infoitems.InvoiceID')
                        ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                })
                ->where(
                    function($query) {
                        $query->where(function($query) {
                            $query->whereDate('infoOrder.DateDeliveryAsk', '<=', date('Y-m-d'));
                            $query->whereNotIn('infoOrder.Status', ['DELIVERED', 'DELIVERD TO STORE', 'SOLD', 'DONATED', 'DONATED TO CHARITY', 'COLLECTED', 'VOIDED', 'FULFILLED', 'VOID', 'DELETE', 'SOLD']);
                        })->orWhere(function($query){
                            $query->where('infoOrder.Paid', 0)->where('infoCustomer.TypeDelivery','=','DELIVERY');
                        })->orWhere(function($query){
                            $query->whereIn('infoOrder.Status',['LATE','LATE DELIVERY','OVERDUE FOR COLLECTION','MISSED PICKUP','OVERDUE STORE','FAILED DELIVERY','FAILED PAYMENT','PART ON HOLD','PART PENDING'])
                                ->where('infoOrder.DateDeliveryAsk','!=','2020-01-01');
                        });
                    })
                  ->where('infoCustomer.CustomerID','=',$customerId);
        }

        if($current_tab=='with_partner')
        $orderlist=$orderlist->where('infoitems.idPartner','!=','0')
            ->where('infoitems.PartnerINOUT','=','1');

        if($current_tab=='due_today')
            $orderlist=$orderlist->whereDate('infoOrder.DateDeliveryAsk','=',date('Y-m-d'));

        if($current_tab=='due_tomorrow')
            $orderlist=$orderlist->whereDate('infoOrder.DateDeliveryAsk','=',date('Y-m-d',strtotime('tomorrow')));

        if($current_tab=='unfulfilled'){
            $orderlist=$orderlist->whereNotIn('infoOrder.Status',['FULFILLED','DELETE','DELIVERED','SOLD','VOID','CANCEL']);
        }
        if($current_tab=='without_delivery_date'){
            $orderlist=$orderlist->whereNotIn('infoOrder.status',['DELIVERED', 'DELIVERD TO STORE', 'SOLD', 'DONATED', 'DONATED TO CHARITY', 'COLLECTED','VOIDED','FULFILLED'])
                ->where('infoOrder.DateDeliveryAsk','<=',date('Y-m-d'))
                ->where('infoitems.ItemTrackingKey','<>','');
        }


        if($current_tab!='all_orders')
            $orderlist=$orderlist->whereNotIn('infoOrder.Status',['VOID', 'DELETE']);
        //filters


        if(!empty($filters))
            foreach($filters as $colname => $values){
                if($colname =='infoitems.express'){
                        $express=[];
                        if(in_array('standard',$values)){
                            $express=array_merge($express,[0,2,3]);
                        }
                    if(in_array('exp24',$values)){
                        $express=array_merge($express,[1,4,5]);
                    }
                    if(in_array('exp48',$values)){
                        $express=array_merge($express,[6]);
                    }
                    if(!empty($express))
                        $orderlist=$orderlist->whereIn($colname,$express);
                }else if( $colname !='infoitems.express' && $colname != 'infoitems.ProdDate' && $colname != 'infoitems.DelivDate'){
                    if(!empty($values))
                    $orderlist=$orderlist->whereIn($colname, $values);
                }else if($colname == 'infoitems.ProdDate' && !empty($values)){
                    $orderlist=$orderlist->whereBetween('infoitems.PromisedDate', [ $values[0], $values[1]]);
                }else if($colname == 'infoitems.DelivDate' && !empty($values)){
                    $orderlist=$orderlist->whereBetween('infoitems.PromisedDate', [ $values[0], $values[1]]);
                }else{

                }
            }

        $orderlist=$orderlist->groupBy('infoOrder.id');

        //sort
        if(!empty($sort)) {
            foreach ($sort as $columns)
                foreach ($columns as $column => $direction)
                    $orderlist = $orderlist->orderBy($column, $direction);
        }else{
            $orderlist = $orderlist->orderByDesc('infoOrder.id');
        }


        $orderlist=$orderlist->skip($skip)->take($take);
        $orderlist=$orderlist->get();
        // adding ready_sub_orders and deliv date
        foreach ($orderlist as $order) {
            if(
                (Carbon::parse($order->PromisedDate)->gt(Carbon::now()) || Carbon::parse($order->PromisedDate)->gt(Carbon::now()->subMonth())) &&
                ($order->datesold == '' || $order->datesold == '2019-01-01 00:00:00')
            ){
                $order->Deliv = DB::table('infoOrder')
                    ->leftJoin('pickup', 'pickup.CustomerID', '=', 'infoOrder.CustomerID')
                    ->leftJoin('deliveryask', 'deliveryask.CustomerID', '=', 'infoOrder.CustomerID')
                    ->where('infoOrder.id', $order->id)
                    ->where('pickup.status', 'not like', '%DEL%')
                    ->where('deliveryask.status', 'not like', '%DEL%')
                    ->select(DB::raw('DATE_FORMAT(IF( MIN(pickup.date) > MIN(deliveryask.date), IF (MIN(deliveryask.date) <> "2020-01-01", MIN(deliveryask.date), MIN(pickup.date)), IF (MIN(pickup.date) <> "2020-01-01", MIN(pickup.date), MIN(deliveryask.date))), "%d/%m/%Y") AS Deliv'))
                    ->value('Deliv');
            }
            if( $order->datesold !='' && $order->datesold !='2019-01-01 00:00:00' ){
                $order->Deliv = Carbon::parse($order->datesold)->format('d/m');
            }
            if($current_tab != 'customer_care'){
                $order->ready_sub_orders = DB::table('infoOrder')
                    ->join('infoInvoice', 'infoOrder.OrderID','=', 'infoInvoice.OrderID')
                    ->distinct('infoInvoice.InvoiceID')
                    ->where('infoOrder.id', $order->id)
                    ->where('infoInvoice.Status', 'READY')->count();
            }
        }
        return response()->json($orderlist);
    }
}
