<template>
    <Teleport to="body">
        <div class="search-layer d-flex align-items-center justify-content-center position-fixed" v-if="showSearchPanel">
            <transition name="list" appear>
                <div class="search-panel m-auto bg-white">
                    <div class="search-header text-center position-relative">
                        Search {{ accountType == 1 ? 'Master' : '' }} Customer
                        <svg @click="closeSearchPanel" class="close-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.78812 5.2973C6.3976 4.90481 5.76444 4.90481 5.37392 5.2973C4.98339 5.6898 4.98339 6.32616 5.37392 6.71865L10.5883 11.9594L5.29289 17.2816C4.90237 17.6741 4.90237 18.3105 5.29289 18.703C5.68341 19.0955 6.31657 19.0955 6.7071 18.703L12.0025 13.3808L17.293 18.6979C17.6835 19.0904 18.3166 19.0904 18.7072 18.6979C19.0977 18.3054 19.0977 17.6691 18.7072 17.2766L13.4167 11.9594L18.6261 6.7237C19.0167 6.33121 19.0167 5.69485 18.6261 5.30235C18.2356 4.90986 17.6025 4.90986 17.2119 5.30235L12.0025 10.5381L6.78812 5.2973Z" fill="black"/>
                        </svg>
                    </div>
                    <div class="search-body">
                        <div class="search-input rounded-pill position-relative">
                            <svg class="position-absolute search-icon" width="24" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_550_21813)">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M16.8257 14.8987L22.2057 20.2787C22.3948 20.468 22.501 20.7246 22.5009 20.9921C22.5008 21.2596 22.3945 21.5161 22.2052 21.7052C22.016 21.8943 21.7594 22.0005 21.4919 22.0004C21.2244 22.0003 20.9678 21.894 20.7787 21.7047L15.3987 16.3247C13.7905 17.5704 11.768 18.1566 9.74287 17.9641C7.71772 17.7716 5.84198 16.8148 4.49723 15.2884C3.15248 13.7619 2.43973 11.7806 2.504 9.74729C2.56826 7.71402 3.4047 5.7816 4.84315 4.34315C6.2816 2.9047 8.21402 2.06826 10.2473 2.004C12.2806 1.93973 14.2619 2.65248 15.7884 3.99723C17.3148 5.34198 18.2716 7.21772 18.4641 9.24287C18.6566 11.268 18.0704 13.2905 16.8247 14.8987H16.8257ZM10.5007 15.9997C12.092 15.9997 13.6182 15.3676 14.7434 14.2424C15.8686 13.1172 16.5007 11.591 16.5007 9.99974C16.5007 8.40845 15.8686 6.88232 14.7434 5.7571C13.6182 4.63189 12.092 3.99974 10.5007 3.99974C8.90944 3.99974 7.38332 4.63189 6.2581 5.7571C5.13289 6.88232 4.50074 8.40845 4.50074 9.99974C4.50074 11.591 5.13289 13.1172 6.2581 14.2424C7.38332 15.3676 8.90944 15.9997 10.5007 15.9997Z" fill="#47454B"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_550_21813">
                                <rect width="20.0009" height="20.0004" fill="white" transform="translate(2.5 2)"/>
                                </clipPath>
                                </defs>
                            </svg>
                            <input type="text" v-model="query" ref="queryElement" placeholder="Enter to search"  @keyup.enter="searchCustomer" class="w-100 search-control">
                        </div>
                        <div class="w-100 m-0 p-0 search-result mt-3">
                            <div class="customer-item" v-for="(customer, index) in customers" :key="index" @click="selectCustomer(customer)">
                                <div class="d-flex justify-content-between">
                                    <p class="customer-name mb-0 text-black text-capitalize">{{ customer.name }}</p>
                                    <span class="cust-type-icon rounded-pill" :class="customer.customerType">{{ customer.customerType }}</span>
                                </div>
                                <div class="d-flex">
                                    <p class="mb-0">{{ formatPhone(customer.phone) }}</p><p class="ms-3 mb-0 text-lowercase">{{ customer.email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </Teleport>
</template>
<script>

import {nextTick, ref} from 'vue';
import axios from 'axios';
import {
    LOADER_MODULE,
    DISPLAY_LOADER,
    HIDE_LOADER,
} from '../../store/types/types';
import { useStore } from 'vuex';

export default {
    name: 'Search',
    props: {
        modelValue: Object,
        customerID : String
    },
    emits: ['selectedSubAccount'],
    components:{
    },
    setup(props, { emit }){
        const store = useStore();
        const customers = ref([
        ]);
        const query = ref('');
        const queryElement = ref(null);
        const accountType = ref(0); // sub account
        const closeSearchPanel = ()=>{
            showSearchPanel.value = !showSearchPanel.value;
        }
        const searchCustomer = async ()=>{
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Search '+ (accountType.value == 1 ? 'Master' : '') +' customers...']);
            await axios.post('/search-customer', {
                query: query.value, accountType: accountType.value , customerID : props.customerID
            }).then((response)=>{
                customers.value = response.data;
            }).catch((error)=>{
                console.log(error)
            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            })
        }
        const formatPhone = (phoneString)=>{
            if(phoneString != ""){
                var phone = phoneString.split('"')[1];
                if(phone.split("|").length > 1){
                    var area_code = phone.split("|")[0];
                    var number = phone.split("|")[1];
                    return '+' + area_code.replace(/\D/g, '') + ' ' + number.replace(/ /g, '').replace(/(\d{3})(\d{3})(\d{3,4})/, "$1 $2 $3");
                }else
                    return phone.replace(/\D/g, '').replace(/(\d{2})(\d{3})(\d{3})(\d{3,4})/, "+$1 $2 $3 $3");
            }else{
                return '--';
            }
        }  
        const showSearchPanel = ref(false);
        const openSearchPanel = (type)=>{
            query.value = "";
            accountType.value = type;
            showSearchPanel.value = !showSearchPanel.value;
            nextTick(()=>{
                queryElement.value.focus();
            })
        }  
        const selectCustomer = (customer)=>{
            showSearchPanel.value = false;
            emit('selectedSubAccount', customer);
        }
        return {
            query,
            customers,
            showSearchPanel,
            queryElement,
            accountType,
            searchCustomer,
            closeSearchPanel,
            openSearchPanel,
            formatPhone,
            selectCustomer
        }
    }

}
</script>
<style lang="scss" scoped>
/* width */
::-webkit-scrollbar {
  width: 9px;
}
/* Track */
::-webkit-scrollbar-track {
  background: #E0E0E0; 
}
/* Handle */
::-webkit-scrollbar-thumb {
  background: #47454B; 
  border-radius: 6px;
}

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
.search-layer{
    width: 100%;
    height: 100%;
    top: 0;
    z-index: 30;
    background: rgba(0, 0, 0, 0.3);
    .search-panel{
        width: 660px;
        height: 450px;
        .search-header{
            height: 100px;
            background: #F8F8F8;
            padding: 45px 0;
            font-family: 'Gilroy';
            font-style: normal;
            font-weight: 800;
            font-size: 22px;
            line-height: 110%;            
            .close-icon{
                position: absolute;
                cursor: pointer;
                top: 20px;
                right: 23px;
            }
        }
        .search-body{
            padding: 20px 80px;
            height: 350px;
            overflow-y: scroll;
            .search-input{
                height: 40px;
                width: 100%;
                border: 1px solid #E0E0E0;
                padding: 5px 30px 5px 65px;
                .search-control,
                .search-control:focus{
                    height: 100%;
                    border: none;
                    outline: none;
                }
                .search-icon{
                    left: 25px;
                    top: 8px;
                }
            }
            .customer-item{
                background: #F8F8F8;
                border-radius: 5px;
                padding: 15px 10px 15px 20px;
                margin-bottom: 5px;
                cursor: pointer;
                p{
                    font-family: 'Gotham Rounded Medium';
                    font-style: normal;
                    font-weight: 400;
                    font-size: 16px;
                    line-height: 140%;
                    color: #868686;
                }
                .cust-type-icon{
                    width: 77px;
                    height: 22px;
                    padding: 4px 25px;
                    font-size: 12px;
                    line-height: 14px;
                }
                .B2B{
                    background: rgba(212, 221, 247, 0.7);
                    color: #4E58E7;
                }
                .B2C{
                    background: rgba(234, 214, 247, 0.7);
                    color: #9E44F2;
                }
            }
            .customer-item:hover{
                background: rgba(0, 0, 0, .3);
                transition: background .3s ease-in;
            }
        }
    }
}
</style>