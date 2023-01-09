<template>
    <router-view />
    <transition enter-active-class="animate__animated animate__fadeIn">

        <table class="table table-hover bg-white">
            <thead>
                <tr>
                    <th v-for="(item, index) in tableColumnsDef" :class="item.thClass" :key="index" style="border-bottom: 2px solid #dee2e6 !important">
                        <span>{{ item.label }}</span>
                    </th>
                </tr>
            </thead>
            <tbody>
            <transition-group name="list" appear>
                <tr v-for="(customer, index) in customerList" :key="index" class="trow" :id="'row_'+customer.id"
                    @click="selectrow(customer.id)"
                >
                    <!-- checkbox column -->
                    <td valign="middle" align="center">
                        <!-- <check-box :id="customer.id" :checked_checkbox="AR_UNPAID_LIST.includes(customer.id)" @checkbox-clicked="checkboxclicked"></check-box> -->
                        <check-box :id="customer.id"  @checkbox-clicked="checkboxclicked"></check-box>
                    </td>
                    <td>{{customer.NumFact}}</td>
                    <!-- Customer Type -->
                    <!--  -->
                    <td valign="middle"><span class="rounded-pill" :class="customer.type.toLowerCase()">{{ customer.type }}</span></td>
                    <!-- Level -->
                    <td class="fw-16" valign="middle">{{ customer.level }}</td>
                    <!-- active in -->
                    <td class="fw-16 text-capitalize" valign="middle"><span>{{ customer.active_in }}</span></td>
                    <!-- Customer Name -->
                    <td class="text-capitalize fw-16" valign="middle">{{ customer.name }}</td>

                    <!-- Email -->
                    <td class="fw-16" valign="middle">
                        <div v-if="customer.notification_status.length > 0">
                            <div v-for="detail in customer.notification_status" style="margin:0;" class="each-email-line d-flex w-100 align-items-center">{{detail.email}} <img v-if="detail.sent==1" src="/images/icon_check.svg"/><img v-else src="/images/unpaid_cross.svg"/></div>
                        </div>
                    </td>
                    <!-- Phone -->
                    <td class="text-nowrap fw-16" valign="middle">{{customer.orders}}</td>
                    <!--  total spent-->
                    <td class="fw-16 fw-bold" valign="middle">{{ customer.order_total ? ('-£'+customer.order_total.toFixed(2)) : "--" }}</td>
                    <td valign="middle" align="center">
                        <a :href="customer.url_invoice" target="_blank"><img src="/images/pdficon.svg" style="width:20px;"/></a>
                    </td>
                </tr>
            </transition-group>
            <tr v-if="customerList.length == 0">
                <td class="tcol" style="text-align: center" :colspan="Object.keys(tableColumnsDef).length">No Data Available</td>
            </tr>
            </tbody>
            <tfoot>
                <tr v-if="currentLoadedCustomerCount < totalCustomerCount">
                    <td class="tcol" style="text-align: center" :colspan="Object.keys(tableColumnsDef).length">  <button class="btn btn-link" @click="loadMoreCustomer">Show more ( {{ currentLoadedCustomerCount }} / {{ totalCustomerCount }} )</button></td>
                </tr>
            </tfoot>
        </table>
    </transition>
    <!-- <button class="ar-btn" v-if="AR_UNPAID_LIST.length" @click="batchPdf">Batch Pdf</button>
    <button class="mark-as-paid" v-if="AR_UNPAID_LIST.length" @click="markAsPaid">Mark As Paid</button> -->
</template>
<script>
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import {
    CUSTOMER_MODULE,
    LOAD_MORE_CUSTOMER,
    LOADER_MODULE,
    DISPLAY_LOADER,
    HIDE_LOADER,
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    // ADD_TO_AR_PAID_LIST,
    // REMOVE_FROM_AR_PAID_LIST,
    // FORMAT_AR_PAID_LIST,
    // GET_AR_PAID_LIST,
} from "../../store/types/types";
import { useStore } from 'vuex';
import CheckBox from '../miscellaneous/CheckBox';
import Modal from '../miscellaneous/Modal.vue';

export default {
    name: 'ArPaid',
    components:{
        CheckBox,
        Modal
    },
    setup(){
        const store = useStore();
        const route = useRoute();
        const customerList = ref([]);
        const totalCustomerCount = ref(0);
        const currentLoadedCustomerCount = ref(99999);

        const tableColumnsDef = [
                {
                    label: '',
                    key: 'checkbox',
                },
                {
                   label: 'Ref',
                    key: 'NumFact',
                    thClass: 'customer-table-th',
                    tdClass: 'text-nowrap fw-16',
                },
                {
                    label: 'Type',
                    key: 'type',
                    thClass: 'customer-table-th text-center',
                },
                {
                    label: 'Level',
                    key: 'level',
                    thClass: 'customer-table-th',
                },
                {
                    label: 'Active in',
                    key: 'active_in',
                    thClass: 'customer-table-th text-nowrap',
                    tdClass: 'text-capitalize fw-16',
                },
                {
                    label: 'Name',
                    key: 'name',
                    thClass: 'customer-table-th',
                    tdClass: 'text-capitalize fw-16',
                },
                /*
                {
                    label: 'Address',
                    key: 'address1',
                    thClass: 'customer-table-th',
                    tdClass: 'fw-16',
                },
                {
                    label: 'Postcode',
                    key: 'postcode',
                    thClass: 'customer-table-th',
                    tdClass: 'fw-16',
                },
                */
                {
                    label: 'Email',
                    key: 'email',
                    thClass: 'customer-table-th',
                    tdClass: 'fw-16',
                },

                {
                    label: 'Orders',
                    key: 'orders',
                    thClass: 'customer-table-th text-nowrap',
                    tdClass: 'fw-16',
                },
                {
                    label: 'Invoice Total',
                    key: 'order_total',
                    thClass: 'customer-table-th text-nowrap',
                    tdClass: 'fw-16',
                },
                {
                    label: 'PDF',
                    key: 'pdf_link',
                },
            ];


        // const CURRENT_SELECTED=computed(()=>{
        //     return store.getters[`${CUSTOMER_MODULE}${GET_CURRENT_SELECTED_CUSTOMER}`];
        // });
        // const AR_UNPAID_LIST=computed(()=>{
        //     return store.getters[`${CUSTOMER_MODULE}${GET_AR_UNPAID_LIST}`];
        // });
        const loadMoreCustomer = ()=>{
            store.dispatch(`${CUSTOMER_MODULE}${LOAD_MORE_CUSTOMER}`);
        }
        onMounted(()=>{
            loadCustomer();
        })

        function loadCustomer(){
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[ true, "Loading A/R Paid data....", ]);

            axios.post('/get-ar-paid-customers',{})
                .then((res)=>{
                    customerList.value = res.data.list;
                    totalCustomerCount.value = Object.values(res.data.list).length;
                }).catch((err)=>{

                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
        }

        const formatPhone = (phoneString)=>{
            if(phoneString !== "" &&  phoneString !="--" && phoneString !== null){
                var phone = phoneString.split('"')[1];
                var area_code = phone.split("|")[0];
                var number = phone.split("|")[1];
                if(phone.split("|").length > 1)
                    return '+' + area_code.replace(/\D/g, '') + ' ' + number.replace(/ /g, '').replace(/(\d{3})(\d{3})(\d{3,4})/, "$1 $2 $3");
                else
                    return phone.replace(/\D/g, '').replace(/(\d{2})(\d{3})(\d{3})(\d{3,4})/, "+$1 $2 $3 $3");
            }
            return phoneString;
        }

        const checkboxclicked = ( check, id)=>{
            // if(check){
            //     store.dispatch(`${CUSTOMER_MODULE}${REMOVE_FROM_AR_UNPAID_LIST}`, id);
            // }else{
            //     store.dispatch(`${CUSTOMER_MODULE}${ADD_TO_AR_UNPAID_LIST}`, id);
            // }
        }

        const selectrow = (rowId)=>{
            // if(AR_UNPAID_LIST.value.includes(rowId)){
            //     store.dispatch(`${CUSTOMER_MODULE}${REMOVE_FROM_AR_UNPAID_LIST}`, rowId);
            // }else{
            //     store.dispatch(`${CUSTOMER_MODULE}${ADD_TO_AR_UNPAID_LIST}`, rowId);
            // }
        }

        return {
            route,
            // AR_PAID_LIST,
            customerList,
            totalCustomerCount,
            currentLoadedCustomerCount,
            tableColumnsDef,
            formatPhone,
            loadMoreCustomer,
            checkboxclicked,
            selectrow,
        }
    }
}
</script>
<style scoped>
    .fw-16{
        font-size: 16px !important;
    }
    .fw-14{
        font-size: 14px !important;
    }
    .trow{
        transition: background-color 300ms ease-out;
        cursor: pointer;
        display: table-row;
    }
    .trow:hover,
    .trow.multi{
        transition: background-color 300ms ease-out;
        background: #F8F8F8;
    }
    .current_sel{
        position: relative;
        z-index: 99;
        background: rgb(247, 251, 246);
        box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.25);
    }
    .invoice-table-th span{
        font-family: "Gotham Rounded";
        font-weight: 400;
        font-size: 14px;
        color: #868686;
    }
    th span{
        font-family: 'Gotham Rounded';
        font-style: normal;
        font-weight: 400;
        font-size: 14px;
        line-height: 140%;
        color: #868686;
    }
    td span.b2b{
        font-family: "Gotham Rounded";
        font-size: 12px;
        line-height: 14px;
        background: rgba(212, 221, 247, 0.7);
        color: #4E58E7;
        padding: 5px 25px;
    }
    td span.b2c{
        font-family: "Gotham Rounded";
        font-size: 12px;
        line-height: 14px;
        color: #47454B;
        background: rgba(238, 229, 22, 0.22);
        padding: 5px 25px;
    }
   /*list transitions*/
    .list-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .list-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .list-enter-active{
        transition: all 1s ease;
    }

    .list-leave-from{
        opacity: 1;
        transform: scale(1);
    }
    .list-leave-to{
        opacity: 0;
        transform: scale(0.6);
    }
    .list-leave-active{
        transition: all 1s ease;
        position: absolute;
        width: 100%;
    }
    .list-move{
        transition:all 0.9s ease;
    }
    .back-layer{
        background:rgba(224, 224, 224,0.6);
        position: fixed;
        top: 0;
        left:0;
        height: 100%;
        width: 100%;
        z-index: 9999;
    }
</style>
<style>


    .chkbox_wrap{
        margin-left:15px;
    }

    .trow span.chkbox {
        border: 2px solid #FFF;
        transition: border-color 300ms ease-out;
    }
    .trow:hover span.chkbox,
    .trow.multi span.chkbox {
        border-color: #868686;
        transition: border-color 300ms ease-out;
    }

    .ar-btn{
        padding:0.5rem 1rem;
        border-radius:4px;
        background:none;
        border:thin solid #000;
        font:normal 16px "Gotham Rounded";
        margin-left:1rem;
        margin-right:2rem;
    }

    .ar-btn:hover{
        background:#000;
        color:#fff;
    }

    .each-email-line img{
        margin-left:5px;
    }
    .mark-as-paid{
        padding:0.5rem 1rem;
        border-radius:4px;
        border:thin solid red;
        font: normal 16px "Gotham Rounded";
        color: red;
    }
</style>
