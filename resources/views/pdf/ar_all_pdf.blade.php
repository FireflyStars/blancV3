<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>

    @page { size: 21cm 29.7cm portrait; }

    body{

        margin-top:40%;
        margin-bottom:22%;

    }

    .page-break {
        page-break-after: always;
    }

    .each-page-content{;
    }

    header { position: fixed; top: 0px; left: 0px; right: 0px; height: 25%;}


    footer{
        position:fixed;
        bottom:160px;
        right:0;
        left:0;
    }

    #footer_total{
        border:2px solid #000;
        width:210px;
        font-size:12px;
        position:absolute;
        right:0;
    }

    main{
        /*
        margin-top:40%;
        margin-bottom:22%;
        */
    }

    #inv_summary{
        position: fixed;
        top:0px;
        right:0px;
        display:table;
        width:35%;
    }

    #inv_summary #heading{
        text-align: center;
        background:#808080;
        color:#fff;
        padding:15px 0;
    }

    #inv_summary #heading h3{
        margin:0;
        font-family:"Times New Roman";
    }

    .heading-cell{
        width:50%;
        border:thin solid #808080;
    }

    .heading-cell-title{
        font-size:10px;
        color:#42A71E;
    }

    .heading-cell-value{
        font-size:11px;
        text-align: right;
    }

    #inv_no{
        font-size:12px;
    }

    #blanc_addr{
        margin-top:10px;
        margin-bottom:10px;
    }

    #blanc_addr div{
        font-size:10px;
        margin-bottom:3px;
    }

    #blanc_addr a{
        color:#42A71E;
    }

    #cust_addr{
        margin-top:10px;
        margin-left:20px;
    }

    #cust_addr div{
        font-size:11px;
        margin-bottom:3px;
    }

    .cust_name{
        color:#42A71E;
        font-size:13px;
        display: block;
        font-weight: bold;
        margin-top:5px;
    }

    .items_table{
        margin-top: 10px;
    }

    .items_table th,
    .items_table td{
        font-size:12px;
    }

    .items_table td.main-cell{
        border-right:thin dashed #000;
        text-indent:5px;
        vertical-align: top;
    }

    .items_table .amount{
        text-align: right;
        padding-right:5px;
    }

    .items_table .header_row th{
        color:#42A71E;
        border-bottom:2px solid #437b7b;
        font-weight: bold;
    }

    .items_table .total_row th.amount{
        border-top:2px solid #437b7b;
        font-weight: bold;
    }

    .items_table .desc-cell{
        line-height: 1rem;
        vertical-align: top;
        text-indent:0;
        padding-left:5px;
        padding-bottom: 10px;
        max-width:35%;
    }

    .each-dept-div{
        display:block;
        margin-bottom:10px;
    }

    .each-dept,
    .each-item{
        display:block;
        white-space: nowrap;
    }

    .each-item{
        font-size:10px;
    }

    .item-text{
        margin:0;
        line-height: 1rem;
        overflow-y: hidden;
    }

    .each-item-desc{
        background: red;
    }

    #footer_total td{
        padding:5px 5px 5px 0;
    }

    #period_text{
        font-size:9px;
        text-indent:10px;
    }

    .total_text{
        text-align: right;
    }

    .amount_total{
        font-weight: bold;
        text-align: right;
        width:30%;
    }

    .vat_total{
        border-bottom:2px solid #000;
    }

    .each-payment-detail,
    .each-payment-detail2{
        display:block;
    }

    .each-payment-detail2{
        font-weight: bold;
        font-size:11px;
    }

    #footer_left{
        position:absolute;
        left:0;
        margin-top:20px;
        font-size:12px;
    }

    #second_payment_bloc{
        margin-top:10px;
    }

    .sub_order_total{
        text-align:right;
    }


</style>
</head>
<body style="font-family: Helvetica;">
    @foreach($details_per_cust as $k=>$v)

    <div class="each-page-content @php if($k+1 < count($details_per_cust)){echo "page-break";} @endphp">
    <header>
        <img src="{{public_path('/images/pdf_logo.jpg')}}"/>

        @include('pdf.pdf_header')

        <div id="cust_addr">
            <div>{{$v['customer']->Name}}</div>
            @if($v['address'])
                @if($v['address']->address1 !='')<div>{{$v['address']->address1}}</div>@endif
                @if($v['address']->address2 !='')<div>{{$v['address']->address2}}</div>@endif
                <div>
                    @if($v['address']->Town!=''){{$v['address']->Town}}@endif
                    @if($v['address']->County!=''), {{$v['address']->County}}@endif, {{$v['address']->postcode}}
                </div>
            @endif
        </div>

        <div id="inv_summary">
            <div id="heading">
                <h3>SUMMARY INVOICE</h3>
            </div>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td class="heading-cell">
                        <div class="heading-cell-title">Account No:</div>
                        <div class="heading-cell-value">{{$v['customer']->id}}</div>
                    </td>
                    <td class="heading-cell">
                        <div class="heading-cell-title">No:</div>
                        <div class="heading-cell-value" id="inv_no">{{$v['facture']->NumFact}}</div>
                    </td>
                </tr>
                <tr>
                    <td class="heading-cell">
                        <div class="heading-cell-title">Due Date:</div>
                        <div class="heading-cell-value">{{$v['date_due']}}</div>
                    </td>
                    <td class="heading-cell">
                        <div class="heading-cell-title">Invoice Date:</div>
                        <div class="heading-cell-value">{{$v['invoice_date']}}</div>
                    </td>
                </tr>
            </table>
        </div>
        </header>

        <footer>
            <div id="footer_left">
                <span class="each-payment-detail">PAYMENT DETAILS</span>
                <span class="each-payment-detail">Bank: HSBC Account No : 90706744 Sort Code: 40-11-60</span>
                <span class="each-payment-detail">Payment terms: 14 days from invoice date</span>

                <span class="each-payment-detail2" id="second_payment_bloc">Please note that if payment is not received as per our</span>
                <span class="each-payment-detail2">payment terms, we will apply an interest fee of 5% to</span>
                <span class="each-payment-detail2">your original total amount due.</span>
            </div>

            <table border="0" cellspacing="0" id="footer_total">
                <tr>
                    <td colspan="3" id="period_text">This period</td>
                </tr>
                <tr><td colspan="3">&nbsp;</td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td class="total_text">Net Total:</td>
                    <td class="amount_total">&#163;{{$v['facture_net']}}</td>
                </tr>
                <!--
                <tr>
                    <td>&nbsp;</td>
                    <td class="total_text">Discount:</td>
                    <td class="amount_total">@if($v['facture_discount'] > 0)-@endif &#163;{{$v['facture_discount']}}</td>
                </tr>
            -->
                <tr>
                    <td>&nbsp;</td>
                    <td class="total_text vat_total">VAT @ 20%:</td>
                    <td class="amount_total vat_total">&#163;{{$v['facture_vat']}}</td>
                </tr>
                <tr>
                    <td class="total_text" colspan="2">Total Due Inc VAT:</td>
                    <td class="amount_total">&#163;{{$v['facture_total']}}</td>
                </tr>

            </table>

        </footer>

        <main>
            @foreach ($v['order_details'] as $customerid=>$invoices)
                <span class="cust_name">{{$v['cust_names'][$customerid]}}</span>


                <table width="100%" class="items_table" cellspacing="0">
                    <tr class="header_row">
                        <th>Date</th><th>Order</th><th>Ticket</th><th>Description</th><th>Net</th><th>VAT</th><th>Total</th>
                    </tr>
                    @foreach ($invoices as $invoiceid=>$invoice )
                        <tr>
                            <td class="main-cell"  width="10%">{{$invoice['date']}}</td>
                            <td class="main-cell" width="10%">{{$invoice['orderid']}}</td>
                            <td class="main-cell" width="10%">{{$invoice['numinvoice']}}</td>
                            <td class="main-cell desc-cell" width="40%">
                                @foreach($invoice['items'] as $dept=>$items)
                                    <span class="each-dept-div">
                                        <span class="each-dept">{{$dept}}</span>
                                        @foreach($items as $item=>$count)
                                        <span class="each-item">{{$count}} {{$item}}</span>
                                        @endforeach
                                    </span>
                                @endforeach
                            </td>
                            <td class="amount main-cell" width="10%">{{$invoice['net']}}</td>
                            <td class="amount main-cell"  width="10%">{{$invoice['vat']}}</td>
                            <td class="amount main-cell"  width="10%">{{$invoice['total']}}</td>
                        </tr>
                    @endforeach
                    <tr class="total_row">
                        <th colspan="4"></th><th class="amount">{{$v['order_totals'][$customerid]['order_net']}}</th><th class="amount">{{$v['order_totals'][$customerid]['order_vat']}}</th><th class="amount">{{$v['order_totals'][$customerid]['order_without_discount']}}</th>
                    </tr>
                    <tr>
                        <th colspan="4"></th><th colspan="2" class="sub_order_total">Discount</th><th class="amount">{{$v['order_totals'][$customerid]['discount']}}</th>
                    </tr>
                    <tr>
                        <th colspan="4"></th><th colspan="2" class="sub_order_total">Total</th><th class="amount">{{$v['order_totals'][$customerid]['order_total']}}</th>
                    </tr>


                </table>
            @endforeach
        </main>


    </div>

    @endforeach

</body>
</html>
