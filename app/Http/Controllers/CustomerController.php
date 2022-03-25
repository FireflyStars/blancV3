<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function customerDetails(Request $request){
        $CustomerID=$request->post('CustomerID');
        $infoCustomer = null;

        if($CustomerID!=''){
            $infoCustomer=DB::table('infoCustomer')->where('infoCustomer.CustomerID','=',$CustomerID)
                ->leftJoin('address',function($join) {
                    $join->on( 'infoCustomer.CustomerID', '=', 'address.CustomerID')
                        ->where('address.status','DELIVERY');
                })
                ->first();
            $ltm_spend=DB::table('infoOrder')->select(['Total'])->where('CustomerID','=',$CustomerID)->where('Status','=','FULFILLED')->where('created_at','>=',date('Y-m-d',strtotime('-12 months')))->get();
            $spend=0;
            foreach ($ltm_spend as $order){
                $spend+=$order->Total;
            }
            $infoCustomer->ltm_spend=$spend;
        }

        return response()->json(['customer'=>$infoCustomer]);
    }

    /**
     * Get All customers 
     * Filter customers
     * 
     * @param skip, invoice_preference, customer_location, customer_type, last_order_start, last_order_end, total_spent
     * 
     * @return all_customers
     */
    public function getAllCustomers(Request $request){
        $customers = DB::table('infoCustomer')
                        ->join( 'address', function ($join){
                            $join->on( 'address.CustomerID', '=', 'infoCustomer.CustomerID')
                                 ->where('address.status', "DELIVERY");
                        })
                        ->Leftjoin( 'infoOrder', function ($join){
                            $join->on( 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID');
                                //  ->whereIn('infoOrder.Status', ["FULFILLED", "READY"]);
                        })
                        ->select(
                            'infoCustomer.id',
                            DB::raw('IF(infoCustomer.CustomerIDMaster = "" AND infoCustomer.CustomerIDMasterAccount = "" AND infoCustomer.IsMaster = 0 AND infoCustomer.IsMasterAccount = 0, "B2C", "B2B") as type'),
                            DB::raw('LCASE(infoCustomer.TypeDelivery) as active_in'), 
                            DB::raw('LCASE(infoCustomer.Name) as name'), 
                            DB::raw('CONCAT_WS(", ", CONCAT_WS(" ", address.address1, address.address2), address.Town, address.Country, address.postcode) as address'),
                            DB::raw('LCASE(infoCustomer.EmailAddress) as email'),
                            DB::raw('IF(infoCustomer.Phone = "", "--", infoCustomer.Phone) as phone'),
                            DB::raw('IF(DATE_FORMAT(MAX(infoOrder.created_at), "%d/%m/%y") = "", "--", DATE_FORMAT(MAX(infoOrder.created_at), "%d/%m/%y")) as last_order'),
                            // DB::raw('IF(infoCustomer.LastOrderDate = "", "--", DATE_FORMAT(infoCustomer.LastOrderDate, "%d/%m/%y")) as last_order'),
                            DB::raw('CEIL(SUM(infoOrder.Total)) as total_spent'),
                            // 'infoCustomer.TotalSpend as total_spent',
                        )
                        ->groupBy('infoCustomer.CustomerID')
                        ->orderBy('infoCustomer.id');
        if($request->selected_nav == 'B2B' || $request->customer_type == 'B2B'){
            $customers = $customers->where( function( $query ) {
                $query->where('infoCustomer.CustomerIDMaster', '!=', '')
                    ->orWhere('infoCustomer.CustomerIDMasterAccount', '!=', '')
                    ->orWhere('infoCustomer.IsMaster', 1)
                    ->orWhere('infoCustomer.IsMasterAccount', 1);
            });    
        }
        if($request->selected_nav == 'B2C' || $request->customer_type == 'B2C'){
            $customers = $customers->where('infoCustomer.CustomerIDMaster', '')
                            ->where('infoCustomer.CustomerIDMasterAccount', '')
                            ->where('infoCustomer.IsMaster', 0)
                            ->where('infoCustomer.IsMasterAccount', 0);
        }
        if( $request->customer_location !='' ){
            $customers = $customers->where('infoCustomer.TypeDelivery', $request->customer_location);
        }
        if( $request->invoice_preference !='' ){
            $customers = $customers->where('infoCustomer.invoicepreference', $request->invoice_preference);
        }
        if( $request->last_order_start !='' && $request->last_order_end ){
            $customers = $customers->whereBetween('infoOrder.created_at', [$request->last_order_start, $request->last_order_end]);
        }

        if( $request->total_spent !='' ){
            $customers =  $customers->get()
                                ->where('total_spent', '>=', explode(',', $request->total_spent)[0])
                                ->where('total_spent', '<=', explode(',', $request->total_spent)[1])
                                ->values();
            $total_count   =  $customers->count();
            $customers   =  $customers->skip($request->skip ? $request->skip : 0)
                                ->take(10)
                                ->values();
        }else{
            $total_count =  $customers->get()->count();
            $customers   =  $customers->skip($request->skip ? $request->skip : 0)
                                    ->take(10)
                                    ->get();
        }
        return response()->json([
            'customers'     => $customers,
            'total_count'   => $total_count,
        ]);
    }
    /**
     * Get Customer detailed infomation
     * 
     * @param customer_id
     * 
     * @return customer_infomation
     */
    public function getCustomerDetail(Request $request){
        $customer = DB::table('infoCustomer')
                    ->join('InfoCustomerPreference', 'InfoCustomerPreference.CustomerID', '=', 'infoCustomer.CustomerID')
                    ->select('Name as name', 'EmailAddress as email', 'Phone as phone',
                        DB::raw('IF(infoCustomer.CustomerIDMaster = "" AND infoCustomer.CustomerIDMasterAccount = "" AND infoCustomer.IsMaster = 0 AND infoCustomer.IsMasterAccount = 0, "B2C", "B2B") as cust_type'),
                        'infoCustomer.RevenueLocation as location', 'infoCustomer.CustomerNotes as notes', 'infoCustomer.id', 'infoCustomer.CustomerID',
                        DB::raw('IF(infoCustomer.DeliverybyDay = 1, "Recuring", "Normal") as booking'),
                        DB::raw( 
                            'CASE WHEN infoCustomer.IsMaster = 1 THEN "MAIN"
                                  WHEN infoCustomer.isMasterAccount = 1 THEN "MASTER"
                                  WHEN infoCustomer.isMaster = 0 AND infoCustomer.CustomerIDMaster <> "" THEN "Sub"
                                  WHEN infoCustomer.isMaster = 0 AND infoCustomer.CustomerIDMaster = "" THEN "Individual"
                            END as account_type'
                        ),
                        DB::raw( 
                            'CASE WHEN InfoCustomerPreference.Titre = "Type Customer" AND InfoCustomerPreference.Value = "VIP GOLD" THEN "VIP"
                                  WHEN InfoCustomerPreference.Titre = "Type Customer" AND InfoCustomerPreference.Value = "VIP RED" THEN "Complaint"
                                  WHEN InfoCustomerPreference.Titre = "Type Customer" AND InfoCustomerPreference.Value = "Standard" THEN "nothing"
                            END as preference'
                        ),
                    )
                    ->where('infoCustomer.id', $request->customer_id)
                    ->first();
        $total = DB::table('infoOrder')->where('CustomerID', $customer->CustomerID)
                    ->select(
                        DB::raw('CEIL(SUM(infoOrder.Total)) as total_spent'),
                        DB::raw('COUNT(infoOrder.id) as total_count'),
                    )->first();
        $customer->total_spent = $total->total_spent;
        $customer->total_count = $total->total_count;
        $customer->current_orders = DB::table('infoOrder')
                                        ->select(
                                            'infoOrder.id as order_id', 'infoInvoice.NumInvoice as sub_order', 'infoInvoice.id as sub_order_id',
                                            DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),
                                            // DB::raw('DATE_FORMAT(infoOrder.created_at, "%d %b %Y") as order_date'),
                                            'infoitems.priceTotal as price', 'infoitems.id as item_id',
                                            'infoitems.typeitem as item_name', 'infoitems.brand', 'infoitems.ItemTrackingKey as barcode',
                                            'TypePost.bg_color as location_color', 'postes.nom as location',
                                            'TypePost.circle_color', 'TypePost.process', 'infoOrder.underquote', 'infoitems.Colors as colors',
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "Store Drop Off"
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN "Pickup"
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN "Drop Off"
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "Pickup"
                                                END as order_left_text'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "In-Store Collection"
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN "Delivery"
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN "Delivery"
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "Delivery"
                                                END as order_right_text'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN "In-Store Collection"
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN "Home Delivery"
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN "Delivery Only"
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "Recuring Delivery"
                                                END as order_text'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.dropoff, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(pickup.date, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(infoOrder.created_at, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_left_date'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.pickup_date, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(deliveryask.date, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(deliveryask.date, "%W %d %M %Y")
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_right_date'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.pickup_date, "%d %b %Y")
                                                WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(deliveryask.date, "%d %b %Y")
                                                WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(deliveryask.date, "%d %b %Y")
                                                WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_date'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.dropoff, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(pickup.trancheFrom, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(infoOrder.created_at, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_left_time'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN DATE_FORMAT(booking_store.pickup_time, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN DATE_FORMAT(deliveryask.trancheFrom, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN DATE_FORMAT(deliveryask.trancheFrom, "%h:%i %p")
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as order_right_time'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN 0
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN IF(CURRENT_DATE() < pickup.date, 1, 0 )
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN 0
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN 0
                                                END as left_edit'
                                            ),
                                            DB::raw( 
                                                'CASE WHEN infoOrder.deliverymethod = "in_store_collection" OR infoOrder.TypeDelivery <> "DELIVERY" THEN IF(CURRENT_DATE() < booking_store.pickup_date, 1, 0 )
                                                      WHEN infoOrder.deliverymethod = "home_delivery" OR (infoOrder.TypeDelivery="DELIVERY" AND infoOrder.deliverymethod = "") THEN IF(CURRENT_DATE() < deliveryask.date, 1, 0 )
                                                      WHEN infoOrder.deliverymethod = "delivery_only" THEN IF(CURRENT_DATE() < deliveryask.date, 1, 0 )
                                                      WHEN infoOrder.deliverymethod = "recurring" THEN "--"
                                                END as right_edit'
                                            ),                                            
                                        )
                                        ->join('infoInvoice', 'infoInvoice.OrderID', '=', 'infoOrder.OrderID')
                                        ->join('infoitems',function($join){
                                            $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                                                ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                                        })
                                        ->join('postes', 'infoitems.nextpost', '=', 'postes.id')
                                        ->join('TypePost', 'TypePost.id', '=', 'postes.TypePost')
                                        ->leftJoin('booking_store', 'booking_store.order_id', '=', 'infoOrder.id')
                                        ->leftJoin('pickup', 'pickup.PickupID', '=', 'infoOrder.PickupID')
                                        ->leftJoin('deliveryask', 'deliveryask.DeliveryaskID', '=', 'infoOrder.DeliveryaskID')
                                        ->where('infoitems.priceTotal', '!=', 0)
                                        ->where('infoOrder.CustomerID', $customer->CustomerID)
                                        ->whereNotIn('infoOrder.Status', ['FULLFILED', 'DELIVERED', 'CANCEL', 'DELETE', 'VOID'])
                                        ->get()->groupBy(['order_id','sub_order_id'])->reverse()->values();
        $customer->past_orders = DB::table('infoOrder')
                                        ->select(
                                            'infoOrder.id as order_id', 'infoInvoice.NumInvoice as sub_order', 'infoInvoice.id as sub_order_id',
                                            DB::raw('if(infoOrder.Paid=0,"unpaid","paid")as paid'),
                                            'infoitems.typeitem as item_name', 'infoitems.brand', 'infoitems.ItemTrackingKey as barcode',
                                            'infoitems.priceTotal as price', 'infoitems.id as item_id',
                                            DB::raw('DATE_FORMAT(infoOrder.created_at, "%d %b %Y") as order_date'),
                                            'TypePost.bg_color as location_color', 'postes.nom as location',
                                            'TypePost.circle_color', 'TypePost.process', 'infoOrder.underquote', 'infoitems.Colors as colors'
                                        )
                                        ->join('infoInvoice', 'infoInvoice.OrderID', '=', 'infoOrder.OrderID')
                                        ->join('infoitems',function($join){
                                            $join->on('infoInvoice.InvoiceID','=','infoitems.InvoiceID')
                                                ->whereNotIn('infoitems.Status',['DELETE','VOID']);
                                        })
                                        ->join('postes', 'infoitems.nextpost', '=', 'postes.id')
                                        ->join('TypePost', 'TypePost.id', '=', 'postes.TypePost')                                        
                                        ->where('infoitems.priceTotal', '!=', 0)
                                        ->where('infoOrder.CustomerID', $customer->CustomerID)
                                        ->whereIn('infoOrder.Status', ['FULLFILED', 'DELIVERED', 'CANCEL', 'DELETE', 'VOID'])
                                        ->get()->groupBy(['order_id', 'sub_order_id'])->reverse()->values();
        return response()->json( $customer );
    }
}
