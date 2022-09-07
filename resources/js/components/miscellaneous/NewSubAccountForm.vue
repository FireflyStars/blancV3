<template>

<transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
    <div v-if="show" class="bmodal-overlay">
        <div class="bmodal-container" id="myModal" v-if="show">
            <i class="icon-close" @click="close"></i>
            <div class="confirmation-title">
                <span class="subtitle"> Create a SubAccount </span>
            </div>

            <div class="page-section">

                    <div class="row mt-3">
                        <div class="d-flex justify-content-between">
                            <div class="form-group col-6 m-0" style="padding-right: 10px;">
                                <label class="form-label d-block m-0" for="last_name"> Last name *</label>
                                <input type="text" v-model="formModal.lastName" class="form-control custom-input" placeholder="Last name">
                            </div>
                            <div class="form-group col-6 m-0">
                                <label class="form-label d-block m-0" for="first_name"> First name *</label>
                                <input type="text" v-model="formModal.firstName" class="form-control custom-input" placeholder="First name">
                            </div>
                        </div>   
                    </div>

                    <div class="row mt-3">
                        <div class="d-flex justify-content-between">
                            <div class="form-group col-6 m-0" style="padding-right: 10px;">
                                <label class="form-label d-block m-0" for="email">Email address</label>
                                <input type="text" v-model="formModal.email" class="form-control custom-input" placeholder="Email">
                            </div>
                            <div class="customer-phone w-45 form-group col-6 m-0">
                                <div>
                                    <label>Phone Number</label>
                                </div>
                                <div class="d-flex">
                                    <div class="phone-country-code">
                                        <select-options
                                            v-model="formModal.phoneCountryCode"
                                            :options="phoneCodesSorted"
                                            :width = "'100px'"
                                            :name="'phoneCountryCode'">
                                        </select-options>
                                    </div>
                                    <div class="form-group ms-2">
                                        <input type="text" v-model="formModal.phoneNumber" class="form-control custom-input">
                                    </div>
                                </div>
                            </div>
                        </div>   
                    </div>

                </div>
            <div class="confirmation-btn">
                <button class="btn btn-outline-danger body_medium" style="margin-right: 59px" @click="close">Cancel</button>
                <button class="btn btn-dark body_medium" style="" @click="CreateSubAccount">Create a SubAccount</button>
            </div>
            
        </div>
     </div>

</transition>
</template>

<script>

    import {watch ,ref} from 'vue';
    import SelectOptions from '../test/SelectOptions';
    import { phoneCountryCode as phoneCodes } from '../../static/PhoneCountryCodes';
    import {
        TOASTER_MODULE,
        TOASTER_MESSAGE
    } from "../../store/types/types";
    import { useStore } from 'vuex';

    export default {
        name: "NewSubAccountForm",
        components:{
            SelectOptions
        },
        props:{
            show_conf:{
                type:Boolean
            },
            form:{
                type:Object
            }
        },
        setup(props,context){
            const formModal = ref({
                lastName: '',
                firstName: '',
                email: '',
                phoneCountryCode: '+44',
                phoneNumber: '',
                customerId: '',
            })
            const store = useStore(); 
            const show=ref(false);
            watch(() => props.show_conf, (toval, fromval) => {
                show.value=toval;
                formModal.value.lastName =  '',
                formModal.value.firstName = '',
                formModal.value.email= '',
                formModal.value.phoneCountryCode= '+44',
                formModal.value.phoneNumber= '',
                formModal.value.customerId= ''
            });

            const close=()=>{
                context.emit('close');
                show.value=false;
            }

            const emailValidation = (email)=>{
                return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email);
            }

            const phoneCodesSorted = [...new Map(phoneCodes.map(item =>
                            [item.value, item])).values()].sort((a, b)=>{
                    return parseInt(a.value.replace(/\D/g, '')) - parseInt(b.value.replace(/\D/g, ''));
            });

            const CreateSubAccount=()=>{

                if(formModal.value.lastName == ''){
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please Enter Last Name', ttl:5, type:'danger' });
                        return;
                    }
                if(formModal.value.firstName == ''){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please Enter First Name', ttl:5, type:'danger' });
                    return;
                }    
                if(formModal.value.email != ''){
                        if( !emailValidation(formModal.value.email) ){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: 'Please enter valid email address', ttl:5, type:'danger' });
                            return;
                        }
                    }
                
                    close();   
                    props.form.linkedAccounts.push(
                        { id: 0,
                          lastname : formModal.value.lastName,
                          firstname : formModal.value.firstName,
                          name: formModal.value.lastName +','+ formModal.value.firstName, 
                          accountType: 'Sub',
                          phoneNumber: formModal.value.phoneNumber,
                          phoneCountryCode: formModal.value.phoneCountryCode,
                          phone: formModal.value.phoneNumber != "" ?'["'+formModal.value.phoneCountryCode+'|'+formModal.value.phoneNumber+'"]' : '',
                          email: formModal.value.email, 
                          date: '', 
                          spent: 0,
                          customerId:''
                         }
                    );
            }
            return {
                show,
                close,
                CreateSubAccount,
                formModal,
                emailValidation,
                phoneCodesSorted
            }
        }
    }
</script>

<style scoped>
    .confirmation-overlay{
        background:rgba(0, 0, 0,0.7);
        position: fixed;
        top: var(--mainlogoheight);
        right:0;
        height: calc(100% - var(--mainlogoheight));
        width: 684px;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .confirmation-title{
        background-color: #E0E0E0;
        min-height: 94px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .confirmation{
        position: relative;
        text-align: center;background-color: #fff;width: 545px; display: block; border-radius: 8px;
        overflow: hidden;
        padding-bottom: 24px;
         }
    .icon-close{
        top:24px;
        right: 30px;
    }
    .btn{
        min-width: 164px;
        padding-top: 17px;
        padding-bottom:17px;
    }
    .confirmation-msg{
        display: flex;
        align-items: center;
        justify-content: center;
        height: 223px;
    }
    .confirmation-msg p{
        max-width: 290px;
        margin: 0;
    }
    .confirmation-btn{
        padding: 2rem;
        justify-content: center;
        display: flex;
    }
    .page-section{
        padding: 2rem;
    }
    .form-control:focus {
    box-shadow: none !important;
    border-color: #C3C3C3;
}
</style>