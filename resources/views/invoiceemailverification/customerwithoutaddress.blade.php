@extends('voyager::master')

@section('content')
<style>
    body {
        font-size: 100%;
    }
</style>
    <div class="d-flex h-100 w-100 bg-white"  style="font-size: initial;">
        <div class="col-lg-12 py-5 mt-5">
            <h3>Customer without address in BLANC software <span class="badge badge-secondary">{{count($data)}} Without address</span></h3>
            @if(empty(!$data))
            <div class="alert alert-danger" role="alert">
                The following customers do not have an address in BLANC software. Please update the following customer's address in SPOT and click on verify.
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
                    <th scope="col">Customer Name</th>
                    <th scope="col">Num Invoice</th>
                    <th scope="col"></th>

                </tr>
                </thead>
                @endif
                <tbody>


                @forelse ($data as $d)
                    <tr>
                        <th scope="row">{{$d->Client}}</th>
                        <td><span style="display: inline-block; width:70vw;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;">{{$d->NumInvoice}}</span></td>

                        <td>
                            <a href="?id_customer={{$d->id_customer}}" class="btn btn-primary">Verify</a>
                        </td>

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