<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
    .page-break {
        page-break-after: always;
    }

    /*
    header { position: fixed; top: 0px; left: 0px; right: 0px;min-height: 100px;}
    */

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
        font-size:16px;
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
                        <div class="heading-cell-value">AT1393</div>
                    </td>
                    <td class="heading-cell">
                        <div class="heading-cell-title">No:</div>
                        <div class="heading-cell-value" id="inv_no">9795</div>
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
    <main>
    </main>

</body>
</html>
<!--    <div class="page-break"></div>-->

