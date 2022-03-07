<template>
    <div class="row">
        <div class="d-none" id="barcode_div">
            <svg id="barcode"></svg>
        </div>
        <teleport to=".item-detail-panel" :disabled="shouldClose" v-if="!shouldClose">
            <Modal @close-modal="closeModal">
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
    import { uesRoute, useRoute, useRouter } from 'vue-router';
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
            const router = useRouter();
            const printer_name = ref('');
            const invoice_id = ref('');
            const invoiceId = ref('');
            const inv_details = ref({});
            const cert = ref('');
            const private_key = ref('');
            const available_printers = ref([]);
            const default_printers = ref([]);
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

                    qz.websocket.connect().then(function() {
                        //Find printers
                        qz.printers.find().then(function (data) {
                            available_printers.value = data;
                        }).catch(function (e) {
                            console.error(e);
                        });

                        //Get default printer
                        qz.printers.getDefault().then(function(data) {
                            printer_name.value = data;
                        }).catch(function (e) {
                            console.error(e);
                        });

                    }).catch(function (e) {
                        console.error(e);
                    });

                });
            })

            onBeforeUnmount( ()=>{
                qz.websocket.disconnect();
            } )
            const shouldClose = ref(true);
            const closeModal = ()=>{
                shouldClose.value = true;
            }
            const loadPrinterModal = (invoice_id)=>{
                invoiceId.value = invoice_id;
                shouldClose.value = false;
            }
            const printReceipt = ()=>{
                let err =false;
                let err_arr = [];
                if(printer_name.value == ''){
                    err = true;
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{ message:`Please enter printer name`,ttl:5, type:'info'},{ root: true });
                }

                if(err){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{ message: "An error has occured",ttl:5, type:'danger'},{ root: true });
                }else{
                    axios.post('/get-suborder-and-print', {
                        invoice_id: invoiceId.value,
                        route_name: route.name,
                        poste_id: store.getters[`${ITEM_DETAIL_MODULE}${ITEM_DETAIL_GET_DETAIL}`].breif_info.poste_id,
                    })
                    .then((res) => {
                        shouldClose.value = true;
                        if(res.data.inv) {
                            let inv = res.data.inv;
                            if(parseInt(inv.count_six_month_items) == 0) {
                                printReceiptQz(printer_name.value, res.data.inv);
                            }else{
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{ message: "ERROR WRONG SubOrder",ttl:5, type:'danger'},{ root: true });
                            }
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
                var config = qz.configs.create(printer);
                let toPrint = '<html><head></head><body style="font-family:Arial, Helvetica, sans-serif; text-align:center; width:90%; margin: auto;"></body>';
                toPrint += '<table border="0" width="100%" style="font-size:10pt;">';
                toPrint += '<tr><td width="60%">Tel. 020 8004 2630</td><td style="text-align: right;">User: '+inv.user_initials+'</td></tr>';
                toPrint += '</table>';
                toPrint += '<p style="text-align: center"><span style="font-size:9pt">www.blancliving.co&nbsp;&nbsp;&nbsp;&nbsp;Email: info@blancliving.co</span><br/><span style="font-size:11pt;">Vat Reg. No. 124 0369 45</span></p>';
                toPrint += '<div style="display:table; margin:auto; padding:10px; border:thin solid #000000;">'+inv.StoreName+'</div>';
                toPrint += '<p style="text-align: center;font-size:20pt;">'+inv.NumInvoice+'</p>';

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

                toPrint += '<span style="font-size:14pt;">'+inv.Client+'</span><br/>';

                if(inv.phone_num) {
                    toPrint += '<span style="font-size:10pt">Tel: '+inv.phone_num[0].replace("|","")+'</span>';
                    toPrint += '<br/>';
                }


                toPrint += '<hr/>';
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
                        if(v.item_comment.length > 0){
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
                    toPrint +='<td style="font-size:10pt; text-align:right;" width="30%;">SUBTOTAL</td><td style="font-size:10pt; text-align: right;">'+total_items_price.toFixed(2)+'</td>';
                    toPrint += '</tr>';
                    toPrint += '<tr><td></td><td width="30%" style="font-size:10pt; text-align: right">TOTAL</td><td width="20%" style="font-size:10pt; text-align: right">'+total_items_price.toFixed(2)+'</td></tr>';
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


                return qz.print(config, data);
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
                printReceiptQz
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
