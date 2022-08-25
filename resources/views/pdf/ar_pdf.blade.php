<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>

    @page { size: 21cm 29.7cm portrait; }
    .page-break {
        page-break-after: always;
    }

    body{
        margin-top:40%;
        margin-bottom:20%;
    }

    header { position: fixed; top: 0px; left: 0px; right: 0px; height: 25%;}


    footer{
        position:fixed;
        bottom:150px;
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
        margin-top:20px;
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
        padding-left:5px;
    }

    .total_text{
        text-align: right;
    }

    .amount_total{
        font-weight: bold;
        text-align: right;
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
        font-size:12px;
    }

    #second_payment_bloc{
        margin-top:10px;
    }


</style>
</head>
<body style="font-family: Helvetica;">

    <header>
        <img src="{{public_path('/images/pdf_logo.jpg')}}"/>

        <div id="blanc_addr">
            <div>BLANC</div>
            <div>BLANC Atelier 16 Gorst Road, London NW10 6LE</div>
            <div id="vat_reg">VAT Reg.No: 124 0369 45</div>
            <div>Telephone: 0203 865 4062</div>
            <div>Email: <a href="mailto:office@blancliving.co">office@blancliving.co</a></div>
            <div>Web: <a href="www.blancliving.co">www.blancliving.co</a></div>
        </div>

        <div id="cust_addr">
            <div>{{$customer->Name}}</div>
            @if($address)
                @if($address->address1 !='')<div>{{$address->address1}}</div>@endif
                @if($address->address2 !='')<div>{{$address->address2}}</div>@endif
                <div>
                    @if($address->Town!=''){{$address->Town}}@endif
                    @if($address->County!=''), {{$address->County}}@endif, {{$address->postcode}}
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
                        <div class="heading-cell-value">{{$customer->id}}</div>
                    </td>
                    <td class="heading-cell">
                        <div class="heading-cell-title">No:</div>
                        <div class="heading-cell-value" id="inv_no">{{$facture->NumFact}}</div>
                    </td>
                </tr>
                <tr>
                    <td class="heading-cell">
                        <div class="heading-cell-title">Due Date:</div>
                        <div class="heading-cell-value">15/06/2022</div>
                    </td>
                    <td class="heading-cell">
                        <div class="heading-cell-title">Invoice Date:</div>
                        <div class="heading-cell-value">{{$invoice_date}}</div>
                    </td>
                </tr>
            </table>
        </div>
    </header>

    <footer>
        <div id="footer_left">
            <span class="each-payment-detail">PAYMENT DETAILS</span>
            <span class="each-payment-detail">Bank: HSBC Account No : 90706744 Sort Code: 40-11-60</span>
            <span class="each-payment-detail">Payment terms: 14 days</span>

            <span class="each-payment-detail2" id="second_payment_bloc">Please note that if payment is not received by the end</span>
            <span class="each-payment-detail2">of the month, we will apply an interest fee of 5% to</span>
            <span class="each-payment-detail2">your original total amount due.</span>
        </div>
        <table border="0" cellspacing="0" id="footer_total">
            <tr>
                <td id="period_text">This period</td><td colspan="2"></td>
            </tr>
            <tr><td colspan="3">&nbsp;</td></tr>
            <tr>
                <td>&nbsp;</td>
                <td class="total_text">Net Total:</td>
                <td class="amount_total">&#163;{{$facture_net}}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td class="total_text vat_total">VAT @ 20%:</td>
                <td class="amount_total vat_total">&#163;{{$facture_vat}}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td class="total_text">Total Due Inc VAT:</td>
                <td class="amount_total">&#163;{{$facture_total}}</td>
            </tr>

        </table>


        <span class="pagenum">
        </span>

    </footer>


    <main>
        @foreach ($order_details as $customerid=>$invoices)
            <span class="cust_name">{{$cust_names[$customerid]}}</span>


            <table width="100%" class="items_table" cellspacing="0">
                <tr class="header_row">
                    <th>Date</th><th>Order</th><th>Sub-order</th><th>Description</th><th>Net</th><th>VAT</th><th>Total</th>
                </tr>
                @foreach ($invoices as $invoiceid=>$invoice )
                    <tr>
                        <td class="main-cell"  width="10%">{{$invoice['date']}}</td>
                        <td class="main-cell" width="10%">{{$invoice['orderid']}}</td>
                        <td class="main-cell" width="10%">{{$invoiceid}}</td>
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
                    <th colspan="4"></th><th class="amount">{{$order_totals[$customerid]['order_net']}}</th><th class="amount">{{$order_totals[$customerid]['order_vat']}}</th><th class="amount">{{$order_totals[$customerid]['order_total']}}</th>
                </tr>


            </table>
        @endforeach
    </main>

</body>
</html>
<!--    <div class="page-break">



</div>-->

