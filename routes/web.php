<?php

use App\Customer;
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
use Stripe\Exception\InvalidRequestException as ExceptionInvalidRequestException;
use Stripe\Exception\OAuth\InvalidRequestException;
use Symfony\Component\Console\Terminal;

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
Route::post('/voidOrder',[OrderController::class, 'voidOrder'])->middleware('auth')->name('voidOrder');
Route::post('/cancelBooking',[OrderController::class, 'cancelBooking'])->middleware('auth')->name('cancelBooking');
Route::post('/create-new-order',[OrderController::class,'createNewOrder'])->name('create-new-order')->middleware('auth');
Route::post('/markaslate',[OrderListController::class, 'markaslate'])->middleware('auth')->name('markaslate');
Route::post('/getorderdetail',[OrderListController::class,'getorderdetail'])->middleware('auth')->name('getorderdetail');
Route::post('/setInvoiceFulfilled',[OrderListController::class,'setInvoiceFulfilled'])->middleware('auth')->name('setInvoiceFulfilled');
Route::post('/SplitSubOrder',[OrderListController::class,'SplitSubOrder'])->middleware('auth')->name('SplitSubOrder');
Route::post('/freeReClean',[OrderListController::class,'freeReClean'])->middleware('auth')->name('freeReClean');
Route::post('/reAssign',[OrderListController::class,'reAssign'])->middleware('auth')->name('reAssign');
Route::post('/getitemdetail',[OrderListController::class,'getitemdetail'])->middleware('auth')->name('getitemdetail');
Route::post('/splititems',[OrderListController::class,'splititems'])->middleware('auth')->name('splititems');
Route::post('/suggestdate',[OrderListController::class,'suggestdate'])->middleware('auth')->name('suggestdate');
Route::post('/newdeliverydate',[OrderListController::class,'newdeliverydate'])->middleware('auth')->name('newdeliverydate');
Route::post('/newPickupdate',[OrderListController::class,'newPickupdate'])->middleware('auth')->name('newPickupdate');
Route::get('/getpermissions',[PermissionController::class,'getPermissions'])->middleware('superadmin')->name('getpermissions');
Route::post('/setpermission',[PermissionController::class,'setPermission'])->middleware('superadmin')->name('setpermission');
Route::post('/setprofile',[PermissionController::class,'setProfile'])->middleware('superadmin')->name('setprofile');
Route::get('/preload-order-form-info',[OrderController::class, 'preloadOrderFormInfo'])->middleware('auth')->name('preload-order-form-info');
Route::get('/get_status_orders',[OrderListController::class, 'getStatusOrders'])->middleware('auth')->name('get_status_orders');
Route::post('/deleteorder',[OrderController::class, 'deleteorder'])->middleware('auth')->name('deleteorder');
Route::post('/getPayementOrder',[OrderController::class, 'getPayementOrder'])->middleware('auth')->name('getPayementOrder');
Route::post('/get-deliverydate-customer',[OrderController::class,'getDeliveyDateCustomer'])->name('get-deliverydate-customer')->middleware('auth');


Route::post('/create-customer',[CustomerController::class, 'createCustomer'])->middleware('auth')->name('create-customer');
Route::post('/getinvoicehistories',[CustomerController::class, 'getInvoiceHistories'])->middleware('auth')->name('getinvoicehistories');
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


Route::get('stripe-test-card',function(){
    $id_customer = 	3428;

    $cust = DB::table('infoCustomer')->where('id',$id_customer)->first();

    $addr = DB::table('address')->where('CustomerID',$cust->CustomerID)->where('status','DELIVERY')->first();

    $stripe = new \Stripe\StripeClient(env('STRIPE_TEST_SECURITY_KEY'));

    $card_exp = '12/23';
    $card_num =  '4242424242424242';
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


        $cardid = $payment_intent->charges->data[0]->payment_method;

        /*
        $stripe_cust  = $stripe->customers->retrieve(
            $custid,
            []
          );
        */
        $card2 = $stripe->paymentMethods->retrieve($cardid);

        echo "<pre>";
        print_r($card2);


        die();

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

    $details = DB::table('infoOrderPrint')->where('FactureID',$facture_id)->first();


    $data = CustomerController::getArPDFData($details);

    if(!empty($data['order_details'])){


        $pdf_data['v'] = $data;

        Pdf::setOptions(['dpi' => 300, 'defaultFont' => 'Helvetica']);


        $pdf = Pdf::loadView('pdf/ar_pdf', $pdf_data);

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


/* A REFAIRE */

Route::get('/test-pi',function(){

    $stripe =  new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));

    $reader = $stripe->terminal->readers->retrieve('tmr_Eqz4ewJhXq5eu6',[]);

    $customer = $stripe->customers->retrieve('cus_MeaLJ5cY4usxnq',[]);

    $si = $stripe->setupIntents->create([
        'customer'=>$customer->id,
        'payment_method_types' => ['card_present'],
    ]);

    $res = $stripe->terminal->readers->processSetupIntent('tmr_Eqz4ewJhXq5eu6',
        ['setup_intent' => $si->id, 'customer_consent_collected' => true]
    );

    echo "<pre>";
    print_r($res);
});


Route::get('/merge-pdf',function(){
    $path = storage_path('app/pdf/').'*.*';

    $arrfiles = glob($path);
    $files = implode(" ",$arrfiles);

    $output_file = storage_path('app/pdf/').'test.pdf';

    //print_r($files);

    shell_exec("gs -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE=$output_file -dBATCH $files");
});

Route::get('/test-create-pi',function(){
    $order_id = 99999;


    $stripe_key = 'STRIPE_TEST_SECURITY_KEY';
    $stripe = new \Stripe\StripeClient(env($stripe_key));


    $pi= $stripe->paymentIntents->create([
        'amount'            => 100*30, //Minimun 30 pence
        'currency'          => 'gbp',
        'confirm'           => true,
        "payment_method"    => 'pm_1LnoA2B2SbORtEDspPBXGNAJ',
        "customer"          => 'cus_MWrt7Gggau7yrE',
        "capture_method"    => "automatic",
        'payment_method_types' => ['card'],
        "description"=>$order_id,
        "receipt_email"=>'rushdi@vpc-direct-service.com', //To change for customer email
        //'off_session' => true,
        //'confirm' => true,
    ]);


    $payment_intent = $stripe->paymentIntents->retrieve($pi->id,[]);

    echo "<pre>";
    print_r($payment_intent);
    die();



});

Route::get('/test-refund',function(){
    //Create payment Intent

    OrderController::logRefund(1,5);

});


Route::get('/test-create-card',function(){
    $stripe_key = 'STRIPE_TEST_SECURITY_KEY';
    $stripe = new \Stripe\StripeClient(env($stripe_key));

    $cust = DB::table('infoCustomer')->where('id',22783)->first();


    //$card_num = '4000000000000002'; //Generic returns card_declined
    //$card_num = '4000000000009979'; //lost card returns card_declined
    // $card_num = '4000000000000069'; //card expired returns card expired
    //$card_num = '4000000000009995'; // insufficient funds
    //$card_num = '4000000000009979'; // stolen card
    //$card_num = '4000000000000127'; //incorrect CVC
    $card_num = '4000002760003184'; // 3D secure all authenticate
   //$card_num = '4000000000003220'; //3D secure ok
   // $card_num = '4000000000003063'; //Setupintent succeeded
    //$card_num = '4000002500003155';
    //$card_num = '4000008260000000';

    $site_url = \Illuminate\Support\Facades\URL::to("/");


    try{
        $card = $stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number'      => $card_num ,
                'exp_month'   => 12,
                'exp_year'    => 30,
                'cvc'         => 999,
            ]
        ]);


        $stripe_customer = $stripe->customers->create([
            'name'              => 'Test Rushdi', //to change
            'email'             => 'rushdi@vpc-direct-service.com',
            'payment_method'    => $card->id,
            'invoice_settings'  => ['default_payment_method' => $card->id],
            'metadata'=>[
                'CustomerID'=>$cust->CustomerID,
            ]

        ]);


        $si = $stripe->setupIntents->create([
            'customer' => $stripe_customer->id,
            'payment_method'=>$card->id,
            'payment_method_types' => ['card'],

            /*
            'payment_method_options'=>[
                'card'=>[
                    'request_three_d_secure'=>'any'
                ]
            ],
            */
        ]);

        $res = $stripe->setupIntents->confirm($si->id,[
            'return_url'=>$site_url.'/confirm-card/?si='.$si->id,
        ]);

        //echo $res->next_action->use_stripe_sdk->type;
        if($res->status=='succeeded'){
            echo "3D secure successful";
        }else{
            echo $res->id."<br/><br/>";
            echo "<a href='".$res->next_action->redirect_to_url->url."' target='_BLANK'>Confirm URL</a><br/><br/><pre>";
            print_r($res);
        }

    }catch(\Stripe\Exception\CardException $e){
        $err = $e->getError();
        echo $err->message." Code: ".$err->decline_code;
        echo "<pre>";
        print_r($err);
    }catch(\Exception $e){
        echo $e->getMessage();
    }

});


/* END TEST ROUTES */

/*DO NOT REMOVE*/

Route::get('/3d-secure',function(Request $request){
    $token = $request->get('token');

    $app_token = setting('admin.url_token');//EjD4L7tgrHxmCY3exnCE31b3

    if(!isset($token)){
       // die('token not set');
    }

    $custid = $request->customer_id;

    $cust = DB::table('infoCustomer')->where('CustomerID',$custid)->first();

    if($cust){
        $card = DB::table('cards')->where('CustomerID',$cust->CustomerID)->where('Actif',1)->latest('id')->first();


        if($card){
            $stripe =  new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));

            $si = $stripe->setupIntents->create([
                'customer' => $card->stripe_customer_id,
                'payment_method_types' => ['card'],
                'usage'=>'off_session',
            ]);

            if($si->id){
                DB::table('cards')->where('id',$card->id)->update(['setup_intent_id'=>$si->id]);
                echo "Setup intent created";
            }
        }else{
            die('card not found');
        }
    }else{
        die('Customer not found');
    }
});

Route::get('/batch-si',function(Request $request){
    $token = $request->get('token');

    $app_token = setting('admin.url_token');//EjD4L7tgrHxmCY3exnCE31b3

    if(!isset($token)){
        //die('token not set');
    }

    $stripe =  new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));

    DB::table('cards')->where('cardNumber','424**********242')->update(['test'=>1,'checked'=>1]);

    $cards = DB::table('cards')->where('Actif',1)
        ->where('checked',0)
        ->where('setup_intent_id','')
        ->get();

    $i = 0;

    while($i<count($cards)){
        $cust_id =  $cards[$i]->stripe_customer_id;

        try{
            $cust = $stripe->customers->retrieve($cust_id);
            $si = $stripe->setupIntents->create([
                'customer'=>$cust_id,
                'payment_method_types' => ['card'],
                'usage'=>'off_session',
            ]);
            DB::table('cards')->where('id',$cards[$i]->id)->update(['checked'=>1]);
        }catch(ExceptionInvalidRequestException $e){
            DB::table('cards')->where('id',$cards[$i]->id)->update(['test'=>1]);
        }

        $i++;
    }

});

Route::get('/unpaid-card-orders',function(Request $request){
    $token = $request->get('token');

    $app_token = setting('admin.url_token');//EjD4L7tgrHxmCY3exnCE31b3

    if(!isset($token)){
        die('token not set');
    }

    if($app_token != $token){
        die('invalid token');
    }

    $start = microtime(true);

    $orders_to_charge = [];
    $customers_to_charge = [];
    $card_details = [];
    $payment_intents = [];
    $count_paid = 0;
    $paid_orders = [];
    $paid_per_order = [];
    $orders_id = [];
    $customers_with_cards = [];

    $cards = DB::table('cards')
    ->select('cards.*','infoCustomer.EmailAddress')
    ->join('infoCustomer','cards.CustomerID','infoCustomer.CustomerID')
    ->where('cards.Actif',1)
    ->get();

    if(count($cards) > 0){
        foreach($cards as $k=>$v){
            $card_details[$v->CustomerID] = $v;

            if(!in_array($v->CustomerID,$customers_with_cards)){
                array_push($customers_with_cards,$v->CustomerID);
            }
        }

    }


    $orders = DB::table('infoOrder')
        ->select('infoOrder.id','infoOrder.TotalDue','infoOrder.CustomerID')
        ->join('infoCustomer','infoOrder.CustomerID','infoCustomer.CustomerID')
        ->where('infoOrder.TypeDelivery','DELIVERY')
        ->where('infoOrder.deliverymethod','!=','')
        ->whereNotIn('infoOrder.Status',['DELETE','VOID','CANCEL','IN DETAILING','RECURRING','SCHEDULED'])
        ->where('infoOrder.Total','>',0)
        ->where('infoOrder.Paid',0)
        ->whereIn('infoOrder.CustomerID',$customers_with_cards)
        ->where('infoCustomer.OnAccount',0)
        ->get();

    if(count($orders) > 0){

        $ctrl = new DetailingController();
        foreach($orders as $k=>$v){
            if(!in_array($v->id, $orders_id)){
                array_push($orders_id,$v->id);
            }
        }

        foreach($orders_id as $k=>$v){
            $ctrl->calculateCheckout($v);
        }

        $orders2 = DB::table('infoOrder')->whereIn('id',$orders_id)->get();

        foreach($orders2 as $k=>$v){

            $orders_to_charge[$v->id] = ['TotalDue'=>$v->TotalDue,'CustomerID'=>$v->CustomerID];

            if(!in_array($v->CustomerID,$customers_to_charge)){
                array_push($customers_to_charge,$v->CustomerID);
            }


        }



        $stripe_key = 'STRIPE_LIVE_SECURITY_KEY';

        $stripe_test = env('STRIPE_TEST');
        if($stripe_test){
            $stripe_key = 'STRIPE_TEST_SECURITY_KEY';
        }

        $stripe = new \Stripe\StripeClient(env($stripe_key));
        $site_url = \Illuminate\Support\Facades\URL::to("/");


        foreach($orders_to_charge as $k=>$v){

            if(isset($card_details[$v['CustomerID']]) && $v['TotalDue'] > 0){
                $card = $card_details[$v['CustomerID']];
                $payment_intents[] = [
                        'amount'            => $v['TotalDue']*100, //100*0.01
                        'currency'          => 'gbp',
                        'confirm'           => true,
                        "payment_method"    => ($stripe_test?'pm_1LnoA2B2SbORtEDspPBXGNAJ':$card->stripe_card_id),
                        "customer"          => ($stripe_test?'cus_MWrt7Gggau7yrE':$card->stripe_customer_id),
                        "capture_method"    => "automatic",
                        'payment_method_types' => ['card'],
                        "description"=>$k,
                        "receipt_email"=>($stripe_test?'rushdi@vpc-direct-service.com':$card->EmailAddress), //To change for customer email
                ];

            }
        }

        foreach($payment_intents as $k=>$pi){
            $si_id = "";

            $card = $card_details[$v['CustomerID']];
            if($card->setup_intent_id!=''){
                $si_id = $card->setup_intent_id;
            }else{
                try{
                    $si = $stripe->setupIntents->create([
                        'customer' => $card->stripe_customer_id,
                        'payment_method'=>$card->stripe_card_id,
                        'payment_method_types' => ['card'],
                        'usage'=>'off_session',
                    ]);
                    $si_id = $si->id;
                    DB::table('cards')->where('id',$card->id)->update(['setup_intent_id'=>$si_id]);

                }catch(\Exception $e){}
            }
            try{
                $three_d_res = $stripe->setupIntents->confirm($si->id,[
                    'return_url'=>$site_url."/confirm-card",
                ]);
                if($three_d_res->status=='succeeded'){
                    DB::table('cards')->where('id',$card->id)->update(['three_d_secure'=>1]);
                }else{
                    //To add SMS or MAIL
                }

            }catch(\Exception $e){}

            $order = DB::table('infoOrder')->where('id',$pi['description'])->first();
            $payment_id = DB::table('payments')->insertGetId([
                'type'=>'card',
                'datepayment'=>date('Y-m-d H:i:s'),
                'status'=>'',
                'montant'=>$pi['amount']/100,
                'order_id'=>$pi['description'],
                'card_id'=>$card_details[$order->CustomerID]->id,
                'CustomerID'=>$order->CustomerID,
                'created_at'=>date('Y-m-d H:i:s'),
            ]);


           try{
                $payment_intent = $stripe->paymentIntents->create($pi);

                if($payment_intent->status=='succeeded'){
                    DB::table('payments')->where('id',$payment_id)->update(['status'=>'succeeded']);
                    $count_paid += 1;
                    DB::table('infoOrder')->where('id',$order->id)->update(['Paid'=>1,'TotalDue'=>0]);
                    DB::table('infoInvoice')->where('OrderID',$order->OrderID)->update(['Paid'=>1]);

                }

            }catch(\Stripe\Exception\CardException $e) {
                //error_log("A payment error occurred: {$e->getError()->message}");
                DB::table('payments')->where('id',$payment_id)->update(['status'=>'cron failed','info'=>json_encode($e->getError())]);
              } catch (\Stripe\Exception\InvalidRequestException $e) {
                //error_log("An invalid request occurred.");
                DB::table('payments')->where('id',$payment_id)->update(['status'=>'cron failed','info'=>json_encode($e->getError())]);
              } catch (Exception $e) {
                //error_log("Another problem occurred, maybe unrelated to Stripe.");
                DB::table('payments')->where('id',$payment_id)->update(['status'=>'cron failed','info'=>"Another problem occurred, maybe unrelated to Stripe | ".json_encode($e)]);
              }
        }


    }

    $end = microtime(true);
    $timetaken = $end - $start;

    echo "Time taken : ".gmdate('H:i:s',$timetaken)."<br/>";
    echo "Total orders : ".count($payment_intents)."<br/>";
    echo "Orders paid : ".$count_paid;


});

Route::get('/confirm-card',function(request $request){
    $si_id = $request->si;
    $err = false;
    $err_txt = "";

    if(!isset($si_id) || $si_id==''){
        //die('Parameter "?si=XXX" not set');
        $err = true;
        $err_txt = "Parameter ?si=XXX not set";
    }

    $stripe_key = 'STRIPE_LIVE_SECURITY_KEY';

    $stripe_test = env('STRIPE_TEST');
    if($stripe_test){
        $stripe_key = 'STRIPE_TEST_SECURITY_KEY';
    }

    $stripe = new \Stripe\StripeClient(env($stripe_key));

    $site_url = \Illuminate\Support\Facades\URL::to("/");

    try{
        $si = $stripe->setupIntents->retrieve($si_id);

        $pm = $stripe->paymentMethods->retrieve($si->payment_method); //payment_method
        $cust = $stripe->customers->retrieve($si->customer);

        if($si->status=='succeeded'){
            $existing_card = DB::table('cards')
                //->where('setup_intent_id',$si->id)
                ->where('CustomerID',$cust->metadata->CustomerID)
                ->where('Actif',1)
                ->first();

            if($existing_card){
                //die('You already have one active card');
                $err = true;
                $err_txt = "You already have one active card";
            }else{
                $last4 = $pm->card->last4;
                $card_details = [
                    'cardNumber'=>sprintf("%'*16d\n",$last4),
                    'cardHolderName'=>$cust->name,
                    'type'=>$pm->card->brand,
                    'Actif'=>1,
                    'dateexpiration'=>$pm->card->exp_month."/".substr($pm->card->exp_year,2),
                    'stripe_customer_id'=>$cust->id,
                    'stripe_card_id'=>$pm->id,
                    'setup_intent_id'=>$si->id,
                    'CustomerID'=>$cust->metadata->CustomerID,
                    'app'=>(strpos($site_url,'blancapi') > -1?1:0),
                    'created_at'=>date('Y-m-d H:i:s'),
                ];

                try{
                    DB::table('cards')->insert($card_details);
                    //echo "Card confirmation successful";
                    $err = false;
                }catch(\Exception $e){
                    //die("Unable to confirm card: ".$e->getMessage());
                    $err = true;
                    $err_txt = "Unable to confirm card: ".$e->getMessage();
                }
            }

        }else{
            //die('3D secure not completed');
            $err = true;
            $err_txt = "3D secure not completed";
        }
    }catch(\Exception $e){
        //die("Invalid setupIntent id");
        $err = true;
        $err_txt = "Invalid setupIntent id";
    }

    return response()->json([
        'err'=>$err,
        'err_txt'=>$err_txt,
    ]);
});

/*END DO NOT REMOVE*/

// added by yonghuan to search customers to be linked
Route::post('/search-customer', [SearchController::class, 'SearchCustomersToLink'])->name('SearchCustomersToLink');

Route::post('/SearchCustomer', [SearchController::class, 'SearchCustomer'])->name('SearchCustomer');
Route::post('/SearchByCustomer', [SearchController::class, 'SearchByCustomer'])->name('SearchByCustomer');
Route::post('/ScanItemAndBag', [ScanController::class, 'ScanItemAndBag'])->name('ScanItemAndBag');

Route::post('/get-statistics',[StatisticsController::class, 'getStatistics'])->name('get-statistics')->middleware('auth');
Route::post('/get-prod-statistics',[StatisticsController::class, 'getProdStatistics'])->name('get.prod.statistics')->middleware('auth');
Route::post('/get-order-csv',[StatisticsController::class, 'getOrderCSV'])->name('get.order.csv')->middleware('auth');
Route::post('/get-revenue-csv',[StatisticsController::class, 'getRevenueCSV'])->name('get.revenue.csv')->middleware('auth');
Route::post('/get-void-order-csv',[StatisticsController::class, 'getVoidOrderCSV'])->name('get.void.order.csv')->middleware('auth');
Route::post('/get-excel-report', [ StatisticsController::class, 'downloadExcel' ])->name('get.report.excel')->middleware('auth');
Route::post('/get-voucher-excel-report', [ StatisticsController::class, 'downloadVoucherExcel' ])->name('get.voucher.report.excel')->middleware('auth');

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
Route::post('get-order-and-print', [ PosteController::class, 'getOrderToPrint'])->name('get-order-and-print')->middleware('auth');


/* Update svg zone label position
*/
Route::post('update-zone-label-pos',[ItemController::class,'updateZoneLabelPos'])->name('update-zone-label-pos')->middleware('auth');

/*  search  by barcode
*/
Route::post('get-item-barcode',[ItemController::class,'getitembyBarcode'])->name('get-item-barcode')->middleware('auth');
Route::post('get-order-barcode',[ItemController::class,'getorderbyBarcode'])->name('get-order-barcode')->middleware('auth');


/*
* Detailing Services
*/

Route::post('/get-services',[DetailingController::class,'getServices'])->name('get-services')->middleware('auth');
Route::post('/complete-detailing-item',[DetailingController::class,'completeDetailing'])->name('complete-detailing-item')->middleware('auth');
Route::post('/update-cust-preference-from-service',[DetailingController::class,'updateCustomerServicePref'])->name('update-cust-preference-from-service')->middleware('auth');
Route::post('/remove-detailing-item',[DetailingController::class,'removeDetailingItem'])->name('remove-detailing-item')->middleware('auth');
Route::post('/set-price-now',[DetailingController::class,'setPriceNow'])->name('set-price-now')->middleware('auth');
Route::post('/set-describe-price-now',[DetailingController::class,'setDescribeAndPriceNow'])->name('set-describe-price-now')->middleware('auth');
Route::post('/getPreferenceCustomer',[DetailingController::class,'getPreferenceCustomer'])->name('getPreferenceCustomer')->middleware('auth');

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
Route::post('/save-price-delivery-now',[DetailingController::class,'savePriceDeliveryNow'])->name('save-price-delivery-now')->middleware('auth');
Route::post('/add-order-voucher',[DetailingController::class,'addCheckoutVoucher'])->name('add-order-voucher')->middleware('auth');
Route::post('/pre-calculate-checkout',[DetailingController::class,'preCalculateCheckout'])->name('pre-calculate-checkout')->middleware('auth');
Route::post('/set-checkout-credit-refund',[DetailingController::class,'setCheckoutCreditRefund'])->name('set-checkout-credit-refund')->middleware('auth');

/*
* AR List
*/
Route::post('/get-ar-customers',[CustomerController::class,'getArCustomers'])->name('get-ar-customers')->middleware('auth');
Route::post('/get-ar-invoiced-customers',[CustomerController::class,'getArInvoicedCustomers'])->name('get-ar-invoiced-customers')->middleware('auth');
Route::post('/mail-invoice-customer',[CustomerController::class,'mailInvoiceCustomer'])->name('mail-invoice-customer')->middleware('auth');
Route::post('/generate-ar-invoice',[CustomerController::class,'generateArInvoice'])->name('generate-ar-invoice')->middleware('auth');
Route::get('/download-ar-pdf',[CustomerController::class,'downloadArPdf'])->name('download-ar-pdf');

/**
 * ORDER DETAIL MINI CHECKOUT
 */
Route::post('/order-to-fulfill',[OrderController::class,'getOrderToFulfill'])->name('order-to-fulfill')->middleware('auth');
Route::post('/fulfill-order',[OrderController::class,'fulfillOrder'])->name('fulfill-order')->middleware('auth');


/**
 * Route for Electron Pos printer - DO NOT REMOVE
 */

Route::get('/electron-suborder-and-print',function(Request $request){
	$id = $request->get('id');
    $type = $request->get('type');
    $route = $request->get('route');
    $userid = $request->get('userid');

    $invoice = null;
    $invoice = PosteController::getSubOrderDataToPrint($id,'CustomerDetail',false,$userid);
    return response()->json([
        'post'=>$request->all(),
        'invoice'=>$invoice,
    ]);
});

Route::get('/electron-order-and-print',[PosteController::class,'getOrderToPrint'])->name('electron-order-and-print');

Route::get('/get-current-user',function(){
    $user = Auth::user();
    return response()->json(['user'=>$user]);
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
            $savecardinfo = false;

            $order = DB::table('infoOrder')->where('id',$order_id)->first();
            $cust = DB::table('infoCustomer')->where('CustomerID',$order->CustomerID)->first();

            $user = Auth::user();

            // For Terminal payments, the 'payment_method_types' parameter must include
            // 'card_present' and the 'capture_method' must be set to 'manual'

            $amount_two_dp = number_format($json_obj->amount,2);

            $si = false;
            $res = false;
            $pi = false;

            if($savecardinfo){
                try{
                    $stripe_customer = $stripe->customers->create([
                        'name'              => $cust->Name,//$cardholder_name,
                        'email'             => $cust->EmailAddress,
                        //'payment_method'    => $card->id,
                        //'invoice_settings'  => ['default_payment_method' => $card->id],
                        'metadata'          => [
                                                    'CustomerID' => $cust->CustomerID
                                            ],
        /*
                        'address'           => [
                                                'city'          => ($addr?$addr->Town:''),
                                                'state'         => ($addr?$addr->County:''),
                                                'country'       => ($addr?$addr->Country:''),
                                                'postal_code'   => ($addr?$addr->postcode:''),
                                                'line1'         => ($addr?$addr->address1:''),
                                                'line2'         => ($addr?$addr->address2:''),
                                            ]
                        */
                    ]);




                    $si = $stripe->setupIntents->create([
                        'customer' => $stripe_customer->id,
                        'payment_method_types' => ['card_present'],
                    ]);



                    $readers_id = [];
                    $readers_id[1] = 'tmr_Eqz4ewJhXq5eu6'; //Atelier
                    $readers_id[2] = 'tmr_Eq0HXA4Oj7Yjqo'; //Marylebone
                    $readers_id[3] = 'tmr_EqzSAXwuoVzKs0'; //Chelsea
                    $readers_id[4] = 'tmr_Eqz9KQMTISyB47'; //South Ken
                    $readers_id[5] = 'tmr_EqzjQIwM2PjDQy'; //Notting Hill

                    $sel_reader = "";

                    if($user && isset($readers_id[$user->store])){
                        $sel_reader = $readers_id[$user->store];

                        $res = $stripe->terminal->readers->processSetupIntent(
                        $sel_reader,
                        ['setup_intent' => $si->id, 'customer_consent_collected' => true]
                    );
                    }

                }catch(\Exception $e){
                    echo json_encode(['error' => $e->getMessage()]);
                }

            }


            $pi = [
                'amount' => 100*$amount_two_dp,
                'currency' => 'gbp',
                'payment_method_types' => [
                                'card_present',
                                        ],
                'capture_method' => 'manual',
                'description'=>$order_id,
                "receipt_email"=>$cust->EmailAddress,
                //'customer'=>$stripe_customer->id,
            ];


            $intent = $stripe->paymentIntents->create($pi);

            if($savecardinfo){
                echo json_encode(['pi'=>$intent,'si'=>$si,'res'=>$res]);
            }else{
                echo json_encode($intent);
            }
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
        $payment_intent_id = $request->payment_intent_id;

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
            'payment_intent_id'=>$payment_intent_id,
            'info'=>json_encode($info),
        ];

        if($status=='succeeded'){
            unset($to_insert['info']);
        }

        $payment_id = DB::table('payments')->insertGetId($to_insert);

        $amount_to_pay = DetailingController::getAmountToPay($order_id);

        if($status=='succeeded' && $amount_to_pay <=0){
            $updated = DB::table("infoOrder")->where('id',$order_id)->update(['Paid'=>1]);

            DB::table('infoInvoice')->where('OrderID',$order->OrderID)->update(['Paid'=>1]);
        }

        echo json_encode(['payment_id'=>$payment_id]);
    });

    Route::post('/refund-payment',function(){
        $json_str = file_get_contents('php://input');
        $request = json_decode($json_str);

        $payment_intent_id = $request->payment_intent_id;

        $stripe = new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));

        $stripe->refunds->create(['payment_intent' => $payment_intent_id]);
    });

    Route::get('/test-si-terminal',function(){
        $readers_id = [];
        $readers_id[1] = 'tmr_Eqz4ewJhXq5eu6'; //Atelier
        $readers_id[2] = 'tmr_Eq0HXA4Oj7Yjqo'; //Marylebone
        $readers_id[3] = 'tmr_EqzSAXwuoVzKs0'; //Chelsea
        $readers_id[4] = 'tmr_Eqz9KQMTISyB47'; //South Ken
        $readers_id[5] = 'tmr_EqzjQIwM2PjDQy'; //Notting Hill

        $cust = DB::table('infoCustomer')->where('id',22783)->first();

        $stripe =  new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));

        //$cust is our customer object

        $stripe_customer = $stripe->customers->create([
            'name'              => $cust->Name,//$cardholder_name,
            'email'             => $cust->EmailAddress,
            'metadata'          => [
                                        'CustomerID' => $cust->CustomerID
                                ],

        ]);


        $si = $stripe->setupIntents->create([
            'customer'=>$stripe_customer->id,
            'payment_method_types' => ['card_present'],
            'metadata'=>[
                'CustomerID'=>$cust->CustomerID,
            ]
        ]);

        echo "<pre>";

        try{
            $res = $stripe->terminal->readers->processSetupIntent($readers_id[1],
                ['setup_intent' => $si->id, 'customer_consent_collected' => true]
            );

        }catch(\Exception $e){
            print_r($e->getMessage());
        }

        echo "SI";

        print_r($si);

    });



    Route::post('/test_payment_intent',function(){
        $stripe = new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));

        try {
            $json_str = file_get_contents('php://input');
            $json_obj = json_decode($json_str);


            $pi = [
                'amount' => 100*1,
                'currency' => 'gbp',
                'payment_method_types' => [
                                'card_present',
                                        ],
                'capture_method' => 'manual',
                'description'=>99999,
                "receipt_email"=>'rushdi@vpc-direct-service.com',
                //'customer'=>$stripe_customer->id,
            ];


            $intent = $stripe->paymentIntents->create($pi);


            echo json_encode($intent);



        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    });

    Route::post('/save-card-details',function(Request $request){
        $user = Auth::user();

        $readers_id = [];
        $readers_id[1] = 'tmr_Eqz4ewJhXq5eu6'; //Atelier
        $readers_id[2] = 'tmr_Eq0HXA4Oj7Yjqo'; //Marylebone
        $readers_id[3] = 'tmr_EqzSAXwuoVzKs0'; //Chelsea
        $readers_id[4] = 'tmr_Eqz9KQMTISyB47'; //South Ken
        $readers_id[5] = 'tmr_EqzjQIwM2PjDQy'; //Notting Hill

        $sel_reader = "";
        $err_txt = "";
        $order_id = $request->order_id;
        $payment = $request->payment;
        $amount = $request->amount;
        $res = false;
        $si = false;
        $pi = false;
        $res_pi = false;

        if($user && isset($readers_id[$user->store])){
            $sel_reader = $readers_id[$user->store];

            $stripe = new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));

            $order = DB::table('infoOrder')->where('id',$order_id)->first();

            $cust = DB::table('infoCustomer')->where('CustomerID',$order->CustomerID)->first();

            $stripe =  new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));

            //$cust is our customer object

            $stripe_customer = $stripe->customers->create([
                'name'              => $cust->Name,//$cardholder_name,
                'email'             => $cust->EmailAddress,
                'metadata'          => [
                                            'CustomerID' => $cust->CustomerID
                                    ],

            ]);


            $si = $stripe->setupIntents->create([
                'customer'=>$stripe_customer->id,
                'payment_method_types' => ['card_present'],
                'metadata'=>[
                    'CustomerID'=>$cust->CustomerID,
                ],

            ]);


            try{
                $res = $stripe->terminal->readers->processSetupIntent($sel_reader,
                    ['setup_intent' => $si->id, 'customer_consent_collected' => true]
                );

            }catch(\Exception $e){
                $err_txt = "Setup Intent: ".$e->getMessage();
            }



            if($payment){

                $pi_arr = [
                    'amount' => 100*$amount,
                    'currency' => 'gbp',
                    'payment_method_types' => [
                                    'card_present',
                                            ],
                    'capture_method' => 'manual',
                    'description'=>$order_id,
                    "receipt_email"=>$cust->EmailAddress,

                    'customer'=>$stripe_customer->id,

                ];

                $pi = $stripe->paymentIntents->create($pi_arr);


                try{
                    $res_pi = $stripe->terminal->readers->processPaymentIntent($sel_reader,['payment_intent'=>$pi->id]);

                }catch(\Exception $e){
                    $err_txt = "Payment Intent: ".$e->getMessage();
                }
            }




        }else{
            $err_txt = "Terminal user->store value not set";
        }


        return response()->json([
            'post'=>$request->all(),
            'err_txt'=>$err_txt,
            'output_si'=>$res,
            'si'=>$si,
            'pi'=>$pi,
            'output_pi'=>$res_pi,
            'user'=>$user,
        ]);
    });

    Route::post('/retrieve-si',function(Request $request){

        $stripe = new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));

        $si_id = $request->si_id;
        $card_id = 0;
        $card_details = [];
        $pm = null;

        try{
            $si = $stripe->setupIntents->retrieve($si_id,[]);

            if($si->payment_method!=''){
                try{

                    $pm = $stripe->paymentMethods->retrieve($si->payment_method); //payment_method
                    $cust = $stripe->customers->retrieve($si->customer);
                    $card = $pm->card_present;

                    $last4 = $card->last4;
                    $card_details = [
                        'cardNumber'=>sprintf("%'*16d\n",$last4),
                        'cardHolderName'=>$cust->name,
                        'type'=>$card->brand,
                        'Actif'=>1,
                        'dateexpiration'=>$card->exp_month."/".substr($card->exp_year,2),
                        'stripe_customer_id'=>$cust->id,
                        'stripe_card_id'=>$pm->id,
                        'setup_intent_id'=>$si->id,
                        'CustomerID'=>$cust->metadata->CustomerID,
                        'app'=>0,
                        'created_at'=>date('Y-m-d H:i:s'),
                        'three_d_secure'=>($si->status=='succeeded'?1:0),
                    ];

                    $card_id = DB::table('cards')->insertGetId($card_details);


                }catch(\Exception $e){
                    return response()->json(['error'=>$e->getMessage()]);
                }

            }

        }catch(\Exception $e){
            return response()->json([
                'error'=>$e->getMessage(),
            ]);
        }
        return response()->json([
            'si'=>$si,
            'card_id'=>$card_id,
            'card_details'=>$card_details,
            'pm'=>$pm,
        ]);
    });


    Route::post('/retrieve-payment-details',function(Request $request){

        $stripe = new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));

        $si_id = $request->si_id;
        $pi_id = $request->pi_id;
        $card_id = 0;
        $card_details = [];
        $pm = null;
        $si = false;
        $pi = false;
        $user = Auth::user();

        try{


            $si = $stripe->setupIntents->retrieve($si_id,[]);

            $pi = $stripe->paymentIntents->retrieve($pi_id,[]);

            if($pi->payment_method!=''){
                $stripe->paymentIntents->confirm($pi->id);
            }

            if($pi->status=='requires_capture'){
                $pi = $stripe->paymentIntents->capture(
                    $pi_id,
                    []
                );
            }

            //

            if($pi->status=='succeeded'){

                    try{
                        $pm = $stripe->paymentMethods->retrieve($si->payment_method); //payment_method
                        $cust = $stripe->customers->retrieve($si->customer);
                        $card = $pm->card_present;

                        $last4 = $card->last4;
                        $card_details = [
                            'cardNumber'=>sprintf("%'*16d\n",$last4),
                            'cardHolderName'=>$cust->name,
                            'type'=>$card->brand,
                            'Actif'=>1,
                            'dateexpiration'=>$card->exp_month."/".substr($card->exp_year,2),
                            'stripe_customer_id'=>$cust->id,
                            'stripe_card_id'=>$pm->id,
                            'setup_intent_id'=>$si->id,
                            'CustomerID'=>$cust->metadata->CustomerID,
                            'app'=>0,
                            'created_at'=>date('Y-m-d H:i:s'),
                            'three_d_secure'=>($si->status=='succeeded'?1:0),
                        ];

                        $card_id = DB::table('cards')->insertGetId($card_details);


                    }catch(\Exception $e){
                        return response()->json(['error'=>$e->getMessage()]);
                    }
            }

        }catch(\Exception $e){
            return response()->json([
                'error'=>$e->getMessage(),
            ]);
        }
        return response()->json([
            'si'=>$si,
            'pi'=>$pi,
            'card_id'=>$card_id,
            'card_details'=>$card_details,
            'pm'=>$pm,
        ]);
    });


});

Route::post('/cancel-terminal-request',function(){
    $user = Auth::user();

    $readers_id = [];
    $readers_id[1] = 'tmr_Eqz4ewJhXq5eu6'; //Atelier
    $readers_id[2] = 'tmr_Eq0HXA4Oj7Yjqo'; //Marylebone
    $readers_id[3] = 'tmr_EqzSAXwuoVzKs0'; //Chelsea
    $readers_id[4] = 'tmr_Eqz9KQMTISyB47'; //South Ken
    $readers_id[5] = 'tmr_EqzjQIwM2PjDQy'; //Notting Hill

    $sel_reader = "";

    $output = false;

    if($user && isset($readers_id[$user->store])){
        $sel_reader = $readers_id[$user->store];

        $stripe = new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));

        $output = $stripe->terminal->readers->cancelAction(
            $sel_reader,
            []
          );
    }


    return response()->json([
        'user'=>$user,
        'sel_reader'=>$sel_reader,
        'output'=>$output,
    ]);
});

Route::get('/cancel-terminal-action',function(Request $request){

    $reader_id = $request->readerid;

    $readers_id = [];
    $readers_id[1] = 'tmr_Eqz4ewJhXq5eu6'; //Atelier
    $readers_id[2] = 'tmr_Eq0HXA4Oj7Yjqo'; //Marylebone
    $readers_id[3] = 'tmr_EqzSAXwuoVzKs0'; //Chelsea
    $readers_id[4] = 'tmr_Eqz9KQMTISyB47'; //South Ken
    $readers_id[5] = 'tmr_EqzjQIwM2PjDQy'; //Notting Hill

    $readers = [];

    foreach($readers_id as $k=>$v){
        if($k!=0)
            $readers[] = $v;
    }

    if(!isset($reader) && $reader_id==''){
        die('Param ?readerid=XX not set');
    }elseif(!in_array($reader_id,$readers)){
        die('Invalid reader id');
    }



        $stripe = new \Stripe\StripeClient(env('STRIPE_LIVE_SECURITY_KEY'));

        $output = $stripe->terminal->readers->cancelAction(
           $reader_id,
            []
          );

    return response()->json([
        'res'=>$output,
    ]);
});





Route::get('/save_setup_intent',function(Request $request){

    $stripe_test = false;
    $err = false;
    $err_txt = "";
    $existing_card = null;

    //if($isLoggedIn===true){
        $si_id = $request->SetupIntent;


        if(!isset($si_id) || $si_id==''){
            //die('Parameter "?si=XXX" not set');
            $err = true;
            $err_txt = "Parameter ?SetupIntent=XXX not set";
        }

        $stripe_key = 'STRIPE_LIVE_SECURITY_KEY';


        if($stripe_test){
            $stripe_key = 'STRIPE_TEST_SECURITY_KEY';
        }

        $stripe = new \Stripe\StripeClient(env($stripe_key));

        $site_url = \Illuminate\Support\Facades\URL::to("/");

        try{
            $si = $stripe->setupIntents->retrieve($si_id);

            $pm = $stripe->paymentMethods->retrieve($si->payment_method); //payment_method
            $cust = $stripe->customers->retrieve($si->customer);

            //if($si->status=='succeeded'){
                $existing_card = DB::table('cards')
                    //->where('setup_intent_id',$si->id)
                    ->where('CustomerID',$cust->metadata->CustomerID)
                    ->where('Actif',1)
                    ->first();

                if($existing_card){
                    //die('You already have one active card');
                    $err = true;
                    $err_txt = "You already have one active card";
                }else{
                    $last4 = $pm->card->last4;
                    $card_details = [
                        'cardNumber'=>sprintf("%'*16d\n",$last4),
                        'cardHolderName'=>$cust->name,
                        'type'=>$pm->card->brand,
                        'Actif'=>1,
                        'dateexpiration'=>$pm->card->exp_month."/".substr($pm->card->exp_year,2),
                        'stripe_customer_id'=>$cust->id,
                        'stripe_card_id'=>$pm->id,
                        'setup_intent_id'=>$si->id,
                        'CustomerID'=>$cust->metadata->CustomerID,
                        'app'=>(strpos($site_url,'blancapi') > -1?1:0),
                        'created_at'=>date('Y-m-d H:i:s'),
                    ];

                    try{
                        DB::table('cards')->insert($card_details);
                        //echo "Card confirmation successful";
                        $err = false;
                    }catch(\Exception $e){
                        //die("Unable to confirm card: ".$e->getMessage());
                        $err = true;
                        $err_txt = "Unable to confirm card: ".$e->getMessage();
                    }
                }
            /*
            }else{
                //die('3D secure not completed');
                $err = true;
                $err_txt = "3D secure not completed";
            }
            */
        }catch(\Exception $e){
            //die("Invalid setupIntent id");
            $err = true;
            $err_txt = "Invalid setupIntent id";

        }
   // }

    return response()->json([
       // 'loggedIn'=>$isLoggedIn,
        'err'=>$err,
        'err_txt'=>$err_txt,
        'existing_card'=>$existing_card,
        'si'=>$si_id,
    ]);

});

Route::get('/process_setup_intent',function(Request $request){
    $si_id = $request->SetupIntent;



    return response()->json([
        'get'=>$request->all(),
    ]);
});


/**
 * END - Route for Stripe Terminal
 */

/**
 * Route for stripe unpaid orders
 */

Route::get('/unpaid-orders',function(Request $request){
    $start = microtime(true);

    $token = $request->get('token');

    $app_token = setting('admin.url_token');//EjD4L7tgrHxmCY3exnCE31b3


    if(!isset($token)){
        die('token not set');
    }

    if($app_token != $token){
        die('invalid token');
    }


    $stripe_key = 'STRIPE_LIVE_SECURITY_KEY';

    $stripe_test = env('STRIPE_TEST');
    if($stripe_test){
        $stripe_key = 'STRIPE_TEST_SECURITY_KEY';
    }

    $stripe = new \Stripe\StripeClient(env($stripe_key));


    $unpaid_orders = DB::table('unpaid_orders')
        ->join('infoOrder','unpaid_orders.order_id','infoOrder.id')
        ->where('unpaid_orders.paid',0)
        ->where('infoOrder.Paid',0)
        ->where('unpaid_orders.created_at','>=',date('Y-m-d H:i:s',strtotime('-5day')))
        ->get();

    $orders_to_update = [];
    $lines_to_update = [];
    $order_ids = [];
    $orders_to_calculate = [];
    $payment_per_order = [];
    $order_totals = [];


    if(count($unpaid_orders) > 0){
        foreach($unpaid_orders as $k=>$v){
            $order_ids[] = $v->order_id;
            $order_totals[$v->id] = $v->TotalDue;
        }

        /*
        $payments = DB::table("payments")->where('status','succeeded')->whereIn('order_id',$order_ids)->get();

        $order_payments = [];

        if(count($payments) > 0){
            foreach($payments as $k=>$v){
                $order_payments[$v->order_id][] = $v->montant;
            }

            foreach($order_payments as $k=>$v){
                $payment_per_order[$k] = array_sum($v);
            }

            foreach($payment_per_order as $orderid=>$montant){
                if(isset($order_totals[$orderid])){
                    $amount_due = $order_totals[$orderid] - $montant;

                    $order_totals[$orderid] = $amount_due;
                }
            }

        }
        */

    $site_url = \Illuminate\Support\Facades\URL::to("/");

    foreach($order_totals as $k=>$v){
        if($v > 0){

            $total_amount = 100*$v;
            $order = DB::table('infoOrder')->where('id',$k)->first();
            $card = DB::table('cards')->where('CustomerID',$order->CustomerID)->where('Actif',1)->first();
            $cust = DB::table('infoCustomer')->where('CustomerID',$order->CustomerID)->first();

        if($card->three_d_secure==0){
            $si_id = "";
            if($card->setup_intent_id!=''){
                $si_id = $card->setup_intent_id;
            }else{
                try{
                    $si = $stripe->setupIntents->create([
                        'customer' => $card->stripe_card_id,
                        'payment_method'=>$card->stripe_customer_id,
                        'payment_method_types' => ['card'],
                        'usage'=>'off_session',
                    ]);
                    DB::table('cards')->where('id',$card->id)->update(['setup_intent_id'=>$si->id]);
                    $si_id = $si->id;
                }catch(\Exception $e){

                }
            }
            try{
                $three_d_res = $stripe->setupIntents->confirm($si->id,[
                    'return_url'=>$site_url."/confirm-card",
                ]);
                if($three_d_res->status=='succeeded'){
                    DB::table('cards')->where('id',$card->id)->update(['three_d_res'=>1]);
                }else{

                }

            }catch(\Exception $e){

            }
        }

        if($order && $card && $cust){
            $payment_arr = [
                'type'=>'card',
                'datepayment'=>date('Y-m-d H:i:s'),
                'status'=>'',
                'montant'=>number_format($total_amount,2),
                'order_id'=>$k,
                'card_id'=>$card->id,
                'CustomerID'=>$order->CustomerID,
                'created_at'=>date('Y-m-d H:i:s'),
            ];

            $payment_id = DB::table('payments')->insertGetId($payment_arr);

            try{

                $pi = [
                    'amount'            => $total_amount, //100*0.01
                    'currency'          => 'gbp',
                    'confirm'           => true,
                    "payment_method"    => ($stripe_test?'pm_1LnoA2B2SbORtEDspPBXGNAJ':$card->stripe_card_id),
                    "customer"          => ($stripe_test?'cus_MWrt7Gggau7yrE':$card->stripe_customer_id),
                    "capture_method"    => "automatic",
                    'payment_method_types' => ['card'],
                    "description"=>$k,
                    "receipt_email"=>($stripe_test?'rushdi@vpc-direct-service.com':$cust->EmailAddress), //To change for customer email
                    'off_session' => true,
                    'confirm' => true,
                ];

                $payment_intent = $stripe->paymentIntents->create($pi);

                if($payment_intent){
                    DB::table('payments')->where('id',$payment_id)->update(['payment_intent_id'=>$payment_intent->id]);

                    if(isset($payment_intent->status) && $payment_intent->status == 'succeeded'){
                        $orders_to_update[] = $k;

                        DB::table('payments')->where('id',$payment_id)->update(['status'=>'succeeded']);

                        //Update order
                        DB::table('infoOrder')->where('id',$k)->update([
                            'Paid'=>1,
                            'payment_id'=>$payment_id,
                        ]);

                        DB::table('infoInvoice')->where('OrderID',$order->OrderID)->update(['Paid'=>1]);
                        DB::table('unpaid_orders')->where('order_id',$k)->update(['paid'=>1]);
                    }

                }

            }catch(\Stripe\Exception\CardException $e) {
                OrderController::logErrorPayment($payment_id,$e->getError());

                $err_payment = "Payment unsuccessful";

                return response([
                    'error_stripe'=>"Stripe error: ".$e->getError()->code,
                ]);

              }catch (\Stripe\Exception\InvalidRequestException $e) {

                OrderController::logErrorPayment($payment_id,$e->getError());

                $err_payment = "Payment unsuccessful";

                return response([
                    'error_stripe'=>"Stripe error: ".$e->getError()->code,
                ]);
              }catch (Exception $e) {
                OrderController::logErrorPayment($payment_id,$e);

                $err_payment = "Payment unsuccessful";

                return response([
                    'error_stripe'=>"Another problem occurred, maybe unrelated to Stripe",
                ]);
              }
            }
        }



        }
    }

    $end = microtime(true);
    $timetaken = $end-$start;

    echo "Time taken: ".gmdate('H:i:s',$timetaken)."<br/>";
    echo count($orders_to_update)." lines updated";

});


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
