<template>
	
  <input id="icon" class="btn input-search body_medium" @keyup.prevent="submit" v-model="clear" type="search"  placeholder="Search ..." />
 <transition
            enter-active-class="animate__animated animate__fadeIn"
 >
 <div v-if="showSearch" class="search">
   <section class="nodata p-2" v-if ="Customer.length == 0 && CustomerEmails.length == 0 && CustomerOrders.length == 0">
            <p v-if="!loader_running">we couldn't find any customers.</p>
  </section>
  
     <ul  v-else class="list-group list-group-flush pt-3" style="background: #FFFFFF;" >
        <li class="list-group-item" v-if ="Customer.length > 0">
            <div class=" row content-wraper ">
                <span class="subtitle col-6">Name</span>
                <a class="d-flex justify-content-end col-6 show-more" @click="featureunavailable('Show more')" >Show more</a>
             </div>
             <ul  class="list-group list-group-flush pt-2" >
                <li v-for ="customer in Customer" :key="customer">
                  <div class="container pt-3">
                    <div class="row">
                        <div class="col">
                         <span class="body_medium">{{customer.FirstName}} {{customer.LastName}}</span>
                        </div>
                      
                          <div class="col-2" style="text-align: end;">
                            <tag   v-if="customer.TypeDelivery=='DELIVERY'" :name="'B2C'" ></tag>
                            <tag   v-else :name="'B2B'" ></tag>
                         </div>
                     
                     </div>                      
                     <div class="row ">
                         <div class="col-4" v-if="customer.Phone!=''&&customer.Phone!=null" >
                            <div v-for="phone in customer.Phone.slice(0,1)" :key="phone">
                           <b class ="body_regular">+{{phone.replace('|',' ')}}</b>
                            </div>
                        </div>
                         <div class="col-4" v-else  >
                            <div class="phone body_small">--</div>
                        </div>
                        <div class="col-md-auto">
                       <b class ="body_regular">{{customer.EmailAddress}}</b>   
                        </div>
                     
                     </div>

                    </div>
               </li>
              
             </ul>
        </li>
        <li class="list-group-item" v-if="CustomerEmails.length > 0">
             <div class=" row content-wraper ">
                <span class="subtitle col-6">Email</span>
                <a class="d-flex justify-content-end col-6 show-more" @click="featureunavailable('Show more')" >Show more</a>
             </div>
             <ul  class="list-group list-group-flush pt-2">
                <li v-for ="customer in CustomerEmails" :key="customer">
                  <div class="container pt-3">
                    <div class="row">
                        <div class="col">
                         <b class = "body_regular" >{{customer.FirstName}} {{customer.LastName}}</b>
                        </div>
                          <div class="col-2" style="text-align: end;">
                            <tag   v-if="customer.TypeDelivery=='DELIVERY'" :name="'B2C'" ></tag>
                            <tag   v-else :name="'B2B'" ></tag>
                         </div>
                     </div>                      
                     <div class="row ">
    
                        <div class="col-4 " v-if="customer.Phone!=''&&customer.Phone!=null" >
                            <div v-for="phone in customer.Phone.slice(0,1)" :key="phone">
                           <b class ="body_regular">+{{phone.replace('|',' ')}}</b>
                            </div>
                        </div>
                         <div class="col-4" v-else  >
                            <div class="phone body_small">--</div>
                        </div>

                        <div class="col-md-auto">
                         <span class="body_medium">{{customer.EmailAddress}}</span>
                        </div>
                     
                     </div>

                    </div>
               </li>
             </ul>
        </li>
          <li class="list-group-item" v-if="CustomerOrders.length>0">
             <div class=" row content-wraper">
                <span class="subtitle col-6">Order</span>
                <a class="d-flex justify-content-end col-6 show-more" @click="featureunavailable('Show more')" >Show more</a>
             </div>

             <ul   class="list-group list-group-flush pt-2" > 
                <li v-for ="order in CustomerOrders" :key="order">
                  <div class="container pt-4">
                    <div class="row">
                        <div class="col-3">
                         <span class="body_medium">{{order.FirstName}} {{order.LastName}}</span>
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

 </transition>
</template>
<script>
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
        CUSTOMER_SET_LOADER,
   
    } from "../../store/types/types";
    import {formatDate} from "../helpers/helpers";
    import {useStore} from 'vuex';
export default({
     name: "SearchCustomer",
     components:{Tag},
       setup(){
           const store =useStore();
           const showSearch=ref(false);
           const timeout =ref('');
           const clear = ref('');
           const featureunavailable=((feature)=>{
               store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:feature+' feature not yet implemented.',ttl:5,type:'success'});
            });

         watch(
        () => clear.value,
        (current_val, previous_val) => {
          if(current_val === ''){
              showSearch.value = false;
          }
        }
      )

            function submit(e) { 
                clearTimeout(timeout.value);
               timeout.value = setTimeout(function(){
                  store.commit(`${CUSTOMERLIST_MODULE}${CUSTOMER_SET_LOADER}`,'');
                   nextTick(() => {
                     store.dispatch(`${CUSTOMERLIST_MODULE}${CUSTOMER_LOAD_LIST}`,{query:e.target.value , PerPage:3}).then((response)=>{
                           showSearch.value = true;
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

           

            return {
                submit,
                clear,
                Customer,
                showSearch,
                CustomerEmails,
                CustomerOrders,
                formatDate,
                featureunavailable,
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
        padding: 12px 12px 12px 55px;
        text-align: left;
        position: absolute;
        width: 440px;
        height: 40px;
        left: 78px;
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

            color: #000000;
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
    top: 56px;
    right: 0;
    padding: 0 23px;
    height: 100%;
    overflow-y: scroll;
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
    }
   
   .list-group-flush > .list-group-item {
    border-width: 0 0 0;      
   }
   .content-wraper{
    border-bottom: 1px solid #C3C3C3;
    padding-bottom: 16px;
    margin: 0 21px;
   }
   .container{
    width: 75%;
    margin-left: 39px;
    height: 79px;
    background: #F8F8F8;
    border-radius: 5px;
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
 margin-bottom: 18px;
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

 @media only screen and (max-width: 1089px) {
          .input-search{
            width: auto !important;

        }
        ul ,.container {
            width: auto !important;
            height: auto;
        }
        .search
          {
            left: 0;
          }
        
        
    }
</style>