<template>

    <transition-group tag="div" name="popinout">

     <div class="panel" v-if="order.infoCustomer==null||edit_customer">
        <h2 class="subtitle">Customer Details <button class="btn-link-green body_regular" @click="featureUnavailable('New Customer')">New</button></h2>
        <div class="row">
            <div class="col">
                <search v-model="CustomerID" name="search" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Search a customer" hint="disabled till 2021-09-10" ></search>
            </div>
        </div>
    </div>

    <div class="panel" v-if="order.infoCustomer!=null &&!edit_customer"><!-- -->
        <div class="row">
            <div class="col-12">
        <h2 class="subtitle text-capitalize">{{order.infoCustomer.LastName.toLowerCase()}} {{order.infoCustomer.FirstName.toLowerCase()}}<button class="btn-link-green body_regular" @click="editCustomer">Edit</button></h2>
            </div>

        </div>
        <div class="row">
            <div class="col-4 ">
                <div class="ltm_spent">
                    <span class="body_regular">LTM spend</span>
                    <span class="body_medium">{{formatPrice(order.infoCustomer.ltm_spend)}}</span>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-6 body_regular mediumgrey">Email</div><div class="col-6 body_regular mediumgrey">Phone number</div>
        </div>
        <div class="row">
            <div class="col-6 body_medium">{{order.infoCustomer.EmailAddress.toLowerCase()}}</div><div class="col-6 body_medium" v-html="formatPhone(order.infoCustomer.Phone)"></div>
        </div>
    </div>

    </transition-group>

</template>

<script>
    import Search from '../miscellaneous/Search'
    import {ref,computed,watch} from 'vue'
    import {useStore} from 'vuex';
    import {featureUnavailable} from "../helpers/helpers";
    import {formatPrice,formatPhone} from '../helpers/helpers';
    import {NEWORDER_GET_CUSTOMER, NEWORDER_MODULE, NEWORDER_PRELOAD_ORDER_GET} from "../../store/types/types";
    export default {
        name: "CustomerDetailsPanel",
        components:{Search},
        setup(props,context){
            const CustomerID=ref('');
            const edit_customer=ref(false);
            const store=useStore();
            watch(()=>CustomerID.value,(current_val,previous_val)=>{


                store.dispatch(`${NEWORDER_MODULE}${NEWORDER_GET_CUSTOMER}`,{CustomerID:current_val}).then((res)=>{
                    console.log('res',res);
                    edit_customer.value=false;

                }).finally(()=>{
                   context.emit('setcustomerid',current_val);
                });

            })

            const order=computed(()=>store.getters[`${NEWORDER_MODULE}${NEWORDER_PRELOAD_ORDER_GET}`]);
            const editCustomer=()=>{
                edit_customer.value=true;
                CustomerID.value = '';
            }
            return{
                featureUnavailable,
                CustomerID,
                order,
                formatPrice,
                formatPhone,
                editCustomer,
                edit_customer
            }

        }
    }
</script>

<style scoped>
.ltm_spent{
    padding:16px 20px;
    background-color: #47454B;
    border-radius: 5px;
    display: flex;
    flex-direction: column;
}
.ltm_spent span{
    color: #FFF;
}
    .mediumgrey{
        color: #868686;
    }
    .popinout-leave-active{
        position: absolute;
    }
</style>
