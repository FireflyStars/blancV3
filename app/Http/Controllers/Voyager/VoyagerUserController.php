<?php

namespace App\Http\Controllers\Voyager;

use App\Group;
use App\Poste;
use App\Screen;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerUserController as BaseVoyagerUserController;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class VoyagerUserController extends BaseVoyagerUserController
{
    public function index(Request $request){
        return parent::index($request);
    }

    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $model = $model->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $model = $model->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        $postes = Poste::all();
        $groups = Group::all();

        $user_groups = explode(",",$dataTypeContent->group_access);

        $user_postes = explode(",",$dataTypeContent->user_postes);
        $cur_employee = Auth::user();

        $cur_user = User::find($id);

        $gp = Screen::getGroupedScreens();

        $group_names = [];
        foreach($groups as $k=>$v){
            $group_names[$v->id] = $v->Name;
        }

        $drivers = $this->getDrivers();
        $cur_drivers = $this->getAlreadySetDrivers();

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable','postes','user_postes','cur_user','cur_employee','groups','user_groups','gp','group_names','drivers','cur_drivers'));
    }

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        $dataTypeContent = (strlen($dataType->model_name) != 0)
            ? new $dataType->model_name()
            : false;

        foreach ($dataType->addRows as $key => $row) {
            $dataType->addRows[$key]['col_width'] = $row->details->width ?? 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'add');

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        $postes = Poste::all();
        $groups = Group::all();
        $user_postes = array();
        $user_groups = array();

        $cur_employee = Auth::user();
        $cur_user = false;

        $gp = Screen::getGroupedScreens();
        $group_names = [];
        foreach($groups as $k=>$v){
            $group_names[$v->id] = $v->Name;
        }

        $drivers = $this->getDrivers();
        $cur_drivers = $this->getAlreadySetDrivers();

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable','postes','user_postes','cur_user','cur_employee','groups','user_groups','gp','group_names','drivers','cur_drivers'));
    }

    public function store(Request $request)
    {

        $postes = $request->post('postes');
        $groups = $request->post('screen_id');

        $driver_id = $request->post('driver_id');


        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        event(new BreadDataAdded($dataType, $data));

        $user_postes = "";
        $user_groups = "";

        $user = User::find($data['id']);

        if(!empty($postes)){
            $user_postes = implode(",",$postes);
            $user->user_postes = $user_postes;
        }

        if(!empty($groups)){
            $user_groups = implode(",",$groups);
            $user->group_access = $user_groups;
        }

        $user->driver_id = $driver_id;
        $user->save();



        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                $redirect = redirect()->route("voyager.{$dataType->slug}.index");
            } else {
                $redirect = redirect()->back();
            }

            return $redirect->with([
                'message'    => __('voyager::generic.successfully_added_new')." {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }


    public function update(Request $request, $id)
    {
        $postes = $request->post('postes');

        $groups = $request->post('screen_id');
        $driver_id = $request->post('driver_id');

        $user_postes = "";
        $user_groups = "";


        if(!empty($postes)){
            $user_postes = implode(",",$postes);
        }

        if(!empty($groups)){
            $user_groups = implode(",",$groups);
        }

        if (Auth::user()->getKey() == $id) {
            $request->merge([
                'role_id'                              => Auth::user()->role_id,
                'user_belongstomany_role_relationship' => Auth::user()->roles->pluck('id')->toArray(),
                //'user_postes'=>$user_postes,
            ]);
        }


        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $data = $model->withTrashed()->findOrFail($id);
        } else {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
        }

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();

        $request->merge([
           'user_postes'=>$user_postes,
            'group_access'=>$user_groups,
        ]);



        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        if($data && $data->id !='') {
            DB::table('users')
                ->where('id', $data->id)
                ->update([
                    'group_access' => $user_groups,
                    'driver_id'=>$driver_id,
                ]);
        }


        event(new BreadDataUpdated($dataType, $data));

        if (auth()->user()->can('browse', $model)) {
            $redirect = redirect()->route("voyager.{$dataType->slug}.index");
        } else {
            $redirect = redirect()->back();
        }


       /* if(!empty($postes)){
            $user = User::find($id);
            $user_postes = implode(",",$postes);

            $user->user_postes = $user_postes;
            $user->save();

        }*/


        return $redirect->with([
            'message'    => __('voyager::generic.successfully_updated')." {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }

    public static function getDrivers(){

        $alldrivers = DB::table('driver')->get();

        return $alldrivers;

    }

    public function getAlreadySetDrivers(){

        $already_set_drivers = [];

        $all_users = User::all();

        foreach($all_users as $k=>$v){
            if($v->driver_id!=0){
                $already_set_drivers[] = $v->driver_id;
            }
        }

        return $already_set_drivers;
    }

}
