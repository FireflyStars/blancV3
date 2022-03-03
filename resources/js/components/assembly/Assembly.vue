<template>
    <transition enter-active-class="animate__animated animate__fadeIn animate__fadeOut">
    <div class="container-fluid h-100 bg-color">
        <main-header></main-header>
        <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax" style="z-index: 100">
            <side-bar></side-bar>
                <div class="col main-view mx-5 py-5" id="assembly-home">
                    <h2 class="mx-0 font-22">Production Track</h2>
                    <div class="nav-panel d-flex justify-content-between mb-1">
                        <ul class="assembly-home-nav list-inline mb-0">
                            <li class="assembly-home-nav-item font-16 list-inline-item px-3 py-2" :class="selected_nav == 'AssemblyHome' ? 'bg-white active' : ''" @click="selected_nav = 'AssemblyHome'">Stations</li>
                            <li class="assembly-home-nav-item font-16 list-inline-item px-3 py-2" :class="selected_nav == 'Commitment' ? 'bg-white active' : ''" @click="selected_nav = 'Commitment'">Commitment</li>
                            <li class="assembly-home-nav-item font-16 list-inline-item px-3 py-2" :class="selected_nav == 'InvoiceList' ? 'bg-white active' : ''" @click="selected_nav = 'InvoiceList'">All items</li>
                            <li class="assembly-home-nav-item font-16 list-inline-item px-3 py-2" :class="selected_nav == 'Overdue' ? 'bg-white active' : ''" @click="selected_nav = 'Overdue'">Overdue</li>
                        </ul>
                        <div class="filter-section position-relative" v-if="selected_nav == 'InvoiceList'">
                            <filters :filterDef="filterDef"></filters>
                        </div>
                    </div>
                    <KeepAlive>
                        <component :is="selected_nav"></component>
                    </KeepAlive>
                </div>
        </div>
    </div>
    </transition>
</template>

<script>
    import SideBar from "../layout/SideBar";
    import MainHeader from "../layout/MainHeader";
    import Filters from '../test/Filter';
    import InvoiceList from './InvoiceList';
    import AssemblyHome from './AssemblyHome';
    import { ref } from "vue";

    export default {
        name: "Assembly",
        components:{
            SideBar, 
            MainHeader,
            Filters,
            InvoiceList,
            AssemblyHome
        },
        setup(){
            const selected_nav = ref('InvoiceList');
            const filterDef =  ref({
                    status: {
                        label: 'Sub Order Status',
                        id: 'sub_order_status',
                        mode: 'single',
                        value: ''
                    },
                    dest: {
                        label: 'Destination',
                        id: 'destination',
                        type: 'select',
                        value: [],
                        mode: 'tags',
                        options: [],
                    },
                    location: {
                        label: 'Location',
                        id: 'location',
                        mode: 'tags',
                        type: 'select',
                        value: [],
                        options: [],
                    },
                    prod_date: {
                        label: 'Prod Date',
                        id: 'prod_date',
                        type: 'datepicker',
                        value:{
                            from: '',
                            to: '',
                        }
                    },
                    deliv_date: {
                        label: 'Deliv Date',
                        id: 'deliv_date',
                        type: 'datepicker',
                        value: {
                            from: '',
                            to: '',
                        }
                    },
                });  
            return {
                filterDef,
                selected_nav
            }
        }
    }
</script>

<style>
*{
    font-family: 'Gotham Rounded Book';
    font-weight: normal;
    font-style: normal;
    font-display: swap;    
}
.main-view .h2,
.main-view h2{
    margin: 48px 0 32px -24px;
}
.is-delivery-stores{
    font:normal 16px/1.3em "Gotham Rounded Book"!important;
}
.delivery_store a{
    font:normal 16px/1.3em "Gotham Rounded Book"!important;
}
.invoice-table-th{
    font-size: 14px;
    color: #868686;
}
.assembly-home-nav-item{
    border-radius: 10px;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
    background: #EEEEEE;
    cursor: pointer;
    font-family: 'Gotham Rounded Book';
    font-weight: 400;
}
.assembly-home-nav-item.active{
    color: #42A71E;
    font-weight: bold;
}
.invoice-location{
    display: flex;
    flex-direction: row;
    justify-content: space-evenly;
    align-items: center;
    padding: 5px 16px 5px 8px;
    font-family: 'Gotham Rounded Book';
    font-style: normal;
    font-weight: normal;
    font-size: 12px;
    line-height: 14px;
    background: rgba(241, 210, 164, 0.7);;
    color: #000000;    
    max-width: 113px;
}
.visible-hidden .form-check{ 
    visibility : hidden;
}
.selected-row .visible-hidden .form-check,
tr:hover .visible-hidden .form-check{ 
    visibility : visible;
}
@media (min-width: 1600px) {
  .total_row {
    padding-left: 1rem !important;
  }
}
</style>
