@extends('voyager::master')
@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/css/bootstrap-notify.css">
@stop
@section('content')
    <example-component></example-component>
    @stop
@section('javascript')
    <script>
        var client_poste_url = "{{Request::url()}}";

        console.log(client_poste_url);

    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.js"></script>

 {{--   <script src="{{asset('js/easyorder.js')}}"></script>--}}
    @stop
