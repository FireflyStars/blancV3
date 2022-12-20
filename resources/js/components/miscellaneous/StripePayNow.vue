<template>
    <button class="pay-btn w-100 py-3" @click="loadSaveCardModal">Pay now</button>



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
        amounttopay: Number,
    },
    emits:['complete-checkout','close-payment-modal','set-terminal-pay','close-awaiting-payment','show-save-card-modal','set-terminal-php'],
    setup(props,context) {
        const store = useStore();
        const terminal = ref();
        const readers = ref([]);
        const paymentIntentId = ref();
        const selected_reader = ref();
        const err_terminal = ref(false);
        const save_card = ref(false);
        const fetch_payment_interval_id = ref();
        const cur_pi_id = ref('');
        const cur_si_id = ref('');
        const cur_terminal_name = ref('');

        const loadSaveCardModal = ()=>{
            context.emit('show-save-card-modal');
        }


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
                    err_terminal.value = true;
                context.emit('close-awaiting-payment');
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
            context.emit('close-payment-modal');
            console.log('call start');

        //    if(props.amounttopay >= 0.3){

                await listReaders();

                if(readers.value.length > 0){
                    let store_id = props.user.store;

                    if(typeof(readers_id[store_id]) !='undefined'){
                        let reader_id = readers_id[store_id];

                        let selected_index = readers.value.findIndex((z) => { return z.id === reader_id});
                        selected_reader.value = readers.value[selected_index];

                        console.log('calling selectReader');
                        await selectReader(selected_reader.value);
                        console.log('End calling selectReader');

                        if(typeof(selected_reader.value.id)!='undefined' && !err_terminal.value){
                            await createPaymentIntent(props.amounttopay,props.order.id);
                        }


                    }else{
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            message: 'Store reader not set for user '+props.user.name,
                            ttl: 5,
                            type: "danger",
                        });
                    }
                }
         /*   }else{
                context.emit('close-awaiting-payment');
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                    message:"Minimum amount to pay is 30 pence",
                    ttl:5,
                    type:'danger',
                });
            }
        */

        }


        async function createPaymentIntent(amount,order_id) {
            console.log(amount,order_id);

            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                true,
                "Sending payment....",
            ]);

            await fetchPaymentIntentClientSecret(amount).then((client_secret)=>{
                //terminal.value.setSimulatorConfiguration({testCardNumber: '4242424242424242'});
        if(typeof(client_secret) !='undefined'){

                console.log('client secret from fetch payment',client_secret);
                console.log('collectPaymentMethod started');

                terminal.value.collectPaymentMethod(client_secret).then((result)=>{

                    //console.log('collect payment',result);
                    if (result.error) {
                        // Placeholder for handling result.error
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    message: JSON.stringify(result.error),
                                    ttl: 5,
                                    type: "danger",
                                });

                        //To add Call for non payment

                    } else {
                        //console.log('client secret',client_secret);

                        terminal.value.processPayment(result.paymentIntent).then((result)=>{

                            context.emit('close-awaiting-payment');
                            if (result.error) {
                                //context.emit('close-awaiting-payment');

                                 updateTerminalOrder(order_id,amount,'Failed',result,selected_reader.value.label);
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    message: "Payment declined",
                                    ttl: 5,
                                    type: "danger",
                                });
                            } else{
                                //To add fetch for card details
                                context.emit('set-terminal-pay');

                                updateTerminalOrder(order_id,amount,'succeeded','',result.paymentIntent.id,selected_reader.value.label);
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
            }

            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });


        }


        async function fetchPaymentIntentClientSecret(amount) {
            console.log('fetch amount',amount);
            const bodyContent = JSON.stringify({ amount: amount,order_id:props.order.id});
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
                console.log('data',data);
                return data.client_secret;
            }).catch((err)=>{
                console.log(err);
            });
        }


        async function capture() {
            console.log('capture started');
            if(paymentIntentId.value !=''){
                console.log('payment_intnet_id',paymentIntentId.value);
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
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);

                   console.log('data',data);
                  // updateTerminalOrder(data.amount,data.status);

                     if(data.status=='succeeded'){
                            let amount = parseInt(data.amount)/100;
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                message: "Payment of GBP "+amount.toFixed(2)+" received",
                                ttl: 5,
                                type: "success",
                            });
                            context.emit('complete-checkout');
                        }

                }).finally(()=>{
                    console.log('capture ended');
                });
            }
        }

        function updateTerminalOrder(order_id,amount,status,msg,payment_intent_id,terminal){
            console.log('update order started');

            const bodyContent = JSON.stringify({
                order_id:order_id,
                amount:amount.toFixed(2),
                terminal:terminal,
                status:status,
                info:msg,
                payment_intent_id:payment_intent_id,
            });

            console.log(bodyContent);

            return fetch('/stripe-test/update-terminal-order', {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: bodyContent
            })
            .then(function(response) {
                //console.log(response);
                return response.json();
            })
            .then(function(data) {
                console.log(data);
            }).finally(()=>{
                console.log('update order ended');
            });
        }

        function refundPayment(){
            console.log('Refund Started');

            const bodyContent = JSON.stringify({
                payment_intent_id:paymentIntentId.value,
            });
            /*
            return fetch('/stripe-test/refund-payment',{
                method:"POST",
                headers: {
                    'Content-Type':'application/json'
                },
                body: bodyContent
            }).then(function(data){
                console.log(data);
            }).finally(()=>{
                console.log('Refund finished');
            })
            */
        }

        function cancelTerminalRequest(){
            // console.log('stripepaynow')
            // context.emit('close-awaiting-payment');

            console.log('terminal status:',terminal.value.getPaymentStatus());
            terminal.value.cancelCollectPaymentMethod()
                .then((response)=>{
                    console.log(response);
                    //return Promise.resolve(response);
                }).catch((error)=>{
                    //return Promise.reject(error);
                }).finally(()=>{
                    context.emit('close-awaiting-payment');
                });
        }

        function saveCardAndPay(){
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                true,
                "Please wait....",
            ]);

            save_card_details_terminal.value=!save_card_details_terminal.value;
            axios.post('/stripe-test/save-card-details',{
                order_id:props.order.id,
                payment:true,
                amount:props.amounttopay,
            }).then((res)=>{
                console.log('data',res.data);
                if(res.data.output_si && res.data.output_pi){
                    let output_si = res.data.output_si;
                    let output_pi = res.data.output_pi;


                    cur_si_id.value = res.data.si.id;
                    cur_pi_id.value = res.data.pi.id;
                    console.log('si:'+output_si.action.status,'pi:'+output_pi.action.status);


                    if(output_si.action.status=='in_progress' && output_pi.action.status=='in_progress'){
                        cur_terminal_name.value = output_pi.label;
                        context.emit('set-terminal-php');
                        context.emit('close-payment-modal');
                        fetch_payment_interval_id.value = setInterval(retrievePaymentDetails,4000);
                    }


                }else{
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                    {
                        message:(res.data.err_txt!=''?res.data.err_txt:"An error occur while processing the terminal"),
                        ttl:5,
                        type:'danger',
                    });
                }
            }).catch((err)=>{
                console.log('err',err);

                /*
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                {
                    message:JSON.stringify(err),
                    ttl:5,
                    type:'danger',
                });
                */

            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });
        }

        const getCurTime = ()=>{
            let date = new Date();
            return date.getHours()+":"+date.getMinutes()+":"+date.getSeconds();
        }

        const retrievePaymentDetails = async ()=>{
            try{

                let time1 = getCurTime();
            console.log('before calling retrieve',time1);


            await axios.post('/stripe-test/retrieve-payment-details',{
                    si_id:cur_si_id.value,
                    pi_id:cur_pi_id.value,
                }).then((res)=>{
                    console.log(res);

                    if(res.data.pi && res.data.pi.payment_method!='' && res.data.pi.charges.total_count > 0 && !['requires_payment_method','requires_capture'].includes(res.data.pi.status)){
                        clearInterval(fetch_payment_interval_id.value);
                        context.emit('close-awaiting-payment');

                        updateTerminalOrder(props.order.id,props.amounttopay,res.data.pi.status,'',res.data.pi.id,cur_terminal_name.value);
                        if(res.data.pi.status=='succeeded'){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                message: "Payment of GBP "+(props.amounttopay/100).toFixed(2)+" received",
                                ttl: 5,
                                type: "success",
                            });

                            context.emit('complete-checkout');
                        }else{
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                message: "Payment error",
                                ttl: 5,
                                type: "danger",
                            });
                        }


                        //context.emit('reload-checkout');
                    }

                }).catch((err)=>{
                    console.log(err);
                }).finally(()=>{

                });
            }finally{
                let time2 = getCurTime();
                console.log('end calling retrieve function',time2);
            }
        }

        function resetRetrieveLoop(){
            console.log('reset loop called');
            clearInterval(fetch_payment_interval_id.value);
        }

        return {
            payNow,
            selected_reader,
            refundPayment,
            err_terminal,
            cancelTerminalRequest,
            loadSaveCardModal,
            saveCardAndPay,
            resetRetrieveLoop,
        }

    },
}
</script>
<style scoped>

</style>
