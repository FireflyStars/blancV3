<template>
    <div class="col-12" v-if="!editcard && cust.bycard==1 && custcard && cust.OnAccount==0" id="has_card">
        <div class="row mt-3">
           <div class="col-5 payment-subtitle mb-2">Payment type</div>
           <div class="col-7 payment-subtitle mb-2">Card details</div>
        </div>
        <div class="row mb-3 align-items-center">
            <div class="col-5 payment-text">Pay as you go</div>
            <div class="col-7 payment-text"><span id="cardnum"><img src="/images/mastercard.svg"/>{{custcard.cardNumber}}</span></div>
        </div>
    </div>
<!--
    <div class="col-12" v-else-if="cust.OnAccount==1">
        <div class="row my-3">
           <div class="col-5">
                <div class="payment-subtitle mb-2">Payment type</div>
                <div class="payment-text">On Account</div>
           </div>
           <div class="col-7">
                <div class="payment-subtitle mb-2">Card details</div>
                <div class="payment-text"><span id="cardnum">{{cust.pay_name}}</span></div>
           </div>
        </div>
    </div>
-->
     <div class="col-12" v-else-if="cust.OnAccount==0 && (cust.bycard==1 && !custcard) || editcard">
            <div class="row" id="credit_card_div">
            <div class="credit-card col-12">

                <div class="row mb-3">
                    <div class="col">  <button id="save_card_details_terminal" class="w-100 py-2" :class="{sel:save_card_details_terminal}" @click="SaveCardDetailsInTerminal">Save Card Details In Terminal</button></div>
                </div>

                <div class="row mb-3">
                    <div class="form-group col-6 cardholder">
                        <label for="" class="payment-subtitle">Cardholder name</label>
                        <input type="text" placeholder="Name" v-model="form.cardHolderName" required class="form-control">
                        <div v-if="cardErrors.cardHolderName" class="error">
                            <small>{{ cardErrors.cardHolderName }}</small>
                        </div>
                    </div>

                    <div class="form-group col-6 carddetails">
                        <label for="" class="payment-subtitle">Card number</label>
                        <div class="input-group mb-0" :class="{ 'error': cardErrors.cardNumber}">
                            <!--
                            <span class="input-group-text">
                                <i class="credit-card-icon" :class="cardBrandClass"></i>
                            </span>
                            -->
                            <input ref="cardNumInput" :class="{ 'error': cardErrors.cardNumber}" :data-error="(cardErrors.cardNumber)?true:false" v-model="form.cardDetails" type="tel" v-cardformat:formatCardNumber class="form-control" placeholder="Enter card details">
                        </div>
                        <div v-if="cardErrors.cardNumber" class="error">
                            <small>{{ cardErrors.cardNumber }}</small>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="form-group col-6 cardexpdate mb-0">
                        <label for="" class="payment-subtitle">Expiration date</label>
                        <input type="text" ref="cardExpInput" placeholder="mm/yy" :class="{ 'error': cardErrors.cardExpiry}" v-model="form.cardExpDate" maxlength="10" class="form-control" v-cardformat:formatCardExpiry>
                        <div v-if="cardErrors.cardExpiry" class="error">
                            <small>{{ cardErrors.cardExpiry }}</small>
                        </div>
                    </div>
                    <div class="form-group col-3 cardexpdate mb-0">
                        <label for="" class="payment-subtitle">CVC</label>
                        <input type="text" ref="cardCvcInput" :class="{ 'error': cardErrors.cardCvc}" placeholder="CVC" v-model="form.cardCVV" class="form-control" v-cardformat:formatCardCVC>
                        <div v-if="cardErrors.cardCvc" class="error">
                            <small>{{ cardErrors.cardCvc }}</small>
                        </div>
                    </div>
                </div>
                <div class="row mt-4 mb-2 justify-content-end">
                    <div class="col-5">
                        <button id="save_card_details" class="w-100 py-2" @click="saveCardDetails('Save')">Save card</button>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <modal ref="save_card_modal">
        <template #closebtn>
            <span class="close" id="addon_modal_close" @click="closeSaveCardModal"></span>
        </template>
        <template #bheader>
            <div class="bmodal-header py-5 text-center"><h5 id="terminal_save_txt">Please insert a card in the terminal</h5></div>
        </template>
        <template #mbuttons>
            <div class="row justify-content-center">
                <div class="col-6">
                    <button class="btn btn-outline-danger w-100" id="cancelTerminalBtn" @click="cancelTerminalRequest">Cancel Terminal request</button>
                </div>
            </div>
        </template>
    </modal>
</template>
<script>
import {ref,watch,inject,onUpdated} from 'vue';
import {useStore} from 'vuex';
import SelectOptions from '../miscellaneous/SelectOptions.vue';
import {
    LOADER_MODULE,
    DISPLAY_LOADER,
    HIDE_LOADER,
    TOASTER_MODULE,
    TOASTER_MESSAGE,
} from '../../store/types/types';

import Modal from './Modal.vue';

export default {
    name: "Payment",
    components:{SelectOptions,Modal},
    props: {
        custcard: Object || null,
        order_id: String,
        cust: Object || null,
        amounttopay: Number,
    },
    emits:['reload-checkout','complete-checkout','require_save_card'],
    setup(props,context) {
         //Payment details

            const cardFormat = inject('cardFormat');
            const paymentMethod = ref("");
            const store = useStore();
            const editcard = ref(false);
            const save_card_modal = ref();
            const fetch_si_interval_id = ref();
            const cur_si_id = ref('');

            const form = ref({
                cardHolderName: '',
                cardDetails: '',
                cardExpDate: '',
                cardCVV: '',
            });
            const save_card_details_terminal=ref(false);


            const cardErrors = ref({});
            //Validation when cardholder name changes

            let name_regex = /^[a-zA-Z ]*$/;
            watch(()=>form.value.cardHolderName,(current_value,previous_value)=>{
                if(current_value.replace(/\s/g,'')=='' || !name_regex.test(current_value)){
                    cardErrors.value.cardHolderName = "Invalid cardholder name.";
                }else{
                    delete cardErrors.value.cardHolderName;
                }
            })


              // validation when the card detail changes
            watch(()=>form.value.cardDetails,(current_value, previous_value)=>{
                if(cardFormat.validateCardNumber(current_value)){
                    delete cardErrors.value.cardNumber;
                }else{
                    cardErrors.value.cardNumber = "Invalid Credit Card Number.";
                }
            })
            // validation when the card exp changes
            watch(()=>form.value.cardExpDate,(current_value, previous_value)=>{
                if(cardFormat.validateCardExpiry(current_value)){
                    delete cardErrors.value.cardExpiry;
                }else{
                    cardErrors.value.cardExpiry = "Invalid Expiration Date.";
                }
            })
            // validation when the card exp changes
            watch(()=>form.value.cardCVV,(current_value, previous_value)=>{
                if(cardFormat.validateCardCVC(current_value)){
                    delete cardErrors.value.cardCvc;
                }else{
                    cardErrors.value.cardCvc = "Invalid CVV.";
                }
            })

            /*
            watch(()=>CustomerID.value,(current_value,previous_value)=>{
                console.log('watching customerID',current_value);
                if(current_value=='' || typeof(current_value)=='undefined'){
                    card_details.value = {};
                }
            });
            */

            function validateCardDetails(){
                let err = false;
                let err_txt = [];

                let card_err_el = document.querySelectorAll('#credit_card_div .error');

                if(form.value.cardHolderName.replace(/\s/g,'')=='' || form.value.cardDetails.replace(/\s/g,'')=='' || form.value.cardExpDate.replace(/\s/g,'')=='' || form.value.cardCVV.replace(/\s/g,'')==''){
                    err = true;
                    err_txt.push("Card details missing");
                }else if(card_err_el.length > 0){
                    err = true;
                    err_txt.push("Invalid card details");
                }

                return err_txt;
            }


            function saveCardDetails(type){
                let err_txt = validateCardDetails();

                if(err_txt.length > 0){
                    err_txt.forEach(function(v,i){
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                            {
                                message: v,
                                ttl: 5,
                                type: 'danger'
                            });
                    })
                }else{
                    effectPayment(type);
                }
            }

            function effectPayment(type){
                if(type=='pay' && props.amounttopay==0){
                    console.log('no card payment');
                    return false;
                }

                let loading_text = "Effecting payment";
                if(type=='Save'){
                    loading_text = "Creating card";
                }


                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                    true,
                    loading_text+"....",
                ]);

                let params = {};
                params = form.value;

                params['order_id'] = props.order_id;
                params['card_id'] = (props.custcard?props.custcard.id:null);
                params['editcard'] = editcard.value;
                params['payment_type'] = type;
                params['amount_to_pay'] = props.amounttopay;

                axios.post('/make-payment-or-create-card',params)
                    .then((res)=>{

                        if(res.data.error_stripe){
                            let err_msg = res.data.error_stripe;

                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                                message: err_msg,
                                ttl:5,
                                type:'danger',
                                });
                        }else{
                            if(type!='Save'){
                                /*
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                                {
                                    message: (res.data.paid?"Payment successful":JSON.stringify(res.data.err_payment)),
                                    ttl: 5,
                                    type:(res.data.paid?'success':'danger'),
                                });
                                */

                               if(res.data.payment_intent){


                                    if(res.data.payment_intent.status=='succeeded'){
                                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                                        {
                                            message:"Payment successful",
                                            ttl:5,
                                            type:'success',
                                        });


                                    }else{
                                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                                        {
                                            message:"Payment unsuccessful - "+res.data.payment_intent.status,
                                            ttl:5,
                                            type:'danger',
                                        });
                                    }
                               }
                            }
                        }
                    }).catch((err)=>{
                        console.log(err);
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                            {
                                message: JSON.stringify(err),
                                ttl: 15,
                                type: 'danger'
                            });
                    }).finally(()=>{
                        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);

                        if(type=='Save'){
                             context.emit("reload-checkout");
                        }else{
                           createItems();
                        }

                    });

                    //*/
            }

            async function createItems(){
                await delayPage(5);

                context.emit('complete-checkout');
            }

            function delayPage(n){
                return new Promise(function(resolve){
                    setTimeout(resolve,n*1000);
                });
            }

            function setEditCard(val){
                editcard.value = val;
            }


            onUpdated(()=>{
                /*
                 if(props.custcard){
                    form.value.cardHolderName = props.custcard.cardHolderName;
                    //form.value.cardDetails = props.custcard.cardNumber;
                    form.value.cardExpDate = props.custcard.dateexpiration;
                }
                */
            });

            const getCurTime = ()=>{
                let date = new Date();
                return date.getHours()+":"+date.getMinutes()+":"+date.getSeconds();
            }

            const retrieveSi = async ()=>{
                try{
                    let time1 = getCurTime();
                    console.log('Before retrieving SI',time1);

                    await axios.post('/stripe-test/retrieve-si',{
                        si_id:cur_si_id.value,
                        payment:false,
                    }).then((res)=>{
                        console.log('result of setup intent',res);
                        if(res.data.card_id!=0){
                            clearInterval(fetch_si_interval_id.value);
                            save_card_modal.value.closeModal();
                            context.emit('reload-checkout');
                        }
                    }).catch((err)=>{
                        console.log(err);
                    }).finally(()=>{

                    });
                }finally{
                    let time2 = getCurTime();
                    console.log('After retrieving SI',time2);
                }

            }

            const SaveCardDetailsInTerminal=()=>{
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                    true,
                    "Please wait....",
                ]);

                save_card_details_terminal.value=!save_card_details_terminal.value;
                axios.post('/stripe-test/save-card-details',{
                    order_id:props.order_id,
                    payment:false,
                }).then((res)=>{
                    if(res.data.output_si && res.data.si){
                        let process_output = res.data.output_si;

                        cur_si_id.value = res.data.si.id;

                        if(process_output.action.status=='in_progress'){
                            save_card_modal.value.showModal();

                            fetch_si_interval_id.value = setInterval(retrieveSi,4000);
                        }
                    }else{
                        console.log(res.data.err_txt);
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                        {
                            message:(res.data.err_txt!=''?res.data.err_txt:"An error occur while processing the terminal"),
                            ttl:5,
                            type:'danger',
                        });
                    }
                }).catch((err)=>{
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                    {
                        message:JSON.stringify(err),
                        ttl:5,
                        type:'danger',
                    });

                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });

                //context.emit('require_save_card',save_card_details_terminal.value);
            }

            const closeSaveCardModal = ()=>{
                save_card_modal.value.closeModal();
                cur_si_id.value = '';
            }

            const cancelTerminalRequest = ()=>{
                clearInterval(fetch_si_interval_id.value);
                axios.post('/cancel-terminal-request',{})
                    .then((res)=>{
                        console.log(res);
                    }).catch((err)=>{

                    }).finally(()=>{
                        cur_si_id.value = '';
                        save_card_details_terminal.value = false;
                        save_card_modal.value.closeModal();
                    });
            }



            return {
                paymentMethod,
                form,
                cardErrors,
                saveCardDetails,
                effectPayment,
                editcard,
                setEditCard,
                save_card_details_terminal,
                SaveCardDetailsInTerminal,
                save_card_modal,
                closeSaveCardModal,
                cancelTerminalRequest,
            }


    },
    data(){
            return {
                cardBrand: null,
            }
        },
        computed: {
            cardBrandClass(){
                return this.getBrandClass(this.cardBrand);
            }
        },
        methods:{
            getBrandClass: (brand)=>{
                let icon = '';
                brand = brand || 'unknown';
                let cardBrandToClass = {
                    'visa': 'cc-visa',
                    'maestro': 'cc-maestro',
                    'mastercard': 'cc-mastercard',
                    'dankort': 'cc-dankort',
                    'amex': 'cc-amex',
                    'discover': 'cc-discover',
                    'dinersclub': 'cc-dinersclub',
                    'unionpay': 'cc-unionpay',
                    'jcb': 'cc-jcb',
                    'unknown': 'cc-unknown',
                };
                if (cardBrandToClass[brand]) {
                    icon = cardBrandToClass[brand];
                };
                return icon;
            }
        }
}
</script>
<style scoped>
    .credit-card label{
        color:white;
    }

    .save_pay_card_btn{
        height:37.6px;
        background: #E0E0E0;
        border:none;
        border-radius:4px;
    }

    .save_pay_card_btn:hover{
        background: #42A71E;
    }



</style>
<style lang="scss">
.form-group{
    .error{
        small{
            color: #EB5757;
        }
    }
}
input.error:focus{
    outline: none;
    border-color: #EB5757;
}

#has_card{
    label{
        color:#fff;
        font:normal 16px "Gotham Rounded";
    }

    input[type='text']{
        border:thin solid #fff;
        background:none;
        color:#fff;
        width:100%;
        padding:0.3rem 0.75rem;
        border-radius: 4px;

        &#saved_card_input{
            padding-right:30px;
            background:url('/images/mastercard.svg') no-repeat 97% center;
            background-size: auto 30px;
        }
    }
}

.payment-subtitle{
    font:normal 16px "Gotham Rounded Light";
    color:#fff;
}

.payment-text{
     font:normal 16px "Gotham Rounded";
    color:#f8f8f8;
}

#save_card_details,#save_card_details_terminal{
    font:normal 16px "Gotham Rounded";
    border:thin solid #42A71E;
    background:#fff;
    border-radius:4px;
    color:#42A71E
}


#save_card_details:hover,#save_card_details_terminal.sel{
    background: #42A71E;
    color:#fff;
}

#terminal_save_txt{
    color:#EB5757;
}

</style>

