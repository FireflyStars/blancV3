@extends('voyager::master')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/css/bootstrap-notify.css">
    <link rel="stylesheet" type="text/css" href="{{asset('js/datatable/datatables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/voyager/customer_preference.css')}}"/>
@stop

@section('content')

    <div class="container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div id="notif_div" class="col-lg-12"></div>
        </div>

        <h3 class="page-title">
            <i class="voyager-window-list"></i>

            Add Customer preference</h3>

        <a href="#" class="btn btn-primary pull-right" id="cust_pref_save_btn">
            <i class="voyager-data"></i> <span>Save</span>
        </a>

        <a href="{{route('customer-preference')}}" class="btn btn-dark pull-right">
            <i class="voyager-double-left"></i> <span>Back</span>
        </a>


    </div>


    <div class="page-content browse container-fluid">
        <form method="post" action="{{route('save-customer-preference')}}" id="save_pref_form">
            <input name="id_cp" value="{{$cp?$cp->id:''}}" type="hidden"/>
        <div class="row">
            <div class="col-md-5">
                <div class="row align-content-center">
                    <label class="col-form-label col-md-2 text-right">Title</label>
                    <div class="form-group col-md-8"><input type="text" class="form-control" id="pref_title" name="pref_title" value="{{$cp?$cp->title:''}}"/></div>
                    <div class="col-md-2"><input type="checkbox" name="no_title" id="no_title" @php echo ($cp && $cp->no_title==1?'checked="checked"':'')@endphp/></div>
                </div>
            </div>
            <div class="col-md-6">

                <div class="row">
                    <label class="col-form-label col-md-2 text-right">Type</label>
                    <div class="form-group col-md-3">
                        <select class="form-control" id="type_pref" name="type_pref">
                            <option value="">-</option>
                            <option value="radio" @php if($cp && $cp->preference_type=='radio'){echo 'selected="selected"';}@endphp>Radio</option>
                            <option value="switch" @php if($cp && $cp->preference_type=='switch'){echo 'selected="selected"';}@endphp>Switch</option>
                        </select>
                    </div>
                    <label class="col-form-label col-md-2 text-right">Category</label>
                    <div class="form-group col-md-3">
                        <select class="form-control" id="cat_pref" name="cat_pref">
                            <option value="">-</option>
                            @foreach($categories as $i=>$c)
                                <option value="{{$i}}" @php if($cp && $cp->category_id==$i){echo 'selected="selected"';}@endphp>{{$c}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-5">
                <div class="row">
                    <label class="col-form-label col-md-2 text-right">Description</label>
                    <div class="form-group col-md-8"><textarea class="form-control" rows="5" name="desc_pref">{{$cp?$cp->description:''}}</textarea></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <label class="col-form-label col-md-2 text-right">Values</label>
                    <div class="form-group col-md-8"><textarea class="form-control" rows="8" id="dropdown_values" name="dropdown_values">{{$cp?$cp->dropdown_values:''}}</textarea></div>
                </div>
            </div>
        </div>

           {{-- <input type="submit" id="submit_cust_pref_btn" value="Submit"/>--}}
        </form>
    </div>
    @endsection

@section('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.js"></script>
    <script src="{{asset('js/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('js/voyager/customer_preference.js')}}"></script>
    @endsection
