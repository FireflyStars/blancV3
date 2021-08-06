<template>
    <transition
            enter-active-class="animate__animated animate__fadeIn"
    >
        <div class="container-fluid h-100 bg-color" v-if="showcontainer">
            <main-header></main-header>
            <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax">
            <side-bar></side-bar>
                <div class="col main-view p-0">
                    <h2>Order List</h2>

                    <div class="container-fluid orderlist-tabs d-flex align-items-center">
                        <div class="orderlist-tab" :class="{active:tabs.due_today.active}" @click="showtab('due_today')">{{tabs.due_today.name}}</div>
                        <div class="orderlist-tab" :class="{active:tabs.due_tomorrow.active}" @click="showtab('due_tomorrow')">{{tabs.due_tomorrow.name}}</div>
                        <div class="orderlist-tab" :class="{active:tabs.all_orders.active}" @click="showtab('all_orders')">{{tabs.all_orders.name}}</div>
                        <div class="orderlist-tab" :class="{active:tabs.customer_care.active}" @click="showtab('customer_care')">{{tabs.customer_care.name}}</div>
                        <div class="orderlist-tab" :class="{active:tabs.with_partner.active}" @click="showtab('with_partner')">{{tabs.with_partner.name}}</div>
                    </div>
                    <order-list-table :tabledef="allordertablefields" tab="all_orders" v-if="tabs.due_today.active"></order-list-table>
                    <order-list-table :tabledef="allordertablefields" tab="all_orders" v-if="tabs.due_tomorrow.active"></order-list-table>
                    <order-list-table :tabledef="allordertablefields" tab="all_orders" v-if="tabs.all_orders.active"></order-list-table>
                    <order-list-table :tabledef="allordertablefields" tab="all_orders" v-if="tabs.customer_care.active"></order-list-table>
                    <order-list-table :tabledef="allordertablefields" tab="all_orders" v-if="tabs.with_partner.active"></order-list-table>
                    <transition
                            enter-active-class="animate__animated animate__fadeIn"
                            leave-active-class="animate__animated animate__fadeOut"
                    >
                    <div v-if="showlayer" class="back-layer"></div>
                    </transition>
                    <transition
                            enter-active-class="animate__animated animate__fadeIn"
                            leave-active-class="animate__animated animate__fadeOut"
                    >
                    <router-view />
                    </transition>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import {ref,onMounted,computed,nextTick} from 'vue';
    import MainHeader from '../layout/MainHeader';

    import SideBar from '../layout/SideBar'
    import OrderListTable from './OrderListTable';
    import {useStore} from 'vuex';
    import {ORDERLIST_LOAD_LIST,ORDERLIST_MODULE,ORDERLIST_GET_CURRENT_SELECTED,ORDERLIST_SET_CURRENTTAB,ORDERLIST_GET_LIST,ORDERLIST_LOADERMSG} from '../../store/types/types';
    import {useRoute} from 'vue-router';

    export default {
        name: "OrderList",
        components: { SideBar, MainHeader,OrderListTable},
        setup(props,context){
            const showcontainer=ref(false);
            const store=useStore();
            const route=useRoute();
            const tabs=ref({
                due_today:{active:true,name:'Due today'},
                due_tomorrow:{active:false,name:'Due tomorrow'},
                all_orders:{active:false,name:'All orders'},
                customer_care:{active:false,name:'Customer Care'},
                with_partner:{active:false,name:'With partner'},
            });

            store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_SET_CURRENTTAB}`,'due_today');

            const allordertablefields=ref({
                line_select:{
                    name:" ",
                    flex:3,
                    sortable:false,
                    identifier:"id",
                    type:'checkbox',
                },
                id:{
                   name:"Order N°",
                   flex:"10",
                   sortable:false
               },
                express:{
                    name:" ",
                    flex:"5",
                    sortable:false,
                    type:'express'
                },
                Name:{
                    name:"Name",
                    flex:"30",
                    sortable:true
                },
                TypeDelivery:{
                    name:"Destination",
                    flex:"10",
                    sortable:true
                },
                PromisedDate:{
                    name:"Promised Date",
                    flex:"10",
                    sortable:true,
                    css:"font-weight:bold;text-align:center"
                },
                numitems:{
                    name:"Items",
                    flex:"10",
                    sortable:true,
                    css:"text-align:center",
                    header_align:"center"
                },
                Status:{
                   name:"Order Status",
                   flex:"15",
                   sortable:true,
                    type:'tag',
                    header_align:"center",
                    css:"text-align:center",
                },
                paid:{
                    name:"Payment",
                    flex:"15",
                    sortable:true,
                    type:'tag',
                    header_align:"center"
                },
                Total:{
                    name:"Total",
                    flex:"10",
                    sortable:true,
                    type:'price',
                    css:"font-weight:bold;text-align:right;"
                }
            });
            onMounted(()=>{
                nextTick(()=>{
                    console.log('mounted');
                    showcontainer.value=true;
                });

            });

         //  store.dispatch(`${ORDERLIST_MODULE}increment`,{id:'xxx'}).then();
            store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOADERMSG}`,'Loading order list. Please wait...');
            store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOAD_LIST}`);

            function showtab(tab) {
                for (const prop in tabs.value)
                    tabs.value[prop].active=false;

                tabs.value[tab].active=true;
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_SET_CURRENTTAB}`,tab);
                if(store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_LIST}`].length==0) {
                    store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOADERMSG}`, `Loading ${tabs.value[tab].name.toLowerCase()}...`);
                    store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOAD_LIST}`);
                }
                console.log(tabs.value);
            }
            
            return {
                showtab,
                tabs,
                showcontainer,
                allordertablefields,
                showlayer:computed(()=>{return (route.params.order_id>0&&store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_CURRENT_SELECTED}`]);})
            }
        }
    }
</script>

<style scoped>

    .hmax{
        height: calc(100% - var(--mainlogoheight));
        padding-top:var(--mainlogoheight) ;
    }

    .auth-logo{
        height: var(--authlogoheight);
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