<template>
    <button @click="checkOrderToFulfill" class="btn btn-success body_medium col-12" id="fulfill_btn">Fulfill</button>
    <modal ref="no_payment_modal">
        <template #closebtn>
            <span class="close" id="addon_modal_close" @click="closeNoPaymentModal"></span>
        </template>
        <template #bheader>
            <div class="bmodal-header py-5 text-center">Order #{{order.id}} unpaid</div>
        </template>
        <template #bcontent>
        </template>
        <template #mbuttons>
            <div class="row mx-0 justify-content-center my-5 py-3">
                <div class="col-10">
                    <div class="row justify-content-center mb-4">
                        <div class="col-6">
                            <stripe-pay-now :user="cur_user" :order="order" :amounttopay="parseFloat(amount_to_pay)" @complete-checkout="fulfillOrder(true)" @close-payment-modal="closePaymentAndShowLoading" @close-awaiting-payment="closeAwaitingPaymentModal" ref="stripePay"></stripe-pay-now>
                        </div>
                        <div class="col-6">
                            <button class="pay-btn w-100 py-3" @click="fulfillOrder(false)">Pay later</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button id="payment_method_btn" class="w-100 py-3">Add payment method</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </modal>

    <modal ref="awaiting_payment_modal">
        <template #closebtn>
            <span class="close" id="addon_modal_close" @click="closeAwaitingPaymentModal"></span>
        </template>
        <template #bheader>
            <div class="bmodal-header py-5 text-center">Awaiting payment</div>
        </template>
        <template #mbuttons>
            <div class="row justify-content-center">
                <div class="col-8">
                    <button class="btn btn-outline-danger w-100" id="cancelTerminalBtn" @click="cancelTerminalRequest">Cancel Terminal request</button>
                </div>
            </div>
        </template>
    </modal>


</template>
<script>

import {ref} from 'vue';
import { useStore } from 'vuex';
import { LOADER_MODULE,DISPLAY_LOADER,HIDE_LOADER,TOASTER_MODULE,TOASTER_MESSAGE} from '../../store/types/types';
import Modal from './Modal';
import StripePayNow from './StripePayNow.vue';

export default {
    name:"MiniCheckout",
    props:{
        order_id:Number,
    },
    components:{Modal,StripePayNow},
    emits:['reload-order-detail'],
    setup(props,context) {
        const order = ref({});
        const store = useStore();
        const no_payment_modal = ref();
        const stripePay = ref();
        const cur_user = ref({});
        const amount_to_pay = ref(0);
        const awaiting_payment_modal = ref();


        const checkOrderToFulfill = async ()=>{
            console.log('before order-details');

            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                    true,
                    "Loading details....",
                ]);

            try{
                await axios.post('/order-to-fulfill',{
                    order_id:props.order_id,
                }).then((res)=>{
                    order.value = res.data.order;
                    cur_user.value = res.data.user;
                    amount_to_pay.value = res.data.order.TotalDue;

                    if(res.data.order.Paid==1 || res.data.order.TotalDue==0){
                        console.log('order is paid');
                        fulfillOrder();
                    }else{
                        console.log('order is unpaid');
                        no_payment_modal.value.makeVisible();
                        no_payment_modal.value.showModal()

                    }
                }).catch((err)=>{

                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
            }finally{
                console.log('after order details');
            };

        }

        const closeNoPaymentModal = ()=> no_payment_modal.value.makeInvisible();

        const fulfillOrder = (paid)=>{

            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                    true,
                    "Fulfilling order "+props.order_id+"....",
                ]);
            axios.post('/fulfill-order',{
                order_id:props.order_id,
                paid:paid,
            }).then((res)=>{
                let output = res.data.output;
                let msg_type = 'danger';
                let fulfill_msg = 'API Error';

                if(output.result=='ok'){
                    msg_type = 'success';
                    fulfill_msg = 'Order fulfilled';
                }

                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                    message:fulfill_msg,
                    type:msg_type,
                    ttl:5,
                });

            }).catch((err)=>{
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                    message:'API error',
                    type:'danger',
                    ttl:5,
                });

            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                closeNoPaymentModal();
                context.emit('reload-order-detail');
            });
        }


        const closePaymentAndShowLoading = ()=>{
            no_payment_modal.value.makeInvisible();
            awaiting_payment_modal.value.showModal();
        }

        const closeAwaitingPaymentModal = ()=>{
            awaiting_payment_modal.value.closeModal();
        }

        function cancelTerminalRequest(){
            stripePay.value.cancelTerminalRequest();
        }

        return {
            checkOrderToFulfill,
            fulfillOrder,
            no_payment_modal,
            closePaymentAndShowLoading,
            cur_user,
            amount_to_pay,
            order,
            closeAwaitingPaymentModal,
            closeNoPaymentModal,
            awaiting_payment_modal,
            cancelTerminalRequest,
            stripePay,
        }

    },
}
</script>
<style scoped>

.pay-btn,#payment_method_btn{
    font:normal 16px "Gotham Rounded";
    border-radius:4px;
}
.pay-btn{
    border:thin solid #47454B;
    background:#fff;
}

#payment_method_btn{
    border:none;
    background:#47454b;
    color:#fff;
}

.pay-btn:hover,#payment_method_btn:hover{
    color:#fff;
    background: #42A71E;
    border-color: #42A71E;
}

.bmodal-header{
    font:bold 22px "Gilroy";
    color:#F4003D;
    background:#FFEFED;
}

#fulfill_btn{
    color:#fff;
}

#fulfill_btn:hover{
    background:#333;
    border:thin solid #333;
}
</style>
