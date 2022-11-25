<main>
    @foreach ($v['order_details'] as $customerid=>$invoices)

        <table width="100%" class="items_table" cellspacing="0">
            <tr>
                <th colspan="7" style="text-align: left; padding:5px 0;"><span class="cust_name">{{$v['cust_names'][$customerid]}}</span></th>
            </tr>
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
                <th colspan="4"></th><th colspan="2" class="sub_order_total">Discount & Fees</th><th class="amount">{{$v['order_totals'][$customerid]['discount']}}</th>
            </tr>
            <tr>
                <th colspan="4"></th><th colspan="2" class="sub_order_total">Total</th><th class="amount">{{$v['order_totals'][$customerid]['order_total']}}</th>
            </tr>
        </table>
    @endforeach
</main>
