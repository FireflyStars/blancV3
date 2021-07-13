require('./bootstrap');


import { createApp } from 'vue';
import App from './components/App';

import router from './router/router';
import store from './store/store'

createApp(App)
    .use(router)
    .use(store)
    .mount('#app');