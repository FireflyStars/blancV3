<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <table class="table table-hover mb-0 bg-white">
            <thead>
                <tr>
                    <th v-for="(item, index) in tableColumnsDef" :class="item.thClass" :key="index" v-html="item.label ? item.label : ''" style="border-bottom: 2px solid #dee2e6 !important"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(invoiceRow, index) in invoiceList" :key="index">
                    <td class="visible-hidden">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" :id="'invoice-'+invoiceRow.order_id">
                            <label class="form-check-label" :for="'invoice-'+invoiceRow.order_id"></label>
                        </div>
                    </td>
                    
                    <td class="text-capitalize fw-16" v-if="invoiceRow.order_id == 'xxx'">
                        <span>{{ invoiceRow.order_id }}</span>&nbsp;&nbsp;
                        <svg width="32" height="32" viewBox="0 0 44 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.7344 16.763V23.0131C20.7344 23.2514 20.8394 23.474 21.0208 23.6225L24.8403 26.7475L25.7952 25.5268L22.2621 22.6381V16.7631L20.7344 16.763Z" fill="#EB5757"/>
                            <path d="M30.9284 22.5C30.9284 27.5601 26.9204 31.6429 21.9999 31.6429C17.0793 31.6429 13.0713 27.5601 13.0713 22.5C13.0713 17.4399 17.0793 13.3571 21.9999 13.3571C26.9204 13.3571 30.9284 17.4399 30.9284 22.5Z" stroke="#EB5757"/>
                        </svg>
                    </td>
                    <td class="text-capitalize fw-16" v-else><span>{{ invoiceRow.order_id }}</span></td>

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
                    <td class="text-capitalize fw-16" v-else>
                        <svg width="11" height="11" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="6.5" cy="6.5" r="6.5" fill="#EF8F00"/>
                        </svg>
                        &nbsp;&nbsp;<span>{{ invoiceRow.sub_order }}</span>
                    </td>
                    
                    <!-- Item -->
                    <td class="text-capitalize fw-16">{{ invoiceRow.iteminfo }}</td>
                    <!-- BarCode -->
                    <td class="text-capitalize fw-16"><a href="javascript:;" class="text-decoration-none text-primary">{{ invoiceRow.barcode }}</a></td>
                    <!-- Location -->
                    <td class="text-center">
                        <div class="invoice-location assembling rounded-pill m-auto" :style="{'background-color': '#'+invoiceRow.location_color }">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" fill="#4E58E7"/>
                            <circle cx="6" cy="6" r="5" stroke="#4E58E7" stroke-width="2"/>
                            </svg>
                            &nbsp;&nbsp;<span class="d-block text-center" :style="{ width: 'calc( 100% - 12px )'}">{{ invoiceRow.location }}</span>
                        </div>
                    </td>
                    <!-- Prod -->
                    <td class="text-capitalize fw-16">{{ invoiceRow.prod }}</td>
                    <!-- Deliv -->
                    <td class="text-capitalize fw-16 fw-bold">{{ invoiceRow.deliv != '01/01' ? invoiceRow.deliv : (invoiceRow.deliv_tmp == '01/01' ? 'N/A': invoiceRow.deliv_tmp)}}</td>
                </tr>
                <tr v-if="invoiceList.length == 0">
                    <td class="tcol" style="text-align: center" :colspan="Object.keys(tableColumnsDef).length">  No Data</td>
                </tr>
            </tbody>
            <tfoot>
                <tr v-if="currentLoadedInvoiceCount < totalInvoiceCount">
                    <td class="tcol" style="text-align: center" :colspan="Object.keys(tableColumnsDef).length">  <button class="btn btn-link" @click="loadMoreInvoice">Show more</button></td>
                </tr>
            </tfoot>
        </table>
    </transition>
</template>
<script>
import { ref, onMounted, computed } from "vue";
import {
    INVOICE_MODULE, 
    SET_INVOICE_LIST,
    GET_INVOICE_LIST, 
    GET_TOTAL_INVOICE_COUNT, 
    GET_LOADED_INVOICE_COUNT, 
    LOAD_MORE_INVOICE,     
} from "../../store/types/types";
import { useStore } from 'vuex';

export default {
    name: 'InvoiceList',
    setup(){
        const store = useStore();
        const tableColumnsDef = ref( [
                {
                    key: 'selected',
                    tdClass: 'visible-hidden'
                },
                {
                    label: 'Order N&deg;',
                    key: 'order_id',
                    thClass: 'text-uppercase invoice-table-th',

                },
                {
                    label: 'Customer',
                    key: 'customer_name',
                    thClass: 'text-uppercase invoice-table-th',
                    tdClass: 'text-capitalize fw-16',                        
                },
                {
                    label: 'Destination',
                    key: 'store',
                    thClass: 'text-uppercase invoice-table-th',
                },

                {
                    label: 'Sub Order',
                    key: 'sub_order',
                    thClass: 'text-uppercase invoice-table-th',
                },
                {
                    label: 'Item',
                    key: 'iteminfo',
                    thClass: 'text-uppercase invoice-table-th',
                },
                {
                    label: 'Barcode',
                    key: 'barcode',
                    thClass: 'text-uppercase invoice-table-th',
                },
                {
                    label: 'Location',
                    key: 'location',
                    thClass: 'text-uppercase invoice-table-th text-center',
                },
                {
                    label: 'Prod',
                    key: 'prod',
                    thClass: 'text-uppercase invoice-table-th',
                },
                {
                    label: 'Deliv',
                    key: 'deliv',
                    thClass: 'text-uppercase invoice-table-th',
                },
            ]);
        const invoiceList = computed(()=>{
            return store.getters[`${INVOICE_MODULE}${GET_INVOICE_LIST}`];
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
            store.dispatch(`${INVOICE_MODULE}${SET_INVOICE_LIST}`);
        })
        return {
            invoiceList,
            totalInvoiceCount,
            currentLoadedInvoiceCount,
            tableColumnsDef,
            loadMoreInvoice
        }
    }
}
</script>
<style scoped>

</style>