@extends('voyager::master')

@section('content')
    <style>
        body {
            font-size: 100%;
        }
        td.rotate{
            border: none!important;
            padding: 3px 0!important;
            text-align: center;
        }
        table.dataTable tbody td, table.dataTable tbody th {
            padding: 3px 4px;
        }
        td.rotate span
        {
            -ms-writing-mode: tb-rl;
            -webkit-writing-mode: vertical-rl;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: nowrap;
        }

        select {
  /* for Firefox */
  -moz-appearance: none;
  /* for Chrome */
  -webkit-appearance: none;
}

/* For IE10 */
select::-ms-expand {
  display: none;
}
        td.checked{
            background-color: blue;
        }

        table.dataTable thead > tr > th {
            padding-left: 0;
            padding-right: 5px;
        }
        .bgmonday{
            background: rgba(245,204,200,0.5);
        }
        .bgtuesday{
            background:rgba(225,163,164,0.5);
        }
        .bgwednesday{
            background: rgba(193,212,229,0.5);
        }
        .bgthursday{
            background: rgba(140,163,179,0.5);
        }
        .bgfriday{
            background: rgba(229,196,189,0.5);
        }
        .sel{
            width: 100%;
            background-color: #FFF;
            color:black;
            font-weight: bold;
        }
    </style>
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-milestone"></i> Postcodes
        </h1>




        <div class="page-content browse container-fluid">
            <div class="alerts">

            </div>
            <div class="row">
                <div class="col">

                    @if(isset($_GET['postcode_count'])&&$_GET['postcode_count']>0)
                        <div class="alert alert-success">
                            <p>Postcodes inserted successfully, you can now configures the timeslots.</p>
                        </div>
                    @endif
                        @if(isset($_GET['existing_postcodes'])&&$_GET['existing_postcodes']!="")
                            <div class="alert alert-warning">
                                <i>The following postcodes already exists and has not been inserted:</i>
                                <p>{{$_GET['existing_postcodes']}}</p>
                            </div>
                        @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-bordered">
                        <div class="panel-body">
                            <form method="post"  action="{{route('save-postcodes')}}">
                                @csrf
                                <div class="form-group">
                                <textarea class="form-control" name="newpostcodes" placeholder="Example: postcode1,poscode2,poscode3,..." autocomplete="off"></textarea>
                                </div>
                                <button class="btn btn-success btn-add-new" type="submit">
                                    <i class="voyager-plus"></i> <span>Add postcodes</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                @if(isset($_GET['delete'])&&$_GET['delete']==0)
                <div class="alert alert-danger">
                    <i>Unable to delete postcodes:</i>
                    <p>Please select postcodes.</p>
                </div>
                    @endif
                    @if(isset($_GET['delete'])&&$_GET['delete']==1)
                        <div class="alert alert-success">
                            <p>Postcodes deleted.</p>
                        </div>
                    @endif
                    @if(isset($_GET['updateok'])&&$_GET['updateok']==1)
                        <div class="alert alert-success">
                            <p>Postcodes updated successfully.</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-bordered">
                        <div class="panel-body">
                            <form method="post"  action="{{route('save-postcodes')}}">
                                @csrf
                                <button type="submit" class="btn btn-danger" id="bulk_delete_btn" name="bulkdelete" value="bulkdelete"><i class="voyager-trash"></i> <span>Bulk Delete</span></button>
                                <button type="submit" class="btn btn-success" id="bulk_save_btn" name="bulksave" value="bulksave"><i class="voyager-check-circle"></i> <span>Save all</span></button>
                                <br>  <br>
                            <table id="example" class="table table-hover dataTable no-footer" style="width:100%">
                                <tbody>

                                @php
                                    $i = 1
                                @endphp
                                @foreach ($group_postcodes as $postcode=>$days)
@if($i==1)
    <tr>
        <th>&nbsp;</th>
        <th class="">Postcodes</th>
        <th class="text-center bgmonday">Monday</th>
        <th class="text-center bgtuesday">Tuesday</th>
        <th class="text-center bgwednesday">Wednesday</th>
        <th class="text-center bgthursday">Thursday</th>
        <th class="text-center bgfriday">Friday</th>
        <th class="text-center">Saturday</th>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th style="padding: 0" class="bgmonday">
            <table style="width: 100%;">
                <tbody>
                <tr style="background: transparent;"><td class="rotate"><span>8-10 am</span></td>
                    <td class="rotate"><span>10-12 pm</span></td>
                    <td class="rotate"><span>12-2 pm</span></td>
                    <td class="rotate"><span>2-4 pm</span></td>
                    <td class="rotate"><span>4-6 pm</span></td>
                    <td class="rotate"><span>6-8 pm</span></td>   </tr>

                </tbody>
            </table>
        </th>
        <th style="padding: 0" class="bgtuesday">
            <table style="width: 100%;">
                <tbody>
                <tr style="background: transparent;"><td class="rotate"><span>8-10 am</span></td>
                    <td class="rotate"><span>10-12 pm</span></td>
                    <td class="rotate"><span>12-2 pm</span></td>
                    <td class="rotate"><span>2-4 pm</span></td>
                    <td class="rotate"><span>4-6 pm</span></td>
                    <td class="rotate"><span>6-8 pm</span></td>   </tr>

                </tbody>
            </table>
        </th>
        <th style="padding: 0" class="bgwednesday">
            <table style="width: 100%;">
                <tbody>
                <tr style="background: transparent;"><td class="rotate"><span>8-10 am</span></td>
                    <td class="rotate"><span>10-12 pm</span></td>
                    <td class="rotate"><span>12-2 pm</span></td>
                    <td class="rotate"><span>2-4 pm</span></td>
                    <td class="rotate"><span>4-6 pm</span></td>
                    <td class="rotate"><span>6-8 pm</span></td>   </tr>

                </tbody>
            </table>
        </th>
        <th style="padding: 0" class="bgthursday">
            <table style="width: 100%;">
                <tbody>
                <tr style="background: transparent;"><td class="rotate"><span>8-10 am</span></td>
                    <td class="rotate"><span>10-12 pm</span></td>
                    <td class="rotate"><span>12-2 pm</span></td>
                    <td class="rotate"><span>2-4 pm</span></td>
                    <td class="rotate"><span>4-6 pm</span></td>
                    <td class="rotate"><span>6-8 pm</span></td>   </tr>

                </tbody>
            </table>
        </th>
        <th style="padding: 0" class="bgfriday">
            <table style="width: 100%;">
                <tbody>
                <tr style="background: transparent;"><td class="rotate"><span>8-10 am</span></td>
                    <td class="rotate"><span>10-12 pm</span></td>
                    <td class="rotate"><span>12-2 pm</span></td>
                    <td class="rotate"><span>2-4 pm</span></td>
                    <td class="rotate"><span>4-6 pm</span></td>
                    <td class="rotate"><span>6-8 pm</span></td>   </tr>

                </tbody>
            </table>
        </th>
        <th style="padding: 0">
            <table style="width: 100%;">
                <tbody>
                <tr style="background: transparent;"><td class="rotate"><span>8-10 am</span></td>
                    <td class="rotate"><span>10-12 pm</span></td>
                    <td class="rotate"><span>12-2 pm</span></td>
                    <td class="rotate"><span>2-4 pm</span></td>
                    <td class="rotate"><span>4-6 pm</span></td>
                    <td class="rotate"><span>6-8 pm</span></td>   </tr>

                </tbody>
            </table>
        </th>
    </tr>
@endif
@php
    $i++;
    if($i==11)
    $i=1;
@endphp
                                <tr>
                                    <td><input type="checkbox" name="sel_postcode[]" value="{{$postcode}}" autocomplete="off"></td>
                                    <td>{{$postcode}}</td>
                                    @foreach ($DAYS as $day)
                                        <td style="padding: 0">
                                            <table style="width: 100%" class="@if($day=="MONDAY")bgmonday @endif @if($day=="TUESDAY")bgtuesday @endif @if($day=="WEDNESDAY")bgwednesday @endif @if($day=="THURSDAY")bgthursday @endif @if($day=="FRIDAY")bgfriday @endif">
                                                <tbody>
                                                <tr style="background: transparent;">
                                                    <td @if (isset($group_postcodes2[$postcode][$day])&&in_array(1,$group_postcodes2[$postcode][$day]))class="checked" @endif><select class="sel" name="#{{$postcode}}_{{$day}}_1" autocomplete="off"><option value="">--</option>@foreach ($preroutes['MONDAY'] as $preroute)<option @if (isset($group_postcodes[$postcode][$day])&&key_exists(1,$group_postcodes[$postcode][$day])&&$group_postcodes[$postcode][$day][1]==$preroute->id) selected="selected" @endif value="{{$preroute->id}}">{{$preroute->number}}</option>@endforeach</select></td>
                                                    <td @if (isset($group_postcodes2[$postcode][$day])&&in_array(3,$group_postcodes2[$postcode][$day]))class="checked" @endif><select class="sel" name="#{{$postcode}}_{{$day}}_3" autocomplete="off"><option value="">--</option>@foreach ($preroutes['TUESDAY'] as $preroute)<option @if (isset($group_postcodes[$postcode][$day])&&key_exists(3,$group_postcodes[$postcode][$day])&&$group_postcodes[$postcode][$day][3]==$preroute->id) selected="selected" @endif value="{{$preroute->id}}">{{$preroute->number}}</option>@endforeach</select></td>
                                                    <td @if (isset($group_postcodes2[$postcode][$day])&&in_array(5,$group_postcodes2[$postcode][$day]))class="checked" @endif><select class="sel" name="#{{$postcode}}_{{$day}}_5" autocomplete="off"><option value="">--</option>@foreach ($preroutes['WEDNESDAY'] as $preroute)<option @if (isset($group_postcodes[$postcode][$day])&&key_exists(5,$group_postcodes[$postcode][$day])&&$group_postcodes[$postcode][$day][5]==$preroute->id) selected="selected" @endif value="{{$preroute->id}}">{{$preroute->number}}</option>@endforeach</select></td>
                                                    <td @if (isset($group_postcodes2[$postcode][$day])&&in_array(7,$group_postcodes2[$postcode][$day]))class="checked" @endif><select class="sel" name="#{{$postcode}}_{{$day}}_7" autocomplete="off"><option value="">--</option>@foreach ($preroutes['THURSDAY'] as $preroute)<option @if (isset($group_postcodes[$postcode][$day])&&key_exists(7,$group_postcodes[$postcode][$day])&&$group_postcodes[$postcode][$day][7]==$preroute->id) selected="selected" @endif value="{{$preroute->id}}">{{$preroute->number}}</option>@endforeach</select></td>
                                                    <td @if (isset($group_postcodes2[$postcode][$day])&&in_array(9,$group_postcodes2[$postcode][$day]))class="checked" @endif><select class="sel" name="#{{$postcode}}_{{$day}}_9" autocomplete="off"><option value="">--</option>@foreach ($preroutes['FRIDAY'] as $preroute)<option @if (isset($group_postcodes[$postcode][$day])&&key_exists(9,$group_postcodes[$postcode][$day])&&$group_postcodes[$postcode][$day][9]==$preroute->id) selected="selected" @endif value="{{$preroute->id}}">{{$preroute->number}}</option>@endforeach</select></td>
                                                    <td @if (isset($group_postcodes2[$postcode][$day])&&in_array(11,$group_postcodes2[$postcode][$day]))class="checked" @endif><select class="sel" name="#{{$postcode}}_{{$day}}_11" autocomplete="off"><option value="">--</option>@foreach ($preroutes['SATURDAY'] as $preroute)<option @if (isset($group_postcodes[$postcode][$day])&&key_exists(11,$group_postcodes[$postcode][$day])&&$group_postcodes[$postcode][$day][11]==$preroute->id) selected="selected" @endif value="{{$preroute->id}}">{{$preroute->number}}</option>@endforeach</select></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach

                            </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('javascript')

<script type="text/javascript">
    $(document).ready( function () {
     //   $('#example').DataTable();
    } );
</script>
@stop