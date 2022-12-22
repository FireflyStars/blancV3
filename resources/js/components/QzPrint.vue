<template>
    <div class="row">
        <div class="d-none" id="barcode_div">
            <svg id="barcode"></svg>
        </div>
        <!--
        <teleport :to="Target" :disabled="shouldClose" v-if="!shouldClose">

            <Modal  @close-modal="closeModal">
                <template #header>
                    <div class="row mt-4 text-modal-red mb-3">
                        <div class="col-12 text-center"><h3>Print receipt</h3></div>
                    </div>
                </template>
                <template #body>
                    <div class="row justify-content-center mb-4">
                        <div class="col-10">
                            <div class="row align-items-center">
                                <div class="col-2">Printer</div>
                                <div class="col-9 form-group mb-0">
                                    <select class="form-control" v-model="printer_name">
                                        <option v-for="( a, index ) in available_printers" :value="a" :key="index">{{ a }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <button @click="printReceipt"  class="btn btn-success">Print</button>
                    <button type="button" class="btn btn-primary" @click="closeModal">Cancel</button>
                </template>
            </Modal>
        </teleport>
        -->

    <modal ref="print_modal">
        <template #closebtn>
            <span class="close" id="pricenow_modal_close" @click="closeModal"></span>
        </template>
        <template #bheader>
            <div class="row mt-4 text-modal-red mb-3">
                <div class="col-12 text-center"><h3>Print receipt</h3></div>
            </div>
        </template>
        <template #bcontent>
            <div class="row justify-content-center mb-4">
                <div class="col-10">
                    <div class="row align-items-center">
                        <div class="col-2">Printer</div>
                        <div class="col-9 form-group mb-0">
                            <select class="form-control" v-model="printer_name">
                                <option v-for="( a, index ) in available_printers" :value="a" :key="index">{{ a }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #mbuttons>
            <div class="row justify-content-around pb-3">
                <div class="col-3"><button @click="printReceipt"  class="btn btn-success w-100">Print</button></div>
                <div class="col-3"><button type="button" class="btn btn-primary w-100" @click="closeModal">Cancel</button></div>
            </div>
        </template>
    </modal>
    </div>
</template>
<script>
    import qz from '../assets/qz-tray';
    import rs from 'jsrsasign';
    import JsBarcode from 'jsbarcode';
    import Modal from '../components/miscellaneous/Modal';
    import {
        TOASTER_MODULE,
        TOASTER_MESSAGE,
        ITEM_DETAIL_MODULE,
        ITEM_DETAIL_GET_DETAIL,

    } from '../../js/store/types/types';
    import { ref, onMounted, onBeforeUnmount } from 'vue';
    import { useRoute } from 'vue-router';
    import { useStore } from 'vuex';
    import axios from 'axios';

    export default {
        name: "QzPrint",
        components:{
           Modal
        },
        setup(){
            const store = useStore();
            const route = useRoute();
            const printer_name = ref('');
            const invoice_id = ref('');
            const invoiceId = ref('');
            const orderId = ref('');
            const inv_details = ref({});
            const cert = ref('');
            const private_key = ref('');
            const available_printers = ref([]);
            const default_printers = ref([]);
            const Target = ref('');
            const print_modal = ref();
            onMounted(()=>{
                axios.post('/get-site-keys', {})
                    .then((res) => {
                        cert.value = res.data.cert;
                        private_key.value = res.data.private_key;
                    })
                    .catch((error) => {
                        console.log(error);
                    }).finally(() => {
                        qz.security.setCertificatePromise(function(resolve, reject) {
                        resolve('"' + cert.value + '"');
                    });

                    qz.security.setSignatureAlgorithm("SHA1"); // Since 2.1
                    qz.security.setSignaturePromise(function(toSign) {
                        return function(resolve, reject) {
                            try {
                                var pk = rs.KEYUTIL.getKey('"'+ private_key.value +'"');
                                var sig = new rs.KJUR.crypto.Signature({"alg": "SHA1withRSA"});  // Use "SHA1withRSA" for QZ Tray 2.0 and older
                                sig.init(pk);
                                sig.updateString(toSign);
                                var hex = sig.sign();
                                resolve( rs.stob64( rs.hextorstr(hex)) );
                            } catch (err) {
                                console.error(err);
                                reject(err);
                            }
                        };
                    });

                    initQZPrinter();
                });
            })

            onBeforeUnmount( ()=>{
                qz.websocket.disconnect();
            } )
            const shouldClose = ref(true);
            const closeModal = ()=>{
                //shouldClose.value = true;
                print_modal.value.closeModal();
            }
            const loadPrinterModal = (invoice_id, target)=>{
                invoiceId.value = invoice_id;
                shouldClose.value = true;
                Target.value = target
                printReceipt()
            }
            const initQZPrinter = ()=>{
                if(!qz.websocket.isActive()){
                    qz.websocket.connect().then(function() {
                        //Find printers
                        qz.printers.find().then(function (data) {
                            available_printers.value = data;
                        }).catch(function (e) {
                            console.error(e);
                        });

                        //Get default printer
                        qz.printers.getDefault().then(function(data) {
                            console.log(data);
                            printer_name.value = data;
                        }).catch(function (e) {
                            console.error(e);
                        });

                    }).catch(function (e) {
                        console.error(e);
                    });
                }
            }
            const printReceipt = ()=>{
                initQZPrinter();
                let err =false;
                let err_arr = [];
                // if(printer_name.value == '' && (Target.value != ".item-detail-panel")){
                //     err = true;
                //     store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{ message:`Please enter printer name`,ttl:5, type:'info'},{ root: true });
                // }

                if(err){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{ message: "An error has occured",ttl:5, type:'danger'},{ root: true });
                }else{
                    axios.post('/get-suborder-and-print', {
                        invoice_id: invoiceId.value,
                        route_name: route.name,
                        poste_id: "",
                    })
                    .then((res) => {
                        shouldClose.value = true;
                        if(res.data.inv) {
                                printReceiptQz(printer_name.value, res.data.inv);
                        }else{
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{ message: "Invoice not found",ttl:5, type:'danger'},{ root: true });
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    }).finally(() => {

                    });
                }
            }
            const printReceiptQz = (printer, inv)=>{
                // Create a default config for the found printer
                console.log('receipt invoice',inv);
                var config = qz.configs.create(printer);
                let toPrint = '<html><head></head><body style="font-family:Arial, Helvetica, sans-serif; text-align:center; width:90%; margin: auto;"></body>';
                toPrint += '<table border="0" width="100%" style="font-size:10pt;">';
                toPrint += '<tr><td width="60%">Tel. 020 8004 2630</td><td style="text-align: right;">User: '+inv.user_initials+'</td></tr>';
                toPrint += '</table>';
                toPrint += '<p style="text-align: center"><span style="font-size:9pt">www.blancliving.co&nbsp;&nbsp;&nbsp;&nbsp;Email: info@blancliving.co</span><br/><span style="font-size:11pt;">Vat Reg. No. 124 0369 45</span></p>';
                toPrint += '<div style="display:table; margin:auto; padding:10px; border:thin solid #000000;">'+inv.StoreName+'</div>';
                toPrint += '<p style="text-align: center;font-size:20pt;">'+inv.NumInvoice+'</p>';
                if(inv.order){
                    toPrint += '<p style="text-align: center;font-size:20pt;">ORDER '+inv.order.id+'</p>';
                }

                var number = inv.storecode+inv.NumInvoice;
                JsBarcode("#barcode", number, {
                    text: number.match(/.{1,4}/g).join("  "),
                    width: 1.25,
                    height: 50,
                    fontSize: 15,
                    displayValue:false,
                });

                toPrint += document.querySelector('#barcode_div').innerHTML;
                toPrint += '<p>';

                toPrint += '<span style="font-size:14pt;">'+inv.customer_name+'</span><br/>';

                if(inv.phone_num) {
                    toPrint += '<span style="font-size:10pt">Tel: '+inv.phone_num[0].replace("|","")+'</span>';
                    toPrint += '<br/>';
                }


                toPrint += '</p><hr/>';
                //Customer preferences
                let cust_pref = inv.customer_preferences;
                if(Object.entries(cust_pref).length > 0){
                    toPrint += '<span style="font-size:12pt;">Preferences</span>';

                    toPrint += '<p style="text-align:left;">';
                    for (let [key, value] of Object.entries(cust_pref)) {
                        toPrint += '<span style="font-size:10pt;">'+key+' : '+value+'</span><br/>';
                    }

                    toPrint += '</p><hr/>';
                }

                //end customer preferences
                toPrint += '<p style="font-size:10pt; margin-bottom: 20px;">'+inv.poste_details+'</p>';

                let nb_pieces = 0;
                let total_items_price = 0;
                if(inv.items.length > 0){
                    toPrint += '<table border="0" width="100%" style="font-size:9pt;">';

                    inv.items.forEach(function(v,i){
                        toPrint += '<tr>';
                        toPrint += '<td width="10%" style="vertical-align: top;">1</td>';
                        toPrint += '<td width="70%">'+v.typeitem;
                        toPrint += '<br><span style="font-size:8pt; padding-left:15px;">HSL: '+v.ItemTrackingKey+'</span>';
                        if(v.Colors !='' || v.brand!=''){
                            toPrint += '<br/>'+v.Colors+(v.brand!=''?', '+v.brand+'<br/>':'');
                        }

                        if(v.item_comment){
                            toPrint += v.item_comment.join("<br/>");
                        }

                        toPrint +='</td>';
                        toPrint += '<td width="20%" style="text-align:right; vertical-align: top;">'+v.totalPrice+'</td>';
                        toPrint += '</tr>';
                        toPrint += '<tr><td colspan="3"><br/></td></tr>';

                        nb_pieces +=1;
                        total_items_price += parseFloat(v.priceTotal);
                    });

                    toPrint += '</table>';

                    toPrint += '<table border="0" width="100%" style="font-size:9pt;">';
                    toPrint += '<tr>';
                    toPrint += '<td style="font-size:14pt" width="50%">'+nb_pieces+' PIECE'+(nb_pieces>1?'S':'')+'</td>';
                    toPrint +='<td style="font-size:10pt; text-align:right;" width="30%;">ITEMS SUBTOTAL</td><td style="font-size:10pt; text-align: right;">'+total_items_price.toFixed(2)+'</td>';
                    toPrint += '</tr>';
                    // toPrint += '<tr><td></td><td width="30%" style="font-size:10pt; text-align: right">DISCOUNT</td><td width="20%" style="font-size:10pt; text-align: right">'+inv.order_discount.toFixed(2)+'</td></tr>';
                    // toPrint += '<tr><td></td><td width="30%" style="font-size:10pt; text-align: right">TOTAL DUE</td><td width="20%" style="font-size:10pt; text-align: right">'+inv.order_total_due.toFixed(2)+'</td></tr>';
                    toPrint += '</table>';

                }


                toPrint += '<br/>';
                toPrint += '<p style="text-align:center; font-size:9pt">20% VAT included in Total Price</p>';
                toPrint += '<hr/>';

                if(inv.order){
                    toPrint += '<p style="text-align:center; font-size:9pt">Dropped off: '+inv.order.date_add+'</p>';
                    toPrint += '<p style="text-align:center; font-size:13pt; font-weight: bold;">Due: '+inv.order.date_due+'<br/></p>';
                }

                toPrint += '</body></html>';

                var data = [{
                    type: 'pixel',
                    format: 'html',
                    flavor: 'plain', // or 'plain' if the data is raw HTML
                    data: toPrint
                }];
                //console.log("toPrinttoPrint" , toPrint)

                return qz.print(config, data);
            }
            ///Print Order

            const loadPrinterOrderModal = (Order)=>{
                orderId.value = Order;
                shouldClose.value = true;
                printReceiptOrder()
            }

            const printReceiptOrder = ()=>{
                initQZPrinter();

                let err =false;
                let err_arr = [];

                if(err){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{ message: "An error has occured",ttl:5, type:'danger'},{ root: true });
                }else{
                    axios.post('/get-order-and-print', {
                        order_id : orderId.value,
                    })
                    .then((res) => {
                        shouldClose.value = true;
                        if(res.data) {
                            let inv = res.data.inv;
                            let order = res.data.order
                                printOrderReceiptQz(printer_name.value, res.data);

                        }else{
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{ message: "Invoice not found",ttl:5, type:'danger'},{ root: true });
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    }).finally(() => {

                    });
                }
            }

            const printOrderReceiptQz = (printer, data)=>{
                // Create a default config for the found printer
                console.log('printing started');

                var config = qz.configs.create(printer);
                let toPrint = '<html><head></head><body style="font-family:Arial, Helvetica, sans-serif; text-align:center; width:90%; margin: auto;"></body>';
                toPrint += '<table border="0" width="100%" style="font-size:10pt;">';
                toPrint += '<tr><td width="60%">Tel. 020 8004 2630</td><td style="text-align: right;">User: '+data.cur_user.name+'</td></tr>';
                toPrint += '</table>';
                toPrint += '<p style="text-align: center">BLANC</p>';
                toPrint +='<div style="font-size: 12px; text-align: center">'+data.TopTicket+'</div>';
                // toPrint += '<p style="text-align: center"><span style="font-size:9pt">www.blancliving.co&nbsp;&nbsp;&nbsp;&nbsp;Email: info@blancliving.co</span><br/></p>';
                toPrint += '<div style="display:table; margin:auto; padding:10px; border:thin solid #000000;">'+data.order.TypeDelivery+'</div>';
                toPrint += '<p style="text-align: center;font-size:20pt;">ORDER '+data.order.id+'</p>';

                toPrint += '<span style="font-size:14pt;">'+data.cust.Name+'</span><br/>';

                if(data.cust.Phone) {
                    toPrint += '<span style="font-size:10pt">Tel: '+data.cust.Phone[0].replace("|","")+'</span>';
                    toPrint += '<br/>';
                }

                toPrint += '</p><hr/>';
                // //Customer preferences
                let cust_pref = data.cust.customer_preferences;
                if(Object.entries(cust_pref).length > 0){
                    toPrint += '<span style="font-size:12pt;">Preferences</span>';

                    toPrint += '<p style="text-align:left;">';
                    for (let [key, value] of Object.entries(cust_pref)) {
                        toPrint += '<span style="font-size:10pt;">'+key+' : '+value+'</span><br/>';
                    }

                    toPrint += '</p><hr/>';
                }

                //List Items
                let nb_pieces = 0;
                let total_items_price = 0;
                let ListItems= data.items;

                if(ListItems.length > 0){

                    toPrint += '<p style="text-align:left;">';

                    if(ListItems.length > 0){
                        toPrint += '<table border="0" width="100%" style="font-size:9pt;">';

                            ListItems.forEach(function(v,i){
                            toPrint += '<tr>';
                            toPrint += '<td width="10%" style="vertical-align: top;">'+1+'</td>';

                            toPrint += '<td width="70%">'+v.typeitem;
                            toPrint += '<br><span style="font-size:8pt; padding-left:15px;">HSL: '+v.tracking+'</span>';
                            if(v.all_colors !='' || v.brand!=''){
                                toPrint += '<br/>'+v.all_colors.join()+(v.brand!=''?', '+v.brand+'<br/>':'');
                            }
                            //claning service
                            if(v.clean_services.length > 0){
                                v.clean_services.forEach(function(clean,i){
                                toPrint += '<span style="font-size:8pt; ">'+clean.name+'</span>';
                              })
                            }
                            if(v.describeprixnow != null){
                                toPrint += '<br><span style="font-size:8pt; ">'+v.describeprixnow+'</span>';
                            }
                            if(v.tail_services.length > 0){
                                    toPrint += '<br><span style="font-size:8pt">'+v.tail_services[0]+'</span>';
                            }
                           if(v.describeprixnowtailoring != null){
                            toPrint += '<br><span style="font-size:8pt;">'+v.describeprixnowtailoring+'</span>';
                           }

                            toPrint +='</td>';

                            toPrint += '<td width="20%" style="text-align:right; vertical-align: top;">£'+v.priceTotal;
                            toPrint += '<br>';
                            toPrint += '<br>';
                            if(v.clean_services.length > 0){
                            toPrint += '<br>';
                            }
                            if(v.describeprixnow != null){
                            toPrint += '<br>';
                            }
                            if(v.tail_services.length > 0){
                            toPrint += '<br>';
                            }
                            if(v.describeprixnowtailoring != null){
                            toPrint +='</td><br>';
                            }

                            toPrint += '</tr>';
                            toPrint += '<tr><td colspan="3"><br/></td></tr>';

                            nb_pieces +=1;
                            total_items_price += parseFloat(v.priceTotal);
                        });

                        toPrint += '</table>';

                        toPrint += '<table border="0" width="100%" style="font-size:9pt;">';
                        toPrint += '<tr>';
                        toPrint += '<td style="font-size:12pt" width="50%">Sub-Orders Total(incl. VAT)</td>';
                        toPrint +='<td style="font-size:10pt; text-align: right;">£'+total_items_price.toFixed(2)+'</td>';
                        toPrint += '</tr>';
                        toPrint += '</table>';

                }

                    toPrint += '</p><hr/>';
                }

                //OrderTotal

                    toPrint += '<table border="0" width="100%" style="font-size:9pt; margin-top:10px">';


                        if(data.order.AccountDiscount > 0){
                            toPrint += '<tr>';
                            toPrint += '<td width="30%" style="vertical-align: top;">Account Discount</td>';
                            toPrint +='</td>';
                            toPrint += '<td width="30%" style="vertical-align: top;  font-size:10px ; color: #c4c4c4;">'
                                if(data.cust.discount > 0){
                                    toPrint += data.cust.discount + '% (applied)';
                                }
                            toPrint +='</td>'
                            toPrint += '<td width="20%" style="text-align:right; vertical-align: top;">'
                            toPrint += '<span>-</span>';
                            toPrint += formatPrice(data.order.AccountDiscount)+'</td>'
                            toPrint += '</tr>';
                        }

                        if(data.order.OrderDiscount > 0){
                            toPrint += '<tr>';
                            toPrint += '<td width="30%" style="vertical-align: top;">Order Discount</td>';
                            toPrint +='</td>';
                            toPrint += '<td width="30%" style="vertical-align: top;  font-size:10px ; color: #c4c4c4;">'
                                if(data.order.DiscountPerc > 0){
                                    toPrint += data.order.DiscountPerc + '% (applied)';
                                }
                            toPrint +='</td>'
                            toPrint += '<td width="20%" style="text-align:right; vertical-align: top;">'
                            toPrint += '<span>-</span>';
                            toPrint += formatPrice(data.order.OrderDiscount)+'</td>'
                            toPrint += '</tr>';
                        }

                        if(data.order.VoucherDiscount > 0){
                            toPrint += '<tr>';
                            toPrint += '<td width="30%" style="vertical-align: top;">Voucher Discount</td>';
                            toPrint += '<td width="30%" style="vertical-align: top;  font-size:10px ; color: #c4c4c4;">'
                                if(data.codes_voucher!= null){
                                        toPrint += data.codes_voucher;
                                }
                            toPrint += '<td width="20%" style="text-align:right; vertical-align: top;">-'+formatPrice(data.order.VoucherDiscount)+'</td>';
                            toPrint += '</tr>';
                        }

                        if(data.order.ExpressCharge > 0){
                            toPrint += '<tr>';
                            toPrint += '<td width="30%" style="vertical-align: top;">Express Charges</td>';
                            toPrint +='<td width="30%"></td>';
                            toPrint += '<td width="20%" style="text-align:right; vertical-align: top;">+'+formatPrice(data.order.ExpressCharge)+'</td>';
                            toPrint += '</tr>';
                        }

                        if(data.order_bundles){
                            toPrint += '<tr>';
                                    toPrint += '<td width="10%" style="vertical-align: top;">'+Object.keys(data.order_bundles)+'</td>';
                                    toPrint +='<td width="30%"></td>';
                                    if(Object.values(data.order_bundles)>0){
                                        toPrint += '<td width="20%" style="text-align:right; vertical-align: top;">+'+Object.values(data.order_bundles) +'</td>';
                                    }
                            toPrint += '</tr>';
                        }

                        toPrint += '</table>';

                        toPrint += '<table border="0" width="100%" style="font-size:9pt;">';

                        // toPrint += '<tr>';
                        // toPrint += '<td width="50%" style="vertical-align: top;"><span style="font-size:12pt;">Order Total (incl. VAT)</span></td>';
                        // toPrint +='</td>';
                        // toPrint += '<td width="20%" style="text-align:right; style="font-size:12pt; vertical-align: top;">'+formatPrice(data.order.SubtotalWithDiscount)+'</td>';
                        // toPrint += '</tr>';

                        if(data.order.FailedDelivery == 1 && data.order.TypeDelivery=='DELIVERY'){
                            toPrint += '<tr>';
                            toPrint += '<td width="10%" style="vertical-align: top;">Failed delivery</td>';
                            toPrint +='</td>';
                            toPrint += '<td width="20%" style="text-align:right; vertical-align: top;">+'+formatPrice(data.order.FailedDeliveryCharge)+'</td>';
                            toPrint += '</tr>';
                        }

                        if(data.order.DeliveryNowFee!=null && data.order.TypeDelivery=='DELIVERY'){
                            toPrint += '<tr>';
                            toPrint += '<td width="10%" style="vertical-align: top;">Delivery fee</td>';
                            toPrint +='</td>';
                            toPrint += '<td width="20%" style="text-align:right; vertical-align: top;">+'+formatPrice(data.order.DeliveryNowFee)+'</td>';
                            toPrint += '</tr>';
                        }

                        if(data.order.AutoDeliveryFee > 0 && data.order.TypeDelivery=='DELIVERY'){
                            toPrint += '<tr>';
                            toPrint += '<td width="10%" style="vertical-align: top;">Auto delivery fee</td>';
                            toPrint +='</td>';
                            toPrint += '<td width="20%" style="text-align:right; vertical-align: top;">+'+formatPrice(data.order.AutoDeliveryFee)+'</td>';
                            toPrint += '</tr>';
                        }
                        toPrint += '</table><hr/>';

                        toPrint += '<table border="0" width="100%" style="font-size:9pt;">';

                        if(data.order.TypeDelivery=='DELIVERY'){
                            toPrint += '<tr>';
                            toPrint += '<td width="50%" style="vertical-align: top;"><span style="text-align: right; font-size:16px;">Order total (incl. VAT)</span></td>';
                            toPrint +='</td>';
                            toPrint += '<td width="20%" style="text-align:right; font-size:16px; font-weight: 700; vertical-align: top;">'+formatPrice(data.order.Total)+'</td>';
                            toPrint += '</tr>';
                        }


                        toPrint += '<tr>';
                        toPrint += '<td width="10%" style="vertical-align: top;">Total (Excl. VAT)</td>';
                        toPrint +='</td>';
                        toPrint += '<td width="20%" style="text-align:right; vertical-align: top;">'+formatPrice(data.order.TotalExcVat)+'</td>';
                        toPrint += '</tr>';

                        toPrint += '<tr>';
                        toPrint += '<td width="10%" style="vertical-align: top;">Tax</td>';
                        toPrint +='</td>';
                        toPrint += '<td width="20%" style="text-align:right; vertical-align: top;">'+formatPrice(data.order.TaxAmount)+'</td>';
                        toPrint += '</tr>';
                        toPrint += '</table>';

                    toPrint += '</table></br>';
                    // toPrint += '<p style="text-align: center"><span style="font-size:11pt;">Vat Reg. No. 124 0369 45</span></p>';
                    toPrint += '<div style="font-size: 12px;">'+data.FooterTicket+'</div>';
                    toPrint += '</body></html>';

                var data = [{
                    type: 'pixel',
                    format: 'html',
                    flavor: 'plain', // or 'plain' if the data is raw HTML
                    data: toPrint
                }];
                 return qz.print(config, data);
            }
            const loadPrinterCustomer = (CustomerID)=>{
                initQZPrinter();
                axios.post('/get-customer-and-print', {
                    CustomerID : CustomerID,
                })
                .then((res) => {
                    if(res.data) {
                        printCustomerTicket(printer_name.value, res.data);
                    }else{
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{ message: "Invoice not found",ttl:5, type:'danger'},{ root: true });
                    }
                })
                .catch((error) => {
                    console.log(error);
                }).finally(() => {

                });
            }
            const printCustomerTicket = (printer, customer)=>{
                // Create a default config for the found printer
                if(customer.address == null){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{ message: "Customer doesn't have a address info",ttl:5, type:'danger'},{ root: true });
                    return;
                }
                console.log('Customer Ticket printing started');
                var config = qz.configs.create(printer);
                var rawHtml = '<html><head></head><body style="font-family:Arial, Helvetica, sans-serif; text-align:center; width:90%; margin: auto;"><table border="0" width="375px" style="margin: auto; padding: 25px;">';
                rawHtml +=  '<tr>';
                rawHtml +=      '<td width="75%">';
                rawHtml +=          '<table border="0" width="100%">';
                rawHtml +=              '<tr>';
                rawHtml +=                  '<td width="24px">';
                if(customer.TypeDelivery == 'DELIVERY'){
                    rawHtml += '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M20.8867 11.1437C20.8543 11.0873 20.8279 11.0274 20.8081 10.9652L19.8615 8.00091C19.7421 7.62706 19.4011 7.45233 19.0165 7.45233H16.8865V6.22331H19.0165C19.7857 6.22331 20.648 6.70463 20.8867 7.45233L21.7857 9.90358L22.8774 12.8132C22.941 12.9861 23 13.1118 23 13.2737V15.4265C23 16.4304 22.2041 17.2443 21.2222 17.2443H2.77778C1.79594 17.2443 1 16.4304 1 15.4265V6.87706C1 5.87311 1.79594 5.00586 2.77778 5.00586H15.6716C16.4309 5.00586 16.8865 5.44681 16.8865 6.22331L16.2663 6.5591C16.2663 6.28458 15.94 6.22331 15.6716 6.22331H2.77778C2.28686 6.22331 2.20968 6.37509 2.20968 6.87706V15.4265C2.20968 15.9285 2.28686 16.0183 2.77778 16.0183H21.2222C21.7131 16.0183 21.7857 15.9285 21.7857 15.4265V13.4761C21.7857 13.3425 21.7589 13.2103 21.707 13.0873L20.8867 11.1437Z" fill="#47454B"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16.888 6.20459V10.0537C16.888 10.2998 16.9958 10.4982 17.2933 10.4982H21.6707V11.7591H17.2933C16.3976 11.7591 15.6741 10.7894 15.6741 10.0537V6.20459L16.888 6.20459Z" fill="#47454B"/><path d="M8.1837 16.3277C8.1837 17.3402 7.37964 18.1611 6.38778 18.1611C5.39592 18.1611 4.59186 17.3402 4.59186 16.3277C4.59186 15.3152 5.39592 14.4944 6.38778 14.4944C7.37964 14.4944 8.1837 15.3152 8.1837 16.3277Z" fill="white"/><circle cx="6.38783" cy="16.3277" r="2.1666" stroke="#47454B"/><path d="M19.8573 16.3277C19.8573 17.3402 19.0533 18.1611 18.0614 18.1611C17.0695 18.1611 16.2655 17.3402 16.2655 16.3277C16.2655 15.3152 17.0695 14.4944 18.0614 14.4944C19.0533 14.4944 19.8573 15.3152 19.8573 16.3277Z" fill="white"/><circle cx="18.0613" cy="16.3277" r="2.1666" stroke="#47454B"/></svg>';
                }else{
                    rawHtml += '<svg width="23" height="24" viewBox="0 0 23 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.4466 24.0001H1.53174V12.2554H2.55302V22.9788H20.4254V12.2554H21.4466V24.0001Z" fill="#47454B"/><path d="M20.2724 12.9192C18.7803 12.9192 17.566 11.7054 17.566 10.2128H18.5872C18.5872 11.1416 19.343 11.8979 20.2724 11.8979C21.2012 11.8979 21.9575 11.1421 21.9575 10.2128V8.29788L18.0766 1.02128H4.90213L1.02128 8.29788V10.2128C1.02128 11.1416 1.77702 11.8979 2.70638 11.8979C3.63575 11.8979 4.39149 11.1421 4.39149 10.2128H5.41277C5.41277 11.7049 4.19847 12.9192 2.70638 12.9192C1.2143 12.9192 0 11.7049 0 10.2128V8.04256L4.28936 0H18.6894L22.9787 8.04256V10.2128C22.9787 11.7049 21.7649 12.9192 20.2724 12.9192Z" fill="#47454B"/><path d="M7.09792 12.9193C5.60584 12.9193 4.39154 11.7055 4.39154 10.2129H5.41282C5.41282 11.1417 6.16856 11.898 7.09792 11.898C8.02729 11.898 8.78303 11.1423 8.78303 10.2129H9.80431C9.80431 11.705 8.59001 12.9193 7.09792 12.9193Z" fill="#47454B"/><path d="M11.4894 12.9193C9.99732 12.9193 8.78302 11.7055 8.78302 10.2129H9.8043C9.8043 11.1417 10.56 11.898 11.4894 11.898C12.4188 11.898 13.1745 11.1423 13.1745 10.2129H14.1958C14.1958 11.705 12.9815 12.9193 11.4894 12.9193Z" fill="#47454B"/><path d="M15.8808 12.9193C14.3887 12.9193 13.1744 11.7055 13.1744 10.2129H14.1957C14.1957 11.1417 14.9514 11.898 15.8808 11.898C16.8096 11.898 17.5659 11.1423 17.5659 10.2129H18.5871C18.5871 11.705 17.3728 12.9193 15.8808 12.9193Z" fill="#47454B"/><path d="M18.3829 20.9364H4.5957V11.7449H5.61698V19.9151H17.3617V11.7449H18.3829V20.9364Z" fill="#47454B"/><path d="M16.4459 17.2558L14.6586 15.4685L13.9365 16.1907L15.7238 17.978L16.4459 17.2558Z" fill="#47454B"/><path d="M14.9141 18.277L11.5952 14.958L10.873 15.6802L14.192 18.9991L14.9141 18.277Z" fill="#47454B"/></svg>';
                }
                rawHtml +=                  '</td>';
                rawHtml +=                  '<td style="font-size:20px; color: #47454B;">'+customer.TypeDelivery+'</td>';
                rawHtml +=              '</tr>';
                // line break
                rawHtml +=              '<tr></tr>';
                // Customer Name
                rawHtml +=              '<tr><td colspan="3" style="font-size:28px; font-weight: bold;">'+customer.Name+'</td></tr>';
                // Customer Company
                rawHtml +=              '<tr><td colspan="3" style="font-size:18px; color: #47454B;">'+(customer.CompanyName != null ? customer.CompanyName : "") +'</td></tr>';
                rawHtml +=          '</table>';
                rawHtml +=      '</td>';

                // 2 demential barcode
                rawHtml +=      '<td style="vertical-align: top; padding-top: 10px;">';
                rawHtml +=          '<table border="0" width="100%">';
                rawHtml +=              '<tr><td style="width: 53px; height: 53px; border: 1px solid #C4C4C4; border-radius: 8px; padding: 11px">';
                rawHtml +=              '';
                rawHtml +=              '</td></tr>';
                rawHtml +=          '</table>';
                rawHtml +=      '</td>';
                rawHtml +=  '</tr>';
                // Emaile and Phone
                rawHtml +=  '<tr>';
                rawHtml +=      '<td colspan="2">';
                rawHtml +=          '<table border="0" width="100%">';
                rawHtml +=              '<tr>';
                rawHtml +=                  '<td style="color: #868686;">Email</td>';
                rawHtml +=                  '<td style="font-weight: bold; color: #47454B;">'+customer.EmailAddress+'</td>';
                rawHtml +=              '</tr>';
                rawHtml +=              '<tr>';
                rawHtml +=                  '<td style="color: #868686;">Phone</td>';
                rawHtml +=                  '<td style="font-weight: bold; color: #47454B;">'+formatPhone(customer.Phone)+'</td>';
                rawHtml +=              '</tr>';
                rawHtml +=          '</table>';
                rawHtml +=      '</td>';
                rawHtml +=  '</tr>';
                // line break
                rawHtml +=  '<tr><td colspan="2" style="height: 15px;"></td></tr>';
                // horizontal separator
                rawHtml +=  '<tr><td colspan="2" style="border-top: solid 1px black; height: 20px;"></td></tr>';
                // delivery detail
                rawHtml += '<tr><td colspan="2" style="font-size: 16px; font-weight: 600;">Delivery details</td></tr>';
                // line break
                rawHtml +=  '<tr><td colspan="2" style="height: 5px;"></td></tr>';
                // address title
                rawHtml +=  '<tr><td colspan="2" style="font-size: 16px;">Address</td></tr>';
                // address1
                rawHtml +=  '<tr><td colspan="2" style="font-size: 16px;">'+customer.address.address1+'</td></tr>';
                // postcode
                rawHtml +=  '<tr><td colspan="2" style="font-size: 16px;">'+customer.address.Town+' '+ customer.address.postcode +'</td></tr>';
                // line break
                rawHtml +=  '<tr><td colspan="2" style="height: 1px;"></td></tr>';
                // delivery instruction
                rawHtml += '<tr>'+
                                '<td colspan="2" style="padding: 15px; background-color: #F8F8F8; border-radius: 8px;">'+
                                    '<table border="0" width="100%">'+
                                        '<tr>'+
                                            '<td style="font-size: 16px; font-weight: 600">Delivery instructions</td>'+
                                        '</tr>'+
                                        '<tr>'+
                                            '<td style="vertical-align: top; height: 45px;">'+
                                                '<p style="font-size: 14px; color: #47454B;">'+ (customer.commentDelivery != null ? customer.commentDelivery : "") +'</p>'+
                                            '</td>'+
                                        '</tr>'+
                                        '<tr>'+
                                            '<td style="font-size: 14px; color: #47454B;"><b>Deliver to:</b> '+customer.deliveryPreference.TypeDelivery+'</td>'+
                                        '</tr>'+
                                    '</table>'+
                                '</td>'+
                            '</tr>';
                // line break
                rawHtml +=  '<tr><td colspan="2" style="height: 5px;"></td></tr>';
                // alt contact
                rawHtml +=  '<tr>'+
                                '<td colspan="2">'+
                                    '<table border="0" width="100%">'+
                                        '<tr>'+
                                            '<td width="50%" style="color: #868686; font-size: 14px;">Alternate contact</td>'+
                                            '<td style="color: #47454B; font-size: 14px; font-weight: 600;">'+(customer.deliveryPreference.Name != null ? customer.deliveryPreference.Name : "") +'</td>'+
                                        '</tr>'+
                                    '</table>'+
                                '</td>'+
                            '</tr>';
                // alt phone number
                rawHtml +=  '<tr>'+
                                '<td colspan="2">'+
                                    '<table border="0" width="100%">'+
                                        '<tr>'+
                                            '<td width="50%" style="color: #868686; font-size: 14px;">Phone</td>'+
                                            '<td style="color: #47454B; font-size: 14px; font-weight: 600;">'+customer.deliveryPreference.CodeCountry + ' ' + customer.deliveryPreference.PhoneNumber+'</td>'+
                                        '</tr>'+
                                    '</table>'+
                                '</td>'+
                            '</tr>';
                // line break
                rawHtml +=  '<tr><td colspan="2" style="height: 15px;"></td></tr>';
                // horizontal separator
                rawHtml +=  '<tr><td colspan="2" style="border-top: solid 1px black; height: 20px;"></td></tr>';
                // Other instruction
                rawHtml +=  '<tr>'+
                                '<td colspan="2" style="font-size: 16px; font-weight: bold; padding-bottom: 10px;">Other instructions</td>'+
                            '</tr>';
                rawHtml +=  '<tr>'+
                                '<td colspan="2" style="padding: 15px; background-color: #F8F8F8; border-radius: 8px; height: 85px; border: 1px solid #C4C4C4;">'+customer.deliveryPreference.OtherInstruction+'</td>'+
                            '</tr>';
                // line break
                rawHtml +=  '<tr><td colspan="2" style="height: 15px;"></td></tr>';
                // horizontal separator
                rawHtml +=  '<tr><td colspan="2" style="border-top: solid 1px black; height: 20px;"></td></tr>';
                // add customer to manifest
                rawHtml +=  '<tr>'+
                                '<td colspan="2" style="font-size: 16px; font-weight: bold; padding-bottom: 10px;">Add customer to manifest</td>'+
                            '</tr>';
                rawHtml +=  '<tr>'+
                                '<td colspan="2" style="padding-bottom: 10px;">'+
                                    '<table border="0" width="100%">'+
                                        '<tr>'+
                                            '<td style="font-size: 14px; color: #868686;">Pick up</td>'+
                                            '<td style="font-size: 14px; color: #868686; text-align: center;">Delivery</td>'+
                                            '<td style="font-size: 14px; color: #868686; text-align: end;">PU/Del</td>'+
                                        '</tr>'+
                                    '</table>'+
                                '</td>'+
                            '</tr>';
                // barcode
                rawHtml += '<tr>';
                rawHtml += '<td colspan="2" style="padding: 15px; background-color: #F8F8F8; border-radius: 8px; height: 63px; border: 1px solid #C4C4C4; text-align: center;">';
                // JsBarcode("#barcode", number, {
                //     text: number.match(/.{1,4}/g).join("  "),
                //     width: 1.25,
                //     height: 50,
                //     fontSize: 15,
                //     displayValue:false,
                // });
                // rawHtml += document.querySelector('#barcode_div').innerHTML;
                rawHtml += '';
                rawHtml += '</td>';
                rawHtml += '</tr>'
                // blanc logo
                rawHtml += '<tr><td colspan="2" style="text-align: center; padding: 15px 0 0;"><svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 1.34607C8.81829 1.34607 1.34778 8.81721 1.34778 18C1.34778 27.1828 8.80866 34.6539 18 34.6539C27.1913 34.6539 34.6546 27.1828 34.6546 18C34.6546 8.81721 27.1937 1.34607 18 1.34607ZM18 36C8.0746 36 0 27.926 0 18C0 8.07397 8.0746 0 18 0C27.9254 0 36 8.07639 36 18C36 27.9236 27.9254 36 18 36Z" fill="#47454B"/><path d="M16.7701 17.765C17.8445 18.5215 19.1241 18.9272 20.4355 18.9272C21.7469 18.9272 23.0266 18.5215 24.101 17.765C23.0283 17.0043 21.748 16.5959 20.4355 16.5959C19.1231 16.5959 17.8427 17.0043 16.7701 17.765ZM11.793 6.24599V29.2671H20.4404C22.116 29.2703 23.7244 28.6048 24.9129 27.4166C26.1013 26.2284 26.7727 24.6145 26.7797 22.929C26.7826 21.3532 26.1977 19.8336 25.1407 18.6705C23.7057 19.7746 21.9317 20.3384 20.1266 20.2641C18.3215 20.1898 16.5992 19.4821 15.2587 18.2637C15.1911 18.201 15.1371 18.1248 15.1002 18.04C15.0633 17.9552 15.0443 17.8636 15.0443 17.7711C15.0443 17.6785 15.0633 17.587 15.1002 17.5022C15.1371 17.4174 15.1911 17.3412 15.2587 17.2784L15.2827 17.2566C16.6191 16.036 18.3392 15.3264 20.1428 15.2516C21.9463 15.1768 23.7186 15.7416 25.1503 16.8475C26.2123 15.6856 26.8015 14.1648 26.8014 12.5865C26.7937 10.9014 26.1221 9.28817 24.9337 8.10047C23.7453 6.91278 22.1372 6.24761 20.462 6.25083L11.793 6.24599ZM20.4404 30.6132H11.5354H11.0998C10.9234 30.6113 10.7548 30.5396 10.6305 30.4137C10.5062 30.2877 10.4362 30.1176 10.4355 29.9401V5.57296C10.4368 5.39424 10.5085 5.22331 10.6348 5.09761C10.7611 4.97192 10.9318 4.9017 11.1094 4.90235H20.5799C22.5871 4.93528 24.5014 5.75839 25.912 7.19502C27.3226 8.63165 28.1171 10.5673 28.1251 12.5865C28.1261 14.5028 27.4094 16.3492 26.1178 17.7578C27.409 19.1666 28.1256 21.0128 28.1251 22.929C28.1168 24.9724 27.303 26.929 25.8624 28.3696C24.4217 29.8102 22.4717 30.617 20.4404 30.6132Z" fill="#47454B"/></svg></td></tr>';
                // user and date
                rawHtml += '<tr>'+
                                '<td colspan="2">'+
                                    '<p style="text-align: center; font-size: 10px; color: #868686; width: 100%; margin: 3px;">Printed by:</p>'+
                                    '<p style="text-align: center; font-size: 10px; color: #868686; width: 100%; margin: 3px;">'+customer.user+'</p>'+
                                    '<p style="text-align: center; font-size: 10px; color: #868686; width: 100%; margin: 3px;">'+customer.date+'</p>'+
                                '</td>'+
                            '</tr>';
                // close table, body and html
                rawHtml += '</table></body></html>';
                var data = [{
                    type: 'pixel',
                    format: 'html',
                    flavor: 'plain', // or 'file' if the data is file
                    data: rawHtml
                }];
                return qz.print(config, data);
            }
            const formatPrice=price=>{
                if(typeof price!="undefined" && price!=null)
                    return  `£${price !== 0 ? price.toFixed(2) : 0}`;
                return '';
            };

            const formatPhone = (phoneString)=>{
                if(phoneString != ""){
                    var phone = phoneString.split('"')[1];
                    if(phone.split("|").length > 1){
                        var area_code = phone.split("|")[0];
                        var number = phone.split("|")[1];
                        console.log("number" , number)
                        return '+' + area_code.replace(/\D/g, '') + ' ' + number.replace(/ /g, '').replace(/(\d{3})(\d{3})(\d{3,4})/, "$1 $2 $3").replace(']' , '');
                    }else
                        return phone.replace(/\D/g, '').replace(/(\d{2})(\d{3})(\d{3})(\d{3,4})/, "+$1 $2 $3 $3").replace(']' , '');
                }else{
                    return '--';
                }
            }
            return {
                route,
                printer_name,
                invoice_id,
                inv_details,
                cert,
                private_key,
                available_printers,
                default_printers,
                shouldClose,
                loadPrinterModal,
                closeModal,
                printReceipt,
                printReceiptQz,
                Target,
                print_modal,
                orderId,
                printOrderReceiptQz,
                formatPrice,
                printReceiptOrder,
                loadPrinterOrderModal,
                loadPrinterCustomer,
                formatPhone
            }
        }
    }

</script>
<style scoped>
    #print_btn{
        color:#333;
    }
    #print_btn .material-icons{
        color:#333;
    }
    .view{
        font-size:32pt;
    }

    #print_btn:hover{
        text-decoration: none;
    }

    #print_btn:hover span{
        color:#42a71e;
    }

    #print_txt{
        margin-top: -10px;
    }
    .pr-3{
        padding-right: 3rem !important;
    }

</style>
