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
use App\Models\DetailingServices;
use App\Models\Infoitem;
use Illuminate\Http\Request;
use Carbon\Carbon;

use function PHPUnit\Framework\isNull;

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
Route::post('/getOrdersByCustomerId',[OrderListController::class, 'getOrdersByCustomerId'])->middleware('auth')->name('ordersByCustomerId');
Route::post('/cancelorders',[OrderListController::class, 'cancelorders'])->middleware('auth')->name('cancelorders');
Route::post('/markaslate',[OrderListController::class, 'markaslate'])->middleware('auth')->name('markaslate');
Route::post('/getorderdetail',[OrderListController::class,'getorderdetail'])->middleware('auth')->name('getorderdetail');
Route::post('/getitemdetail',[OrderListController::class,'getitemdetail'])->middleware('auth')->name('getitemdetail');
Route::post('/splititems',[OrderListController::class,'splititems'])->middleware('auth')->name('splititems');
Route::post('/suggestdate',[OrderListController::class,'suggestdate'])->middleware('auth')->name('suggestdate');
Route::post('/newdeliverydate',[OrderListController::class,'newdeliverydate'])->middleware('auth')->name('newdeliverydate');
Route::get('/getpermissions',[PermissionController::class,'getPermissions'])->middleware('superadmin')->name('getpermissions');
Route::post('/setpermission',[PermissionController::class,'setPermission'])->middleware('superadmin')->name('setpermission');
Route::post('/setprofile',[PermissionController::class,'setProfile'])->middleware('superadmin')->name('setprofile');
Route::get('/preload-order-form-info',[OrderController::class, 'preloadOrderFormInfo'])->middleware('auth')->name('preload-order-form-info');

Route::post('/create-customer',[CustomerController::class, 'createCustomer'])->middleware('auth')->name('create-customer');
Route::post('/check-stripe',[CustomerController::class, 'checkStripe'])->middleware('auth')->name('check-stripe');
Route::post('/create-sub-account',[CustomerController::class, 'createSubAccount'])->middleware('auth')->name('create-sub-account');
Route::post('/generate-customer-id',[CustomerController::class, 'generateCustomerID'])->middleware('auth')->name('generate-customer-id');
Route::post('/customerdetails',[CustomerController::class, 'customerDetails'])->middleware('auth')->name('customerdetails');
Route::post('/get-all-customers',[CustomerController::class, 'getAllCustomers'])->name('get-all-customers')->middleware('auth');
Route::post('/get-customer-detail',[CustomerController::class, 'getCustomerDetail'])->middleware('auth')->name('get-customer-detail');
Route::post('/get-customer-full-detail',[CustomerController::class, 'getCustomerFullDetail'])->middleware('auth')->name('get-customer-full-detail');
Route::post('/get-customer-preferences',[CustomerController::class, 'getCustomerPreferences'])->middleware('auth')->name('get-customer-preferences');
Route::post('/add-credit-customer',[CustomerController::class, 'AddCreditCustomer'])->middleware('auth')->name('add-credit-customer');

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
    $id_customer = 	19688;

    $cust = DB::table('infoCustomer')->where('id',$id_customer)->first();

    $addr = DB::table('address')->where('CustomerID',$cust->CustomerID)->where('status','DELIVERY')->first();

    $stripe = new \Stripe\StripeClient(env('STRIPE_TEST_SECURITY_KEY'));

    $card_exp = '12/34';
    $card_num =  '4242 4242 4242 4242';
    $cardholder_name = $cust->FirstName.$cust->LastName;

    $stripe_customer = null;

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
                    'exp_month'   => 12,
                    'exp_year'    => 34,
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
            'amount'            => 100*100, //100*0.01
            'currency'          => 'gbp',
            'confirm'           => true,
            "payment_method"    => $card->stripe_card_id,
            "customer"          => $stripe_customer->id,
            "capture_method"    => "automatic",
            'payment_method_types' => ['card'],
            "description"=>"Order: 12345",
            "receipt_email"=>"rushdi@vpc-direct-service.com",
        ]);

        if($payment_intent->status == 'succeeded'){
            //Update order
            echo "payment succeeded";
        }

    }catch(Exception $e){

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

});

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

/* END TEST ROUTES */

// added by yonghuan to search customers to be linked
Route::post('/search-customer', [SearchController::class, 'SearchCustomersToLink'])->name('SearchCustomersToLink');

Route::post('/SearchCustomer', [SearchController::class, 'SearchCustomer'])->name('SearchCustomer');
Route::post('/SearchByCustomer', [SearchController::class, 'SearchByCustomer'])->name('SearchByCustomer');
Route::post('/ScanItemAndBag', [ScanController::class, 'ScanItemAndBag'])->name('ScanItemAndBag');

Route::post('/get-statistics',[StatisticsController::class, 'getStatistics'])->name('get-statistics')->middleware('auth');

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

/**
 * Voyager custom routes
 *  */

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('category-tailoring',[CategoryTailoringController::class,'index'])->name('category-tailoring')->middleware('auth');
});


/*ALWAYS AT THE BOTTOM*/
Route::get('{any}', function () {
    return view('welcome');
})->where('any','.*');
