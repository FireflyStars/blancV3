<template>

<div class="position-relative ">
  <input id="icon" class="btn input-search body_medium" @keyup.prevent="submit" v-model="clear" type="texte" placeholder="Search a name, email, barcode, etc"/>
   <span v-if="showbutton" @click='clearSearch' class="position-absolute"><i class="icon-close"></i></span>
</div>

 <transition
            enter-active-class="animate__animated animate__fadeIn"
 >
 <div v-if="showSearch" class="search" @click='HideSearch($event)'>
   <section class="nodata p-2" v-if ="Customer.length == 0 && CustomerEmails.length == 0 && CustomerOrders.length == 0 && ListItems.length == 0">
            <p v-if="!loader_running">we couldn't find any customers.</p>
  </section>

     <ul  v-else class=" list-group-flush" style="background: #FFFFFF;cursor: default;" >
        <li class="list-group-item" v-if ="Customer.length > 0">
            <div class="content-wraper " style="padding-top: 31px;">
                <span class="subtitle col-6">Name</span>
                <div class="d-flex justify-content-end col-6 ">
                <a class="d-flex justify-content-end col-3 display-all" @click="displayAll('search_name')" >Display all</a>
                <a class="d-flex justify-content-end col-3 show-more"   @click="loadMore('search_name')" >Show more</a>
                </div>
             </div>
             <ul  class="list-group list-group-flush" >
                <li v-for ="customer in Customer" :key="customer">
                  <div class="container">
                    <div class="row">
                       <div class="col-3">
                          <span class="body_medium"><a class = "text_wrap" href="javascript:void(0)" @click="goCustomerView(customer.id)" >{{customer.Name}}</a></span>
                          <div class="col-2">
                            <tag v-if="customer.cust_type=='B2C'" :name="'B2C'" ></tag>
                            <tag v-else :name="'B2B'" ></tag>
                          </div>
                       </div>
                      <div class="col-3">
                            <div  v-if="customer.Phone!=''&&customer.Phone!=null" >
                              <div v-for="phone in customer.Phone.slice(0,1)" :key="phone">
                               <b class ="body_regular">+{{phone.replace('|',' ').replace(']' , '')}}</b>
                              </div>
                            </div>
                            <div v-else>

                            <div class="phone body_small">--</div>
                            </div>

                             <b class ="body_regular">{{customer.EmailAddress}}</b>
                       </div>
                       <div class="col-1" >
                           <b class ="body_regular text_wrap">{{customer.CompanyName}}</b>
                        </div>
                        <div class="col-1">
                          <print-bag-tag :CustomerID="customer.CustomerID"></print-bag-tag>
                        </div>
                       <div class="col-2" style="text-align: end;" >
                            <button class="btn btn-white body_medium text-nowrap btn-new-orders" @click="goToNewOrder(customer)"> New order</button>
                        </div>
                        <div class="col-2" style="text-align: end;">
                            <button class="btn btn-white body_medium text-nowrap btn-all-orders"  @click="goToOrderList(customer.CustomerID)"> All Orders</button>
                        </div>

                     </div>
                    </div>
               </li>

             </ul>
        </li>
        <!-- <li class="list-group-item" v-if="CustomerEmails.length > 0">
             <div class="content-wraper ">
                <span class="subtitle col-6">Email</span>
                <a class="d-flex justify-content-end col-6 show-more"  @click="loadMore('search_email')" >Show more</a>
             </div>
             <ul  class="list-group list-group-flush">
                <li v-for ="customer in CustomerEmails" :key="customer">
                  <div class="container">
                    <div class="row">
                        <div class="col">
                          <b class = "body_regular" ><a href="javascript:void(0)" @click="goToOrderList(customer.CustomerID)">{{customer.Name.replace(',','').toLowerCase()}}</a></b>
                            <div v-if="customer.Phone!=''&&customer.Phone!=null" >
                              <div v-for="phone in customer.Phone.slice(0,1)" :key="phone">
                            <b class ="body_regular">+{{phone.replace('|',' ')}}</b>
                              </div>
                          </div>
                          <div v-else  >
                              <div class="phone body_small">--</div>
                          </div>
                        </div>
                        <div class="col-6">
                         <span class="body_medium">{{customer.EmailAddress}}</span>
                        </div>
                          <div class="col-2" style="text-align: end;">
                            <tag   v-if="customer.TypeDelivery=='DELIVERY'" :name="'B2C'" ></tag>
                            <tag   v-else :name="'B2B'" ></tag>
                         </div>
                     </div>
                    </div>
               </li>
             </ul>
        </li> -->
          <li class="list-group-item" v-if="CustomerOrders.length>0">

             <div class="content-wraper">
                <span class="subtitle col-6">Order</span>

                 <div class="d-flex justify-content-end col-6 ">
                <a class="d-flex justify-content-end col-3 display-all"  @click="displayAll('search_order')" >Display all</a>
                <a class="d-flex justify-content-end col-3 show-more"    @click="loadMore('search_order')" >Show more</a>
                </div>
             </div>
             <ul   class="list-group list-group-flush" >
                <li v-for ="(order,index) in CustomerOrders" :key="order">
                  <div class="container">
                    <div class="row" @click="selectrow(order.id,order.Status,index)">
                        <div class="col-3">
                         <span class="body_medium">{{order.Name}}</span>
                        </div>
                         <div class="col-1" >
                            <span class="body_small">{{order.id}}</span>
                        </div>
                        <div class="col-2 " style = "text-align: center;">
                        <b class = "body_small">{{order.TypeDelivery}}</b>
                        </div>
                        <div class=" col-2 ">
                            <b class ="body_small">{{formatDate(order.DateDeliveryAsk, 'DD/MM/YYYY')}}</b>
                        </div>

                        <div class=" col-2">
                            <tag  :name="order.Status" ></tag>
                        </div>
                          <div class="col-2" style="text-align: end;">
                            <tag   v-if="order.cust_type=='B2C'" :name="'B2C'" ></tag>
                            <tag   v-else :name="'B2B'" ></tag>
                         </div>
                     </div>
                    </div>
               </li>
             </ul>
        </li>
         <li class="list-group-item" v-if="ListItems.length>0">

             <div class="content-wraper">
                <span class="subtitle col-6">ITEM</span>

                 <div class="d-flex justify-content-end col-6 ">
                <a class="d-flex justify-content-end col-3 display-all"  @click="displayAll('search_item')" >Display all</a>
                <a class="d-flex justify-content-end col-3 show-more"    @click="loadMore('search_items')" >Show more</a>
                </div>
             </div>
             <ul   class="list-group list-group-flush" >
                <li v-for ="item in ListItems" :key="item">
                  <div class="container" >

                    <div class="row" @click="selectItem(item.item_id)">
                         <div class="col-3">
                         <span class="body_medium">{{item.iteminfo}}</span>
                        </div>
                         <div class="col-1" >
                            <span class="body_small">{{item.ItemTrackingKey}}</span>
                        </div>
                        <div class="col-2 " style = "text-align: center;">
                        <b class = "body_small">{{item.brand}}</b>
                        </div>
                        <div class=" col-2 ">
                            <b class ="body_small">{{item.sub_order}}</b>
                        </div>

                        <div class=" col-2">
                            <tag  :name="item.nominterface" ></tag>
                        </div>
                          <div class="col-2" style="text-align: end;">
                         </div>
                     </div>


                    </div>
               </li>
             </ul>
        </li>


     </ul>

 </div>
  <wave-loader :show_loader="show_loader" msg="please wait..." v-else></wave-loader>

 </transition>
</template>
<script>
  import WaveLoader from '../WaveLoader';
  import PrintBagTag from './PrintBagTag.vue';
  import {useRouter, useRoute} from 'vue-router'
  import {ref,computed,watch, nextTick } from 'vue';
  import Tag from  './Tag'
      import {
      TOASTER_MODULE,
      TOASTER_MESSAGE,
      CUSTOMERLIST_MODULE,
      CUSTOMER_LOAD_LIST,
      CUSTOMER_GET_LIST,
      CUSTOMEREMAILS_GET_LIST,
      CUSTOMERORDERS_GET_LIST,
      ORDERLIST_MODULE,
      ORDERLIST_SELECT_CURRENT,
      LOADER_MODULE,
      DISPLAY_LOADER,
      HIDE_LOADER,
      ORDERLIST_FILTER,
      CUSTOMER_MODULE,
      SET_CUSTOMER_FILTER,
      FILTER_CUSTOMER_LIST,
      CUSTOMERITEMS_GET_LIST,
      ORDERLIST_LOAD_LIST,
      ASSEMBLY_HOME_MODULE,
      INVOICE_MODULE,
      SET_INVOICE_LIST,
      SET_SELECTED_NAV 
  } from "../../store/types/types";
  import {formatDate} from "../helpers/helpers";
  import {useStore} from 'vuex';
  import templateBuilder from '@babel/template';
export default({
     name: "SearchCustomer",
     components:{Tag, WaveLoader, PrintBagTag},
       setup(){
           const router = useRouter();
           const route = useRoute();
           const store =useStore();
           const showSearch=ref(false);
           const timeout =ref('');
           const clear = ref('');
           const show_loader= ref(false);
           const showbutton = ref(false);
           const preselection=ref({});
           const search_value =ref('');
           const filterDef = ref({});

           clear.value = window.sessionStorage.getItem('search_value');       
           if(clear.value != null){
            showbutton.value = true
           }
           const featureunavailable=((feature)=>{
               store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:feature+' feature not yet implemented.',ttl:5,type:'success'});
            });

      function clearSearch(){
         window.sessionStorage.removeItem('search_value')
         window.sessionStorage.removeItem('orders_customer')
         clear.value = null;
         showSearch.value = false;
         showbutton.value = false;
         show_loader.value= false;
         location.reload();
       }
       function HideSearch(event){
        event.target.classList.forEach((element)=>{
          if(element == "search" ){
            showSearch.value = false;
            show_loader.value= false;
          }
        })
       }
         function selectrow(id,status,colname){

               showSearch.value = false;
               show_loader.value= false;
              //  if(colname=='line_select') return;

                if(status=='PENDING'){
                     router.push({
                        name:'DetailingItemList',
                        params: {
                            order_id:id,
                        },
                    });

                }else{
                    store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_SELECT_CURRENT}`,id);
                    router.push({
                        name:'OrderDetails',
                        params: {
                            order_id:id,
                        },
                    });
                }
            }


            function submit(e) {
               showbutton.value = true;
               clearTimeout(timeout.value);
               timeout.value = setTimeout(function(){
                     show_loader.value= true;
                   nextTick(() => {
                     store.dispatch(`${CUSTOMERLIST_MODULE}${CUSTOMER_LOAD_LIST}`,{query:e.target.value}).then((response)=>{
                            if(e.target.value){
                              showSearch.value = true;
                              filterDef.value={
                                        'Customername':{
                                            name:"Customer name",
                                            value: e.target.value
                                        },
                            };
                            preselection.value = filterDef.value
                            search_value.value = e.target.value
                            window.sessionStorage.setItem('search_value', search_value.value);
                            } else {
                              showSearch.value = false;
                              show_loader.value= false;
                            }
                     }).catch((error)=>{
                        if(typeof error.response!="undefined")
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'});
                    });;
                });
               }
              , 500)
            };



           const Customer=computed(()=>{
               return store.getters[`${CUSTOMERLIST_MODULE}${CUSTOMER_GET_LIST}`];


            });

            const CustomerEmails=computed(()=>{
                return store.getters[`${CUSTOMERLIST_MODULE}${CUSTOMEREMAILS_GET_LIST}`];
            });

             const CustomerOrders=computed(()=>{

                return store.getters[`${CUSTOMERLIST_MODULE}${CUSTOMERORDERS_GET_LIST}`];
            });
            const ListItems=computed(()=>{

                return store.getters[`${CUSTOMERLIST_MODULE}${CUSTOMERITEMS_GET_LIST}`];
            });
           
          function loadMore(tab){
                 store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, ' please wait...']);
                 store.dispatch(`${CUSTOMERLIST_MODULE}${CUSTOMER_LOAD_LIST}`,{showmore:tab,query:clear.value}).finally(()=>{
                 store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
            }

          function displayAll(tab){
                showSearch.value = false;
                show_loader.value= false;
                window.sessionStorage.setItem('search_value', search_value.value);
                window.sessionStorage.removeItem('orders_customer');
              if(tab == "search_name"){
                if(route.name == "Customer"){
                  store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, ' please wait...']);
                  store.dispatch(`${CUSTOMER_MODULE}${SET_CUSTOMER_FILTER}`, _.cloneDeep(preselection.value))
                  store.dispatch(`${CUSTOMER_MODULE}${FILTER_CUSTOMER_LIST}`).finally(()=>{
                  store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    });
                } else {
                   router.push({
                    name:'Customer',
                    params: {
                           'name':"Customer name",
                           'value': search_value.value
                    },
                })
                }

              } else if (tab == "search_order") {
                    router.push({
                    name:'LandingPage',
                    params: {
                           'name':"search",
                           'value': search_value.value
                    },
                })
              //  }
             } else if (tab == 'search_item'){

                if(route.name == "Assembly"){
                  const searchvalue = {
                                            'name':"searchitem",
                                            'value': search_value.value
                                        };
                  store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, ' please wait...']);
                  store.dispatch(`${INVOICE_MODULE}${SET_INVOICE_LIST}`, searchvalue).finally(()=>{
                  store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    });
                } else {
                   router.push({
                        name: 'Assembly',
                        params: {
                           'name':"searchitem",
                           'value': search_value.value
                    },
                    });
                }   
               store.dispatch(`${ASSEMBLY_HOME_MODULE}${SET_SELECTED_NAV}`, "InvoiceList")

             }
                
          }
            
          function goToOrderList(customerId){
            showSearch.value = false;
            show_loader.value= false;
            window.sessionStorage.setItem('orders_customer', customerId);
            router.push({
                    name:'LandingPage',
                    params: {
                       customerId,
                    },
                })
                
          }

          function goToNewOrder(customer){
            window.sessionStorage.setItem('orders_customer', customer.CustomerID);
            let customerId = customer.CustomerID
            showSearch.value = false;
            show_loader.value= false;
              router.push({
                    name:'NewOrder',
                    params: {
                       customerId,
                    },
                })
          }

          function goCustomerView(customerId){
            showbutton.value = false;
            show_loader.value= false;
             router.push('/customer-detail/'+ customerId);
          }

          function selectItem(item_id){
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, ' please wait...']);
            router.push({
                name:'ItemDetails',
                params: {
                    item_id:item_id,
                },
            })
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);

            

          }
            return {
                submit,
                clearSearch,
                clear,
                Customer,
                showSearch,
                CustomerEmails,
                CustomerOrders,
                ListItems,
                formatDate,
                featureunavailable,
                showbutton,
                selectrow,
                show_loader,
                loadMore,
                goToOrderList,
                goCustomerView,
                displayAll,
                filterDef,
                selectItem,
                HideSearch,
                goToNewOrder

            }
        }

})
</script>



<style scoped>

#icon{
    background-image:url("/images/search.svg");
    background-repeat: no-repeat;
    background-position: 19px 10px;
}
    .input-search{
        display: flex;
        flex-direction: row;
        align-items: center;
        padding: 12px 55px 12px 55px;
        text-align: left;
        position: absolute;
        width: 440px;
        height: 40px;
        left: 28px;
        top: 12px;
        background: rgba(255, 255, 255, 0.3);
        border: 1px solid #ECECEC;
        box-sizing: border-box;
        box-shadow: 0px 2px 12px rgba(0, 0, 0, 0.05);
        border-radius: 36px;
    }
    input::placeholder {
            position: static;
            width: 316px;
            height: 20px;
            left: 34px;
            top: 4px;

            font-family: Gotham Rounded;
            font-style: normal;
            font-weight: normal;
            font-size: 14px;
            line-height: 140%;

            color: #FFFFFF;
            flex: none;
            order: 1;
            align-self: flex-end;
            flex-grow: 0;
            margin: 0px 10px;
    }
    .search{
    flex-direction: column;
    position: fixed;
    bottom: 0;
    background-color: #41464bad;
    left: 70px;
    top: 64px;
    right: 0;
    padding: 0 23px;
    overflow-y: scroll;
    cursor: pointer;
    }

    .show-more{
        font-family: Gilroy;
        font-style: normal;
        font-weight: normal;
        font-size: 18px;
        line-height: 100%;
        display: flex;
        align-items: center;
        text-decoration-line: underline;
        color: #000000;
        cursor: pointer;
    }
    .display-all{
      font-family: Gilroy;
        font-style: normal;
        font-weight: normal;
        font-size: 18px;
        line-height: 100%;
        display: flex;
        align-items: center;
        text-decoration-line: underline;
        color: #000000;
        cursor: pointer;

    }
    .col{
    display: flex;
    flex-direction: column;
    justify-content: center;
    }

   .list-group-flush > .list-group-item {
    border-width: 0 0 0;
   }
   .content-wraper{
    border-bottom: 1px solid #C3C3C3;
    padding-bottom: 16px;
    margin: 0 21px;
    justify-content: center;
    display: flex;
    align-items: center;
    line-height: 22px;

   }
   .container{
    width: calc(100% - 87px);
    background: #F8F8F8;
    border-radius: 5px;
    /* max-width: 785px; */
    margin: 0  0 0 39px !important;
   }
   .row{
    height: 79px;
    justify-content: center;
    display: flex;
    align-items: center;
    cursor: pointer;
   }
   ul
{
    border-radius: 5px;
    margin-top:14px;
    width:1099px;
    list-style-type:none;
    padding:0px;
}
li{
     margin: 0 0 8px 0;
     /* width: calc(100% - 87px);
     max-width: 785px; */
}
.tag.b2c{
    color: #9E44F2;
    background: rgba(234, 214, 247, 0.7);
    border-radius: 70px;
}
.tag.b2b {
    color: #4E58E7;
   background: rgba(212, 221, 247, 0.7);
   border-radius: 70px;
}

input[type="search"]::-webkit-search-cancel-button {
  appearance: none;
    height: 27px;
    width: 30px;
    background-image: url(https://icon-library.com/images/close-icon-png-transparent/close-icon-png-transparent-29.jpg);
    background-repeat: no-repeat;
    background-size: 27px;
}
 .nodata{
        background: #FFFFFF;
        min-height: 380px;
        display: flex;
        align-items: center;justify-content: center;
        max-width: 1099px;
        margin-top: 15px;
        border-radius: 5px;
    }
    .body_medium{
      color:#000000;
    }
    .position-relative{
       width: calc(100% - 231px);
       height: 100%;
    }
    .position-absolute{
      left: 425px;
      top: 22px;
      color: white;

    }
    .icon-close:before {
      background: white;
    }
     .icon-close:after {
      background: white;
    }

  .position-absolute:hover {
  opacity: 1;
}

@media only screen and (max-width: 1280px)  {
          .input-search{
            width: auto !important;

        }
        ul ,.container {
            width: auto !important;
            height: auto;
        }

    .search{
    padding-left: 10px !important;
    padding-right: 10px !important;
    left:0px;
  }
  .position-absolute {
    left: 300px;
    top: 26px;
    color: white;
}
.icon-close:before {
  width: 16px;
  left: 4px;
  top: 2px;
}
.icon-close:after {
  width: 16px;
  top: 2px;
  left: -2px;
 }
    input::placeholder {
            font-size: 12px;
    }
    }
    .btn-all-orders{
      background-color: #EEEEEE;
      height: 35px;
      width: 124px;
      padding: 0;
      font-weight: 800;
      size: 16px;
    }
    .btn-new-orders{
      background-color: #42A71E;
      height: 35px;
      width: 124px;
      padding: 0;
      font-weight: 500;
      size: 16px;
      color: #FFFFFF;
    }
    .text_wrap{
      overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    line-height: 21px;
    max-height: 79px;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    }
  

</style>
