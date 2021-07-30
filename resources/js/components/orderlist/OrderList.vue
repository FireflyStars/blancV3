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
                        <div class="orderlist-tab active">Due today</div>
                        <div class="orderlist-tab">Due tomorrow</div>
                        <div class="orderlist-tab">all orders</div>
                        <div class="orderlist-tab">Customer care</div>
                        <div class="orderlist-tab">With partner</div>
                    </div>
                    <order-list-table :tabledef="allordertablefields" tab="allorders"></order-list-table>
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
    import {ALL_ORDER_LOAD_LIST,ALL_ORDER_GET_LIST,ORDERLIST_MODULE,ALL_ORDER_GET_CURRENT_SELECTED} from '../../store/types/types';
    import {useRoute} from 'vue-router';

    export default {
        name: "OrderList",
        components: {SideBar, MainHeader,OrderListTable},
        setup(props,context){
            const showcontainer=ref(false);
            const store=useStore();
            const route=useRoute();
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
            store.dispatch(`${ORDERLIST_MODULE}${ALL_ORDER_LOAD_LIST}`);

            return {
                showcontainer,
                allordertablefields,
                showlayer:computed(()=>{return (store.getters[`${ORDERLIST_MODULE}${ALL_ORDER_GET_CURRENT_SELECTED}`])&&route.params.order_id>0;})
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