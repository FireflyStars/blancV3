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
