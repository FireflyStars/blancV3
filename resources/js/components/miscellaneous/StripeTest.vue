<template>
    <div class="container-fluid h-100 bg-color">
            <main-header></main-header>
            <div
                class="row d-flex align-content-stretch align-items-stretch flex-row hmax"
                style="z-index: 100"
            >
                <side-bar></side-bar>
                <div class="col main">
                    <div class="content mt-4">
                        <h2>Stripe test</h2>
                        <div class="row">
                            <div class="col-7">
                                <div class="row py-4">
                                    <div class="col-5">
                                        <button class="btn btn-outline-success w-100" @click="listReaders">1. List Readers</button>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col-12 form-group" v-if="readers.length > 0">
                                        <div class="row" v-for="(reader,index) in readers">
                                            <div class="col-1">
                                                <input type="radio" :value="reader.id" name="eachreader" class="eachreader" v-model="selected_reader_id"/>
                                            </div>
                                            <div class="col-11">
                                                {{reader.label}} - {{reader.status}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row pt-4">
                                    <div class="col-5">
                                        <button class="btn btn-outline-success w-100" @click="selectReader">2. Select Reader</button>
                                    </div>
                                </div>
                                <div class="row pt-2 pb-4">
                                    <div class="col-12">
                                        Connected reader: {{connected_reader}}
                                    </div>
                                </div>
                                <div class="row pt-4 pb-2">
                                     <div class="col-2 text-align-right">Amount</div>
                                    <div class="col-2 form-group">
                                        <input type="text" class="form-control" v-model="payment_amount"/>
                                    </div>
                                </div>
                                <div class="row pt-2">
                                    <div class="col-5">
                                        <button class="btn btn-outline-success w-100" @click="createPaymentIntent(payment_amount)">3. Create payment intent</button>
                                    </div>
                                </div>
                                <div class="row pt-2 mt-4">
                                    <div class="col-5">
                                        <button class="btn btn-outline-danger w-100" @click="cancelPaymentIntent">4. Cancel payment intent</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-5"></div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</template>
<script>
import {ref,watch} from 'vue';
import {useStore} from 'vuex';
import SideBar from '../layout/SideBar.vue';
import MainHeader from '../layout/MainHeader.vue';
import CheckBox from './CheckBox.vue';
import {
    LOADER_MODULE,
    SET_LOADER_MSG,
    DISPLAY_LOADER,
    HIDE_LOADER,
    TOASTER_MESSAGE,
    TOASTER_MODULE,
} from '../../store/types/types';

export default {
    name:'StripeTest',
    components:{MainHeader, SideBar, CheckBox},
    setup() {
        const store = useStore();

        const terminal = ref();
        const readers = ref([]);
        const selected_reader_id = ref("");
        const selected_reader = ref();
        const connected_reader = ref("");
        const payment_amount = ref(0.30);
        const paymentIntentId = ref("");
        const paymentIntent = ref({});

        function getConnectionToken(){
            terminal.value = StripeTerminal.create({
                onFetchConnectionToken: fetchConnectionToken,
                onUnexpectedReaderDisconnect: unexpectedDisconnect,
            });
        }

        getConnectionToken();

        function unexpectedDisconnect() {
            // In this function, your app should notify the user that the reader disconnected.
            // You can also include a way to attempt to reconnect to a reader.
            console.log("Disconnected from reader")
        }

        function fetchConnectionToken() {

            // Do not cache or hardcode the ConnectionToken. The SDK manages the ConnectionToken's lifecycle.
            return fetch('/stripe-test/connection_token', { method: "POST" })
                .then(function(response) {
                return response.json();
                })
                .then(function(data) {
                return data.secret;
                });
        }

        function listReaders(){
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                true,
                "Please wait....",
            ]);

            getConnectionToken();
            var config = {simulated: false};
            let discoveredReaders = [];

            terminal.value.discoverReaders(config).then((discoverResult)=>{

                if (discoverResult.error) {
                console.log('Failed to discover: ', discoverResult.error);
                } else if (discoverResult.discoveredReaders.length === 0) {
                    console.log('No available readers.');
                } else {
                    discoveredReaders = discoverResult.discoveredReaders;
                    console.log('terminal.discoverReaders', discoveredReaders);
                    readers.value = discoveredReaders;
                }

            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });
        }

        function selectReader(){

            if(selected_reader_id.value==''){
                console.log('selected',selected_reader_id.value);

                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: "Reader not selected",
                        ttl: 5,
                        type: "danger",
                    });
            }else{
                let selected_index = readers.value.findIndex((z) => { return z.id === selected_reader_id.value});

                selected_reader.value = readers.value[selected_index];

                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                    true,
                    "Connecting to "+selected_reader.value.label+"....",
                ]);


                terminal.value.connectReader(selected_reader.value).then((connectResult)=>{
                    if (connectResult.error) {
                        //console.log('Failed to connect: ', connectResult.error);
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            message: 'Failed to connect: '+JSON.stringify(connectResult.error),
                            ttl: 5,
                            type: "danger",
                        });
                    } else {
                        console.log('Connected to reader: ', connectResult.reader.label);
                        connected_reader.value = selected_reader.value.label;
                    }
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
            }
        }


        function createPaymentIntent(amount) {
            let pay_amount = amount*100;

            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                true,
                "Sending payment....",
            ]);


            fetchPaymentIntentClientSecret(pay_amount).then((client_secret)=>{

                //terminal.value.setSimulatorConfiguration({testCardNumber: '4242424242424242'});


                //console.log('client secret from fetch payment',client_secret);
                console.log('collectPaymentMethod started');

                terminal.value.collectPaymentMethod(client_secret).then((result)=>{

                    console.log('collect payment',result);
                    if (result.error) {
                        // Placeholder for handling result.error
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    message: JSON.stringify(result.error),
                                    ttl: 5,
                                    type: "danger",
                                });
                    } else {
                        //console.log('client secret',client_secret);

                        terminal.value.processPayment(result.paymentIntent).then((result)=>{

                            if (result.error) {
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    message: JSON.stringify(result.error),
                                    ttl: 5,
                                    type: "danger",
                                });
                            } else{
                                console.log('terminal.collectPaymentMethod', result.paymentIntent);

                                const savePI = async ()=>{
                                    await savePaymentIntent(result.paymentIntent);
                                }

                                savePI;

                                paymentIntentId.value = result.paymentIntent.id;
                                capture();
                                //console.log('terminal.processPayment', result.paymentIntent);
                            }
                        });
                    }
                }).catch((err)=>{
                    console.log(err);
                }).finally(()=>{
                    console.log('collectPayment method ended');
                });


            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });

        }


        function fetchPaymentIntentClientSecret(amount) {
            const bodyContent = JSON.stringify({ amount: amount,order_id:0, });
            return fetch('/stripe-test/create_payment_intent', {
                method: "POST",
                headers: {
                'Content-Type': 'application/json'
                },
                body: bodyContent
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                return data.client_secret;
            });
        }


        function capture() {
            console.log('capture started');
            if(paymentIntentId.value !=''){
                return fetch('/stripe-test/capture_payment_intent', {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({"payment_intent_id": paymentIntentId.value})
                })
                .then(function(response) {
                    //console.log(response);
                    return response.json();
                })
                .then(function(data) {
                    console.log('server.capture', data);
                    if(data.status=='succeeded'){
                        let amount = parseInt(data.amount)/100;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    message: "Payment of GBP"+amount.toFixed(2)+" received",
                                    ttl: 5,
                                    type: "success",
                        });
                    }

                }).finally(()=>{
                    console.log('capture ended');
                });
            }
        }


        async function savePaymentIntent(paymentIntent){
            console.log('save payment intent started');

             return fetch('/stripe-test/save_payment_intent', {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({"payment_intent": paymentIntent})
                }).then(function(data){
                    console.log(data);
                }).finally(()=>{
                    console.log('save payment intent ended');
                })


        }

        function cancelPaymentIntent(){
            //if(paymentIntentId.value !=''){
             return fetch('/stripe-test/cancel_payment_intent', {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({"payment_intent_id": paymentIntentId.value})
                }).then(function(data){
                    console.log(data);
                }).finally(()=>{
                    console.log('save payment intent ended');
                })
           /* }else{
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                    message: "payment intent id is blanc",
                    ttl:5,
                    type:'danger',
                });
            }*/
        }



        return {
            listReaders,
            readers,
            selectReader,
            selected_reader,
            selected_reader_id,
            connected_reader,
            payment_amount,
            createPaymentIntent,
            paymentIntentId,
            capture,
            cancelPaymentIntent,
        }
    }

}
</script>
<style scoped>
    select{
        height: 45.2px;
    }

</style>
