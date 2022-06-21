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
                            <div class="col-4">
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
                                <div class="row py-4">
                                    <div class="col-5">
                                        <button class="btn btn-outline-success w-100" @click="selectReader">2. Select Reader</button>
                                    </div>
                                </div>
                                <div class="row py-4">
                                    <div class="col-12">
                                        Connected reader: {{connected_reader}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-8"></div>
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




        return {
            listReaders,
            readers,
            selectReader,
            selected_reader,
            selected_reader_id,
            connected_reader,
        }
    }

}
</script>
<style scoped>
    select{
        height: 45.2px;
    }

</style>
