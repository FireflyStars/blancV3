<template>
<transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
    <div v-if="show_modal" class="order-detail-modal">
        <div class="order-detail-modal-container">
            <div class="order-detail-modal-header position-relative">
                Free reclean <i class="icon-close" @click="closeModal"></i>
            </div>
            <div class="order-detail-modal-content">
                <p>Do you want to send item <b>{{ itemId }}</b> for a free reclean?</p>
                <p>This action will split this item OUT OF sub-order {{ subOrder }}.</p>
            </div>
            <div class="order-detail-modal-footer">
                <div class="cancel-btn" @click="closeModal">NO, cancel</div>
                <div class="confirm-btn" @click="confirm">Confirm</div>
            </div>
        </div>
    </div>
</transition>
</template>

<script>
import axios from 'axios';
import {useStore} from 'vuex';
import {ref} from 'vue';
import {
        TOASTER_MODULE,
        TOASTER_MESSAGE,
    } from "../../store/types/types";
export default {
    name: "FreeReclean",
    emits:['close-modal'],
    props:{
        showBtn:Boolean
    },
    setup(props, context){
        const store = useStore();
        const show_modal = ref(false);
        const subOrder = ref(null);
        const itemId = ref(null);
        const orderId = ref(null);

        const closeModal = ()=>{
            show_modal.value = false;
        }

        const openModal = (id, order, suborder)=>{
            show_modal.value = true;
            subOrder.value = suborder;
            itemId.value = id;
            orderId.value = order;
        }
        const confirm = ()=>{
            var freeRecleanUrl = '/freeReClean';
            axios.post(freeRecleanUrl,{ 
                orderId:    orderId.value,
                itemId:     itemId.value,
            }).then((res)=>{
                if(res.data.status_code == 200){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'Free Reclean Done.',ttl:5,type:'success'});
                    window.location.reload();
                }else{
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message: res.data.status_message, ttl:5, type:'danger'});
                }
            }).catch((error)=>{
                console.log(error);
            }).finally(()=>{

            })
        }

        return {
            show_modal,
            subOrder,
            itemId,
            closeModal,
            openModal,
            confirm,
        }
    }
};
</script>
<style scoped>
.order-detail-modal{
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 1;
    transition: opacity 0.3s ease-in-out;
    background: rgba(0, 0, 0, 0.7);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}
.order-detail-modal-container{
    width: 545px;
    height: 400px;
    border-radius: 10px;
    overflow: hidden;
    background: white;
}
.order-detail-modal-header{
    background: #E0E0E0;
    height: 94px;
    font-family: 'Gilroy';
    font-style: normal;
    font-weight: 800;
    font-size: 22px;
    line-height: 110%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.order-detail-modal-content{
    padding: 45px 110px 58px;
}
.icon-close{
    top:24px;
    right: 30px;
}
p{
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: 325;
    font-size: 16px;
    line-height: 140%;
    color: #151920;
    text-align: center;
}
.order-detail-modal-footer{
    padding: 0 70px 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.cancel-btn{
    width: 164px;
    height: 56px;
    background: #FFFFFF;
    border: 1px solid #EB5757;
    border-radius: 4px;
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: 350;
    font-size: 16px;
    line-height: 140%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #EB5757;
    cursor: pointer;
}
.confirm-btn{
    width: 164px;
    height: 56px;
    border-radius: 4px;
    background: #47454B;
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: 350;
    font-size: 16px;
    line-height: 140%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #FFFFFF;
    cursor: pointer;
}
</style>
