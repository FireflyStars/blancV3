import {createStore} from 'vuex';
import { loader as LOADER_MODULE}  from './modules/loader'
import { orderlist as ORDERLIST_MODULE} from './modules/orderlist'
import {toaster as TOASTER_MODULE} from "./modules/toaster";
import {orderdetail as ORDERDETAIL_MODULE} from "./modules/orderdetail";
import {select as SELECT_MODULE} from "./modules/select";

export default createStore({
    modules:{
        LOADER_MODULE,
        ORDERLIST_MODULE,
        TOASTER_MODULE,
        ORDERDETAIL_MODULE,
        SELECT_MODULE
    }
})