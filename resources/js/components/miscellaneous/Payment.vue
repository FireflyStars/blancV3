<template>
    <div class="col-12" v-if="custcard" id="has_card">
        <div class="row my-3">
            <div class="col-8">
                <button id="pay_card_btn" class="save_pay_card_btn w-100" @click="effectPayment">Pay now</button>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label>Cardholder name</label>
                <input type="text" :value="custcard.cardHolderName" readonly/>
            </div>
            <div class="col-6">
                <label>Card number</label>
                <input type="text" :value="custcard.cardNumber" id="saved_card_input" readonly/>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4">
                <label>Date</label>
                <input type="text" :value="custcard.dateexpiration" readonly/>
            </div>
            <div class="col-3">
                <label>CVV</label>
                <input type="text" value="***" readonly/>
            </div>
        </div>
    </div>
     <div class="col-12" v-else>
        <!--
        <div class="row mb-2 mt-3">
            <div class="form-group mb-0 col-6 payment-method">
                <select-options
                    v-model="paymentMethod"
                    :options="[
                        { display:'Credit Card', value: 'Credit Card' },
                        { display:'BACS', value: 'BACS' },
                    ]"
                    :placeholder="'Select'"
                    :label="''"
                    :name="'paymentMethod'">
                </select-options>
            </div>
        </div>
        -->
            <div class="row" id="credit_card_div">
            <div class="credit-card col-12">

                <div class="row mb-2">
                    <div class="form-group col-12 cardholder">
                        <label for="">Cardholder name</label>
                        <input type="text" placeholder="Name" v-model="form.cardHolderName" required class="form-control">
                        <div v-if="cardErrors.cardHolderName" class="error">
                            <small>{{ cardErrors.cardHolderName }}</small>
                        </div>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="form-group col-12 carddetails">
                        <label for="">Card details</label>
                        <div class="input-group mb-0" :class="{ 'error': cardErrors.cardNumber}">
                            <span class="input-group-text">
                                <i class="credit-card-icon" :class="cardBrandClass"></i>
                            </span>
                            <input ref="cardNumInput" :class="{ 'error': cardErrors.cardNumber}" :data-error="(cardErrors.cardNumber)?true:false" v-model="form.cardDetails" type="tel" v-cardformat:formatCardNumber class="form-control" placeholder="Enter card details">
                        </div>
                        <div v-if="cardErrors.cardNumber" class="error">
                            <small>{{ cardErrors.cardNumber }}</small>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="form-group col-6 cardexpdate mb-0">
                        <label for="">Expiration date</label>
                        <input type="text" ref="cardExpInput" placeholder="mm/yy" :class="{ 'error': cardErrors.cardExpiry}" v-model="form.cardExpDate" maxlength="10" class="form-control" v-cardformat:formatCardExpiry>
                        <div v-if="cardErrors.cardExpiry" class="error">
                            <small>{{ cardErrors.cardExpiry }}</small>
                        </div>
                    </div>
                    <div class="form-group col-3 cardexpdate mb-0">
                        <label for="">CVC</label>
                        <input type="text" ref="cardCvcInput" :class="{ 'error': cardErrors.cardCvc}" placeholder="CVC" v-model="form.cardCVV" class="form-control" v-cardformat:formatCardCVC>
                        <div v-if="cardErrors.cardCvc" class="error">
                            <small>{{ cardErrors.cardCvc }}</small>
                        </div>
                    </div>
                </div>
                <div class="row mt-4 mb-2">
                    <div class="col-8">
                        <!--
                        <button class="btn btn-default w-100 py-0 save_pay_card_btn" @click="saveCardDetails">Save and pay</button>
                        -->
                        <select-options
                            v-model="saveOrPay"
                            :options="[
                                { display:'Save and pay', value: 'Save and pay' },
                                { display:'Save', value: 'Save' },
                            ]"
                            :placeholder="'Save or Pay'"
                            :label="''"
                            :name="'paymentType'">
                        </select-options>

                    </div>
                </div>

            </div>
        </div>

    </div>
</template>
<script>
import {ref,watch,inject,onMounted} from 'vue';
import {useStore} from 'vuex';
import SelectOptions from '../miscellaneous/SelectOptions.vue';
import {
    LOADER_MODULE,
    DISPLAY_LOADER,
    HIDE_LOADER,
    TOASTER_MODULE,
    TOASTER_MESSAGE,
} from '../../store/types/types';

export default {
    name: "Payment",
    components:{SelectOptions},
    props: {
        custcard: Object || null,
        order_id: String,
    },
    emits:['reload-checkout','complete-checkout'],
    setup(props,context) {
         //Payment details

         console.log(props.custcard);

            const cardFormat = inject('cardFormat');
            const paymentMethod = ref("");
            const saveOrPay = ref("");
            const store = useStore();

            const form = ref({
                cardHolderName: '',
                cardDetails: '',
                cardExpDate: '',
                cardCVV: '',
            });

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

            watch(()=>saveOrPay.value,(current_value,previous_value)=>{
                if(current_value !=''){
                    saveCardDetails(current_value);
                }
            });


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

            function effectPayment(){
                let loading_text = "Effecting payment";
                if(saveOrPay.value=='Save'){
                    loading_text = "Creating card";
                }

                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                    true,
                    loading_text+"....",
                ]);

                let params = {};

                if(!props.custcard){
                    params = form.value;
                    params['payment_type'] = saveOrPay.value;
                }else{
                    params['payment_type'] = 'Pay';
                }

                params['order_id'] = props.order_id;
                params['card_id'] = (props.custcard?props.custcard.id:null);

                axios.post('/make-payment-or-create-card',params)
                    .then((res)=>{
                        if(saveOrPay.value!='Save'){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                            {
                                message: (res.data.paid?"Payment successful":res.data.err_payment),
                                ttl: 5,
                                type:(res.data.paid?'success':'danger'),
                            });

                            if(res.data.paid){
                                async function createItems(){
                                    await delayPage(5);

                                    context.emit('complete-checkout');
                                }
                            }
                        }
                    }).catch((err)=>{
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                            {
                                message: err,
                                ttl: 5,
                                type: 'danger'
                            });
                    }).finally(()=>{
                        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);

                        if(saveOrPay.value=='Save'){
                             context.emit("reload-checkout");
                        }

                    });
            }

            function delayPage(n){
                return new Promise(function(resolve){
                    setTimeout(resolve,n*1000);
                });
            }

            return {
                paymentMethod,
                form,
                cardErrors,
                saveCardDetails,
                effectPayment,
                saveOrPay,
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

</style>

