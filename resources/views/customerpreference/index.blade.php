@extends('voyager::master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('js/datatable/datatables.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/css/bootstrap-notify.css">
    <link rel="stylesheet" href="{{asset('css/voyager/customer_preference.css')}}"/>

    <style>
        .align-cell-middle{
            vertical-align: middle !important;
        }

      .each-pref-btn{
            color:white;
        }

        .statut-cp{
            display:block;
            font-size:16pt;
            margin-top:5px;
        }

        .statut-cp.voyager-check{
            color:#42a71e;
        }

        .statut-cp.voyager-x{
            color:#eb5757;
        }
    </style>

    @endsection

@section('content')
    <style>
        body {
            font-size: 100%;
        }
    </style>
    <div class="container-fluid">
            <h3 class="page-title"><i class="voyager-window-list"></i>Customer preferences</h3>
            <a href="{{route('view-customer-preference')}}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>Add New</span>
            </a>
    </div>
    <div class="page-content browse container-fluid">

        <div id="result" style="display:none;">
            Event result:
        </div>


        <table class="table" id="cust_pref_table">

                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Category</th>
                        <th scope="col">Title</th>
                        <th scope="col">Description</th>
                        <th scope="col">Type</th>
                        <th scope="col">Values</th>
                        <th scope="col">Sequence</th>
                        <th scope="col">Active</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                <tbody>
                 @if(!empty($data))
                        @foreach($data as $d)
                            <tr id="{{$d['id']}}">
                                <td>{{$d['id']}}</td>
                                <td>{{$d['category_name']}}</td>
                                <td>{{$d['title']}}</td>
                                <td>{{$d['description']}}</td>
                                <td>{{$d['preference_type']}}</td>
                                <td>{{$d['dropdown_values']}}</td>
                                <td class="text-center align-cell-middle">{{$d['sequence']}}</td>
                                <td class="align-cell-middle"><i class="statut-cp @php echo 'voyager-'.($d['deleted']==1?'x':'check'); @endphp"></i></td>
                                <td width="20%" class="align-cell-middle">
                                    <a class="each-pref-btn btn btn-dark text-white" href="{{route('view-customer-preference')}}?id={{$d['id']}}" style="text-decoration: none; margin-right: 30px;">Edit</a>{{--<i class="voyager-pen"></i>--}}
                                    <a class="each-pref-btn btn @php echo ($d['deleted']==1?'btn-success':'btn-danger'); @endphp" href="{{route('delete-customer-pref')}}?id={{$d['id']}}" style="text-decoration: none;">@if($d['deleted']==0)Delete @else Restore @endif</a>{{--<i class="voyager-trash"></i>--}}
                                </td>
                            </tr>
                            @endforeach

                     @endif


                </tbody>

                @if(!empty($data))
                <tfoot>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Title</th>
                    <th scope="col">Description</th>
                    <th scope="col">Type</th>
                    <th scope="col">Values</th>
                    <th scope="col"></th>
                </tr>
                </tfoot>
                @endif
            </table>

    </div>

    @endsection

@section('javascript')
    <script>
        var cust_pref_url =  "{{Request::url()}}";
    </script>
    <script src="{{asset('js/datatable/datatables.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.js"></script>
    <script src="{{asset('js/voyager/customer_preference.js')}}"></script>
    @endsection