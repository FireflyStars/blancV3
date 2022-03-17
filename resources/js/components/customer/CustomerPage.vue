<template>
    <transition enter-active-class="animate__animated animate__fadeIn animate__fadeOut">
        <div class="container-fluid bg-color">
            <main-header></main-header>
            <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax" style="z-index: 100">
                <side-bar></side-bar>
                <div class="col main-view mx-5" id="Customer List">
                    <h2 class="mx-0 font-22">Customer List</h2>
                    <div class="nav-panel d-flex justify-content-between mb-1">
                        <ul class="tab-nav list-inline mb-0">
                            <li class="tab-nav-item font-16 list-inline-item px-3 py-2" :class="selected_nav == 'CustomerList' ? 'active' : ''" @click="setNav('CustomerList')">All Customers</li>
                            <li class="tab-nav-item font-16 list-inline-item px-3 py-2" :class="selected_nav == 'B2B' ? 'active' : ''" @click="setNav('B2B')">B2B</li>
                            <li class="tab-nav-item font-16 list-inline-item px-3 py-2" :class="selected_nav == 'B2C' ? 'active' : ''" @click="setNav('B2C')">B2C</li>
                            <li class="tab-nav-item font-16 list-inline-item px-3 py-2" :class="selected_nav == 'CurrentBookings' ? 'active' : ''" @click="setNav('CurrentBookings')">CurrentBookings</li>
                            <li class="tab-nav-item font-16 list-inline-item px-3 py-2" :class="selected_nav == 'RecurringBookings' ? 'active' : ''" @click="setNav('RecurringBookings')">RecurringBookings</li>
                        </ul>
                        <div class="filter-section position-relative">
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
import { ref } from 'vue';
import SideBar from "../layout/SideBar";
import MainHeader from "../layout/MainHeader";
import Filters from '../test/Filter';
import CustomerList from './CustomerList';
export default {
    name: 'CustomerPage',
    components:{
        Filters,
        SideBar,
        MainHeader,
        CustomerList
    },
    setup(){
        const selected_nav = ref('CustomerList');
        const setNav = (nav)=>{
            selected_nav.value = nav;
        }
        const filterDef = ref({
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
        })
        return{
            selected_nav,
            filterDef,
            setNav
        }
    }
}
</script>
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
    margin: 48px 0 32px -24px;
}
</style>