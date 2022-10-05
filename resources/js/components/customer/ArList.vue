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
                        <check-box :id="customer.id" @checkbox-clicked="checkboxclicked"></check-box>
                    </td>
                    <!-- Customer Type -->
                    <!--  -->
                    <td valign="middle"><span class="rounded-pill" :class="customer.type.toLowerCase()">{{ customer.type }}</span></td>
                    <!-- active in -->
                    <td class="fw-16 text-capitalize" valign="middle"><span>{{ customer.active_in }}</span></td>
                    <!-- Customer Name -->
                    <td class="text-capitalize fw-16" valign="middle">{{ customer.name }}</td>
                    <!-- address -->
                    <td class="fw-14" valign="middle">{{ customer.address1 }}<span v-if="customer.address2 && customer.address2 !=''">,&nbsp; {{customer.address2}}</span></td>
                    <!-- Postcode -->
                    <th class="fw-14">{{customer.postcode}}</th>
                    <!-- Email -->
                    <td class="fw-16" valign="middle">{{ customer.email }}</td>
                    <!-- Phone -->
                    <td class="text-nowrap fw-16" valign="middle">{{ formatPhone(customer.phone) }}</td>
                    <!-- last order -->
                    <td class="fw-16" valign="middle">{{ customer.last_order_date ? customer.last_order_date : "--" }}</td>
                    <!--  total spent-->
                    <td class="fw-16 fw-bold" valign="middle">{{ customer.order_total ? ('-£'+customer.order_total.toFixed(2)) : "--" }}</td>
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


    <button class="ar-btn" v-if="Object.values(customerList).length > 0" @click="loadInvoiceModal">Batch invoice</button>

    <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
        <div v-if="showlayer" class="back-layer"></div>
    </transition>

    <modal ref="invoice_modal">
    <template #closebtn>
            <span class="close" id="addon_modal_close" @click="closeBatchInvoiceModal"></span>
        </template>
        <template #bheader>
            <div class="bmodal-header py-4 text-center">Batch invoice</div>
        </template>
        <template #bcontent>

        </template>
        <template #mbuttons>
            <div class="row mx-0 justify-content-center mt-3 mb-5">
                <div class="col-5">
                    <button class="btn btn-outline-dark w-100" @click="generateInvoice('mail')">Mail</button>
                </div>
                <div class="col-1"></div>
                <div class="col-5">
                    <button class="btn btn-outline-dark w-100" @click="generateInvoice('pdf')">Pdf</button>
                </div>
            </div>
        </template>
    </modal>


</template>
<script>
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import {
    CUSTOMER_MODULE,
    GET_ALL_SELECTED_CUSTOMER,
    GET_CURRENT_SELECTED_CUSTOMER,
    GET_CUSTOMER_LIST,
    GET_LOADED_CUSTOMER_COUNT,
    GET_TOTAL_CUSTOMER_COUNT,
    LOAD_MORE_CUSTOMER,
    SET_CURRENT_SELECTED_CUSTOMER,
    SET_CUSTOMER_DETAIL,
    SET_CUSTOMER_LIST,
    SET_CUSTOMER_FILTER,
    FILTER_CUSTOMER_LIST,
    LOADER_MODULE,
    DISPLAY_LOADER,
    HIDE_LOADER,
    TOASTER_MODULE,
    TOASTER_MESSAGE,

} from "../../store/types/types";
import { useStore } from 'vuex';
import CheckBox from '../miscellaneous/CheckBox';
import Modal from '../miscellaneous/Modal.vue';

export default {
    name: 'ArList',
    components:{
        CheckBox,
        Modal
    },
    setup(){
        const store = useStore();
        const route = useRoute();
        const router = useRouter();
        const filterDef = ref({});
        const customerList = ref([]);
        const invoice_modal = ref();


        const totalCustomerCount = ref(0);
        const currentLoadedCustomerCount = ref(99999);

        filterDef.value={
            'Customername':{
                name:route.params.name,
                value:route.params.value
            },
        };
        const tableColumnsDef = [
                {
                    label: '',
                    key: 'checkbox',
                },
                {
                    label: 'Type',
                    key: 'type',
                    thClass: 'customer-table-th text-center',
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

                {
                    label: 'Email',
                    key: 'email',
                    thClass: 'customer-table-th',
                    tdClass: 'fw-16',
                },
                {
                    label: 'Phone',
                    key: 'phone',
                    thClass: 'customer-table-th',
                    tdClass: 'text-nowrap fw-16',
                },
                {
                    label: 'Last Order',
                    key: 'last_order_date',
                    thClass: 'customer-table-th text-nowrap',
                    tdClass: 'fw-16',
                },
                {
                    label: 'Uninvoiced',
                    key: 'order_total',
                    thClass: 'customer-table-th text-nowrap',
                    tdClass: 'fw-16',
                },
            ];
        /*
        const customerList = computed(()=>{
            return store.getters[`${CUSTOMER_MODULE}${GET_CUSTOMER_LIST}`];
        });

        const totalCustomerCount = computed(()=>{
            return store.getters[`${CUSTOMER_MODULE}${GET_TOTAL_CUSTOMER_COUNT}`];
        })


        const currentLoadedCustomerCount = computed(()=>{
            return store.getters[`${CUSTOMER_MODULE}${GET_LOADED_CUSTOMER_COUNT}`];
        });

        */

        const CURRENT_SELECTED=computed(()=>{
            return store.getters[`${CUSTOMER_MODULE}${GET_CURRENT_SELECTED_CUSTOMER}`];
        });
        const MULTI_SELECTED=computed(()=>{
            return store.getters[`${CUSTOMER_MODULE}${GET_ALL_SELECTED_CUSTOMER}`];
        });
        const loadMoreCustomer = ()=>{
            store.dispatch(`${CUSTOMER_MODULE}${LOAD_MORE_CUSTOMER}`);
        }
        onMounted(()=>{
            loadCustomer();


            /*
            if(route.params != null){
              store.dispatch(`${CUSTOMER_MODULE}${SET_CUSTOMER_FILTER}`, _.cloneDeep(filterDef.value))
              store.dispatch(`${CUSTOMER_MODULE}${FILTER_CUSTOMER_LIST}`)
            } else {
             store.dispatch(`${CUSTOMER_MODULE}${SET_CUSTOMER_LIST}`);
            }
            */

        })

        function loadCustomer(){
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[
                true,
                "Loading Customer data....",
            ]);

            axios.post('/get-ar-customers',{})
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
            let row = document.getElementById('row_'+id);

            if(check){
                row.classList.add('current_sel');
            }else{
                row.classList.remove('current_sel');
            }

            /*
            if(CURRENT_SELECTED.value == id && check == false){
                store.dispatch(`${CUSTOMER_MODULE}${SET_CURRENT_SELECTED_CUSTOMER}`,'');
                store.commit(`${CUSTOMER_MODULE}${SET_CUSTOMER_DETAIL}`, {
                    name: ''
                });
                router.back();
            }
            */
        }
        const selectrow = (customerID)=>{
            let el = document.getElementById('row_'+customerID);
            el.classList.toggle('current_sel');

            let classes = Object.values(el.classList);

            let checkbox = document.querySelector('#row_'+customerID+' .chkbox');

            if(classes.includes('current_sel')){
                checkbox.classList.add('checked');
            }else{
                checkbox.classList.remove('checked');
            }

        }

        function loadInvoiceModal(){
            invoice_modal.value.showModal();
        }

        function closeBatchInvoiceModal(){
            invoice_modal.value.closeModal();
        }

        function generateInvoice(type){
            let els = Object.values(document.querySelectorAll('.current_sel'));
            let customer_ids = [];

            if(els.length==0){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                    message:"No customer selected",
                    ttl:5,
                    type:'danger'
                });
            }else{
                els.forEach((v,i)=>{
                    let id = v.getAttribute('id').replace('row_','');
                    customer_ids.push(id);
                });

                closeBatchInvoiceModal();
                let load_text = "";

                if(type=='pdf'){
                   load_text = "Generating PDF....";
                }
                if(type=='mail'){
                    load_text = 'Sending mail....';
                }

                 store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[
                        true,
                        load_text,
                    ]);

                axios.post('/generate-ar-invoice',{
                    customer_ids:JSON.stringify(customer_ids),
                    type:type,
                }).then((res)=>{
                    if(type=='pdf'){
                        if(res.data.url){
                            window.location = res.data.url;
                        }else{
                            if(type=='pdf' && res.data.details_per_cust.length==0){
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                                    message:"No valid invoices available",
                                    ttl:5,
                                    type:'danger'
                                });
                            }
                        }
                    }
                    if(type=='mail'){
                        if(res.data.sent){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                                    message:"Mail sent",
                                    ttl:5,
                                    type:'success'
                                });
                        }
                    }
                }).catch((err)=>{

                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });

            }
        }


        function batchInvoice(){
            let els = Object.values(document.querySelectorAll('.current_sel'));
            let customer_ids = [];

            if(els.length==0){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                    message:"No customer selected",
                    ttl:5,
                    type:'danger'
                });
            }else{
                els.forEach((v,i)=>{
                    let id = v.getAttribute('id').replace('row_','');
                    customer_ids.push(id);
                });


            }

        }

        return {
            route,
            CURRENT_SELECTED,
            MULTI_SELECTED,
            customerList,
            totalCustomerCount,
            currentLoadedCustomerCount,
            tableColumnsDef,
            formatPhone,
            loadMoreCustomer,
            checkboxclicked,
            selectrow,
            showlayer: computed( ()=> {
                return (route.params.customer_id > 0 && CURRENT_SELECTED.value != '');
                // return false;
            }),
            batchInvoice,
            invoice_modal,
            loadInvoiceModal,
            closeBatchInvoiceModal,
            generateInvoice,
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
        z-index: 10000;
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
</style>
