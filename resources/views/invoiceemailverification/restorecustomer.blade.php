@extends('voyager::master')

@section('content')
<style>
    body {
        font-size: 100%;
    }
</style>
    <div class="d-flex h-100 w-100 bg-white"  style="font-size: initial;">
        <div class="col-lg-12 py-5 mt-5">
            <h3>Restore Customer</h3>

        </div>
        <div class="container">
        <div class="row">
            <div class="col-lg-6 ">
                <form class=" py-5 mt-5" method="get" action="https://blancspot.vpc-direct-service.com/import-2-client-jour-v9-restaure.php">
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control" id="email" name="customeremail" placeholder="Enter email" required>
                        <input type="hidden" name="token" value="GhtfvbbG44hGtyEfgARRGht31">
                        <input type="hidden" name="backto" value="restore-customer">
                    </div>

                    <button type="submit" class="btn btn-primary">Restore</button>
                </form>
            </div>
        </div>
        </div>
    </div>
@endsection

@section('javascript')


@stop