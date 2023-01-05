<template>
    <div class="mini-search">
        <input type="text" @keyup.enter="searchCustomer" class="mini-search-input" v-model="miniSearch.accountName" placeholder="Search by account name">
        <input type="text" @keyup.enter="searchCustomer" class="mini-search-input" v-model="miniSearch.customerName" placeholder="Search by customer name">
        <input type="text" @keyup.enter="searchCustomer" class="mini-search-input" v-model="miniSearch.phoneNumber" placeholder="Search by phone number">
        <input type="text" @keyup.enter="searchCustomer" class="mini-search-input" v-model="miniSearch.orderNo" placeholder="Search by orders no">
        <input type="text" @keyup.enter="searchCustomer" class="mini-search-input" v-model="miniSearch.ticketNo" placeholder="Search by ticket no">
        <input type="text" @keyup.enter="searchCustomer" class="mini-search-input" v-model="miniSearch.hsl" placeholder="Search by HSL">
    </div>
</template>
<script>
import { ref } from 'vue';
import { useStore } from 'vuex';
import {
    CUSTOMER_MODULE,
    CUSTOMER_SET_MINI_SEARCH,
    SET_CUSTOMER_LIST
} from '../../store/types/types'
export default{
    name: 'CustomerMiniSearch',
    setup(){
        const store = useStore();
        const miniSearch = ref({
            orderNo: '',
            ticketNo: '',
            hsl: '',
            accountName: '',
            customerName: '',
            phoneNumber: '',
        })
        const searchCustomer = ()=>{
            store.dispatch(`${CUSTOMER_MODULE}${CUSTOMER_SET_MINI_SEARCH}`, miniSearch.value)
            store.dispatch(`${CUSTOMER_MODULE}${SET_CUSTOMER_LIST}`,{});
        }
        return{
            miniSearch,
            searchCustomer
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