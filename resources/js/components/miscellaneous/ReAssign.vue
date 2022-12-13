<template>
<transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
    <div v-if="show_modal" class="order-detail-modal">
        <div class="order-detail-modal-container">
            <div class="order-detail-modal-header position-relative">
                Reassign <i class="icon-close" @click="closeModal"></i>
            </div>
            <div class="order-detail-modal-content">
                <p>Do you want to reassign sub-order <b>{{ subOrder }}</b> to </p>
                <div class="customer-search" v-if="step == 1">
                    <search :label="'Search Customer'" @selected-customer="selectedCustomer"></search>
                </div>
                <div class="customer-info w-100" v-else>
                    <div class="customer-name d-flex">
                        <h3>{{ customer.Name }}</h3> <a href="javascript:;" @click="editCustomer" class="customer-edit-btn ms-3">Edit</a>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="col-7">
                            <label for="">Email</label>
                            <p class="text-start fw-bold">{{ customer.EmailAddress }}</p>
                        </div>
                        <div class="col-5">
                            <label for="">Phone number</label>
                            <p class="text-start fw-bold" v-for="phone in customer.Phone.slice(0,1)" :key="phone">+{{phone.replace('|',' ')}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="order-detail-modal-footer" v-if="step == 2">
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
// import {formatPhone} from '../helpers/helpers'
import Search from './Search.vue';
export default {
    name: "ReAssign",
    emits:['close-modal'],
    props:{
        showBtn:Boolean
    },
    components: { Search },
    setup(props, context){
        const store = useStore();
        const show_modal = ref(false);
        const customer = ref({});
        const subOrder = ref(null);
        const invoiceId = ref(null);
        const step = ref(1);

        const closeModal = ()=>{
            show_modal.value = false;
            step.value = 1;
        }

        const openModal = (suborder, invoice, customer)=>{
            show_modal.value = true;
            invoiceId.value = invoice;
            subOrder.value = suborder;
        }
        const confirm = ()=>{
            var reAssaignUrl = '/reAssign';
            axios.post(reAssaignUrl,{ 
                invoiceId:    invoiceId.value,
                customerId:    customer.value.CustomerID,
            }).then((res)=>{
                if(res.data.status_code == 200){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'Reassign Done.',ttl:5,type:'success'});
                    window.location.reload();
                }else{
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message: res.data.status_message, ttl:5, type:'danger'});
                }
            }).catch((error)=>{
                console.log(error);
            }).finally(()=>{

            })
        }
        const selectedCustomer = (cust)=>{
            customer.value = cust;
            step.value = 2;
        }
        const editCustomer = ()=>{
            step.value = 1;
        }
        return {
            show_modal,
            subOrder,
            step,
            customer,
            closeModal,
            openModal,
            confirm,
            editCustomer,
            selectedCustomer,
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
    padding: 20px 40px;
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
.customer-info{
    padding: 20px 15px;
    border: 1px solid #E0E0E0;
    border-radius: 5px;    
}
.customer-info label{
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: 325;
    font-size: 16px;
    line-height: 140%;    
    color: #868686;
}
.customer-info label{
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: 350;
    font-size: 16px;
    line-height: 140%;
    color: #47454B    
}
.customer-name h3{
    font-family: 'Gilroy';
    font-style: normal;
    font-weight: 800;
    font-size: 22px;
    line-height: 110%;    
}
.customer-edit-btn{
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: 300;
    font-size: 16px;
    line-height: 19px;    
    text-decoration-line: underline;
    color: #42A71E;    
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
    