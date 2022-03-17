<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <table class="table table-hover mb-0 bg-white">
            <thead>
                <tr>
                    <th v-for="(item, index) in tableColumnsDef" :class="item.thClass" :key="index" style="border-bottom: 2px solid #dee2e6 !important">
                        <span>{{ item.label }}</span>
                    </th>
                </tr>
            </thead>
            <tbody>
            <transition-group name="list" appear>
                <tr v-for="(customer, index) in customerList" :key="index" class="trow">
                    <!-- Customer Type -->
                    <td valign="middle"><span class="rounded-pill" :class="customer.type.toLowerCase()">{{ customer.type }}</span></td>
                    <!-- active in -->
                    <td class="fw-16 text-capitalize" valign="middle"><span>{{ customer.active_in }}</span></td>
                    <!-- Customer Name -->
                    <td class="text-capitalize fw-16" valign="middle">{{ customer.name }}</td>
                    <!-- address -->
                    <td class="fw-14" valign="middle">{{ customer.address }}</td>
                    <!-- Email -->
                    <td class="fw-16" valign="middle">{{ customer.email }}</td>
                    <!-- Phone -->
                    <td class="text-nowrap fw-16" valign="middle">{{ formatPhone(customer.phone) }}</td>
                    <!-- last order -->
                    <td class="fw-16" valign="middle">{{ customer.last_order ? customer.last_order : "--" }}</td>
                    <!--  total spent-->
                    <td class="fw-16 fw-bold" valign="middle">{{ customer.total_spent ? ('£'+customer.total_spent) : "--" }}</td>
                </tr>
            </transition-group>
            <tr v-if="customerList.length == 0">
                <td class="tcol" style="text-align: center" :colspan="Object.keys(tableColumnsDef).length">No Data Available</td>
            </tr>
            </tbody>
            <tfoot>
                <tr v-if="currentLoadedCustomerCount < totalCustomerCount">
                    <td class="tcol" style="text-align: center" :colspan="Object.keys(tableColumnsDef).length">  <button class="btn btn-link" @click="loadMoreCustomer">Show more ( {{ currentLoadedCustomerCount }} / {{ totalCustomerCount }} )</button></td>
                </tr>
            </tfoot>
        </table>
    </transition>
    <!-- <router-view /> -->
</template>
<script>
import { ref, onMounted, computed } from "vue";
import {
    CUSTOMER_MODULE,
    GET_CUSTOMER_LIST,
    GET_LOADED_CUSTOMER_COUNT,
    GET_TOTAL_CUSTOMER_COUNT,
    LOAD_MORE_CUSTOMER,
    SET_CUSTOMER_LIST
} from "../../store/types/types";
import { useStore } from 'vuex';

export default {
    name: 'CustomerList',
    components:{
    },
    setup(){
        const store = useStore();
        const tableColumnsDef = [
                {
                    label: 'Type',
                    key: 'type',
                    thClass: 'customer-table-th text-center',
                },
                {
                    label: 'Active in',
                    key: 'active_in',
                    thClass: 'customer-table-th text-nowrap',
                    tdClass: 'text-capitalize fw-16',
                },
                {
                    label: 'Name',
                    key: 'name',
                    thClass: 'customer-table-th',
                    tdClass: 'text-capitalize fw-16',
                },
                {
                    label: 'Address',
                    key: 'address',
                    thClass: 'customer-table-th',
                    tdClass: 'fw-16',
                },

                {
                    label: 'Email',
                    key: 'email',
                    thClass: 'customer-table-th',
                    tdClass: 'fw-16',
                },
                {
                    label: 'Phone',
                    key: 'phone',
                    thClass: 'customer-table-th',
                    tdClass: 'text-nowrap fw-16',
                },
                {
                    label: 'Last Order',
                    key: 'last_order',
                    thClass: 'customer-table-th text-nowrap',
                    tdClass: 'fw-16',
                },
                {
                    label: 'Total Spent',
                    key: 'total_spent',
                    thClass: 'customer-table-th text-nowrap',
                    tdClass: 'fw-16',
                },
            ];
        const customerList = computed(()=>{
            return store.getters[`${CUSTOMER_MODULE}${GET_CUSTOMER_LIST}`];
        });
        const currentLoadedCustomerCount = computed(()=>{
            return store.getters[`${CUSTOMER_MODULE}${GET_LOADED_CUSTOMER_COUNT}`];
        });
        const totalCustomerCount = computed(()=>{
            return store.getters[`${CUSTOMER_MODULE}${GET_TOTAL_CUSTOMER_COUNT}`];
        })

        const loadMoreCustomer = ()=>{
            store.dispatch(`${CUSTOMER_MODULE}${LOAD_MORE_CUSTOMER}`);
        }
        onMounted(()=>{
            store.dispatch(`${CUSTOMER_MODULE}${SET_CUSTOMER_LIST}`);
        })
        function formatPhone(phoneString){
            if(phoneString !="--"){
                var phone = phoneString.split('"')[1];
                var area_code = phone.split("|")[0];
                var number = phone.split("|")[1];
                return '+' + area_code + ' ' + number.replace(/(\d{3})(\d{3})(\d{4})/, "$1 $2 $3");
            }
            return phoneString;
        }
        return {
            customerList,
            totalCustomerCount,
            currentLoadedCustomerCount,
            tableColumnsDef,
            formatPhone,
            loadMoreCustomer,
        }
    }
}
</script>
<style scoped>
    .fw-16{
        font-size: 16px !important;
    }
    .fw-14{
        font-size: 14px !important;
    }
    .trow{
        transition: background-color 300ms ease-out;
        cursor: pointer;
        display: table-row;
        min-height: 65px;
    }
    .trow:hover,
    .trow.multi{
        transition: background-color 300ms ease-out;
        background: #F8F8F8;
    } 
    tr th:first-child,
    tr td:first-child{
        padding: 0.5rem 0.5rem 0.5rem 1rem;
    }   
    tr th:last-child,
    tr td:last-child{
        padding: 0.5rem 1rem 0.5rem 0.5rem;
    }   
    th span{
        font-family: 'Gotham Rounded';
        font-style: normal;
        font-weight: 400;
        font-size: 14px;
        line-height: 140%;
        color: #868686;        
    }
    td span.b2b{
        font-family: "Gotham Rounded";
        font-size: 12px;
        line-height: 14px;
        background: rgba(212, 221, 247, 0.7);
        color: #4E58E7;
        padding: 5px 25px;
    }
    td span.b2c{
        font-family: "Gotham Rounded";
        font-size: 12px;
        line-height: 14px;
        color: #47454B;
        background: #F8F8F8;
        padding: 5px 25px;
    }
   /*list transitions*/
    .list-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .list-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .list-enter-active{
        transition: all 1s ease;
    }

    .list-leave-from{
        opacity: 1;
        transform: scale(1);
    }
    .list-leave-to{
        opacity: 0;
        transform: scale(0.6);
    }
    .list-leave-active{
        transition: all 1s ease;
        position: absolute;
        width: 100%;
    }
    .list-move{
        transition:all 0.9s ease;
    } 
 
</style>