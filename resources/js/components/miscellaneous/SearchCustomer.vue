<template>

<div class="position-relative ">
  <input id="icon" class="btn input-search body_medium" @keyup.prevent="submit" v-model="clear" type="texte" placeholder="Search a name, email, barcode, etc"/>
   <span v-if="showbutton" @click='clearSearch' class="position-absolute"><i class="icon-close"></i></span>
</div>

 <transition
            enter-active-class="animate__animated animate__fadeIn"
 >
 <div v-if="showSearch" class="search">
   <section class="nodata p-2" v-if ="Customer.length == 0 && CustomerEmails.length == 0 && CustomerOrders.length == 0">
            <p v-if="!loader_running">we couldn't find any customers.</p>
  </section>

     <ul  v-else class=" list-group-flush" style="background: #FFFFFF;cursor: default;" >
        <li class="list-group-item" v-if ="Customer.length > 0">
            <div class="content-wraper " style="padding-top: 31px;">
                <span class="subtitle col-6">Name</span>
                <a class="d-flex justify-content-end col-6 show-more"  @click="loadMore('search_name')" >Show more</a>
             </div>
             <ul  class="list-group list-group-flush" >
                <li v-for ="customer in Customer" :key="customer">
                  <div class="container">
                    <div class="row">
                       <div class="col">
                          <span class="body_medium"><a href="javascript:void(0)" @click="goToOrderList(customer.CustomerID)">{{customer.Name.replace(',','').toLowerCase()}}</a></span>
                            <div  v-if="customer.Phone!=''&&customer.Phone!=null" >
                              <div v-for="phone in customer.Phone.slice(0,1)" :key="phone">
                               <b class ="body_regular">+{{phone.replace('|',' ')}}</b>
                              </div>
                            </div>
                            <div v-else>

                              <div class="phone body_small">--</div>
                            </div>
                       </div>
                      <div class="col-6">
                             <b class ="body_regular">{{customer.EmailAddress}}</b>
                       </div>
                       <div class="col-2" style="text-align: end;">
                            <tag   v-if="customer.TypeDelivery=='DELIVERY'" :name="'B2C'" ></tag>
                            <tag   v-else :name="'B2B'" ></tag>
                        </div>

                     </div>
                    </div>
               </li>

             </ul>
        </li>
        <li class="list-group-item" v-if="CustomerEmails.length > 0">
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
        </li>
          <li class="list-group-item" v-if="CustomerOrders.length>0">

             <div class="content-wraper">
                <span class="subtitle col-6">Order</span>

                <a class="d-flex justify-content-end col-6 show-more"  @click="loadMore('search_order')" >Show more</a>
             </div>
             <ul   class="list-group list-group-flush" >
                <li v-for ="(order,index) in CustomerOrders" :key="order">
                  <div class="container">
                    <div class="row" @click="selectrow(order.id,order.Status,index)">
                        <div class="col-3">
                         <span class="body_medium">{{order.Name.replace(',','').toLowerCase()}}</span>
                        </div>
                        <div class="col-3  text-align: center;">
                        <b class = "body_small">{{order.TypeDelivery}}</b>
                        </div>
                        <div class=" col-2">
                            <b class ="body_small">{{formatDate(order.DateDeliveryAsk)}}</b>
                        </div>

                        <div class=" col-2">
                            <tag  :name="order.Status" ></tag>
                        </div>
                          <div class="col-2" style="text-align: end;">
                            <tag   v-if="order.TypeDelivery=='DELIVERY'" :name="'B2C'" ></tag>
                            <tag   v-else :name="'B2B'" ></tag>
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
    import {useRouter} from 'vue-router'
    import {ref,computed,watch, nextTick } from 'vue';
    import Tag from  '../miscellaneous/Tag'
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
        HIDE_LOADER


    } from "../../store/types/types";
    import {formatDate} from "../helpers/helpers";
    import {useStore} from 'vuex';
export default({
     name: "SearchCustomer",
     components:{Tag,WaveLoader},
       setup(){
           const router = useRouter();
           const store =useStore();
           const showSearch=ref(false);
           const timeout =ref('');
           const clear = ref('');
           const show_loader= ref(false);
           const showbutton = ref(false);


           const featureunavailable=((feature)=>{
               store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:feature+' feature not yet implemented.',ttl:5,type:'success'});
            });

      function clearSearch(){
         clear.value = null;
         showSearch.value = false;
         showbutton.value = false;
         show_loader.value= false;
       }
         function selectrow(id,status,colname){
               console.log(id,colname);

               showSearch.value = false;
               show_loader.value= false;
                if(colname=='line_select') return;

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

          function loadMore(tab){
                 store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, ' please wait...']);
                 store.dispatch(`${CUSTOMERLIST_MODULE}${CUSTOMER_LOAD_LIST}`,{showmore:tab,query:clear.value}).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
            }
          function goToOrderList(customerId){

            this.clearSearch()
          
            router.push({
                    name:'LandingPage',
                    params: {
                       customerId,
                    },
                })
                
          }
            return {
                submit,
                clearSearch,
                clear,
                Customer,
                showSearch,
                CustomerEmails,
                CustomerOrders,
                formatDate,
                featureunavailable,
                showbutton,
                selectrow,
                show_loader,
                loadMore,
                goToOrderList

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
    max-width: 785px;
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

</style>
