<template>
    <InvoiceMiniSearch></InvoiceMiniSearch>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <table class="table table-hover mb-0 bg-white">
            <thead>
                <tr>
                    <th v-for="(item, index) in tableColumnsDef" :class="item.thClass" :key="index" :style="{borderBottom: '2px solid #dee2e6 !important', width: item.width}">
                        <check-box v-if="item.key == 'selected' && invoiceList.length" :checked_checkbox="(invoiceList.length==MULTI_SELECTED.length)" @checkbox-clicked="checkboxallclicked"></check-box>
                        <span v-else>{{ item.label }}</span>
                    </th>
                </tr>
            </thead>
            <tbody>
            <transition-group name="list" appear>
                <tr v-for="(invoiceRow, index) in invoiceList" :key="index" class="trow" :class="{current_sel:invoiceRow.item_id == CURRENT_SELECTED&&route.params.item_id>0}"
                    @click="selectrow(invoiceRow.item_id)"
                    >
                    <!-- checkbox column -->
                    <td valign="middle" align="center">
                        <check-box :checked_checkbox="(invoiceRow.item_id == CURRENT_SELECTED&&route.params.item_id>0) || MULTI_SELECTED.includes(invoiceRow.item_id)" :id="invoiceRow.item_id" @checkbox-clicked="checkboxclicked"></check-box>
                    </td>
                    <!-- <td class="text-capitalize fw-16" v-if="invoiceRow.item_id == 'xxx'">
                        <span>{{ invoiceRow.item_id }}</span>&nbsp;&nbsp;
                        <svg width="32" height="32" viewBox="0 0 44 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.7344 16.763V23.0131C20.7344 23.2514 20.8394 23.474 21.0208 23.6225L24.8403 26.7475L25.7952 25.5268L22.2621 22.6381V16.7631L20.7344 16.763Z" fill="#EB5757"/>
                            <path d="M30.9284 22.5C30.9284 27.5601 26.9204 31.6429 21.9999 31.6429C17.0793 31.6429 13.0713 27.5601 13.0713 22.5C13.0713 17.4399 17.0793 13.3571 21.9999 13.3571C26.9204 13.3571 30.9284 17.4399 30.9284 22.5Z" stroke="#EB5757"/>
                        </svg>
                    </td> -->
                    <td class="text-capitalize fw-16"><span>{{ invoiceRow.item_id }}</span></td>

                    <!-- Customer Name -->
                    <td class="text-capitalize fw-16">{{ invoiceRow.customer_name }}</td>

                    <!-- Destination -->
                    <td class="text-capitalize fw-16">{{ invoiceRow.store }}</td>

                    <!-- sub order -->
                    <td class="text-capitalize fw-16" v-if="invoiceRow.sub_order == 'xxx'">
                        <svg width="11" height="11" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="6.5" cy="6.5" r="6.5" fill="#9E44F2"/>
                        </svg>
                        &nbsp;&nbsp;<span>{{ invoiceRow.sub_order }}</span>
                    </td>
                    <td class="text-capitalize fw-16 text-nowrap" v-else>
                        <svg width="11" height="11" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="6.5" cy="6.5" r="6.5" fill="#EF8F00"/>
                        </svg>
                        &nbsp;&nbsp;<span class="text-nowrap">{{ invoiceRow.sub_order }}</span>
                    </td>

                    <!-- Item -->
                    <td class="text-capitalize fw-16">{{ invoiceRow.iteminfo }}</td>
                    <!-- BarCode -->
                    <td class="text-capitalize fw-16"><a href="javascript:;" class="text-decoration-none text-primary">{{ invoiceRow.barcode }}</a></td>
                    <!-- Location -->
                    <td class="text-center">
                        <div class="invoice-location assembling rounded-pill w-100" :style="{'background-color': invoiceRow.location_color }">
                            <svg v-if="invoiceRow.process != 0" width="12" height="12" viewBox="0 0 12 12" :fill="'#'+invoiceRow.circle_color" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" :fill="'#'+invoiceRow.circle_color"/>
                                <circle cx="6" cy="6" r="5" :stroke="'#'+invoiceRow.circle_color" stroke-width="2"/>
                            </svg>
                            <svg v-else width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" :fill="'#'+invoiceRow.circle_color"/>
                                <circle cx="6" cy="6" r="5" :stroke="'#'+invoiceRow.circle_color" stroke-width="2"/>
                            </svg>
                            &nbsp;&nbsp;<span class="d-block text-center" :style="{ width: 'calc( 100% - 12px )'}">{{ invoiceRow.location }}</span>
                        </div>
                    </td>
                    <!-- Detailed Date -->
                    <td class="text-capitalize fw-16">{{ invoiceRow.detailed_at }}</td>
                    <!-- Prod -->
                    <td class="text-capitalize fw-16">{{ invoiceRow.prod }}</td>
                    <!-- Deliv -->
                    <td class="text-capitalize fw-16 fw-bold">{{ invoiceRow.deliv != '01/01' ? invoiceRow.deliv : (invoiceRow.deliv_tmp == '01/01' ? 'N/A': invoiceRow.deliv_tmp)}}</td>
                </tr>
            </transition-group>
            <tr v-if="invoiceList.length == 0">
                <td class="tcol" style="text-align: center" :colspan="Object.keys(tableColumnsDef).length">No Data Available</td>
            </tr>
            </tbody>
            <tfoot>
                <tr v-if="currentLoadedInvoiceCount < totalInvoiceCount">
                    <td class="tcol" style="text-align: center" :colspan="Object.keys(tableColumnsDef).length">  <button class="btn btn-link" @click="loadMoreInvoice">Show more ( {{ currentLoadedInvoiceCount }} / {{ totalInvoiceCount }} )</button></td>
                </tr>
            </tfoot>
        </table>
    </transition>
    <!-- <router-view /> -->
</template>
<script>
import { ref, onMounted, computed } from "vue";
import { useRouter, useRoute } from 'vue-router'
import {
    INVOICE_MODULE,
    SET_INVOICE_LIST,
    GET_INVOICE_LIST,
    GET_TOTAL_INVOICE_COUNT,
    GET_LOADED_INVOICE_COUNT,
    LOAD_MORE_INVOICE,
    INVOICELIST_GET_CURRENT_SELECTED,
    INVOICELIST_GET_ALL_SELECTED,
    INVOICELIST_SET_CURRENT_SELECTED,
    INVOICELIST_SET_ALL_SELECTED,
    INVOICELIST_SET_MULTI_UNCHECKED,
    INVOICE_RESET_MULITCHECKED,
    ITEM_DETAIL_MODULE,
    ITEM_DETAIL_SET_DETAIL
} from "../../store/types/types";
import { useStore } from 'vuex';
import CheckBox from '../miscellaneous/CheckBox';
import InvoiceMiniSearch from './InvoiceMiniSearch';

export default {
    name: 'InvoiceList',
    components:{
        CheckBox,
        InvoiceMiniSearch
    },
    setup(){
        const store = useStore();
        const router = useRouter();
        const route = useRoute();
        const tableColumnsDef = ref( [
                {
                    key: 'selected',
                    width:"3%",
                },
                {
                    label: 'Order N??',
                    key: 'item_id',
                    thClass: 'text-uppercase invoice-table-th',
                    width:"7%",

                },
                {
                    label: 'Customer',
                    key: 'customer_name',
                    thClass: 'text-uppercase invoice-table-th',
                    tdClass: 'text-capitalize fw-16',
                    width:"14%",
                },
                {
                    label: 'Destination',
                    key: 'store',
                    thClass: 'text-uppercase invoice-table-th',
                    width:"8%",
                },

                {
                    label: 'Sub Order',
                    key: 'sub_order',
                    thClass: 'text-uppercase invoice-table-th',
                    width:"8%",
                },
                {
                    label: 'Item',
                    key: 'iteminfo',
                    thClass: 'text-uppercase invoice-table-th',
                    width:"15%",
                },
                {
                    label: 'Barcode',
                    key: 'barcode',
                    thClass: 'text-uppercase invoice-table-th',
                    width:"8%",
                },
                {
                    label: 'Location',
                    key: 'location',
                    thClass: 'text-uppercase invoice-table-th',
                    width:"16%",
                },
                {
                    label: 'DETAILED',
                    key: 'detailed_at',
                    thClass: 'text-uppercase invoice-table-th',
                    width:"7%",
                },
                {
                    label: 'Prod',
                    key: 'prod',
                    thClass: 'text-uppercase invoice-table-th',
                    width:"7%",
                },
                {
                    label: 'Deliv',
                    key: 'deliv',
                    thClass: 'text-uppercase invoice-table-th',
                    width:"7%",
                },
            ]);
        const invoiceList = computed(()=>{
            return store.getters[`${INVOICE_MODULE}${GET_INVOICE_LIST}`];
        });
        const CURRENT_SELECTED=computed(()=>{
            return store.getters[`${INVOICE_MODULE}${INVOICELIST_GET_CURRENT_SELECTED}`];
        });
        const MULTI_SELECTED=computed(()=>{
            return store.getters[`${INVOICE_MODULE}${INVOICELIST_GET_ALL_SELECTED}`];
        });
        const currentLoadedInvoiceCount = computed(()=>{
            return store.getters[`${INVOICE_MODULE}${GET_LOADED_INVOICE_COUNT}`];
        });
        const totalInvoiceCount = computed(()=>{
            return store.getters[`${INVOICE_MODULE}${GET_TOTAL_INVOICE_COUNT}`];
        })

        const loadMoreInvoice = ()=>{
            store.dispatch(`${INVOICE_MODULE}${LOAD_MORE_INVOICE}`);
        }
        onMounted(()=>{
            store.dispatch(`${INVOICE_MODULE}${SET_INVOICE_LIST}`,route.params);
        })

        const checkboxclicked = ( check, id, name )=>{
            if(CURRENT_SELECTED.value == id && check == false){

                store.dispatch(`${INVOICE_MODULE}${INVOICELIST_SET_CURRENT_SELECTED}`,'');
                store.commit(`${ITEM_DETAIL_MODULE}${ITEM_DETAIL_SET_DETAIL}`, {
                    breif_info: {
                        id: ''
                    },
                    location_history: []
                });
                router.back();
            }
            if(check == true){
                store.dispatch(`${INVOICE_MODULE}${INVOICELIST_SET_ALL_SELECTED}`, id);
            }
            if(check==false){
                store.dispatch(`${INVOICE_MODULE}${INVOICELIST_SET_MULTI_UNCHECKED}`, id);
            }
        }
        const checkboxallclicked = ( check, id, name )=>{
            if(check==false)
                store.dispatch(`${INVOICE_MODULE}${INVOICE_RESET_MULITCHECKED}`);
            if(check){
                const list=_.cloneDeep(invoiceList.value);
                list.forEach(item => store.dispatch(`${INVOICE_MODULE}${INVOICELIST_SET_ALL_SELECTED}`, item.item_id));
            }
        }
        const selectrow = (item_id)=>{
            store.dispatch(`${INVOICE_MODULE}${INVOICELIST_SET_CURRENT_SELECTED}`, item_id);
            router.push({
                name:'ItemDetails',
                params: {
                    item_id:item_id,
                },
            })
        }
        return {
            route,
            invoiceList,
            totalInvoiceCount,
            currentLoadedInvoiceCount,
            tableColumnsDef,
            CURRENT_SELECTED,
            MULTI_SELECTED,
            loadMoreInvoice,
            checkboxclicked,
            checkboxallclicked,
            selectrow,
        }
    }
}
</script>
<style scoped>
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

</style>
<style>
    .trow span.chkbox {
        border: 2px solid #FFF;
        transition: border-color 300ms ease-out;
    }
    .trow:hover span.chkbox,
    .trow.multi span.chkbox {
        border-color: #868686;
        transition: border-color 300ms ease-out;
    }
    .invoice-table-th span{
        font-family: "Gotham Rounded";
        font-weight: 400;
        font-size: 14px;
        color: #868686;
    }
</style>