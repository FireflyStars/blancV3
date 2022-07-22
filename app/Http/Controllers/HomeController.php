<?php

namespace App\Http\Controllers;

use App\Group;
use App\Item;
use App\Mail\MailCustomerCare;
use App\Poste;
use App\Screen;
use App\Subgroup;
use App\User;
use http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        if($user->firstname=='' && $user->lastname==''){
            $name_arr = explode(' ',$user->name);

            $user->firstname = $name_arr[0];
            $user->lastname = (isset($name_arr[1])?$name_arr[1]:"");
        }

        $postes = $user->postes();


        //$timeout = Voyager::setting('site.deconnection_timeout');

        $timeout = env('SESSION_LIFETIME', 120);

        return view('poste.index',[
            'user'=>$user,
            'postes'=>$postes,
            'conn_timeout'=>$timeout,
        ]);

    }

    public function getUserDetails(Request $request){
        $user_id = $request->post('user_id');
        $user = User::find($user_id);

        $screens = $user->group_access;
        $groups = [];
        $screen_ids = [];

        if($screens !=''){
            $screen_ids = explode(",",$screens);
            $groups = DB::table('groups')
                ->select('groups.*')
                ->join('screens','groups.id','screens.group_id')
                ->whereIn('screens.id',$screen_ids)
                ->groupBy('groups.id')
                ->get();
        }

        if($user->role_id==1){ //Superadmin bie.email.testeur
            $groups = Group::all();
        }
		
		if($user->driver_id !=0){ //Drivers
			$groups = Group::where('id',4)->get();
		}


        return \response()->json([
            'groups'=>$groups,
        ]);
    }


    public function setUserDetails(Request $request){
        $POST = $request->all();

        $err = [];
        $emailregex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

        if(trim($POST['firstname'])=='' && trim($POST['lastname'])==''){
            $err[] = "Please enter firstname or lastname";
        }
        if(trim($POST['email'])=='' || !preg_match($emailregex, $POST['email'])){
            $err[] = "Please enter a valid email address";
        }

        if($POST['phone']!='' && !is_numeric($POST['phone'])){
            $err[] = "Please enter a valid phone number";
        }

        $saved = false;

        if(empty($err)){
            $user = Auth::user();
            $user->firstname = $POST['firstname'];
            $user->lastname = $POST['lastname'];
            $user->email = $POST['email'];
            $user->phone = $POST['phone'];
            $user->name = $POST['firstname'].($POST['lastname']!=''?" ":"").$POST['lastname'];

            if(isset($POST['password'])){
                $new_pass = Hash::make($POST['password']);
                $saved = DB::table('users')->where('id',$user->id)
                    ->update([
                        'password'=>$new_pass,
                    ]);
            }

            $saved = $user->save();
        }

        return \response()->json([
            'post'=>$request->all(),
            'err'=>$err,
            'saved'=>$saved,
        ]);
    }

    public function getGroupLinks(Request $request){
        $route_name = $request->post('route_name');
        $user = Auth::user();

        $postes = [];

        if($user){
            $user_screens = $user->group_access;

            $screen_ids = explode(",",$user_screens);

			$screens = Screen::all();

            if($user->role_id==1){ //Super admin
                foreach($screens as $k=>$v){
                    $screen_ids[] = $v->id;
                }
            }
						
            if(!empty($screen_ids)){
                $postes = DB::table('screens')
                    ->select('screens.*','screens.route AS route_name')
                    ->join('groups','screens.group_id','groups.id')
                    ->where('groups.URL',$route_name)
                    ->where('screens.is_quick_link',1)
                    ->whereIn('screens.id',$screen_ids)
                    ->get();


            }
			
			if($user->driver_id !=0){ //drivers
				$postes = DB::table('screens')
                    ->select('screens.*','screens.route AS route_name')
                    ->join('groups','screens.group_id','groups.id')
					->where('screens.id',24)
					->get();
			}

        }

        return \response()->json([
           'postes'=>$postes,
        ]);
    }

    public function getBreadcrumb(Request $request){
        $route_name = $request->post('route_name');

        $details = DB::table('screens')
            ->select('groups.Name as group','screens.breadcrumb_name as poste','subgroups.name as subgroup')
            ->join('groups','screens.group_id','groups.id')
            ->leftJoin('subgroups','screens.subgroup_id','subgroups.id')
            ->where('screens.route',$route_name)
            ->first();

        return \response()->json(
            [
                'details'=>$details,
                //'post'=>$request->all(),
            ]
        );
    }

    public function getSidebarLinks(Request $request){
        $route_name = $request->post('route_name');
        if($route_name=='edit-customer-blanc'){
            $route_name = 'customers';
        }
        if($route_name=='edit-driver'){
            $route_name = 'delivery-settings';
        }
        if($route_name=='dispatch-detail'){
            $route_name = 'dispatch';
        }
        if($route_name=='invoice-item'){
            $route_name = 'assembly-all-orders';
        }

        $user = Auth::user();
        $screen_ids = [];


        $menus = [];
        $id_group = 0;
        $menus1 = [];
        $menus2 = [];
        $grouped_menus = [];
        $subgroups = [];

        $screen_ids = [];

        if($user) {
            $screen_ids = explode(",",$user->group_access);

            $group = DB::table('groups')->where('URL', $route_name)->first();

            if ($group) {
                $id_group = $group->id;
            }else {
                $screen = DB::table('screens')->where('route',$route_name)->first();

                if($screen){
                    $id_group = $screen->group_id;
                }

            }

            if($id_group > 0 && count($screen_ids) > 0){
                $gp = Group::find($id_group);

                $menus[] = [
                    'name'=>$gp->Name." Home",
                    'route_name'=>$gp->URL,
                    'icon'=>'home_icon.jpg',
                    'subgroup_id'=>0,
                ];

                $screens = Group::getScreens($id_group);

                $sg = Subgroup::all();
                if(count($sg) > 0){
                    foreach ($sg as $k=>$v){
                        $subgroups[$v->id] = [
                            'name'=>$v->name,
                            'icon'=>$v->icon,
                        ];
                    }
                }


                if(count($screens) > 0){
                    foreach($screens as $k=>$v){
                        if($v->name=='Operations'){
                            $v->name = 'Operators';
                        }
                        if($v->name=='Choose a workstation'){
                            $v->name = 'Workstations';
                        }

                        if($v->route != 'my-delivery-run'){
                            if($user->role_id==1){
                                $menus[] = [
                                    'name' => $v->name,
                                    'route_name' => $v->route,
                                    'icon' => $v->icon,
                                    'subgroup_id'=>$v->subgroup_id,
                                    'subgroup_name'=>'',
                                    'is_sidebar_link'=>$v->is_sidebar_link
                                ];
                            }else {

                                if (in_array($v->id, $screen_ids)) {
                                    $menus[] = [
                                        'name' => $v->name,
                                        'route_name' => $v->route,
                                        'icon' => $v->icon,
                                        'subgroup_id'=>$v->subgroup_id,
                                        'subgroup_name'=>'',
                                        'is_sidebar_link'=>$v->is_sidebar_link
                                    ];
                                }
                            }
                        }
                    }

                    foreach($menus as $k=>$v){
                        if($v['subgroup_id']==0){
                            $menus1[] = $v;
                        }else{
                            $v['icon'] = $subgroups[$v['subgroup_id']]['icon'];
                            $v['subgroup_name'] = $subgroups[$v['subgroup_id']]['name'];
                            $menus2[$v['subgroup_id']][] = $v;
                        }
                    }

                    $grouped_menus = array_merge($menus1,$menus2);

                }
				
				if($user->driver_id !=0 && $user->role_id!=1){ //menus for drivers
					$grouped_menus = [
						[
							"name"=> "Logistics Home",
							"route_name"=> "logistics-home",
							"icon"=> "home_icon.jpg",
							"subgroup_id"=> 0
						],
						[
							"name"=> "My delivery run",
							"route_name"=> "my-delivery-run",
							"icon"=> "message_icon.jpg",
							"subgroup_id"=> 0
						],
						[
							"name"=> "Settings",
							"route_name"=> "driver-settings",
							"icon"=> "settings_icon.jpg",
							"subgroup_id"=> 0
						],
						
					];
				}
            }
        }

        //if(in_array($ro))


        return \response()->json([
            'post'=>$request->all(),
            'menus'=>$grouped_menus,
        ]);

    }


    public function saveAndSendCC(Request $request){
        $user = Auth::user();
        $saved = false;
        $POST = $request->all();
        $err_no_value_changed = false;

        foreach ($POST AS $k=>$v){
            $POST[$k] = (is_null($v)?0:$v);
        }

        $value_changed = [];

        if($user && isset($POST['items_id'])) {
            $id_item = $POST['items_id'];

            $item = Item::where('id',$id_item)->first();


            if($item) {
                $inv = DB::table('infoInvoice')
                    ->select('infoOrder.id AS order_id','infoInvoice.*')
                    ->join('infoOrder','infoInvoice.OrderID','infoOrder.OrderID')
                    ->where('id_invoice',$item->id_invoice)->first();

                $POST['infoOrder_id'] = ($inv?$inv->order_id:'');
                $POST['id_item'] = $item->id_items;
                $POST['items_id'] = $id_item;
                $POST['id_invoice'] = $item->id_invoice;
                $POST['CustomerID'] = ($inv?$inv->CustomerID:"");
                $POST['user_id'] = $user->id;
                $POST['created_at'] = date('Y-m-d H:i:s');

                $has_item = DB::table('itemAuto')->where('items_id',$id_item)->first();

                if($has_item){
                    $item_arr = (array) $has_item;

                    foreach($POST as $k=>$v){
                        if($k!='created_at' && $item_arr[$k]!=$v){
                            $value_changed[$k] = $v;
                        }
                    }

                    $POST['updated_at'] = date('Y-m-d H:i:s');
                    $saved = DB::table('itemAuto')->where('items_id',$id_item)->update($POST);
                }else {
                    $saved = DB::table('itemAuto')->insert($POST);
                }

                if(!$has_item || count($value_changed) > 0){
                    //send mail to customer
                }elseif($has_item && count($value_changed) == 0){
                    $err_no_value_changed = true;
                    $saved = false;
                }
            }

        }

        return \response()->json([
           'post'=>$request->all(),
            'value_changed'=>$value_changed,
            'saved'=>$saved,
            'err_no_value_changed'=>$err_no_value_changed,
        ]);
    }

    public function getItemAuto(Request $request){
        $id_item = $request->post('id_item');
        $auto_details = DB::table('itemAuto')->where('items_id',$id_item)->first();
        
        $has_response = false;
        if ($auto_details->ReponseCleaning > 0 || $auto_details->ReponseMinorRepair > 0 || $auto_details->ReponseTailoring > 0 || $auto_details->ReponseRisk > 0 || $auto_details->ReponseLeather > 0){
            $has_response = true;
        }

        return \response()->json([
            'auto_details'=>$auto_details,
            'has_response'=>$has_response,
        ]);
    }

    public function resendMail(Request $request){

    }


    public static function sendMail($id_item){
        $sent = false;
        $cc_details = DB::table('itemAuto')
            ->select('itemAuto.*','infoInvoice.numInvoice','infoCustomer.FirstName','infoCustomer.Title','infoCustomer.LastName','infoitems.brand','infoitems.typeitem','infoitems.ItemTrackingKey')
            ->join('infoitems','itemAuto.items_id','infoitems.id_items')
            ->join('infoInvoice','itemAuto.infoOrder_id','infoInvoice.id_invoice')
            ->join('infoCustomer','itemAuto.CustomerID','infoCustomer.CustomerID')
            ->where('itemAuto.items_id',$id_item)->first();

        if($cc_details) {
            $data = (array) $cc_details;
            $cost_fields = ['QuoteMinorRepair','QuoteTailoring','QuoteLeather'];
            $timing_fields = ['TimingTailoring','TimingLeather'];

            foreach ($data as $k=>$v){
                if(in_array($k,$cost_fields) && $v > 0){
                    $data[$k] = number_format($v,2);
                }
                if(in_array($k,$timing_fields)){
                    $data[$k] = $v." ".($v > 1?"days":"day");
                }
            }

            $recipients = ['rushdi@vpc-gestion.com'];

            Mail::to($recipients)
                //->cc('')
                ->send(new MailCustomerCare($data));

            $sent = true;
        }

        return $sent;

    }

}
