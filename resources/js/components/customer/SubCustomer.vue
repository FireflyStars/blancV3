<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <div class="container-fluid h-100 bg-color p-0">
            <div class="d-flex align-content-stretch align-items-stretch flex-row hmax">
                <side-bar></side-bar>
                <div class="w-100">
                    <bread-crumb :paths="paths"></bread-crumb>
                    <div class="page-fluid cust-page m-0">
                        <div class="page-header">
                            <h1 class="d-flex align-items-center m-0">
                                New Customer 
                                <span class="d-flex align-items-center justify-content-center rounded-pill ms-3 mt-2"
                                    :class="{ 'b2c-icon': form.customerType == 'B2C', 'b2b-icon': form.customerType == 'B2B' }"
                                >
                                {{ form.customerType == 'B2B' ? 'B2B' : form.customerType == 'B2C' ? 'B2C' : '' }}
                                </span>
                            </h1>
                        </div>
                        <ul class="full-nav d-flex p-0 m-0">
                            <li class="full-nav-item title active border-right col-3 d-flex align-items-center justify-content-center"
                            >
                                <svg class="icon" width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="10.9058" cy="10" r="9" stroke="#47454B" stroke-width="2"/>
                                </svg>
                                Account details
                            </li>
                        </ul>
                        <transition name="list" appear>
                            <div class="cust-page-content m-auto pt-5">
                                <div class="account-type mb-3">
                                    <p class="mb-0 font-gilroy font-22">
                                        Sub Account
                                    </p>
                                </div>                                
                                <div class="page-section">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h3 class="title m-0">Contact details</h3>
                                        <div class="type-delivery">
                                            <select-options 
                                                v-model="form.typeDelivery" 
                                                :options="[ 
                                                    { display:'Marylebone', value: 'MARYLEBONE' }, 
                                                    { display:'Chelsea', value: 'CHELSEA' },
                                                    { display:'Notting Hill', value: 'NOTTING HILL' },
                                                    { display:'South Ken', value: 'SOUTH KEN' },
                                                    { display:'Delivery', value: 'DELIVERY' },
                                                ]"
                                                :placeholder="'Delivery Type'"
                                                :name="'typeDelivery'">
                                            </select-options>
                                        </div>
                                    </div>
                                    <div class="customer-type mt-3">
                                        <select-options 
                                            v-model="form.customerType" 
                                            :options="[ 
                                                { display:'B2B', value: 'B2B' }, 
                                                { display:'B2C', value: 'B2C' }
                                            ]"
                                            :label="'Customer Type'"
                                            :name="'customerType'">
                                        </select-options>                
                                    </div>
                                    <div class="d-flex mt-3">
                                        <div class="customer-contact w-55 d-flex justify-content-between">
                                            <div class="form-group m-0">
                                                <label class="form-label d-block m-0" for="first_name">{{ form.customerType == 'B2C' ? 'Contact' : 'Company representative' }}</label>
                                                <input type="text" v-model="form.firstName" class="form-control custom-input" placeholder="First name">
                                            </div>
                                            <div class="form-group m-0">
                                                <label class="form-label d-block m-0" for="first_name">&nbsp;</label>
                                                <input type="text" v-model="form.lastName" class="form-control custom-input" placeholder="Last name">
                                            </div>
                                        </div>
                                        <div class="customer-phone w-45">
                                            <div>
                                                <label>Phone Number</label>
                                            </div>
                                            <div class="d-flex">
                                                <div class="phone-country-code">
                                                    <select-options 
                                                        v-model="form.phoneCountryCode" 
                                                        :options="phoneCodesSorted"
                                                        :width = "'100px'"
                                                        :name="'phoneCountryCode'">
                                                    </select-options>
                                                </div>
                                                <div class="form-group ms-2">
                                                    <input type="text" v-model="form.phoneNumber" class="form-control custom-input">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="customer-email w-55 mt-3">
                                        <div class="form-group m-0">
                                            <label class="form-label d-block m-0" for="email">{{ form.customerType == 'B2C' ? "Email" : form.accountType == 'Main' ? 'Representative email address' : 'Business email address' }}</label>
                                            <input type="text" v-model="form.email" class="form-control custom-input" placeholder="Email">
                                        </div>
                                    </div>
                                </div>
                                <div class="page-section">
                                    <h3 class="title m-0">Address</h3>
                                    <div class="d-flex mt-3 justify-content-between">
                                        <div class="form-group m-0 col-5">
                                            <label for="post_code">Search postcode</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1034_1828)">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.1605 14.8985L22.5405 20.2785C22.7296 20.4677 22.8357 20.7243 22.8356 20.9919C22.8356 21.2594 22.7292 21.5159 22.54 21.705C22.3507 21.8941 22.0941 22.0003 21.8266 22.0002C21.5591 22.0001 21.3026 21.8937 21.1135 21.7045L15.7335 16.3245C14.1252 17.5702 12.1027 18.1564 10.0776 17.9639C8.05244 17.7713 6.17669 16.8145 4.83194 15.2881C3.48719 13.7617 2.77445 11.7803 2.83871 9.74705C2.90297 7.71378 3.73941 5.78136 5.17787 4.34291C6.61632 2.90445 8.54874 2.06801 10.582 2.00375C12.6153 1.93949 14.5967 2.65223 16.1231 3.99698C17.6495 5.34173 18.6063 7.21748 18.7988 9.24263C18.9913 11.2678 18.4051 13.2902 17.1595 14.8985H17.1605ZM10.8355 15.9995C12.4268 15.9995 13.9529 15.3674 15.0781 14.2421C16.2033 13.1169 16.8355 11.5908 16.8355 9.9995C16.8355 8.4082 16.2033 6.88208 15.0781 5.75686C13.9529 4.63164 12.4268 3.9995 10.8355 3.9995C9.24416 3.9995 7.71804 4.63164 6.59282 5.75686C5.4676 6.88208 4.83546 8.4082 4.83546 9.9995C4.83546 11.5908 5.4676 13.1169 6.59282 14.2421C7.71804 15.3674 9.24416 15.9995 10.8355 15.9995V15.9995Z" fill="#C3C3C3"/>
                                                    </g>
                                                    <defs>
                                                    <clipPath id="clip0_1034_1828">
                                                    <rect width="20.0009" height="20.0004" fill="white" transform="translate(2.83472 1.99976)"/>
                                                    </clipPath>
                                                    </defs>
                                                    </svg>
                                                </span>
                                                <input type="text" ref="postcode" class="form-control custom-input" v-model="form.postCode" id="addressAutocompleteRef">
                                            </div>
                                        </div>
                                        <div class="form-group col-3 m-0">
                                            <label for="customer_city">City</label>
                                            <input type="text" v-model="form.city" class="form-control custom-input">
                                        </div>
                                        <div class="form-group col-3 m-0">
                                            <label for="customer_country">Country</label>
                                            <input type="text" v-model="form.country" class="form-control custom-input">
                                        </div>
                                    </div>
                                    <div class="w-55 mt-3">
                                        <div class="form-group mb-0">
                                            <label for="customer_address1">Delivery address</label>
                                            <input type="text" v-model="form.deliveryAddress1" placeholder="Address line 1" class="form-control custom-input">
                                            <input type="text" v-model="form.deliveryAddress2" placeholder="Address line 2" class="form-control custom-input mt-3">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </transition>
                        <div class="cust-page-footer d-flex align-items-center justify-content-end">
                            <a class="btn-cancel" href="javascript:;" @click="cancel">Cancel</a>
                            <a class="btn-next" href="javascript:;" @click="createSubAccount">Create sub-account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>                
    </transition>
</template>
<script>
import {onMounted, ref} from 'vue';
import BreadCrumb from '../layout/BreadCrumb'
import SideBar from '../layout/SideBar'
import SelectOptions from '../test/SelectOptions';
import { phoneCountryCode as phoneCodes } from '../../static/PhoneCountryCodes';
import { useRoute, useRouter } from 'vue-router';
import { useStore } from 'vuex';
import { DISPLAY_LOADER, HIDE_LOADER, LOADER_MODULE, TOASTER_MESSAGE, TOASTER_MODULE } from '../../store/types/types';

export default {
    name: 'SubCustomer',
    components:{
        BreadCrumb,
        SideBar,
        SelectOptions
    },
    setup(){
        const store = useStore();
        const router = useRouter();
        const postcode = ref(null);
        const form = ref({
            mainId: '',
            customerType: 'B2C',
            typeDelivery: 'DELIVERY',
            firstName: '',
            lastName: '',
            phoneCountryCode: '+44',
            phoneNumber: '',
            email: '',
            postCode: '',
            city: '',
            state: '',
            county: '',
            country: '',
            customerLat: '',
            customerLon: '',
            deliveryAddress1: '',
            deliveryAddress2: '',
            // customerNote: '',
        });
        const paths=ref([
            { name:'Customer', route:'Customer'},
            { name:'New Customer', route:'NewCustomer'}
        ]);        
        const phoneCodesSorted = [...new Map(phoneCodes.map(item =>
                        [item.value, item])).values()].sort((a, b)=>{
                return parseInt(a.value.replace(/\D/g, '')) - parseInt(b.value.replace(/\D/g, ''));
        });
        onMounted(()=>{
            if(localStorage.getItem('CustomerID')){
                form.value.mainId = localStorage.getItem('CustomerID');
                localStorage.removeItem('CustomerID');
            }
            setTimeout(() => {
                const customerAddress = new google.maps.places.Autocomplete(postcode.value);
                customerAddress.addListener("place_changed", () => {
                    const place = customerAddress.getPlace();
                    form.value.customerLat = place.geometry.location.lat();
                    form.value.customerLon = place.geometry.location.lng();
                    setCustomerAddress(place.address_components);
                });  
            }, 1);            
        })
        const setCustomerAddress = ( address_components )=>{
            address_components.forEach(component => {
                const type = component.types[0];
                if(type == "postal_code"){
                    form.value.postCode = component.long_name
                }else if(type == "country"){
                    form.value.country = component.long_name
                }else if(type == "locality"){
                    form.value.city = component.long_name
                }else if(type == "administrative_area_level_1"){
                    form.value.state = component.long_name
                }else if(type == "street_number"){
                    // form.value.deliveryAddress1 = component.long_name
                }
            });
        }        
        const cancel = ()=>{
            router.push({
                name:'NewCustomer'
            });
        }
        const createSubAccount = ()=>{
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Creating Sub Account...']);
            axios.post('create-sub-account', form.value).then((res)=>{
                localStorage.setItem('subCustomerInfo', JSON.stringify(res.data));    
                router.push({
                    name:'NewCustomer'
                });
            }).catch((errors)=>{
                Object.values(errors.response.data).forEach((item)=>{
                    console.log(item);
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: item[0], ttl:5, type:'danger' });
                });
            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            })
        }
        return{
            form,
            paths,
            phoneCodesSorted,
            cancel,
            createSubAccount,
            postcode
        }
    }
}
</script>
<style lang="scss" scoped>
.hmax{
    padding-top: 0;
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
.page-header{
  height: 80px;
  padding: 20px 70px;
  h1{
      font-family: 'Gilroy';
      font-style: normal;
      font-weight: 800;
      font-size: 36px;
      line-height: 106%;
      color: #000000;
      .b2c-icon,
      .b2b-icon{
        width: 50px;
        height: 25px;
        font-family: 'Gotham Rounded';
        font-style: normal;
        font-weight: 400;
        font-size: 12px;
        line-height: 140%;        
        background: #47454B;
        color: #FFF;
      }
      .b2b-icon{
        background: #4E58E7;
      }
  }
}
.full-nav{
  height: 70px;
  border-top: 1px solid #C3C3C3;
  .full-nav-item{
      cursor: pointer;
      position: relative;
      .icon{
          margin-right: 30px;
      }
      &::after{
          content: "";
          position: absolute;
          bottom: 0;
          width: 100%;
          height: 1px;
          background: #C3C3C3;
      }
      &.active,
      &:hover{
          background: rgba(217, 237, 210, 0.2);
          transition: background .3s ease-in-out;
      }
      &.active::after,
      &:hover::after{
          height: 4px;
          background: #42A71E;
          transition: background .3s ease-in-out;
      }
  }
  .border-right{
      border-right: 1px solid #C3C3C3;
  }
}
.cust-page-content{
  width: 1000px;
  margin-top: 3.125rem;
  .account-type{
      min-width: 12rem;
      width: max-content;
      .border-none{
          border: none;
          outline: none;
      } 
      .font-gilroy{
        font-family: 'Gilroy';
        font-weight: 800;
      } 
      .font-22{
        font-size: 22px;
        line-height: 110%;
      }
  }
  .title{
    font-family: 'Gilroy';
    font-style: normal;
    font-weight: 800;
    font-size: 1.375rem;
    line-height: 110%;
    label{
        font-family: 'Gotham Rounded';
        font-style: normal;
        font-weight: 400;
        font-size: 14px;
        line-height: 140%;
    }
  }
    span.chkbox {
        border: solid #868686;
    }  
    .company-legal-name{
        width: 325px;
    }
  .page-section{
    padding: 1.875rem 5rem 1.875rem;
    background: #FFFFFF;
    box-shadow: 0px 0px 4px rgba(80, 80, 80, 0.2);
    border-radius: 4px;
    margin-bottom: 30px;
    // input[type="text"]{
        
    // }
    input[type="text"]:focus,
    input[type="tel"]:focus,
    input[type="email"]:focus{
        outline: 2px #000000 solid;
        border-color: #000000;
        box-shadow: none;
    }
    .type-delivery{
        width: 200px;
    }
    .customer-type,
    .payment-method{
        width: 220px;
    }
    .w-75{
        width: 75%;
    }
    .w-55{
        width: 55%;
    }
    .w-45{
        width: 45%;
        padding-left: 2.5rem;
    }
  }
}
.input-group-text{
    background-color: white;
    border-right: none;
}
.input-group{
    input{
        border-left: none;
    }
}
.cust-page-footer{
  padding: 1.563rem;
  .btn-cancel{
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: 400;
    font-size: 1rem;
    line-height: 19px;
    color: #47454B;
    margin-right: 50px;
  }
  .btn-next{
    padding: 15px 60px;
    background: #47454B;
    border-radius: 4px;
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 140%;
    color: #FFFFFF;
    text-decoration: none;
  }
}
</style>