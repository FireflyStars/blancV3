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
        page-break-inside: auto;
    }

    header { position: fixed; top: 0px; left: 0px; right: 0px; height: 25%;}


    footer{
        position:fixed;
        bottom:100px;
        right:0;
    }

    #footer_total{
        border:2px solid #000;
        width:250px;
        float:right;
        font-size:12px;
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
        font-size:12px;
        display: block;
        margin-top:10px;
    }


    .items_table th,
    .items_table td{
        font-size:12px;
    }

    .items_table td{
        border-right:thin dashed #000;
        text-indent:5px;
    }

    .items_table .amount{
        text-align: right;
        padding-right:5px;
    }

    .items_table th{
        color:#42A71E;
        border-bottom:2px solid #437b7b;
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
        <table border="0" id="footer_total">
            <tr>
                <td colspan="2">This period</td>
            </tr>
            <tr>
                <td>Net Total:</td>
                <td></td>
            </tr>
            <tr>
                <td>VAT @ 20%:</td>
                <td></td>
            </tr>
            <tr>
                <td>Total Due Inc VAT:</td>
            </tr>

        </table>
    </footer>


    <main>
        @foreach ($order_details as $customerid=>$invoices)
            <span class="cust_name">{{$cust_names[$customerid]}}</span>


            <table width="100%" class="items_table" cellspacing="0">
                <tr>
                    <th>Date</th><th>Order</th><th>Sub-order</th><th>Description</th><th>Net</th><th>VAT</th><th>Total</th>
                </tr>
                @foreach ($invoices as $invoiceid=>$invoice )
                    <tr>
                        <td></td>
                        <td width="10%">{{$invoice['orderid']}}</td>
                        <td width="10%">{{$invoiceid}}</td>
                        <td width="35%">

                        </td>
                        <td class="amount">{{$invoice['net']}}</td>
                        <td class="amount">{{$invoice['vat']}}</td>
                        <td class="amount">{{$invoice['total']}}</td>
                    </tr>
                @endforeach

            </table>
        @endforeach
    </main>

</body>
</html>
<!--    <div class="page-break"></div>-->

