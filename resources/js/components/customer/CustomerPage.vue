<template>
    <transition enter-active-class="animate__animated animate__fadeIn animate__fadeOut">
        <div class="container-fluid bg-color">
            <main-header></main-header>
            <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax" style="z-index: 100">
                <side-bar></side-bar>
                <div class="col main-view" id="Customer-List" :class="{'mx-5':selected_nav!='AR'}">
                    <h2 class="mx-0 font-22">Customer List</h2>
                    <div class="nav-panel d-flex justify-content-between mb-1">
                        <ul class="tab-nav list-inline mb-0">
                            <li class="tab-nav-item font-16 list-inline-item px-3 py-2" v-if="current_user && current_user.role_id==1" :class="selected_nav == 'AR' ? 'active' : ''" @click="setNav('AR')">A/R</li>
                            <li class="tab-nav-item font-16 list-inline-item px-3 py-2" v-if="current_user && current_user.role_id==1" :class="selected_nav == 'ARInvoiced' ? 'active' : ''" @click="setNav('ARInvoiced')">A/R invoiced</li>
                            <li class="tab-nav-item font-16 list-inline-item px-3 py-2" :class="selected_nav == 'CustomerList' ? 'active' : ''" @click="setNav('CustomerList')">All Customers</li>
                            <li class="tab-nav-item font-16 list-inline-item px-3 py-2" :class="selected_nav == 'B2B' ? 'active' : ''" @click="setNav('B2B')">B2B</li>
                            <li class="tab-nav-item font-16 list-inline-item px-3 py-2" :class="selected_nav == 'B2C' ? 'active' : ''" @click="setNav('B2C')">B2C</li>
                            <li class="tab-nav-item font-16 list-inline-item px-3 py-2" :class="selected_nav == 'CurrentBookings' ? 'active' : ''" @click="setNav('CurrentBookings')">CurrentBookings</li>
                            <li class="tab-nav-item font-16 list-inline-item px-3 py-2" :class="selected_nav == 'RecurringBookings' ? 'active' : ''" @click="setNav('RecurringBookings')">RecurringBookings</li>
                        </ul>
                        <div class="filter-section position-relative">
                            <CustomerFilter :filterDef="filterDef" :nav="selected_nav"></CustomerFilter>
                        </div>
                    </div>
                    <component :is="component"></component>
                </div>
            </div>
        </div>
    </transition>
</template>
<script>
import { onMounted, ref, computed } from 'vue';
import { useStore } from 'vuex';
import SideBar from "../layout/SideBar";
import MainHeader from "../layout/MainHeader";
import CustomerFilter from '../miscellaneous/CustomerFilter';
import CustomerList from './CustomerList';
import ArList from './ArList';
import ArInvoiced from './ArInvoiced.vue';

import { CUSTOMER_MODULE, SET_CUSTOMER_SELECTED_TAB, SET_CUSTOMER_LIST, GET_CUSTOMER_SELECTED_TAB } from '../../store/types/types';
export default {
    name: 'CustomerPage',
    components:{
        CustomerFilter,
        SideBar,
        MainHeader,
        CustomerList,
        ArList,
        ArInvoiced,
    },
    setup(){
        const store = useStore();
        const component = ref('CustomerList');
        const selected_nav =  computed(()=>{
            return store.getters[`${CUSTOMER_MODULE}${GET_CUSTOMER_SELECTED_TAB}`];
        })

        const current_user = ref(null);


        const setNav = (nav)=>{
            store.dispatch(`${CUSTOMER_MODULE}${SET_CUSTOMER_SELECTED_TAB}`, nav);
            if(nav == 'B2B' || nav == 'B2C' || nav == 'CustomerList' || nav == 'CurrentBookings' || nav == 'RecurringBookings'){
                store.dispatch(`${CUSTOMER_MODULE}${SET_CUSTOMER_LIST}`);
                component.value = 'CustomerList';
            }else if(nav=='AR'){
                component.value = 'ArList';
            }else if(nav=='ARInvoiced'){
                component.value = 'ArInvoiced';
            }else{
                component.value = nav;
            }
        }
        const filterDef = ref({
            customer_type: {
                label: 'Customer Type',
                type: 'select',
                value: '',
                options: [
                    { display:'B2B', value: 'B2B' },
                    { display:'B2C', value: 'B2C' }
                ]
            },
            customer_location: {
                label: 'Customer Location',
                type: 'select',
                value: '',
                options:[
                    { display: 'Delivery', value: 'DELIVERY'},
                    { display: 'Marylebone', value: 'MARYLEBONE'},
                    { display: 'Notting Hill', value: 'NOTTING HILL'},
                    { display: 'Chelsea', value: 'CHELSEA'},
                    { display: 'South ken', value: 'SOUTH KEN'}
                ]
            },
            invoice_preference: {
                label: 'Invoice Preference',
                type: 'select',
                value: '',
                options: [
                    { display:'No Tax Invoice', value: 0 },
                    { display:'Tax Invoice', value: 1 },
                ],
            },
            total_spent: {
                label: 'Total Spent',
                type: 'select',
                value: '',
                options:[
                    { display: '£0 - 100', value: '0,100' },
                    { display: '£100 - 500', value: '100,500' },
                    { display: '£500 - 1,000', value: '500,1000' },
                    { display: '£1,000 - 5,000', value: '1000,5000' },
                    { display: '£5,000 - 10,000', value: '5000,10000' },
                    { display: '> £10,000', value: '10001' },
                ]
            },
            last_order: {
                label: 'Last Order Date',
                type: 'datepicker',
                value: {
                    start: '',
                    end: '',
                }
            },
        })

        onMounted(()=>{
             axios.post('/get-current-user',{})
                    .then((res)=>{
                        current_user.value = res.data.user;
                    }).catch((err)=>{

                    }).finally(()=>{

                    });

        });

        return{
            component,
            selected_nav,
            filterDef,
            setNav,
            current_user,
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
