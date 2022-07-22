@extends('voyager::master')

@section('content')
<style>
    body {
        font-size: 100%;
    }
</style>
    <div class="d-flex h-100 w-100 bg-white"  style="font-size: initial;">
        <div class="col-lg-12 py-5 mt-5">
            <h3>Wrong customer emails in BLANC software <span class="badge badge-secondary">{{count($data)}} Wrong email(s)</span></h3>
            @if(empty(!$data))
            <div class="alert alert-danger" role="alert">
                The following customers have a wrong email address in BLANC software. Please update the following customer's email below and click on Save and verify.
            </div>
            @endif

            @if (session('status'))
                <div class="alert alert-danger">
                    {{ session('status') }}
                </div>
            @endif
            <table class="table">
                @if(empty(!$data))
                <thead class="thead-dark">
                <tr>
                    <th scope="col">Customer ID</th>
                    <th scope="col">Customer Name</th>
                    <th scope="col">Invoice Key</th>
                    <th scope="col">Current Email</th>
                    <th scope="col"></th>
                    <th scope="col">Error</th>
                </tr>
                </thead>
                @endif
                <tbody>


                @forelse ($data as $d)
                    <tr @if(session('err_cus_id')==$d->CustomerID) class="table-danger" @endif>
                        <th scope="row">{{$d->CustomerID}}</th>
                        <td>{{$d->CustomerName}}</td>
                        <td>{{$d->InvoiceKey}}</td>
                        <td>{{$d->email}}</td>
                        <td><form method="post"  action="{{route('bad-customer-email-save')}}">
                                @csrf
                            <div class="row">
                            <div class="input-group col">
                                <input type="hidden" name="customerid" value="{{$d->CustomerID}}">
                                <input type="hidden" name="previousEmail" value="{{$d->email}}">
                                <input type="email" name="newemail" class="form-control" placeholder="Customer's email" aria-label="Customer's email" aria-describedby="button-addon2">
                                <div class="input-group-append">
                                    <button type="submit" id="button-addon2" class="btn btn-primary">Save & Verify</button>
                                </div>
                            </div>
                            </div>
                            </form>
                        </td>
                        <td>@if ($d->error=="")Not Verified @else {{$d->error}}@endif</td>
                    </tr>
                @empty
                    <div class="alert alert-success" role="alert">
                        Verification completed. You can now close this page.
                    </div>
                @endforelse
                </tbody>
            </table>
        </div>


    </div>
@endsection

@section('javascript')


@stop