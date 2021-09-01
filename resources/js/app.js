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


//custom tooltip
const body=document.querySelector('body');
let overTimeout;
let outTimeout;
const tooltipOver=function( event ) {
    clearTimeout(outTimeout);
    event.target.classList.remove("active-tooltip");
    overTimeout=setTimeout(function() {
        event.target.classList.add("active-tooltip");
    }, 2000);
};
const tooltipOut=function( event ) {
    clearTimeout(overTimeout);
    outTimeout=setTimeout(function() {
        event.target.classList.remove("active-tooltip");
    }, 100);
};

const observer= new MutationObserver(mutations=>{
   const tooltip= body.querySelectorAll('.tool-tip');
    tooltip.forEach(t=>{
       t.removeEventListener("mouseover",tooltipOver);
       t.addEventListener("mouseover", tooltipOver, false);
        t.removeEventListener("mouseout",tooltipOut);
        t.addEventListener("mouseout", tooltipOut, false);
    });

});

observer.observe(body,{
   childList:true,
    subtree:true
});


