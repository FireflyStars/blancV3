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
        window.location='/';
        /*router.push({
            name:'Login',
        })*/
    }
)
createApp(App)
    .use(router)
    .use(store)
    .mount('#app');