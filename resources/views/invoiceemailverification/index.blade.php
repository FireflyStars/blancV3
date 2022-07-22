@extends('voyager::master')

@section('content')
    <style>
        body {
            font-size: 100%;
        }
    </style>
    <div class="d-flex h-100 w-100 bg-white"  style="font-size: initial;">
        <div class="col-lg-12 py-5 mt-5">
            <h3>Missing customer emails in SPOT <span class="badge badge-secondary">{{count($data)}} Missing</span></h3>
            @if(Request::get('suggestedemail')=='ok')
                <div class="alert alert-success" role="alert">
                    Email Updated!  Please update the customer's email in SPOT with the new email address you provided in the table below and click on verify.
                </div>
            @endif
            @if(empty(!$data))
            <div class="alert alert-danger" role="alert">
                The following customers do not have an email address in SPOT. Please update the following customer's email in SPOT with the email addresses provided in the table below and click on verify.
            </div>
            @endif
            <table class="table">
                @if(empty(!$data))
                <thead class="thead-dark">
                <tr>
                    <th scope="col">Customer ID</th>
                    <th scope="col">Customer Name</th>
                    <th scope="col">Invoice Key</th>
                    <th scope="col">Email</th>
                    <th scope="col"></th>
                    <th scope="col">Error</th>
                </tr>
                </thead>
                @endif
                <tbody>


                @forelse ($data as $d)

                    <tr>
                        <th scope="row">{{$d->CustomerID}}</th>
                        <td>{{$d->CustomerName}}</td>
                        <td>{{$d->InvoiceKey}}</td>
                        <td class="suggested_email">
                            <form action="{{route('customer-email-verification')}}" method="get">
                            <div class="input-group" style="margin: 5px 0;">
                                <input type="text" name="newemail" class="form-control"  value="{{$d->email}}">
                                <input type="hidden" name="CustomerID" value="{{$d->CustomerID}}">
                                <div class="input-group-btn">
                                    <button type="submit" class="btn-primary btn" style="margin: 0;">Update</button>
                                </div>
                            </div>
                            </form>
                        </td>
                        <td>
                            <button type="button" class="copie btn btn-secondary">Copy email</button>
                            <a class="btn btn-primary" href="https://blancspot.vpc-direct-service.com/import-2-client-jour-v6-recupEmail.php?email={{$d->email}}&backto=customer-email-verification" role="button">Verify</a></td>
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

    <script type="text/javascript">
        $(function () {
            $('.copie').click(function () {
                $('.copie').removeClass('btn-success');
                $('.copie').html('Copy email');
                $(this).addClass('btn-success');
                $(this).html('Copied!');
                var suggested_email=$(this).parent().siblings('.suggested_email').find('input[name="newemail"]');
                copyToClipboard(suggested_email.val())
            });
        })

        function copyToClipboard(value) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
        }
    </script>
@stop