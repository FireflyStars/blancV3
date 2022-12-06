<template>
    <router-view v-slot="{ Component }">
        <transition enter-active-class="animate__animated animate__fadeIn">
            <div class="container-fluid h-100 bg-color" v-if="showcontainer">
                <main-header></main-header>
                <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" style="z-index:100" >
                    <side-bar></side-bar>
                    <div class="col main-view p-0">
                        <h2 class="subtitle">Order List</h2>
                        <div class="container-fluid orderlist-tabs d-flex align-items-center">
                            <template v-for="(tab,tab_index) in tabs" :key="tab_index">
                                <div class="orderlist-tab body_bold" :class="{active:tab.active}" @click="showtab(tab_index)">{{tab.name}}</div>
                            </template>
                        </div>
                        <template v-for="(tab,tab_index) in tabs" :key="tab_index">
                            <order-list-table :tabledef="allordertablefields" :tab="tab" :id="tab_index" :customer_id ="customerID" :search_value ="searchValue" v-if="tab.active && tab.name != 'Customer Care' && tab.name =='All orders'"></order-list-table>
                            <order-list-table :tabledef="allordertablefieldsWithoutType" :tab="tab" :id="tab_index" :customer_id ="customerID" v-if="tab.active && tab.name != 'Customer Care' && tab.name !='All orders'"></order-list-table>
                            <order-list-table :tabledef="customercaretablefields" :tab="tab" :id="tab_index" :customer_id ="customerID" v-if="tab.active && tab.name == 'Customer Care'"></order-list-table>
                        </template>
                        <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
                            <div v-if="showlayer" class="back-layer" @click="hideOrderDetail"></div>
                        </transition>
                        <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
                            <component :is="Component" />
                        </transition>
                    </div>
                </div>
            </div>
        </transition>
    </router-view>
</template>

<script>
    import {ref,onMounted,computed,nextTick,watch} from 'vue';
    import MainHeader from '../layout/MainHeader';
    import {hasRoles} from '../helpers/helpers'
    import SideBar from '../layout/SideBar'
    import OrderListTable from './OrderListTable';
    import {useStore} from 'vuex';
    import {useRouter} from 'vue-router';
    import {
        ORDERLIST_LOAD_LIST,
        ORDERLIST_MODULE,
        ORDERLIST_GET_CURRENT_SELECTED,
        ORDERLIST_SET_CURRENTTAB,
        ORDERLIST_GET_LIST,
        ORDERLIST_LOADERMSG,
        ORDERLIST_RESET_ORDERLIST, ORDERLIST_SET_LIMIT, ORDERLIST_LOAD_TAB,
        ORDERLIST_CUSTOMER_ORDERS,
        LOADER_MODULE,
        HIDE_LOADER,
        ORDERLIST_FILTER,
        DISPLAY_LOADER,
        ORDER_SET_STATUS
    } from '../../store/types/types';
    import {useRoute} from 'vue-router';

    export default {
        name: "OrderList",
        components: { SideBar, MainHeader,OrderListTable},
        setup(props,context){

            const showcontainer=ref(false);
            const store=useStore();
            const route=useRoute();
            const filterDef = ref({});
            const customerID = ref('');
            const searchValue = ref('');
            const router = useRouter();
            const tabs=ref({});
            if(hasRoles(['cc'])){
                tabs.value= {
                    customer_care: {active: true, name: 'Customer Care'},
                    unfulfilled: {active: false, name: 'Unfulfilled'},
                    due_tomorrow: {active: false, name: 'Due tomorrow'},
                    without_delivery_date: {active: false, name: 'Without delivery date'},
                    with_partner: {active: false, name: 'With partner'},
                };
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_SET_CURRENTTAB}`,'customer_care');
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOADERMSG}`,'Loading customer care. Please wait...');
            }else  if(hasRoles(['user'])){
                tabs.value= {
                    all_orders: {active: true, name: 'All orders'},
                    due_today: {active: false, name: 'Due today'},
                    due_tomorrow: {active: false, name: 'Due tomorrow'},
                    customer_care: {active: false, name: 'Customer Care'},
                    with_partner: {active: false, name: 'With partner'},
                };
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_SET_CURRENTTAB}`,'all_orders');
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOADERMSG}`,'Loading order list. Please wait...');
            }else {
                tabs.value= {
                    all_orders: {active: true, name: 'All orders'},
                    due_today: {active: false, name: 'Due today'},
                    // unfulfilled: {active: false, name: 'Unfulfilled'},
                    due_tomorrow: {active: false, name: 'Due tomorrow'},
                    customer_care: {active: false, name: 'Customer Care'},
                    // without_delivery_date: {active: false, name: 'Without delivery date'},
                    with_partner: {active: false, name: 'With partner'},
                };
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_SET_CURRENTTAB}`,'all_orders');
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOADERMSG}`,'Loading order list. Please wait...');
            }
            const allordertablefields=ref({
                line_select:{
                    name:" ",
                    width:"3%",
                    sortable:false,
                    identifier:"id",
                    type:'checkbox',
                },
                id:{
                   name:"Order N°",
                    width:"5%",
                   sortable:false
               },
                express:{
                    name:" ",
                    width:"2%",
                    sortable:false,
                    type:'express'
                },
                type:{
                    name:"Type",
                    width:"7%",
                    sortable:false
                },
                AccountName:{
                    name:"Account Name",
                    width:"18%",
                    sortable:false
                },
                Name:{
                    name:"Customer",
                    width:"18%",
                    sortable:false
                },
                TypeDelivery:{
                    name:"Destination",
                    width:"10%",
                    css:"text-align:left",
                    sortable:false,
                    header_align:"left"
                },
                // PromisedDate:{
                //     name:"Promised Date",
                //     width:"13%",
                //     sortable:true,
                //     css:"font-weight:bold;text-align:left",
                //     header_align:""
                // },
                // numitems:{
                //     name:"Items",
                //     width:"7%",
                //     sortable:true,
                //     css:"text-align:left",
                //     header_align:""
                // },
                Status:{
                    name:"Order Status",
                    width:"14%",
                    sortable:false,
                    type:'tag',
                    header_align:"left",
                    css:"text-align:left",
                },
                subOrderCount:{
                    name:"Sub Order Ready",
                    width:"8%",
                    sortable:false,
                    type:'percent',
                    header_align:"left",
                    css:"text-align:left",
                },
                // paid:{
                //     name:"Payment",
                //     width:"13%",
                //     sortable:true,
                //     type:'tag',
                //     header_align:"left"
                // },
                Total:{
                    name:"Total",
                    width:"10%",
                    sortable:false,
                    type:'price',
                    css:"font-weight:bold;text-align:left;"
                },
                Det:{
                    name:"DETAILED",
                    width:"6%",
                    sortable:false,
                    css:"font-weight:bold;text-align:left;",
                    header_align:"left"
                },
                Prod:{
                    name:"PRODUCTION",
                    width:"6%",
                    sortable:false,
                    css:"font-weight:bold;text-align:left;",
                    header_align:"left"
                },
                Deliv:{
                    name:"DELIVERY",
                    width:"6%",
                    sortable:false,
                    css:"font-weight:bold;text-align:left",
                    header_align:"left"
                }
            });
            const allordertablefieldsWithoutType=ref({
                line_select:{
                    name:" ",
                    width:"3%",
                    sortable:false,
                    identifier:"id",
                    type:'checkbox',
                },
                id:{
                   name:"Order N°",
                    width:"5%",
                   sortable:false
               },
                express:{
                    name:" ",
                    width:"2%",
                    sortable:false,
                    type:'express'
                },
                Name:{
                    name:"Customer",
                    width:"28%",
                    sortable:false
                },
                TypeDelivery:{
                    name:"Destination",
                    width:"14%",
                    css:"text-align:left",
                    sortable:false,
                    header_align:"left"
                },
                // PromisedDate:{
                //     name:"Promised Date",
                //     width:"13%",
                //     sortable:true,
                //     css:"font-weight:bold;text-align:left",
                //     header_align:""
                // },
                // numitems:{
                //     name:"Items",
                //     width:"7%",
                //     sortable:true,
                //     css:"text-align:left",
                //     header_align:""
                // },
                Status:{
                    name:"Order Status",
                    width:"14%",
                    sortable:false,
                    type:'tag',
                    header_align:"left",
                    css:"text-align:left",
                },
                subOrderCount:{
                    name:"Sub Order Ready",
                    width:"12%",
                    sortable:false,
                    type:'percent',
                    header_align:"left",
                    css:"text-align:left",
                },
                // paid:{
                //     name:"Payment",
                //     width:"13%",
                //     sortable:true,
                //     type:'tag',
                //     header_align:"left"
                // },
                Total:{
                    name:"Total",
                    width:"10%",
                    sortable:false,
                    type:'price',
                    css:"font-weight:bold;text-align:left;"
                },
                Det:{
                    name:"DETAILED",
                    width:"6%",
                    sortable:false,
                    css:"font-weight:bold;text-align:left;",
                    header_align:"left"
                },
                Prod:{
                    name:"PRODUCTION",
                    width:"6%",
                    sortable:false,
                    css:"font-weight:bold;text-align:left;",
                    header_align:"left"
                },
                Deliv:{
                    name:"DELIVERY",
                    width:"6%",
                    sortable:false,
                    css:"font-weight:bold;text-align:left",
                    header_align:"left"
                }
            });
            const customercaretablefields=ref({
                line_select:{
                    name:" ",
                    width:"3%",
                    sortable:false,
                    identifier:"id",
                    type:'checkbox',
                },
                id:{
                    name:"Order N°",
                    width:"5%",
                    sortable:false
               },
               express:{
                    name:" ",
                    width:"2%",
                    sortable:false,
                    type:'express'
                },
               type:{
                    name:"Type",
                    width:"7%",
                    sortable:false
                },
                Customer:{
                    name:"Customer",
                    width:"26%",
                    sortable:false
                },
                TypeDelivery:{
                    name:"Destination",
                    width:"12%",
                    css:"text-align:left",
                    sortable:false,
                    header_align:""
                },
                // PromisedDate:{
                //     name:"Promised Date",
                //     width:"13%",
                //     sortable:true,
                //     css:"font-weight:bold;text-align:center",
                //     header_align:"center"
                // },
                // numitems:{
                //     name:"Items",
                //     width:"7%",
                //     sortable:true,
                //     css:"text-align:center",
                //     header_align:"center"
                // },
                Status:{
                    name:"Order Status",
                    width:"14%",
                    sortable:false,
                    type:'tag',
                    header_align: "left",
                    css:"text-align: left",
                },
                // paid:{
                //     name:"Payment",
                //     width:"13%",
                //     sortable:true,
                //     type:'tag',
                //     header_align:"center"
                // },
                Action:{
                    name:"Action Needed",
                    width:"12%",
                    sortable:false,
                    type:'tag',
                    header_align:"left"
                },
                Total:{
                    name:"Total",
                    width:"8%",
                    sortable:false,
                    type:'price',
                    css:"font-weight:bold;text-align:left;"
                },
                Det:{
                    name:"DETAILED",
                    width:"6%",
                    sortable:false,
                    css:"font-weight:bold;text-align:left;",
                    header_align:"left"
                },
                Prod:{
                    name:"PRODUCTION",
                    width:"6%",
                    sortable:false,
                    css:"font-weight:bold;text-align:left;",
                    header_align:"left"
                },
                Deliv:{
                    name:"DELIVERY",
                    width:"6%",
                    sortable:false,
                    css:"font-weight:bold;text-align:left",
                    header_align:"left"
                }
            });
            onMounted(()=>{
                nextTick(()=>{
                    showcontainer.value=true;
                    let data = route.params.customerId?route.params.customerId:window.sessionStorage.getItem('orders_customer')
                     if(route.params.name == "Customer name"){
                        filterDef.value={
                                        'Customername':{
                                            name:"Customer name",
                                            value: route.params.value
                                        },
                        };
                       store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, ' please wait...']);
                       store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_FILTER}`,_.cloneDeep(filterDef.value)).finally(()=>{
                       store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                     });
                    }

                    if( data != null &&  data != undefined ){
                    store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_CUSTOMER_ORDERS}`, {customer:data} );

                    } else {
                        if(window.sessionStorage.getItem('search_value') != null){
                          searchValue.value = window.sessionStorage.getItem('search_value')
                          store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOAD_LIST}`,{search:window.sessionStorage.getItem('search_value')});
                        }else{
                          store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOAD_LIST}`,{search:route.params.value});
                          searchValue.value = route.params.value
                        }   
                    }
                });
                store.dispatch(`${ORDERLIST_MODULE}${ORDER_SET_STATUS}`);
            });


            watch(route, (to) => {

                    customerID.value =  route.params.customerId ? route.params.customerId : window.sessionStorage.getItem('orders_customer')

                if((customerID.value != null && customerID.value != "null" && customerID.value != 'undefined' && customerID.value != undefined)){
                    store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_CUSTOMER_ORDERS}`, {customer:customerID.value} );
                }else{
                       if(window.sessionStorage.getItem('search_value') != null){
                        store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOAD_LIST}`,{search:window.sessionStorage.getItem('search_value')});
                        searchValue.value = window.sessionStorage.getItem('search_value')
                       }else{
                        store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOAD_LIST}`,{search:route.params.value});
                        searchValue.value = route.params.value
                       }       
                }
             })


            function showtab(tab) {   
                for (const prop in tabs.value)
                    tabs.value[prop].active=false;

                tabs.value[tab].active=true;
                
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOAD_TAB}`,{tab:tab,name:tabs.value[tab].name, customer:route.params.customerId ? route.params.customerId : window.sessionStorage.getItem('orders_customer') , search: route.params.value ? route.params.value :window.sessionStorage.getItem('search_value')});
            }

            function hideOrderDetail(event){
                router.push({
                    name:'LandingPage',
                    params: {
                        customerId:customerID.value,
                        value:searchValue.value,
                    },

                });
            }

            return {
                showtab,
                tabs,
                showcontainer,
                allordertablefields,
                customercaretablefields,
                allordertablefieldsWithoutType,
                customerID,
                searchValue,
                showlayer:computed(()=>{return (route.params.order_id>0&&store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_CURRENT_SELECTED}`]);}),
                hideOrderDetail,
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
    .orderlist-tab{
        font-family : 'Gotham Rounded Book'
    }
</style>
