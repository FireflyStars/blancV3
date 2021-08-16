import {TOASTER_MESSAGE, TOASTER_MODULE} from "./store/types/types";

require('./bootstrap');

import axios from 'axios';
import { createApp } from 'vue';
import App from './components/App';

import router from './router/router';
import store from './store/store'

axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response.status !== 401&&error.response.status !== 419) return Promise.reject(error)
        sessionStorage.clear();
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'Session expired. Please login again.',ttl:8,type:'danger'});
        //window.location='/';
        router.push({
            name:'Login',
        })
    }
)
createApp(App)
    .use(router)
    .use(store)
    .mount('#app');