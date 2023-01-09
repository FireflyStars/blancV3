<template>
    <div class="mini-search">
        <input type="text" @keyup.enter="searchInvoice" class="mini-search-input" v-model="miniSearch.orderNo" placeholder="Search by orders no">
        <input type="text" @keyup.enter="searchInvoice" class="mini-search-input" v-model="miniSearch.ticketNo" placeholder="Search by ticket no">
        <input type="text" @keyup.enter="searchInvoice" class="mini-search-input" v-model="miniSearch.hsl" placeholder="Search by HSL">
        <input type="text" @keyup.enter="searchInvoice" class="mini-search-input" v-model="miniSearch.itemName" placeholder="Search by item name">
        <input type="text" @keyup.enter="searchInvoice" class="mini-search-input" v-model="miniSearch.customerName" placeholder="Search by customer name">
        <input type="text" @keyup.enter="searchInvoice" class="mini-search-input" v-model="miniSearch.type" placeholder="Search by type">
        <input type="text" @keyup.enter="searchInvoice" class="mini-search-input" v-model="miniSearch.brand" placeholder="Search by brand">
        <input type="text" @keyup.enter="searchInvoice" class="mini-search-input" v-model="miniSearch.color" placeholder="Search by color">
    </div>
</template>
<script>
import { ref } from 'vue';
import { useStore } from 'vuex';
import {
    INVOICE_MODULE,
    SET_INVOICE_LIST,
    SET_INVOICE_MINI_SEARCH
} from '../../store/types/types'
export default{
    name: 'InvoiceMiniSearch',
    setup(){
        const store = useStore();
        const miniSearch = ref({
            orderNo: '',
            ticketNo: '',
            hsl: '',
            itemName: '',
            customerName: '',
            type: '',
            brand: '',
            color: '',
        })
        const searchInvoice = ()=>{
            store.dispatch(`${INVOICE_MODULE}${SET_INVOICE_MINI_SEARCH}`, miniSearch.value)
            store.dispatch(`${INVOICE_MODULE}${SET_INVOICE_LIST}`,{});
        }
        return{
            miniSearch,
            searchInvoice
        }
    }
}
</script>
<style>
.mini-search{
    height: 64px;
    width: 100%;
    padding: 17px 14px;
    display: flex;
    align-items: center;
    background: white;
    box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.25);    
}
.mini-search-input{
    width: 200px;
    height: 30px;
    background: #FFFFFF;
    border: 1px solid #C4C4C4;
    border-radius: 4px;    
    padding-left: 33px;
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: 350;
    font-size: 12px;
    line-height: 140%; 
    background-image: url(../../../img/mini-search.svg);   
    background-position-x: 10px;
    background-position-y: center;
    background-repeat: no-repeat;
    margin-left: 4px;
}
</style>