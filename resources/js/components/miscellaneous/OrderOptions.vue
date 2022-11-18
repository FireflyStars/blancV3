<template>

    <div class="options position-absolute">
        <div class="row">
          
            <div  class="col-12 row-option" @click="goToCheckout()"  >
                <img class="img-arrow" src="/images/payment.png" />
                <span>Take payment</span>
            </div>
            <!-- <div class="col-12 row-option" >
                <img class="img-arrow" src="/images/offload.png" />
                <span>Offload</span>
            </div>
            <div class="col-12 row-option" row-option>
                <img class="img-arrow" src="/images/link.svg" />
                <span>Copy link</span>
            </div>
             <div class="col-12 row-option" >
                <img class="img-arrow" src="/images/arrow.png" />
                <span>Reassign</span>
            </div>-->
             <div class="col-12 row-option" v-if="order.Status == 'IN DETAILING'"  @click="DeleteOrder()">
                <img class="img-arrow" src="/images/garbage.png" />
                <span>Delete</span>
            </div> 
            <div class="col-12 row-option" v-if="user_id == 1 || user_id == 4" @click="VoidOrder()">
                <img class="img-arrow" src="/images/erase.png" />
                <span>Void</span>
            </div>
        </div>
       

    </div>
</template>

<script>

    import {useStore} from 'vuex';
    import { useRouter} from "vue-router";
    import {
        TOASTER_MODULE,
        TOASTER_MESSAGE
    } from "../../store/types/types";
    export default {
        name: "OrderOptions",
        props:[
            'user',
            'order',
            'items',
        ],
        setup(props){
           const store=useStore();
           const router = useRouter();
           const user_id = props.user

           function goToCheckout(){
            router.push('/checkout/'+props.order.order_id);
           }

           function VoidOrder(){
            axios.post('/voidOrder',{
                   order_id: props.order.order_id,
                   items : props.items
               }).then((res)=>{
                          if( res.data.done == "ok"){
                             store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'Success',ttl:5,type:'success'});
                             location.reload();
                        }
                    })
                    .catch((error)=>{
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'});
                    })
           }

           function DeleteOrder(){
            axios.post('/deleteorder',{
                   order_id: props.order.order_id
               }).then((res)=>{
                          if( res.data.done == "ok"){
                             store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'Order deleted successfully',ttl:5,type:'success'});
                             location.reload();
                        }
                    })
                    .catch((error)=>{
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured`,ttl:5,type:'danger'});
                    })
           }

            return {
                user_id,
                goToCheckout,
                VoidOrder,
                DeleteOrder
            }
        }
    }
</script>

<style scoped>

    .row-option{
        height: 56px;
        padding: 10px 32px;
    }
    .row-option span {
       vertical-align: middle;
    }
    .row-option:hover{
        background-color: #EEEEEE;
    }
    .options{
        top:56px;
        background: #F8F8F8;
        box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.12);
        width: 298px;
        min-height: auto;
        z-index: 2;
        transform-origin: top center;
        padding:0 10px;
    }
    img.img-arrow {
    margin-right: 25px;
    width: 25px;
    height: auto;
    object-fit: contain;
    object-position: center;
    }

      .custom-trans-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }

</style>