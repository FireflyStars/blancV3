<template>
    <router-view />
    <transition enter-active-class="animate__animated animate__fadeIn animate__fadeOut">
        <div class="container-fluid bg-color">
            <main-header></main-header>
            <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax" style="z-index: 100">
                <side-bar></side-bar>
                <div class="col main-view p-5" id="assembly-home">
                    <h2 class="mx-0 font-22">Production Track</h2>
                    <div class="nav-panel d-flex justify-content-between mb-1">
                        <ul class="tab-nav list-inline mb-0">
                            <li class="tab-nav-item list-inline-item font-16 px-3 py-2" :class="selected_nav == 'AssemblyHome' ? 'active' : ''" @click="setNav('AssemblyHome')">Stations</li>
                            <li class="tab-nav-item list-inline-item font-16 px-3 py-2" :class="selected_nav == 'InvoiceList' ? 'active' : ''" @click="setNav('InvoiceList')">All items</li>
                            <li class="tab-nav-item list-inline-item font-16 px-3 py-2" :class="selected_nav == 'DueTodayInvoiceList' ? 'active' : ''" @click="setNav('DueTodayInvoiceList')">Due today</li>
                            <li class="tab-nav-item list-inline-item font-16 px-3 py-2" :class="selected_nav == 'DueTomorrowInvoiceList' ? 'active' : ''" @click="setNav('DueTomorrowInvoiceList')">Due tomorrow</li>
                            <li class="tab-nav-item list-inline-item font-16 px-3 py-2" :class="selected_nav == 'OverDueInvoiceList' ? 'active' : ''" @click="setNav('OverDueInvoiceList')">Overdue</li>
                            <li class="tab-nav-item list-inline-item font-16 px-3 py-2" :class="selected_nav == 'Pending' ? 'active' : ''" @click="setNav('Pending')">Pending</li>
                        </ul>
                        <div class="filter-section position-relative d-flex align-items-center" v-if="selected_nav == 'InvoiceList'">
                            <a class="export-csv" v-if="userRole == 1" @click="exportCSV">Export CSV</a>
                            <InvoiceFilter :filterDef="filterDef"></InvoiceFilter>
                        </div>
                        <div class="filter-section position-relative d-flex align-items-center" v-if="selected_nav == 'OverDueInvoiceList'">
                            <a class="export-csv" v-if="userRole == 1" @click="exportOverDueCSV">Export CSV</a>
                        </div>
                    </div>
                    <!-- <KeepAlive> -->
                        <component :is="selected_nav"></component>
                    <!-- </KeepAlive> -->
                </div>
            </div>
            <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
                <div v-if="showlayer" class="back-layer"></div>
            </transition>
        </div>
    </transition>
</template>

<script>
    import {
        ASSEMBLY_HOME_MODULE,
        INVOICE_MODULE,
        SET_SELECTED_NAV,
        GET_SELECTED_NAV,
        INVOICELIST_GET_CURRENT_SELECTED,
        SET_DUE_TODAY_FLAG,
        SET_DUE_TOMORROW_FLAG,
        SET_OVERDUE_FLAG,
        SET_ALL_ITEMS_FLAG,
        GET_INVOICE_LIST,
        GET_USER_ROLE
    } from "../../store/types/types";
    import SideBar from "../layout/SideBar";
    import MainHeader from "../layout/MainHeader";
    import InvoiceFilter from '../miscellaneous/InvoiceFilter';
    import InvoiceList from './InvoiceList';
    import AssemblyHome from './AssemblyHome';
    import DueTodayInvoiceList from './DueTodayInvoiceList';
    import DueTomorrowInvoiceList from './DueTomorrowInvoiceList';
    import OverDueInvoiceList from './OverDueInvoiceList';
    import { ref, computed } from "vue";
    import { useStore } from "vuex";
    import { useRoute } from "vue-router";
    import exportFromJSON from "export-from-json";
    export default {
        name: "Assembly",
        components:{
            SideBar,
            MainHeader,
            InvoiceFilter,
            InvoiceList,
            AssemblyHome,
            OverDueInvoiceList,
            DueTodayInvoiceList,
            DueTomorrowInvoiceList
        },
        setup(){
            const store = useStore();
            const route = useRoute();
            const filterDef =  ref({
                    status: {
                        label: 'Sub Order Status',
                        id: 'sub_order_status',
                        type: 'select',
                        value: [],
                        options: [],
                    },
                    dest: {
                        label: 'Destination',
                        id: 'destination',
                        type: 'select',
                        value: [],
                        options: [],
                    },
                    location: {
                        label: 'Location',
                        id: 'location',
                        type: 'select',
                        value: [],
                        options: [],
                    },
                    prod_date: {
                        label: 'Production Date',
                        id: 'prod_date',
                        type: 'datepicker',
                        value:{
                            start: '',
                            end: '',
                        }
                    },
                    deliv_date: {
                        label: 'Delivery Date',
                        id: 'deliv_date',
                        type: 'datepicker',
                        value: {
                            start: '',
                            end: '',
                        }
                    },
                });
                const exportCSV = ()=>{
                    const fileName = 'item-Date-hours.csv';
                    const exportType = exportFromJSON.types.csv;
                    const data = store.getters[`${INVOICE_MODULE}${GET_INVOICE_LIST}`]
                    if (data) exportFromJSON({ data, fileName, exportType });
                }
                const exportOverDueCSV = ()=>{
                    const fileName = 'item-overdue.csv';
                    const exportType = exportFromJSON.types.csv;
                    const data = store.getters[`${INVOICE_MODULE}${GET_INVOICE_LIST}`]
                    if (data) exportFromJSON({ data, fileName, exportType });
                }

            return {
                filterDef,
                userRole: computed(()=>store.getters[`${ASSEMBLY_HOME_MODULE}${GET_USER_ROLE}`]),
                selected_nav: computed(()=>store.getters[`${ASSEMBLY_HOME_MODULE}${GET_SELECTED_NAV}`]),
                showlayer: computed( ()=> {
                        if(store.getters[`${ASSEMBLY_HOME_MODULE}${GET_SELECTED_NAV}`] == 'AssemblyHome')
                            return (route.params.item_id>0 && store.getters[`${ASSEMBLY_HOME_MODULE}${INVOICELIST_GET_CURRENT_SELECTED}`]);
                        else if (store.getters[`${ASSEMBLY_HOME_MODULE}${GET_SELECTED_NAV}`] == 'InvoiceList')
                            return (route.params.item_id>0&&store.getters[`${INVOICE_MODULE}${INVOICELIST_GET_CURRENT_SELECTED}`]);
                        else if (store.getters[`${ASSEMBLY_HOME_MODULE}${GET_SELECTED_NAV}`] == 'DueTodayInvoiceList')
                            return (route.params.item_id>0&&store.getters[`${INVOICE_MODULE}${INVOICELIST_GET_CURRENT_SELECTED}`]);
                        else if (store.getters[`${ASSEMBLY_HOME_MODULE}${GET_SELECTED_NAV}`] == 'DueTomorrowInvoiceList')
                            return (route.params.item_id>0&&store.getters[`${INVOICE_MODULE}${INVOICELIST_GET_CURRENT_SELECTED}`]);
                        else if (store.getters[`${ASSEMBLY_HOME_MODULE}${GET_SELECTED_NAV}`] == 'OverDueInvoiceList')
                            return (route.params.item_id>0&&store.getters[`${INVOICE_MODULE}${INVOICELIST_GET_CURRENT_SELECTED}`]);
                        else return false;
                }),
                setNav:( nav_val )=>{
                    store.dispatch(`${ASSEMBLY_HOME_MODULE}${SET_SELECTED_NAV}`, nav_val)
                    if(nav_val == 'DueTodayInvoiceList'){
                        store.dispatch(`${INVOICE_MODULE}${SET_DUE_TODAY_FLAG}`)
                    }else if(nav_val == 'DueTomorrowInvoiceList'){
                        store.dispatch(`${INVOICE_MODULE}${SET_DUE_TOMORROW_FLAG}`)
                    }else if(nav_val == 'OverDueInvoiceList'){
                        store.dispatch(`${INVOICE_MODULE}${SET_OVERDUE_FLAG}`)
                    }else{
                        store.dispatch(`${INVOICE_MODULE}${SET_ALL_ITEMS_FLAG}`)
                    }
                },
                exportCSV,
                exportOverDueCSV
            }
        }
    }
</script>

<style scoped>
.main-view{
  margin: 0 1.375rem;
}
</style>
<style>
.hmax{
    height: calc(100% - var(--mainlogoheight));
    padding-top:var(--mainlogoheight) ;
}

.auth-logo{
    height: var(--authlogoheight);
}
*{
    font-family: 'Gotham Rounded Book';
    font-weight: normal;
    font-style: normal;
    font-display: swap;
}
.main-view .h2,
.main-view h2{
    margin: 3rem 0 2rem -1.5rem;
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
.invoice-location{
    display: flex;
    flex-direction: row;
    justify-content: space-evenly;
    align-items: center;
    padding: 0 16px 0 8px;
    width: max-content;
    min-width: 135px;
}
.invoice-location span{
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: normal;
    font-size: 12px;
    line-height: 14px;
    color: #000000;
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
.back-layer{
    background:rgba(224, 224, 224,0.6);
    position: fixed;
    top: 0;
    left:0;
    height: 100%;
    width: 100%;
    z-index: 9999;
}
.export-csv{
    display: block;
    color: #F4003D !important;
    font-weight: bold;
    cursor: pointer;
    margin-right: 20px;
}
</style>
