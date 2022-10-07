<style>

    @page { size: 21cm 29.7cm portrait; }

    body{

        margin-top:43%;
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
