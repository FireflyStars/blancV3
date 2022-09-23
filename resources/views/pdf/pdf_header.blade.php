<header>
    <img src="{{public_path('/images/pdf_logo.jpg')}}"/>

    <div id="blanc_addr">
        <div>BLANC DE PROVENCE Ltd</div>
        <div>Trading as "BLANC"</div>
        <div>BLANC Atelier 16 Gorst Road, London NW10 6LE</div>
        <div id="vat_reg">VAT Reg.No: 124 0369 45</div>
        <div>Company Reg.No: 07689489</div>
        <div>Telephone: 0203 865 4062</div>
        <div>Email: <a href="mailto:bruno@blancliving.co">bruno@blancliving.co</a></div>
        <div>Web: <a href="www.blancliving.co">www.blancliving.co</a></div>
    </div>


    <div id="cust_addr">
        @if($v['contact'])
            <div>{{$v['contact']->company}}</div>
            <div>{{$v['contact']->name}}</div>
        @else
            <div>{{$v['customer']->CompanyName}}</div>
            <div>{{$v['customer']->Name}}</div>
        @endif

        @if($v['address'])
            @if($v['address']->address1 !='')<div>{{$v['address']->address1}}</div>@endif
            @if($v['address']->address2 !='')<div>{{$v['address']->address2}}</div>@endif
            <div>
                @if($v['address']->Town!=''){{$v['address']->Town}}, @endif{{$v['address']->postcode}}
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
