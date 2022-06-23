<template>
    <button class="pay-btn w-100 py-3" @click="payNow">Pay now</button>
</template>
<script>
import {ref,watch} from 'vue';
import {useStore} from 'vuex';
import {
    LOADER_MODULE,
    SET_LOADER_MSG,
    DISPLAY_LOADER,
    HIDE_LOADER,
    TOASTER_MESSAGE,
    TOASTER_MODULE,
} from '../../store/types/types';

export default {
    name:"StripePayNow",
    props:{
        user: Object || null,
        order: Object || null,
    },
    emits:['complete-checkout'],
    setup(props,context) {
        const store = useStore();
        const terminal = ref();
        const readers = ref([]);
        const paymentIntentId = ref();
        const selected_reader = ref();

        let readers_id = {};
        readers_id[1] = 'tmr_Eqz4ewJhXq5eu6'; //Atelier
        readers_id[2] = 'tmr_Eq0HXA4Oj7Yjqo'; //Marylebone
        readers_id[3] = 'tmr_EqzSAXwuoVzKs0'; //Chelsea
        readers_id[4] = 'tmr_Eqz9KQMTISyB47'; //South Ken
        readers_id[5] = 'tmr_EqzjQIwM2PjDQy'; //Notting Hill


        async function getConnectionToken(){
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

        getConnectionToken();

        async function listReaders(){
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                true,
                "Connecting to reader....",
            ]);

            await getConnectionToken();

            var config = {simulated: false};
            let discoveredReaders = [];

            await terminal.value.discoverReaders(config).then((discoverResult)=>{

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
                //store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });
        }

        async function selectReader(reader){
            console.log('connecting to reader',reader);
           await terminal.value.connectReader(reader).then((connectResult)=>{
                if (connectResult.error) {
                    console.log('Failed to connect: ', connectResult.error);
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: 'Failed to connect: '+JSON.stringify(connectResult.error),
                        ttl: 5,
                        type: "danger",
                    });
                } else {
                    selected_reader.value = connectResult.reader;
                    console.log('Connected to reader: ', connectResult.reader.label);
                }
            }).catch((err)=>{
                console.log(err);
            })
            .finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                console.log('finished connecting with reader');
            });
        }



        async function payNow(){
            console.log('call start');

            await listReaders();

            if(readers.value.length > 0){
                let store_id = props.user.store;

                if(typeof(readers_id[store_id]) !='undefined'){
                    let reader_id = readers_id[store_id];

                    let selected_index = readers.value.findIndex((z) => { return z.id === reader_id});
                    selected_reader.value = readers.value[selected_index];

                    await selectReader(selected_reader.value);

/*
                    if(typeof(selected_reader.value.id)!='undefined'){
                        await createPaymentIntent(props.order.Total);
                    }
                    */

                }
            }

        }


        async function createPaymentIntent(amount) {
            let pay_amount = amount*100;

            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                true,
                "Sending payment....",
            ]);


            await fetchPaymentIntentClientSecret(pay_amount).then((client_secret)=>{
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


        async function fetchPaymentIntentClientSecret(amount) {
            const bodyContent = JSON.stringify({ amount: amount,order_id:props.order.id });
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


        async function capture() {
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
                    //To log data for payment logs


                    if(data.status=='succeeded'){
                        let amount = parseInt(data.amount)/100;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    message: "Payment of GBP"+amount.toFixed(2)+" received",
                                    ttl: 5,
                                    type: "success",
                        });

                        async function setOrderPaid(){
                            await axios.post('/set-order-paid',{
                                order_id:props.order.id
                            }).then((res)=>{

                            }).catch((err)=>{
                                console.log(err);
                            }).finally(()=>{
                                context.emit('complete-checkout');
                            });
                        }

                        setOrderPaid();
                    }

                }).finally(()=>{
                    console.log('capture ended');
                });
            }
        }


        return {
            payNow,
            selected_reader,
        }

    },
}
</script>
<style scoped>

</style>
