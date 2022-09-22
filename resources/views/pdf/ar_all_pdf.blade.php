<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
@include('pdf.pdf_style')
</head>
<body style="font-family: Helvetica;">
    @foreach($details_per_cust as $k=>$v)

    <div class="each-page-content @php if($k+1 < count($details_per_cust)){echo "page-break";} @endphp">
        @include('pdf.pdf_header')
        @include('pdf.pdf_footer')
        @include('pdf.pdf_main')
    </div>

    @endforeach

</body>
</html>
