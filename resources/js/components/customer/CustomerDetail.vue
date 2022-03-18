<template>
    <div class="container detail-panel" v-if="showCustomerDetail">
        <div class="detail-progressbar" :class="loaderclass"></div>
        <div class="w-100" v-if="CUSTOMER.name != ''">
            <div class="detail-header d-flex align-items-center justify-content-between">
                <div class="detail-title">
                    {{ CUSTOMER.name }}
                </div>
                <div class="detail-close-section d-flex align-items-center">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.2976 16H1.021V8.17017H1.70185V15.3191H13.6167V8.17017H14.2976V16Z" fill="white"/>
                        <path d="M13.5149 8.61277C12.5202 8.61277 11.7106 7.80358 11.7106 6.80851H12.3915C12.3915 7.42774 12.8953 7.93192 13.5149 7.93192C14.1341 7.93192 14.6383 7.42809 14.6383 6.80851V5.53192L12.0511 0.680851H3.26809L0.680851 5.53192V6.80851C0.680851 7.42774 1.18468 7.93192 1.80426 7.93192C2.42383 7.93192 2.92766 7.42809 2.92766 6.80851H3.60851C3.60851 7.80323 2.79898 8.61277 1.80426 8.61277C0.809532 8.61277 0 7.80323 0 6.80851V5.3617L2.85957 0H12.4596L15.3191 5.3617V6.80851C15.3191 7.80323 14.51 8.61277 13.5149 8.61277Z" fill="white"/>
                        <path d="M4.73199 8.61285C3.73727 8.61285 2.92773 7.80366 2.92773 6.80859H3.60859C3.60859 7.42783 4.11241 7.932 4.73199 7.932C5.35156 7.932 5.85539 7.42817 5.85539 6.80859H6.53625C6.53625 7.80332 5.72671 8.61285 4.73199 8.61285Z" fill="white"/>
                        <path d="M7.65972 8.61285C6.665 8.61285 5.85547 7.80366 5.85547 6.80859H6.53632C6.53632 7.42783 7.04015 7.932 7.65972 7.932C8.2793 7.932 8.78313 7.42817 8.78313 6.80859H9.46398C9.46398 7.80332 8.65445 8.61285 7.65972 8.61285Z" fill="white"/>
                        <path d="M10.587 8.61285C9.59225 8.61285 8.78271 7.80366 8.78271 6.80859H9.46357C9.46357 7.42783 9.96739 7.932 10.587 7.932C11.2062 7.932 11.7104 7.42817 11.7104 6.80859H12.3912C12.3912 7.80332 11.5817 8.61285 10.587 8.61285Z" fill="white"/>
                        <path d="M12.2555 13.9575H3.06396V7.82983H3.74482V13.2766H11.5746V7.82983H12.2555V13.9575Z" fill="white"/>
                        <path d="M10.964 11.5038L9.77246 10.3123L9.29103 10.7937L10.4826 11.9852L10.964 11.5038Z" fill="white"/>
                        <path d="M9.94263 12.1846L7.72998 9.97192L7.24855 10.4534L9.4612 12.666L9.94263 12.1846Z" fill="white"/>
                    </svg>
                    <span class="cust-location-name ms-1">{{ CUSTOMER.location }}</span>
                    <svg class="detail-close ms-4" width="12" height="12" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" @click="close">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.78812 0.297179C1.3976 -0.0953162 0.764438 -0.0953162 0.373917 0.297179C-0.0166053 0.689674 -0.0166053 1.32604 0.373916 1.71853L5.58834 6.9593L0.292891 12.2815C-0.0976305 12.674 -0.0976304 13.3104 0.292891 13.7029C0.683413 14.0954 1.31657 14.0954 1.7071 13.7029L7.00254 8.38065L12.293 13.6978C12.6835 14.0903 13.3166 14.0903 13.7072 13.6978C14.0977 13.3053 14.0977 12.6689 13.7072 12.2765L8.41675 6.9593L13.6261 1.72358C14.0167 1.33109 14.0167 0.694726 13.6261 0.302231C13.2356 -0.0902646 12.6025 -0.0902643 12.2119 0.302231L7.00254 5.53795L1.78812 0.297179Z" fill="white"/>
                    </svg>  
                </div>
            </div>
            <div class="detail-body">
                <div class="private-info d-flex flex-wrap">
                    <div class="private-info-left">
                        <p class="email mb-0">{{ CUSTOMER.email }}</p>
                        <p class="phone mb-0">{{ formatPhone(CUSTOMER.phone) }}</p>
                    </div>
                    <div class="private-info-right">
                        <div class="total d-flex">
                            <div class="col-4 px-2 text-end">£{{ CUSTOMER.total_spent ? CUSTOMER.total_spent : 0 }}</div>
                            <div class="col-8 px-2 d-flex align-items-center">Total Spent</div>
                        </div>
                        <div class="total d-flex">
                            <div class="col-4 px-2 text-end">{{ CUSTOMER.total_count }}</div>
                            <div class="col-8 px-2 d-flex align-items-center">Orders</div>
                        </div>
                    </div>
                </div>
                <div class="account-info d-flex mt-3">
                    <div class="col-6">
                        <div class="d-flex mb-2">
                            <div class="col-6">
                                <p class="m-0">Cust Type</p>
                            </div>
                            <div class="col-6 px-2">
                                <div class="cust-type-icon rounded-pill">
                                    {{ CUSTOMER.cust_type }}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex mb-2">
                            <div class="col-6">
                                <p class="m-0">Account Type</p>
                            </div>
                            <div class="col-6 px-2">
                                <div class="account-type-icon rounded-pill">
                                    {{ CUSTOMER.account_type }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex mb-2">
                            <div class="col-6">
                                <p class="m-0">Bookings</p>
                            </div>
                            <div class="col-6 px-2">
                                <div class="booking-icon rounded-pill ms-auto d-flex align-items-center justify-content-center">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" v-if="CUSTOMER.booking == 'Recuring'">
                                        <path d="M5.37598 11.5244C5.37598 15.1009 8.15655 18.0002 11.5866 18.0002C14.2001 18.0002 16.4366 16.3169 17.3532 13.9334" stroke="#42A71E" stroke-linecap="round"/>
                                        <path d="M4 12.2178L5.37613 10.7829L6.75227 12.2178" stroke="#42A71E" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M17.624 11.4756C17.624 7.89908 14.8435 4.99976 11.4134 4.99976C8.79991 4.99976 6.56343 6.68306 5.64679 9.06664" stroke="#42A71E" stroke-linecap="round"/>
                                        <path d="M19 10.7822L17.6239 12.2171L16.2477 10.7822" stroke="#42A71E" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    {{ CUSTOMER.booking }}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="col-6">
                                <p class="m-0">Spend Category</p>
                            </div>
                            <div class="col-6 px-2">
                                <div class="category-icon rounded-pill ms-auto">
                                    Frequent
                                </div>
                            </div>
                        </div>                    
                    </div>
                </div>
                <div class="toggle-panel" :class="{ active: selected_panel =='current_orders' }" >
                    <div class="toggle-panel-header" @click="selectPanel('current_orders')">
                        <h4 class="m-0">Current Orders</h4>
                    </div>
                    <div class="toggle-panel-content" :class="{ 'd-none': selected_panel != 'current_orders' }">

                    </div>
                </div>            
                <div class="toggle-panel" :class="{ active: selected_panel =='past_orders' }" >
                    <div class="toggle-panel-header" @click="selectPanel('past_orders')">
                        <h4 class="m-0">Past Orders</h4>
                    </div>
                    <div class="toggle-panel-content" :class="{ 'd-none': selected_panel != 'past_orders' }">

                    </div>
                </div>            
                <div class="toggle-panel" :class="{ active: selected_panel =='payment_method' }" >
                    <div class="toggle-panel-header" @click="selectPanel('payment_method')">
                        <h4 class="m-0">Payment Method</h4>
                    </div>
                    <div class="toggle-panel-content" :class="{ 'd-none': selected_panel != 'payment_method' }">

                    </div>
                </div>            
                <div class="toggle-panel" :class="{ active: selected_panel =='preferences' }" >
                    <div class="toggle-panel-header" @click="selectPanel('preferences')">
                        <h4 class="m-0">Preferences</h4>
                    </div>
                    <div class="toggle-panel-content" :class="{ 'd-none': selected_panel != 'preferences' }">

                    </div>
                </div>            
                <div class="toggle-panel" :class="{ active: selected_panel =='customer_notes' }" >
                    <div class="toggle-panel-header" @click="selectPanel('customer_notes')">
                        <h4 class="m-0">Customer notes</h4>
                    </div>
                    <div class="toggle-panel-content" :class="{ 'd-none': selected_panel != 'customer_notes' }">

                    </div>
                </div>            
            </div>
            <div class="detail-footer">
                <div class="d-flex col-12 p-0">
                    <div class="col-6 p-0 d-flex justify-content-between">
                        <button class="detail-btn detail-btn-question text-center" @click="question">
                            ?
                        </button>
                        <button class="detail-btn detail-btn-archive text-center" @click="archiveCustomer">
                            Archive
                        </button>
                    </div>
                    <div class="col-6 p-0 text-end">
                        <button class="detail-btn detail-btn-edit text-center" @click="editCustomer">
                            Edit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { 
  CUSTOMER_MODULE,
  SET_CURRENT_SELECTED_CUSTOMER,
  GET_CURRENT_SELECTED_CUSTOMER,
  SET_CUSTOMER_DETAIL,
  LOAD_CUSTOMER_DETAIL,
  GET_CUSTOMER_DETAIL,
  GET_LOADER_CLASS,
  TOASTER_MESSAGE,
  TOASTER_MODULE
} from '../../store/types/types';
import { ref, computed, onMounted, nextTick, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useStore } from 'vuex';

export default {
    name: "CustomerDetail",
    components:{

    },
    setup(){
        const store = useStore();
        const route = useRoute();
        const router= useRouter();
        const selected_panel = ref('');
        const selectPanel = (panelName)=>{
            if(selected_panel.value == panelName){
                selected_panel.value = '';
            }else{
                selected_panel.value = panelName;
            }
        }
        const close = ()=>{
            store.dispatch(`${CUSTOMER_MODULE}${SET_CURRENT_SELECTED_CUSTOMER}`,'');
            store.commit(`${CUSTOMER_MODULE}${SET_CUSTOMER_DETAIL}`, { 
                name: ''
            });            
            router.push({
                name: 'Customer'
            });
        }
        const CURRENT_SELECTED=computed(()=>{
            return store.getters[`${CUSTOMER_MODULE}${GET_CURRENT_SELECTED_CUSTOMER}`];
        });        
        if(CURRENT_SELECTED.value == '' && route.params.customer_id >0){
            store.dispatch(`${CUSTOMER_MODULE}${SET_CURRENT_SELECTED_CUSTOMER}`,route.params.customer_id);
        }   
        
        const showCustomerDetail = computed(()=>{
            return CURRENT_SELECTED.value !='' && route.params.customer_id > 0;
        })        
        const CUSTOMER = computed( () =>{
            return store.getters[`${CUSTOMER_MODULE}${GET_CUSTOMER_DETAIL}`];
        })
        const editCustomer =()=>{
            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:' Edit is not implemented yet.',ttl:5,type:'success'});
        }
        const archiveCustomer =()=>{
            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:' Archive is not implemented yet.',ttl:5,type:'success'});
        }
        const question =()=>{
            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:' Question is not implemented yet.',ttl:5,type:'success'});
        }
        if(showCustomerDetail) {
            nextTick(() => {
                store.dispatch(`${CUSTOMER_MODULE}${LOAD_CUSTOMER_DETAIL}`, CURRENT_SELECTED.value).catch((error)=>{
                    if(typeof error.response!="undefined")
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'});
                });;
            });
        }          
        onMounted(()=>{
        })
        const formatPhone = (phoneString)=>{
            if(phoneString !="--"){
                var phone = phoneString.split('"')[1];
                var area_code = phone.split("|")[0];
                var number = phone.split("|")[1];
                if(phone.split("|").length > 1)
                    return '+' + area_code.replace(/\D/g, '') + ' ' + number.replace(/ /g, '').replace(/(\d{3})(\d{3})(\d{3,4})/, "$1 $2 $3");
                else
                    return phone.replace(/\D/g, '').replace(/(\d{2})(\d{3})(\d{3})(\d{3,4})/, "+$1 $2 $3 $3");
            }
            return phoneString;
        }        
        return{
            route,
            selected_panel,
            CUSTOMER,
            showCustomerDetail,
            formatPhone,
            selectPanel,
            close,
            editCustomer,
            archiveCustomer,
            question,
            loaderclass: computed(()=>{
                return store.getters[`${CUSTOMER_MODULE}${GET_LOADER_CLASS}`];
            }),
        }
    }   
}
</script>
<style lang="scss">
.detail-panel *{
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: 400;
}
.detail-panel{
    max-width: 684px;
    background: #FFF;
    height: calc(100% - var(--mainlogoheight));
    position: fixed;
    top: var(--mainlogoheight);
    overflow-y: auto;
    right: 0;
    z-index: 10001;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.18);
    padding: 0;
    .detail-header{
        height: 65px;
        background: #47454B;
        padding: 20px 30px;
        .detail-title{
            font-size: 22px;
            line-height: 110%;
            color: #FFFFFF;
        }
        .cust-location-name{
            font-size: 16px;
            line-height: 140%;
            color: #F8F8F8;
        }
        .detail-close{
            cursor: pointer;
        }
        .detail-close:hover{
            transform: scale(1.3, 1.3);
            transition: transform 0.5s ease-out;
        }
    }
    .detail-body{
        padding: 20px;
        .private-info{
            height: 65px;
            .private-info-left{
                width: 60%;
                .email{
                    font-size: 24px;
                    line-height: 140%;
                    color: #000000;
                }
                .phone{
                    font-size: 14px;
                    line-height: 140%;
                }
            }
            .private-info-right{
                width: 40%;
                padding: 5px 0;
                border-left: solid 3px #42A71E;
                background: #ECECEC;
                .total div:first-child{
                    font-size: 20px;
                    line-height: 140%;
                    color: #47454B;
                }
                .total div:last-child{
                    font-size: 16px;
                    line-height: 140%;
                    color: #47454B;
                }
            }
        }
        .account-info{
            p{
                font-family: 'Gotham Rounded Book';
                font-size: 16px;
                line-height: 20px;
                color: #868686;
            }
            .cust-type-icon,
            .account-type-icon{
                padding: 4px 27.5px;
                font-size: 12px;
                line-height: 140%;
                width: fit-content;
            }
            .cust-type-icon{
                background: #47454B;
                color: #FFFFFF;
            }
            .account-type-icon{
                background: #E0E0E0;
                color: #868686;
            }
            .booking-icon,
            .category-icon{
                width: 114px;
                text-align: center;
                font-size: 12px;
                line-height: 14px;
            }
            .booking-icon{
                padding: 2px 16px;
                background: rgba(66, 167, 30, 0.2);
                color: #42A71E;
            }
            .category-icon{
                padding: 5px 16px;
                background: #4E58E7;
                color: #FFFFFF;
            }
        }
        .toggle-panel{
            background: #F8F8F8;
            border: 0.5px solid #C3C3C3;
            box-sizing: border-box;
            border-radius: 6px;
            margin-top: 20px;        
            .toggle-panel-header{
                padding: 20px;
                cursor: pointer;
                h4{
                    font-family: 'Gilroy';
                    font-style: normal;
                    font-weight: 800;
                    font-size: 22px;
                    line-height: 110%;
                    color: #47454B;
                }
            }
            .toggle-panel-header:hover{
                background: #F8F8F8;
                border-radius: 6px;
            }
            .toggle-panel-content{
                padding: 0 30px 40px 30px; 
            }
        }
        .toggle-panel.active{
            background: #FFFFFF;
        }
    }
    .detail-footer{
        padding: 30px 20px;
        .detail-btn{
            cursor: pointer;
            font-size: 16px;
            line-height: 140%;        
            padding: 15px 0;
            border-radius: 4px;
            background: transparent;
            border: none;
            outline: none;
        }
        .detail-btn-question{
            border: 1px solid #47454B;
            width: 140px;
        }
        .detail-btn-archive{
            border: 1px solid #EB5757;
            color: #EB5757;
            width: 130px;
        }
        .detail-btn-edit{
            background: #42A71E;
            color: #FFFFFF;
            width: 225px;
        }
    }
}
    .detail-progressbar{
        background: #FFF;
        height: 6px;
        width: 100%;
        display: block;
        position: absolute;
    }
    .detail-progressbar:after{
        background: #42A71E;
        position: absolute;
        width: 0%;
        left: 0;
        content: "";
        height:6px;
    }

    .animate40:after{
        transition: width 2s ease;
        width: 40%;
        animation: pulse 500ms infinite;
    }
    .animate100:after{
        animation: ani100 0.5s ease forwards;
    }
    .detail-progressbar.animate40.animate100{
        display: none;
    }
    @keyframes ani100 {
        50%{
            width: 50%;
            opacity: 1;
        }
        60%{
            width: 50%;
            opacity: 1;
        }
       99%{
           width: 99%;
            opacity: 1;
        }
        100%{
            width: 100%;
            opacity: 0;
        }
    }

    @keyframes pulse {
        0%{
            opacity: 0.7;
        }
        100%{
            opacity: 1;
        }
    }
</style>