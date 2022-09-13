<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PermissionController;
use App\Models\Authorization;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderListController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\DetailingController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\PosteController;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Facades\Voyager;
use App\Http\Controllers\CategoryTailoringController;
use App\Http\Controllers\StripeTerminalController;
use App\Models\DetailingServices;
use App\Models\Infoitem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Stripe\Service\CustomerService;

use function PHPUnit\Framework\isNull;
use App\Http\Controllers\CustomerPreferenceController;
use App\Http\Controllers\Voyager\VoyagerPostcodesController;
use App\Http\Controllers\InvoiceEmailVerificationController;
use App\Http\Controllers\SupervisionController;
use App\Models\Delivery;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::post('/authenticate',[LoginController::class, 'authenticate'])->name('authenticate');
Route::post('/register',[LoginController::class, 'register'])->name('register');
Route::get('/logout',[LoginController::class, 'logout'])->name('logout');
Route::post('/auth/login',function () {
    return view('welcome');
})->name('login');
Route::post('/getorderlist',[OrderListController::class, 'getorderlist'])->middleware('auth')->name('orderlist');
Route::post('/getorderlistbysearch',[OrderListController::class, 'getorderlistbysearch'])->middleware('auth')->name('orderlistbysearch');
Route::post('/getOrdersByCustomerId',[OrderListController::class, 'getOrdersByCustomerId'])->middleware('auth')->name('ordersByCustomerId');
Route::post('/cancelorders',[OrderController::class, 'cancelorders'])->middleware('auth')->name('cancelorders');
Route::post('/voidSuborder',[OrderController::class, 'voidSuborder'])->middleware('auth')->name('voidSuborder');
Route::post('/cancelBooking',[OrderController::class, 'cancelBooking'])->middleware('auth')->name('cancelBooking');
Route::post('/create-new-order',[OrderController::class,'createNewOrder'])->name('create-new-order')->middleware('auth');
Route::post('/markaslate',[OrderListController::class, 'markaslate'])->middleware('auth')->name('markaslate');
Route::post('/getorderdetail',[OrderListController::class,'getorderdetail'])->middleware('auth')->name('getorderdetail');
Route::post('/setInvoiceFulfilled',[OrderListController::class,'setInvoiceFulfilled'])->middleware('auth')->name('setInvoiceFulfilled');
Route::post('/SplitSubOrder',[OrderListController::class,'SplitSubOrder'])->middleware('auth')->name('SplitSubOrder');
Route::post('/getitemdetail',[OrderListController::class,'getitemdetail'])->middleware('auth')->name('getitemdetail');
Route::post('/splititems',[OrderListController::class,'splititems'])->middleware('auth')->name('splititems');
Route::post('/suggestdate',[OrderListController::class,'suggestdate'])->middleware('auth')->name('suggestdate');
Route::post('/newdeliverydate',[OrderListController::class,'newdeliverydate'])->middleware('auth')->name('newdeliverydate');
Route::post('/newPickupdate',[OrderListController::class,'newPickupdate'])->middleware('auth')->name('newPickupdate');
Route::get('/getpermissions',[PermissionController::class,'getPermissions'])->middleware('superadmin')->name('getpermissions');
Route::post('/setpermission',[PermissionController::class,'setPermission'])->middleware('superadmin')->name('setpermission');
Route::post('/setprofile',[PermissionController::class,'setProfile'])->middleware('superadmin')->name('setprofile');
Route::get('/preload-order-form-info',[OrderController::class, 'preloadOrderFormInfo'])->middleware('auth')->name('preload-order-form-info');


Route::post('/create-customer',[CustomerController::class, 'createCustomer'])->middleware('auth')->name('create-customer');
Route::post('/check-customer-unique',[CustomerController::class, 'checkCustomerUnique'])->middleware('auth')->name('check.customer.unique');
Route::post('/check-stripe',[CustomerController::class, 'checkStripe'])->middleware('auth')->name('check-stripe');
Route::post('/create-sub-account',[CustomerController::class, 'createSubAccount'])->middleware('auth')->name('create-sub-account');
Route::post('/create-customer-sub-account',[CustomerController::class, 'createCustomerSubAccount'])->middleware('auth')->name('create-customer-sub-account');
Route::post('/generate-customer-id',[CustomerController::class, 'generateCustomerID'])->middleware('auth')->name('generate-customer-id');
Route::post('/unlink-Account',[CustomerController::class, 'unlinkAccount'])->middleware('auth')->name('unlink-Account');
Route::post('/customerdetails',[CustomerController::class, 'customerDetails'])->middleware('auth')->name('customerdetails');
Route::post('/get-all-customers',[CustomerController::class, 'getAllCustomers'])->name('get-all-customers')->middleware('auth');
Route::post('/get-customer-detail',[CustomerController::class, 'getCustomerDetail'])->middleware('auth')->name('get-customer-detail');
Route::post('/get-customer-full-detail',[CustomerController::class, 'getCustomerFullDetail'])->middleware('auth')->name('get-customer-full-detail');
Route::post('/get-customer-preferences',[CustomerController::class, 'getCustomerPreferences'])->middleware('auth')->name('get-customer-preferences');
Route::post('/add-credit-customer',[CustomerController::class, 'AddCreditCustomer'])->middleware('auth')->name('add-credit-customer');
Route::post('/add-credit-card',[CustomerController::class, 'AddCreditCard'])->middleware('auth')->name('add-credit-card');
Route::post('/delete-credit-card',[CustomerController::class, 'DeleteCreditCard'])->middleware('auth')->name('delete-credit-card');
Route::post('/set-customer-discount',[CustomerController::class,'setCustomerDiscount'])->middleware('auth')->name('set-customer-discount');
Route::post('/update-customer-contact',[CustomerController::class,'updateCustomerContact'])->name('update-customer-contact')->middleware('auth');
Route::post('/update-customer-address',[CustomerController::class,'updateCustomerAddress'])->name('update-customer-address')->middleware('auth');
Route::post('/update-customer-billing-address',[CustomerController::class,'updateCustomerBillingAddress'])->name('update-customer-billing-address')->middleware('auth');
Route::post('/update-invoice-details',[CustomerController::class,'updateInvoiceDetails'])->name('update-invoice-details')->middleware('auth');
Route::post('/update-customer-note',[CustomerController::class,'updateCustomerNote'])->name('update-customer-note')->middleware('auth');
Route::post('/save-customer-preferences',[CustomerController::class,'saveCustomerPreferences'])->name('save-customer-preferences')->middleware('auth');
Route::post('/save-customer-delivery-instructions',[CustomerController::class,'saveCustomerDeliveryInstructions'])->name('save-customer-delivery-instructions')->middleware('auth');
Route::post('/save-customer-communication',[CustomerController::class,'saveCustomerCommunication'])->name('save.customer.communication')->middleware('auth');
Route::post('/save-customer-recurring',[CustomerController::class,'saveCustomerRecurring'])->name('save.customer.recurring')->middleware('auth');
Route::post('/pause-customer-recurring',[CustomerController::class,'pauseCustomerRecurring'])->name('pause.customer.recurring')->middleware('auth');
Route::post('/unpause-customer-recurring',[CustomerController::class,'unpauseCustomerRecurring'])->name('unpause.customer.recurring')->middleware('auth');
Route::post('/get-recurring-booking-timeslot',[CustomerController::class, 'getRecurringBookingTimeSlot'])->name('get.recurring.booking.time.slot')->middleware('auth');

Route::post('/get-customer-order-details',[CustomerController::class,'getCustomerOrderDetails'])->name('get-customer-order-details')->middleware('auth');
Route::post('/update-detailing-issues-text',[DetailingController::class,'updateIssuesText'])->name('update-detailing-issues-text')->middleware('auth');
Route::post('/setCustomerSmsDelivery',[CustomerController::class,'setCustomerSmsDelivery'])->name('setCustomerSmsDelivery')->middleware('auth');
Route::post('/get-current-user',[CustomerController::class,'getCurrentUser'])->name('get-current-user')->middleware('auth');

Route::get('/permissions-test',function(){
    $user=User::find(56);
 foreach ($user->profiles as $profile){
     $_authorization = Authorization::find($profile->authorizations[0]->pivot->authorization_id);
     dump($_authorization);
     dump($profile->authorizations[0]->pivot);
     dump($profile->authorizations[0]->group);
 }
})->name('permissions');

Route::get('clear-logs',function(){
    $logfile = storage_path('logs/laravel.log');
    $fp = fopen($logfile, "r+");
    if(ftruncate($fp, 0)){
        echo "logs clear";
    }
})->middleware('auth');

/* TEST ROUTES*/

Route::get('tailoring-import',function(){
    $file = storage_path('files/01.csv');

    $handle = fopen($file,'r+');

    $data = [];
    $services = [];
    $categories = [];
    $limit = [];
    $categories_to_insert = [];

    while ( ($row = fgetcsv($handle) ) !== FALSE ) {
        $data[] = $row;
    }

    foreach($data as $k=>$v){
        if($k > 2){
            if($v[0]!=''){
                if(!in_array($v[0],$categories)){
                    array_push($categories,$v[0]);
                }
            }
            if($v[0]!='' && $v[1]!='' && $v[2]!=''){
                $services[] = [
                    'category'=>trim($v[0]),
                    'category_id'=>0,
                    'name'=>trim($v[1]),
                    'price'=>(trim($v[2])=='Quote'?0:$v[2]),
                    'is_quote'=>(trim($v[2])=='Quote'?1:0),
                    'created_at'=>date('Y-m-d H:i:s')
                ];
            }
        }
    }

    if(!empty($categories)){
        foreach($categories as $k=>$v){
            $categories_to_insert[] = [
                'name'=>trim($v),
                'created_at'=>date('Y-m-d H:i:s')
            ];
        }

        DB::table('category_tailorings')->truncate();
        DB::table('category_tailorings')->insert($categories_to_insert);
    }

    $sc = DB::table('category_tailorings')->get();
    $categories_id = [];

    if(count($sc) > 0){
        foreach($sc as $k=>$v){
            $categories_id[$v->name] = $v->id;
        }

        foreach($services as $k=>$v){
            $services[$k]['category_id'] = (isset($categories_id[$v['category']])?$categories_id[$v['category']]:0);
        }
    }

    DB::table('tailoring_services')->truncate();
    DB::table('tailoring_services')->insert($services);

    echo count($categories)." categories added<br/>".count($services)." services added";


})->middleware('auth');

/*
Route::get('tailoring-services-test',function(){
   $group_services = DetailingServices::getTailoringServicesByTypeitem(2);
   $detailingitem = (array) DB::table('detailingitem')->where('id',9)->first();

   $cust_tailoring_services = [];
   if(!is_null($detailingitem['tailoring_services'])){
    $cust_tailoring_services = @json_decode($detailingitem['tailoring_services']);
   }

   foreach($group_services as $k=>$v){
       foreach($v as $i=>$x){
          $group_services[$k][$i]->cust_selected = (in_array($x->id,$cust_tailoring_services)?1:0);
       }
   }

});
*/


Route::get('stripe-test',function(){
    $id_customer = 	3428;

    $cust = DB::table('infoCustomer')->where('id',$id_customer)->first();

    $addr = DB::table('address')->where('CustomerID',$cust->CustomerID)->where('status','DELIVERY')->first();

    $stripe = new \Stripe\StripeClient(env('STRIPE_TEST_SECURITY_KEY'));

    $card_exp = '12/23';
    $card_num =  '4850180100577561';
    $cardholder_name = 'Franck Gavois';

    $stripe_customer = null;
    $card = null;

    try {

        $card = DB::table('cards')
            ->where('CustomerID',$cust->CustomerID)
            ->where('Actif',1)
            ->first();

        //If Customer has card
        if($card){
            $stripe_customer = $stripe->customers->retrieve(
                $card->stripe_customer_id,
                []
              );

        }else{

            //create a card object to stripe
            $card = $stripe->paymentMethods->create([
                'type' => 'card',
                'card' => [
                    'number'      => $card_num ,
                    'exp_month'   => 03,
                    'exp_year'    => 23,
                    'cvc'         => 999,
                ],
            ]);


            //create a customer object to stripe
            $stripe_customer = $stripe->customers->create([
                'name'              => $cardholder_name,
                'email'             => $cust->EmailAddress,
                'payment_method'    => $card->id,
                'invoice_settings'  => ['default_payment_method' => $card->id],
                'metadata'          => [
                                            'CustomerID' => $cust->CustomerID
                                    ],
                'address'           => [
                                        'city'          => $addr->Town,
                                        'state'         => $addr->County,
                                        'country'       => $addr->Country,
                                        'postal_code'   => $addr->postcode,
                                        'line1'         => $addr->address1,
                                        'line2'         => $addr->address2,
                                    ]
            ]);



            $credit_card = [
                'CustomerID'        => $cust->CustomerID,
                'cardHolderName'    => $cardholder_name,
                'type'              => $card->card->brand,
                'cardNumber'        => substr_replace($card_num, str_repeat('*', strlen($card_num) - 6), 3, -3),
                'dateexpiration'    => $card_exp,
                'stripe_customer_id'=> $stripe_customer->id,
                'stripe_card_id'    => $card->id,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];

            DB::table('cards')->insert($credit_card);
        }
        /*
        $payment_intent = $stripe->paymentIntents->create([
            'amount'            => 30, //100*0.01
            'currency'          => 'gbp',
            'confirm'           => true,
            "payment_method"    => $card->stripe_card_id,
            "customer"          => $stripe_customer->id,
            "capture_method"    => "automatic",
            'payment_method_types' => ['card'],
            "description"=>"Order: 12345",
            "receipt_email"=>"rushdi@vpc-direct-service.com",
        ]);

        echo "<pre>";
        print($payment_intent);

        if($payment_intent->status == 'succeeded'){
            //Update order
            echo "payment succeeded";
        }
        //*/

    }catch(Exception $e){
        echo "<pre>";
        print_r($e);
    }

    /*
    $stripe_customer = $stripe->customers->create([
        'name'              => $cust->FirstName.$cust->LastName,
        'email'             => $cust->EmailAddress,
        'payment_method'    => $card->id,
        'invoice_settings'  => ['default_payment_method' => $card->id],
        'metadata'          => [
                                    'CustomerID' => $CustomerUUID
                            ],
        'address'           => [
                                'city'          => $request->city,
                                'state'         => $request->state,
                                'country'       => $request->country,
                                'postal_code'   => $request->postCode,
                                'line1'         => $request->deliveryAddress1,
                                'line2'         => $request->deliveryAddress2,
                            ]
    ]);
    */

})->middleware('auth');

Route::get('create-invoice-test',function(){
    $order_id = 83080;

    $di = DB::table('detailingitem')->where('order_id',$order_id)->get();



    $id_items = [];
    if(count($di) > 0){
        foreach($di as $k=>$v){
            if($v->item_id !=0 && !in_array($v->item_id,$id_items)){
                array_push($id_items,$v->item_id);
            }
        }
    }

    $items = [];
    if(!empty($id_items)){
        $items = DB::table('infoitems')->whereIn('id',$id_items)->orderBy('typeitem','ASC')->get()->toArray();
    }

    $perc = 0;

    $grouped_by_type = [];
    $grouped_by_inv = [];
    $group_by_perc = [];
    $sum_perc = [];

    $two_slots_items = [];

    if(count($items) > 0){
        foreach($items as $k=>$v){
            if($v->PERC==200){
                $two_slots_items[] = $v->id;
            }

            $perc += $v->PERC;
        }

        if(!empty($two_slots_items)){
            foreach($two_slots_items as $k=>$v){
                //Creer un invoice par item a 200%
            }

            foreach($items as $k=>$v){
                if(in_array($v->id,$two_slots_items)){
                    unset($items[$k]);
                }
            }
        }


        if($perc <= 100){
            //Cree un invoice
        }else{

            foreach($items as $k=>$v){
                $grouped_by_type[$v->typeitem][] = (array) $v;
            }




            $i = 0;
            $perc = 0;
            $new_key = 0;

            //Group BY PERC

            for($i=0; $i < count($items); $i++){
                $item = $items[$i];
                $perc += $item->PERC;

                $grouped_by_perc[$new_key][] = [
                    'item_id'=>$item->id,
                    'ItemID'=>$item->ItemID,
                    'PERC'=>$item->PERC,
                    'typeitem'=>$item->typeitem,
                ];

                if($perc >= 100){
                    $new_key +=1;
                    $perc = 0;
                    continue;
                }
            }
            //*/

            //GROUP BY TYPEITEM et PERC
            echo "<pre>";

            foreach($grouped_by_type as $k=>$v){
                $arr_perc = array_column($v,'PERC');
                print_r($arr_perc);
            }



            echo "<pre>";

            //print_r($grouped_by_type);

            echo "<br/><h2>Invoices by PERC</h2>";
            print_r($grouped_by_perc);
/*
            echo "<br/><h2>Invoices by TYPE and PERC</h2>";
            print_r($grouped_by_inv);
*/


        }
    }

})->middleware('auth');


Route::get('test-validate-order',function(Request $request){
    $order_id = $request->order_id;

    if(!isset($order_id)){
        die('?order_id not set');
    }

    $endpoint = "http://blancspot.vpc-direct-service.com/validorder.php";

    $client = new \GuzzleHttp\Client();

    $response = $client->request('GET', $endpoint, ['query' => [
        'order_id'=>$order_id,
    ]]);

    $statusCode = $response->getStatusCode();
    @$content = $response->getBody();
    $content = str_replace('\\"','',$content);

    //Si ok, passe infoOrder.Status = 'In process'

    $res =  json_decode($content);
    echo "<pre>";
    print_r($res);

    /*
    echo "<br/>";
    echo $res->result;
    */
});

Route::get('test-stripe-terminal',function(Request $request){
    $reader = $request->reader;

    if(!isset($reader) && $reader==''){
        die('Reader not set');
    }

    $readers_id = [
        'ATELIER'=>'tmr_Eqz4ewJhXq5eu6',
    ];

    if(isset($readers_id[$reader])){
        $reader_id = $readers_id[$reader];

        $stripe =  new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));
        $stripe->terminal->readers->retrieve($reader_id, []);


        $payment_intent = $stripe->paymentIntents->create([
            'amount' => 30,
            'currency' => 'gbp',
            'payment_method_types' => ['card_present'],
            'capture_method' => 'manual',
          ]);

/*
          $stripe->terminal->readers->processPaymentIntent(
            $reader_id,
            ['payment_intent' => $payment_intent->id]
          );
*/

    }

});



Route::get('inv-pdf',function(Request $request){
    $facture_id = $request->id;

    if(!isset($facture_id) || $facture_id ==''){
        die('Facture not set');
    }

    $details = DB::table('infoOrderPrint')->where('FactureID',$facture_id)->get();
    $details_per_cust = [];

    foreach($details as $key=>$details){
        $details_per_cust[] = CustomerController::getArPDFData($details);
    }

    foreach($details_per_cust as $k=>$v){
        if(empty($v['order_details'])){
            unset($details_per_cust[$k]);
        }
    }


   if(!empty($details_per_cust)){
        $data = [
            'details_per_cust'=>$details_per_cust,
        ];

        echo "<pre>";
        print_r($data);
        die();


        Pdf::setOptions(['dpi' => 300, 'defaultFont' => 'Helvetica']);


        $pdf = Pdf::loadView('pdf/ar_all_pdf', $data);

        $pdf->output();

        $canvas = $pdf->getDomPDF()->getCanvas();

        $fontMetrics = $pdf->getDomPDF()->getFontMetrics();
        $font = $fontMetrics->getFont('Times-Roman');

        $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
        $size = 10;

        $width = $fontMetrics->getTextWidth($text, $font, $size) / 2;

        $x = $canvas->get_width() - $width;
        $y = $canvas->get_height() - 35;

        $canvas->page_text($x, $y, $text , $font, 10, array(0, 0, 0));

        return $pdf->download('invoice'.$details->CustomerID.'.pdf');
    }else{
        die("Invoice has no valid items");
    }

});

Route::get('notify-test', function () {
    $row_ids = [58];
    $all_details = DB::table('infoOrderPrint')->whereIn('id',$row_ids)->get();

    foreach($all_details as $k=>$v){
        $cust = DB::table('infoCustomer')->where('CustomerID',$v->CustomerID)->first();

        if($cust){
            $mail_vars = [
                'FirstName'=>$cust->FirstName,
                'url_invoice'=>\Illuminate\Support\Facades\URL::to("/inv-pdf")."?id=".$v->FactureID,
            ];
        }

        echo "<pre>";
        print_r($mail_vars);
    }

    /*
    $mail_vars = [
        'FirstName' => 'Test',
        'CreatedOn' => date('D d m Y H:i:s'),
        'UserFullName' => 'Test at' . date('H:i'),
        'UserAddress' => '10 test street<br>City<br>Country',
        'PickupDate' => 'xx',
        'PickupTime' => 'xx',
        'DeliveryDate' => 'xx',
        'DeliveryTime' => 'xx',
        'DeliveryTo' => 'xx',
        'AppTrackOrderLink' => 'xx',
    ];

    NotificationController::Notify('rushdi@vpc-direct-service.com', '+123456789', '3A_BOOKING_CONFIRM', '', $mail_vars, true, 0, '');
    */
});



/* END TEST ROUTES */

// added by yonghuan to search customers to be linked
Route::post('/search-customer', [SearchController::class, 'SearchCustomersToLink'])->name('SearchCustomersToLink');

Route::post('/SearchCustomer', [SearchController::class, 'SearchCustomer'])->name('SearchCustomer');
Route::post('/SearchByCustomer', [SearchController::class, 'SearchByCustomer'])->name('SearchByCustomer');
Route::post('/ScanItemAndBag', [ScanController::class, 'ScanItemAndBag'])->name('ScanItemAndBag');

Route::post('/get-statistics',[StatisticsController::class, 'getStatistics'])->name('get-statistics')->middleware('auth');
Route::post('/get-prod-statistics',[StatisticsController::class, 'getProdStatistics'])->name('get.prod.statistics')->middleware('auth');

Route::post('/assembly-home-stats',[StatisticsController::class, 'getAssemblyHomeStats'])->name('get-assembly-home-stats')->middleware('auth');
Route::post('/partner-details',[StatisticsController::class, 'getPartnerDetails'])->name('partner-details')->middleware('auth');
Route::post('/get-all-invoices',[StatisticsController::class, 'getAllInvoices'])->name('get-all-invoices')->middleware('auth');



Route::post('/init-detailing',[DetailingController::class, 'initDetailing'])->name('init-detailing')->middleware('auth');
Route::post('/update-detailing',[DetailingController::class, 'updateItemDetailing'])->name('update-detailing')->middleware('auth');
Route::post('/get-detailing-list',[DetailingController::class, 'getDetailingList'])->name('get-detailing-list')->middleware('auth');

Route::post('/item-picto',[ItemController::class, 'getItemPicto'])->name('item-picto');
Route::post('/get-picto-names',[ItemController::class,'getPictoNames'])->name('get-picto-names');

/*
* NEW ORDER
* */
Route::post('/get-shipping-partners',[ShippingController::class,'getPartnerList'])->name('get-shipping-partners')->middleware('auth');
Route::post('/get-tranche-by-postcode',[BookingController::class,'getTrancheByPostcode'])->name('get-tranche-by-postcode')->middleware('auth');
Route::post('/create-new-order',[OrderController::class,'createNewOrder'])->name('create-new-order')->middleware('auth');
Route::post('/get-slots-by-day',[BookingController::class,'getSlotsByDay'])->name('get-slots-by-day')->middleware('auth');
Route::post('/save-order-card-details',[OrderController::class,'saveCardDetails'])->name('save-order-card-details')->middleware('auth');
Route::post('/update-order-to-detailing',[OrderController::class,'updateOrderToDetailing'])->name('updateOrderToDetailing')->middleware('auth');
Route::post('/get-holidays',[BookingController::class,'getHolidays'])->name('get-holidays')->middleware('auth');
Route::post('/validate-checkout-order',[OrderController::class,'validateCheckOutOrder'])->name('validate-checkout-order')->middleware('auth');

/*
 * QZ Print
 * */
Route::post('get-site-keys', [ PosteController::class, 'getSiteKeys'])->name('get-site-keys')->middleware('auth');
Route::post('get-suborder-and-print', [ PosteController::class, 'getSubOrderToPrint'])->name('get-suborder-and-print')->middleware('auth');


/* Update svg zone label position
*/
Route::post('update-zone-label-pos',[ItemController::class,'updateZoneLabelPos'])->name('update-zone-label-pos')->middleware('auth');


/*
* Detailing Services
*/

Route::post('/get-services',[DetailingController::class,'getServices'])->name('get-services')->middleware('auth');
Route::post('/complete-detailing-item',[DetailingController::class,'completeDetailing'])->name('complete-detailing-item')->middleware('auth');
Route::post('/update-cust-preference-from-service',[DetailingController::class,'updateCustomerServicePref'])->name('update-cust-preference-from-service')->middleware('auth');
Route::post('/remove-detailing-item',[DetailingController::class,'removeDetailingItem'])->name('remove-detailing-item')->middleware('auth');
Route::post('/set-price-now',[DetailingController::class,'setPriceNow'])->name('set-price-now')->middleware('auth');

/*
* Create items from detailing
*/

Route::post('/check-detailing-tracking',[DetailingController::class,'checkDetailingTracking'])->name('check-detailing-item')->middleware('auth');
Route::post('/create-order-items',[DetailingController::class,'createOrderItems'])->name('create-order-items')->middleware('auth');


/**
 * Checkout items
 */
Route::post('/get-checkout-items',[DetailingController::class,'getCheckoutItems'])->name('get-checkout-items')->middleware('auth');
Route::post('/change-detailing-etape',[DetailingController::class,'changeDetailingEtape'])->name('change-detailing-etape')->middleware('auth');
Route::post('/set-checkout-discount',[DetailingController::class,'setCheckoutDiscount'])->name('set-checkout-discount')->middleware('auth');
Route::post('/make-payment-or-create-card',[OrderController::class,'makePaymentOrCreateCard'])->name('make-payment-or-create-card')->middleware('auth');
Route::post('/complete-checkout',[OrderController::class,'completeCheckout'])->name('complete-checkout')->middleware('auth');
Route::post('/get-stripe-terminal',[DetailingController::class,'getStripeTerminal'])->name('get-stripe-terminal')->middleware('auth');
Route::post('/get-terminal-token',[DetailingController::class,'getTerminalToken'])->name('get-terminal-token')->middleware('auth');
Route::post('/pay-from-credit',[OrderController::class,'payFromCredit'])->name('pay-from-credit')->middleware('auth');
Route::post('/set-checkout-addon',[DetailingController::class,'setCheckoutAddon'])->name('set-checkout-addon')->middleware('auth');
Route::post('/remove-order-voucher',[DetailingController::class,'removeCheckoutVoucher'])->name('remove-order-voucher')->middleware('auth');
Route::post('/add-order-voucher',[DetailingController::class,'addCheckoutVoucher'])->name('add-order-voucher')->middleware('auth');

/*
* AR List
*/
Route::post('/get-ar-customers',[CustomerController::class,'getArCustomers'])->name('get-ar-customers')->middleware('auth');
Route::post('/mail-invoice-customer',[CustomerController::class,'mailInvoiceCustomer'])->name('mail-invoice-customer')->middleware('auth');
Route::post('/generate-ar-invoice',[CustomerController::class,'generateArInvoice'])->name('generate-ar-invoice')->middleware('auth');
Route::get('/download-ar-pdf',[CustomerController::class,'downloadArPdf'])->name('download-ar-pdf');

/**
 * Route for Electron Pos printer - DO NOT REMOVE
 */

Route::get('/electron-suborder-and-print',function(Request $request){
	$invoice_id = $request->get('invoice_id');

    $invoice = PosteController::electonGetSubOrderToPrint($invoice_id,'CustomerDetail');

    return response()->json([
        'post'=>$request->all(),
        'invoice'=>$invoice,
    ]);
});


/**
 * Routes for stripe terminal - DO NOT REMOVE
 *  */


Route::group(['prefix'=>'stripe-test'],function(){
    Route::get('/',[StripeTerminalController::class,'index'])->name('stripe-test')->middleware('auth');

    //Get connection
    Route::post('/connection_token',function(){
        $stripe = new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));


        try {
            // The ConnectionToken's secret lets you connect to any Stripe Terminal reader
            // and take payments with your Stripe account.
            // Be sure to authenticate the endpoint for creating connection tokens.
            $connectionToken = $stripe->terminal->connectionTokens->create();
            echo json_encode(array('secret' => $connectionToken->secret));
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    });

    Route::post('/create_payment_intent',function(){
        $stripe = new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));

        try {
            $json_str = file_get_contents('php://input');
            $json_obj = json_decode($json_str);

            $order_id = $json_obj->order_id;
            $order = DB::table('infoOrder')->where('id',$order_id)->first();
            $cust = DB::table('infoCustomer')->where('CustomerID',$order->CustomerID)->first();

            // For Terminal payments, the 'payment_method_types' parameter must include
            // 'card_present' and the 'capture_method' must be set to 'manual'

            $amount_two_dp = number_format($json_obj->amount,2);

            $intent = $stripe->paymentIntents->create([
                'amount' => 100*$amount_two_dp,
                'currency' => 'gbp',
                'payment_method_types' => [
                                            'card_present',
                                        ],
                'capture_method' => 'manual',
                'description'=>'Order: '.$order_id,
                "receipt_email"=>$cust->EmailAddress,
            ]);

            echo json_encode($intent);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    });

    Route::post('/capture_payment_intent',function(){
        $stripe = new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));

        try {
        $json_str = file_get_contents('php://input');
        $json_obj = json_decode($json_str);

        $intent = $stripe->paymentIntents->capture($json_obj->payment_intent_id);

        echo json_encode($intent);
        } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        }
    });

    Route::post('/update-terminal-order',function(){
        $json_str = file_get_contents('php://input');
        $request = json_decode($json_str);

        $order_id = $request->order_id;
        $terminal = $request->terminal;
        $amount = $request->amount;
        $status = $request->status;
        $info = $request->info;

        $stamp = date('Y-m-d H:i:s');

        $order = DB::table("infoOrder")->where('id',$order_id)->first();

        $to_insert = [
            'type'=>$terminal,
            'datepayment'=>$stamp,
            'status'=>$status,
            'montant'=>$amount,
            'CustomerID'=>$order->CustomerID,
            'created_at'=>$stamp,
            'order_id'=>$order_id,
            'info'=>json_encode($info),
        ];

        if($status=='succeeded'){
            unset($to_insert['info']);
        }

        $payment_id = DB::table('payments')->insertGetId($to_insert);

        $amount_to_pay = DetailingController::getAmountToPay($order_id);

        if($status=='succeeded' && $amount_to_pay <=0){
            $updated = DB::table("infoOrder")->where('id',$order_id)->update(['Paid'=>1]);
        }

        echo json_encode(['payment_id'=>$payment_id]);
    });

});

/**
 * END - Route for Stripe Terminal
 */


/**
 * Voyager custom routes
 *  */

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
/*
    Route::get('category-tailoring',[CategoryTailoringController::class,'index'])->name('category-tailoring')->middleware('auth');
    Route::get('client-poste', 'ClientPosteController@index')->name('client-poste');
*/
    Route::get('supervision', [SupervisionController::class,'index'])->name('supervision')->middleware('auth');
    Route::get('supervisiondata', [SupervisionController::class,'supervisionData'])->name('supervisiondata')->middleware('auth');

    Route::get('customer-email-verification', [InvoiceEmailVerificationController::class,'index'])->name('customer-email-verification')->middleware('auth');
    Route::get('bad-customer-email-edit', [InvoiceEmailVerificationController::class,'checkbademail'])->name('bad-customer-email-edit')->middleware('auth');
    Route::get('customer-without-address', [InvoiceEmailVerificationController::class,'customerWithoutAddress'])->name('customer-without-address')->middleware('auth');
    Route::post('bad-customer-email-save', [InvoiceEmailVerificationController::class,'postprocess'])->name('bad-customer-email-save')->middleware('auth');
    Route::get('restore-customer',[InvoiceEmailVerificationController::class,'restoreCustomer'])->name('restore-customer')->middleware('auth');

    Route::get('configure-postcodes', [VoyagerPostcodesController::class,'index'])->name('configure-postcodes')->middleware('auth');
    Route::post('save-postcodes', [VoyagerPostcodesController::class,'postprocess'])->name('save-postcodes')->middleware('auth');

    Route::get('customer-preference', [CustomerPreferenceController::class,'index'])->name('customer-preference')->middleware('auth');
    Route::get('view-customer-preference', [CustomerPreferenceController::class,'show'])->name('view-customer-preference')->middleware('auth');
    Route::post('save-customer-preference', [CustomerPreferenceController::class,'upsert'])->name('save-customer-preference')->middleware('auth');

    Route::post('update-sequence', [CustomerPreferenceController::class,'updateSequence'])->name('update-sequence')->middleware('auth');
    Route::get('delete-customer-pref', [CustomerPreferenceController::class,'setPrefDeleted'])->name('delete-customer-pref')->middleware('auth');
});

/*ALWAYS AT THE BOTTOM*/
Route::get('{any}', function () {
    return view('welcome');
})->where('any','.*');
