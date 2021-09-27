<template>

 <input id="icon" class="btn input-search body_medium" @keyup.prevent="submit"  type="text"  placeholder="Search ..." />
 <transition
            enter-active-class="animate__animated animate__fadeIn"
 >
 <div v-if="showSearch" class="search">
     <ul  class="list-group list-group-flush" style="background: #FFFFFF;">
        <li class="list-group-item">
            <div class=" row content-wraper pt-3">
                <span class="d-flex justify-content-start col-6">Name</span>
                <a class="d-flex justify-content-end col-6 show-more" >Show more</a>
             </div>
             <ul  class="list-group list-group-flush pt-2" v-if="Customer.length>0">
                <li v-for ="customer in Customer" :key="customer">
                  <div class="container pt-3">
                    <div class="row">
                        <div class="col">
                         <span class="text-name">{{customer.FirstName}} {{customer.LastName}}</span>
                        </div>
                      
                          <div class="col-2" style="text-align: end;">
                            <tag   v-if="customer.TypeDelivery=='DELIVERY'" :name="'B2C'" ></tag>
                            <tag   v-else :name="'B2B'" ></tag>
                         </div>
                     
                     </div>                      
                     <div class="row ">
                         <div class="col-4" v-if="customer.Phone!=''&&customer.Phone!=null" >
                            <div v-for="phone in customer.Phone.slice(0,1)" :key="phone">
                           <b class ="sous-title">+{{phone.replace('|',' ')}}</b>
                            </div>
                        </div>
                         <div class="col-4" v-else  >
                            <div class="phone body_small">--</div>
                        </div>
                        <div class="col-md-auto">
                       <b class ="sous-title">{{customer.EmailAddress}}</b>   
                        </div>
                     
                     </div>

                    </div>
               </li>
              
             </ul>
               <div class=" pt-2" style="text-align: center;" v-else>
                    <b>we couldn't find any customers</b> 
               </div>  
        </li>
        <li class="list-group-item">
             <div class=" row content-wraper ">
                <span class="d-flex justify-content-start col-6">Email</span>
                <a class="d-flex justify-content-end col-6 show-more" >Show more</a>
             </div>
             <ul  class="list-group list-group-flush pt-2" v-if="CustomerEmails.length>0">
                <li v-for ="customer in CustomerEmails" :key="customer">
                  <div class="container pt-3">
                    <div class="row">
                        <div class="col">
                         <b>{{customer.FirstName}} {{customer.LastName}}</b>
                        </div>
                          <div class="col-2" style="text-align: end;">
                            <tag   v-if="customer.TypeDelivery=='DELIVERY'" :name="'B2C'" ></tag>
                            <tag   v-else :name="'B2B'" ></tag>
                         </div>
                     </div>                      
                     <div class="row ">
    
                        <div class="col-4" v-if="customer.Phone!=''&&customer.Phone!=null" >
                            <div v-for="phone in customer.Phone.slice(0,1)" :key="phone">
                           <b class ="sous-title">+{{phone.replace('|',' ')}}</b>
                            </div>
                        </div>
                         <div class="col-4" v-else  >
                            <div class="phone body_small">--</div>
                        </div>

                        <div class="col-md-auto">
                         <span class="f14">{{customer.EmailAddress}}</span>
                        </div>
                     
                     </div>

                    </div>
               </li>
             </ul>
             <div class=" pt-2" style="text-align: center;" v-else>
                    <b>we couldn't find any customers</b> 
               </div>
        </li>
          <li class="list-group-item">
             <div class=" row content-wraper">
                <span class="d-flex justify-content-start col-6">Order</span>
                <a class="d-flex justify-content-end col-6 show-more" >Show more</a>
             </div>

             <ul   class="list-group list-group-flush pt-2" v-if="CustomerOrders.length>0"> 
                <li v-for ="order in CustomerOrders" :key="order">
                  <div class="container pt-4">
                    <div class="row">
                        <div class="col-3">
                         <span class="f14">{{order.FirstName}} {{order.LastName}}</span>
                        </div>
                        <div class="col-3  text-align: center;">
                        <b>{{order.TypeDelivery}}</b>
                        </div>
                        <div class=" col-2">
                            <b class ="sous-title">{{formatDate(order.DateDeliveryAsk)}}</b>
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
             <div class=" pt-2" style="text-align: center;" v-else>
                    <b>we couldn't find any orders</b> 
               </div>
        </li>
       
       
     </ul>

 </div>

 </transition>
</template>
<script>
    import {ref,computed } from 'vue';
    import Tag from  '../miscellaneous/Tag'
       import {
        CUSTOMERLIST_MODULE,
        CUSTOMERLIST_LOAD_DETAILS,
        CUSTOMERLIST_GET_DETAILS,
        CUSTOMEREMAILS_LOAD_LIST,
        CUSTOMERORDERS_LOAD_LIST,
        CUSTOMEREMAILS_GET_LIST,
        CUSTOMERORDERS_GET_LIST
   
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
           
         function submit(e) { 
                 if(e.target.value) {  
                   showSearch.value = true;
                } else {
                     showSearch.value = false;
                }
              
                clearTimeout(timeout.value);
               timeout.value = setTimeout(function(){
            
                  store.dispatch(`${CUSTOMERLIST_MODULE}${CUSTOMERLIST_LOAD_DETAILS}`,{query:e.target.value})
                  store.dispatch(`${CUSTOMERLIST_MODULE}${CUSTOMEREMAILS_LOAD_LIST}`,{query:e.target.value})
                  store.dispatch(`${CUSTOMERLIST_MODULE}${CUSTOMERORDERS_LOAD_LIST}`,{query:e.target.value})
               }  
              , 500)
            };

           const Customer=computed(()=>{
               return store.getters[`${CUSTOMERLIST_MODULE}${CUSTOMERLIST_GET_DETAILS}`];
               
            });
         
            const CustomerEmails=computed(()=>{
                return store.getters[`${CUSTOMERLIST_MODULE}${CUSTOMEREMAILS_GET_LIST}`];
            });
           
             const CustomerOrders=computed(()=>{
                 
                return store.getters[`${CUSTOMERLIST_MODULE}${CUSTOMERORDERS_GET_LIST}`];
            });

            return {
                submit,
                Customer,
                showSearch,
                CustomerEmails,
                CustomerOrders,
                formatDate,

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
        padding: 12px 55px;
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
    left: 86px;
    top: 56px;
    right: 0;
    border-radius: 5px;
    padding: 0 23px;
 
    }
  .justify-content-start{
    font-family: Gilroy;
    font-style: normal;
    font-weight: 800;
    font-size: 22px;
    padding-left: 20px;
    line-height: 110%;
    display: flex;
    color: #000000;
  }
    span{
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 16px;
    line-height: 140%;
    color: #000000;
    border-radius: 2px;

        }
    .text-name{
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 16px;
    line-height: 140%;
    color: #000000;
    border-radius: 2px;

    }  
    .sous-title{
     font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 16px;
    line-height: 140%;
    color: #868686;
    border-radius: 2px;
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
    width:946px;
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


</style>