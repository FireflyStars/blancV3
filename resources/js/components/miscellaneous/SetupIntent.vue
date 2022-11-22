<template>
    <div>
        <button @click="saveTerminalDetail" class="btn btn-outline-success">Save terminal details</button>
    </div>
    <br/>

    <div>
        <button @click="createPaymentIntent" class="btn btn-outline-success">Create intent</button>
    </div>
    <br/>
    <div>
        <button @click="cancelRequest" class="btn btn-outline-danger">Cancel terminal request</button>
    </div>
</template>
<script>
import {ref} from 'vue';
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
    'name':'SetupIntent',
    setup() {
        const selected_reader = ref();
        const err_terminal = ref(false);
        const terminal = ref();
        const readers = ref([]);

        const store = useStore();


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
               // context.emit('close-awaiting-payment');
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

        async function saveTerminalDetail(){
            await listReaders();

                if(readers.value.length > 0){
                    let store_id = 1;

                    if(typeof(readers_id[store_id]) !='undefined'){
                        let reader_id = readers_id[store_id];

                        let selected_index = readers.value.findIndex((z) => { return z.id === reader_id});
                        selected_reader.value = readers.value[selected_index];

                        console.log('calling selectReader');
                        await selectReader(selected_reader.value);
                        console.log('End calling selectReader');

                        if(typeof(selected_reader.value.id)!='undefined' && !err_terminal.value){
                           await getClientSecret(100);

                        }


                    }else{
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            message: 'Store reader not set for user '+props.user.name,
                            ttl: 5,
                            type: "danger",
                        });
                    }
                }
        }


        async function createPaymentIntent(){
            await listReaders();

                if(readers.value.length > 0){
                    let store_id = 1;

                    if(typeof(readers_id[store_id]) !='undefined'){
                        let reader_id = readers_id[store_id];

                        let selected_index = readers.value.findIndex((z) => { return z.id === reader_id});
                        selected_reader.value = readers.value[selected_index];

                        console.log('calling selectReader');
                        await selectReader(selected_reader.value);
                        console.log('End calling selectReader');

                        if(typeof(selected_reader.value.id)!='undefined' && !err_terminal.value){
                           await getPaymentClientSecret(100);

                        }


                    }else{
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            message: 'Store reader not set for user '+props.user.name,
                            ttl: 5,
                            type: "danger",
                        });
                    }
                }
        }



        const getClientSecret = (amount)=>{
            fetchSetupIntentClientSecret(amount).then((data)=>{
                console.log('data',data);

                terminal.value.collectSetupIntentPaymentMethod(data.client_secret).then((res)=>{
                    console.log(res);
                });
            });
        }

        const getPaymentClientSecret = (amount)=>{
            fetchPaymentIntentClientSecret(amount).then((data)=>{
                console.log('data',data);

                terminal.value.collectPaymentMethod(data.client_secret).then((res)=>{
                    console.log(res);
                });
            });
        }


        function fetchSetupIntentClientSecret(amount) {

            const bodyContent = JSON.stringify({ amount: amount});

            return fetch('/stripe-test/create_setup_intent', {
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
                return data;
            }).catch((err)=>{
                console.log(err);
            });
        }


        function fetchPaymentIntentClientSecret(amount){
            const bodyContent = JSON.stringify({ amount: amount});

            return fetch('/stripe-test/test_payment_intent', {
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
                return data;
            }).catch((err)=>{
                console.log(err);
            });
        }


        const cancelRequest = ()=>{
            console.log('selected_reader',selected_reader.value);



            /*
            selected_reader.value.disconnectReader().then((res)=>{
                console.log(res);
            });
            */
        }

        return {
            saveTerminalDetail,
            createPaymentIntent,
            cancelRequest,
        }



    },
}
</script>
