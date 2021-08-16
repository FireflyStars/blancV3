import {createStore} from 'vuex';
import { loader as LOADER_MODULE}  from './modules/loader'
import { orderlist as ORDERLIST_MODULE} from './modules/orderlist'
import {toaster as TOASTER_MODULE} from "./modules/toaster";

export default createStore({
    modules:{
        LOADER_MODULE,
        ORDERLIST_MODULE,
        TOASTER_MODULE
    }
})