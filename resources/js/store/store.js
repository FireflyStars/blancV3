import {createStore} from 'vuex';
import { loader as LOADER_MODULE}  from './modules/loader'
import { orderlist as ORDERLIST_MODULE} from './modules/orderlist'
import {toaster as TOASTER_MODULE} from "./modules/toaster";
import {orderdetail as ORDERDETAIL_MODULE} from "./modules/orderdetail";
import {select as SELECT_MODULE} from "./modules/select";
import {sidebar as SIDEBAR_MODULE} from "./modules/sidebar";
import {search as CUSTOMERLIST_MODULE} from "./modules/search";
import {permission as PERMISSION_MODULE} from "./modules/permission";
import {scan as SCAN_MODULE} from "./modules/scan";
import {neworder as NEWORDER_MODULE} from "./modules/neworder";
import {statistics as STATISTICS_MODULE} from "./modules/statistics";
import {detailingItem as DETAILING_MODULE} from "./modules/detailingItem";
import {shipping as SHIPPING_MODULE } from './modules/shipping';
import {assemblyHome as ASSEMBLY_HOME_MODULE } from './modules/assembly';
import {invoicelist as INVOICE_MODULE } from './modules/invoice';


export default createStore({
    modules:{
        LOADER_MODULE,
        ORDERLIST_MODULE,
        TOASTER_MODULE,
        ORDERDETAIL_MODULE,
        SELECT_MODULE,
        SIDEBAR_MODULE,
        CUSTOMERLIST_MODULE,
        PERMISSION_MODULE,
        SCAN_MODULE,
        NEWORDER_MODULE,
        STATISTICS_MODULE,
        DETAILING_MODULE,
        SHIPPING_MODULE,
        ASSEMBLY_HOME_MODULE,
        INVOICE_MODULE
    }
})
