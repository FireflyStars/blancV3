@extends('voyager::master')

@section('page_title', __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

<meta name="csrf-token" content="{{ csrf_token() }}">


@section('css')
    <style>
        #all_postes ul li{
            list-style: none;
        }

        .group_label{
            font-weight: bolder;
        }

    </style>

@stop

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
@stop

@section('content')


    <div class="page-content container-fluid">
        @if($cur_user && $cur_user->role_id==1 && $cur_employee->role_id!=1)
            <div class="alert alert-danger">You cannot edit an Administrator
        @else


        <form class="form-edit-add" role="form"
              action="@if(!is_null($dataTypeContent->getKey())){{ route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) }}@else{{ route('voyager.'.$dataType->slug.'.store') }}@endif"
              method="POST" enctype="multipart/form-data" autocomplete="off">
            <!-- PUT Method if we are editing -->
            @if(isset($dataTypeContent->id))
                {{ method_field("PUT") }}
            @endif
            {{ csrf_field() }}

            <div class="row">
                <div class="col-md-8">
                    <div class="panel panel-bordered">
                    {{-- <div class="panel"> --}}
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name">{{ __('voyager::generic.name') }}</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('voyager::generic.name') }}"
                                       value="{{ old('name', $dataTypeContent->name ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="name">User Initials</label>
                                <input type="text" class="form-control" id="UserInitials" name="UserInitials" placeholder="{{ __('voyager::generic.name') }}"
                                       value="{{ old('UserInitials', $dataTypeContent->UserInitials ?? '') }}">
                            </div>


                            <div class="form-group">
                                <label for="email">{{ __('voyager::generic.email') }}</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('voyager::generic.email') }}"
                                       value="{{ old('email', $dataTypeContent->email ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="password">{{ __('voyager::generic.password') }}</label>
                                @if(isset($dataTypeContent->password))
                                    <br>
                                    <small>{{ __('voyager::profile.password_hint') }}</small>
                                @endif
                                <input type="password" class="form-control" id="password" name="password" value="" autocomplete="new-password">
                            </div>

                            @can('editRoles', $dataTypeContent)
                                <div class="form-group">
                                    <label for="default_role">{{ __('voyager::profile.role_default') }}</label>
                                    @php
                                        $dataTypeRows = $dataType->{(isset($dataTypeContent->id) ? 'editRows' : 'addRows' )};

                                        $row     = $dataTypeRows->where('field', 'user_belongsto_role_relationship')->first();
                                        $options = $row->details;
                                    @endphp
                                    @include('voyager::formfields.relationship')
                                </div>
                                <div class="form-group @if($cur_employee->role_id==4) hidden @endif">
                                    <label for="additional_roles">{{ __('voyager::profile.roles_additional') }}</label>
                                    @php
                                        $row     = $dataTypeRows->where('field', 'user_belongstomany_role_relationship')->first();
                                        $options = $row->details;
                                    @endphp
                                    @include('voyager::formfields.relationship')
                                </div>
                            @endcan
                            @php
                            if (isset($dataTypeContent->locale)) {
                                $selected_locale = $dataTypeContent->locale;
                            } else {
                                $selected_locale = config('app.locale', 'en');
                            }

                            @endphp
                            <div class="form-group">
                                <label for="locale">{{ __('voyager::generic.locale') }}</label>
                                <select class="form-control select2" id="locale" name="locale">
                                    @foreach (Voyager::getLocales() as $locale)
                                    <option value="{{ $locale }}"
                                    {{ ($locale == $selected_locale ? 'selected' : '') }}>{{ $locale }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{--<div class="form-group">
                                <label for="postes">Group Access</label>
                                <select class="form-control select2" multiple id="group_postes" name="group_postes[]">
                                    @foreach($groups as $k=>$v)
                                        <option value="{{$v->id}}" @if(in_array($v->id,$user_groups)) selected @endif>{{$v->Name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        --}}

                            <div class="form-group">
                                <label>Is Driver</label>
                                <select class="form-control" name="driver_id">
                                    <option value="0">-</option>
                                    @if(count($drivers) > 0)
                                    @foreach($drivers as $k=>$v)
                                            <option value="{{$v->id}}" @php if($cur_user && $cur_user->driver_id==$v->id){echo "selected";} else if(in_array($v->id,$cur_drivers)){echo " disabled";} @endphp>{{$v->name}}</option>
                                        @endforeach
                                        @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="postes">Workstations</label>
                                <select class="form-control select2" multiple id="postes" name="postes[]">
                                    @foreach($postes as $k=>$v)
                                        <option value="{{$v->id}}" @if(in_array($v->id,$user_postes)) selected @endif>{{$v->nom}}</option>
                                        @endforeach
                                </select>
                            </div>



                            {{--Start multi-level--}}

                            <div class="form-group" id="all_postes">

                                <label for="postes">Group Access</label>

                                <ul>
                                    @foreach($gp as $k=>$v)
                                        <li>
                                            <input type="checkbox" id="group_{{$k}}" class="each-group-check">
                                            <label for="group_{{$k}}" class="group_label">{{$group_names[$k]}}</label>
                                            <ul class="each-group-ul">
                                                @foreach($v as $i=>$x)
                                                    <li>
                                                    <input type="checkbox" name="screen_id[]" id="poste{{$x['screen_id']}}" value="{{$x['screen_id']}}" class="each-poste-check" @php if(in_array($x['screen_id'],$user_groups) || ($x['screen_name']=='Choose a workstation' && !empty($user_postes))){echo "checked";}@endphp>
                                                    <label for="{{$x['screen_id']}}">{{$x['screen_name']}}</label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endforeach

                                </ul>





                            </div>





                            {{--end multi-level--}}




                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="panel panel panel-bordered panel-warning">
                        <div class="panel-body">
                            <div class="form-group">
                                @if(isset($dataTypeContent->avatar))
                                    <img src="{{ filter_var($dataTypeContent->avatar, FILTER_VALIDATE_URL) ? $dataTypeContent->avatar : Voyager::image( $dataTypeContent->avatar ) }}" style="width:200px; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:10px;" />
                                @endif
                                <input type="file" data-name="avatar" name="avatar">
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <button type="submit" class="btn btn-primary pull-right save">
                {{ __('voyager::generic.save') }}
            </button>
        </form>



        <iframe id="form_target" name="form_target" style="display:none"></iframe>
        <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post" enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
            {{ csrf_field() }}
            <input name="image" id="upload_file" type="file" onchange="$('#my_form').submit();this.value='';">
            <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
        </form>
    </div>
        @endif
@stop

@section('javascript')


    <script>
        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();


            $('.each-group-ul').each(function () {
                var num_postes = $(this).find('.each-poste-check');
                var active_postes = $(this).find('.each-poste-check:checked');
                var group_check = $(this).siblings('.each-group-check');

                //console.log(group_check);

                if(parseInt(num_postes.length)==parseInt(active_postes.length)){
                    group_check.prop({
                        indeterminate: false,
                        checked: true,
                    });
                }else if(parseInt(active_postes.length) > 0 && parseInt(num_postes.length) > parseInt(active_postes.length)){
                    group_check.prop({
                        indeterminate: true,
                        checked: false,
                    });
                }
            });

            $('#all_postes input[type="checkbox"]').change(function(e) {

                var checked = $(this).prop("checked"),
                    container = $(this).parent(),
                    siblings = container.siblings();

                container.find('input[type="checkbox"]').prop({
                    indeterminate: false,
                    checked: checked
                });


                function checkSiblings(el) {

                    var parent = el.parent().parent(),
                        all = true;

                    el.siblings().each(function() {
                        let returnValue = all = ($(this).children('input[type="checkbox"]').prop("checked") === checked);
                        return returnValue;
                    });

                    if (all && checked) {

                        parent.children('input[type="checkbox"]').prop({
                            indeterminate: false,
                            checked: checked
                        });

                        checkSiblings(parent);

                    } else if (all && !checked) {

                        parent.children('input[type="checkbox"]').prop("checked", checked);
                        parent.children('input[type="checkbox"]').prop("indeterminate", (parent.find('input[type="checkbox"]:checked').length > 0));
                        checkSiblings(parent);

                    } else {

                        el.parents("li").children('input[type="checkbox"]').prop({
                            indeterminate: true,
                            checked: false
                        });

                    }

                }

                checkSiblings(container);
            });



        });




    </script>

    <script src="{{ asset('js/voyager.js') }}" defer></script>

@stop
