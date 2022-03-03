
import {
    SET_INVOICE_LIST,
    SET_INVOICE_FILTER_FLAG,
    SET_INVOICE_FILTER,
    GET_INVOICE_LIST, 
    GET_INVOICE_STATUS, 
    GET_INVOICE_DESTINATION, 
    GET_INVOICE_LOCATION, 
    GET_TOTAL_INVOICE_COUNT, 
    ADD_MORE_INVOICE_TO_LIST,
    GET_LOADED_INVOICE_COUNT, 
    FILTER_INVOICE_LIST, 
    LOAD_MORE_INVOICE, 
    LOADER_MODULE,
    DISPLAY_LOADER,
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    HIDE_LOADER
} from "../types/types";
import axios from 'axios';

export const invoicelist= {
    namespaced:true,
    state: {
        invoice_list: [],
        total_invoice_count: 0,
        order_status: [
            'ARCHIVED',
            'ASSEMBLING',
            'CONVEYOR IN',
            'DELIVERED',
            'FAILED DELIVERY',
            'FULFILLED',
            'In process',
            'OFFLOADED',
            'ON VAN',
            'READY',
            'READY IN STORE',
        ],
        destinations: [
            'CHELSEA',
            'DELIVERY',
            'MARYLEBONE',
            'STORE',
            'STORES',
        ],
        locations: [
            'Archive',
            'Archive IN',
            'Archive OUT',
            'Assembly',
            'Assembly conveyor',
            'Bedlinen Partner',
            'CC Authorisations',
            'Charity',
            'CHELSEA - ST1 - IN',
            'CHELSEA - ST1 - OFFLOADED',
            'CHELSEA - ST2 - IN',
            'CHELSEA - ST2 - OFFLOADED',
            'CHELSEA - STORAGE 1',
            'CHELSEA - STORAGE 2',
            'Cleaning / co2',
            'Cleaning / Delicates',
            'Cleaning / Laundry',
            'Curtains partner',
            'Customer Care',
            'DELIVERED',
            'Detailing',
            'Dry Cleaner',
            'Folding / Packing',
            'FULFILLED',
            'Handbags',
            'IN STORE',
            'ITEM IN',
            'Leather partner',
            'Loading Station',
            'MARYLEBONE - ST1 - IN',
            'MARYLEBONE - ST1 - OFFLOADED',
            'MARYLEBONE - STORAGE1',
            'NOTTING HILL -  LOWER - STORAGE',
            'NOTTING HILL -  UPPER - STORAGE',
            'NOTTING HILL - LOWER - IN',
            'NOTTING HILL - LOWER - OFFLOADED',
            'NOTTING HILL - UPPER - IN',
            'NOTTING HILL - UPPER - OFFLOADED',
            'OFFLOADING ARM',
            'ON VAN',
            'Partner Hub',
            'Pressing / Bedlinen',
            'Pressing / Delicates',
            'Pressing / Laundry',
            'Pressing / Shirts',
            'Pressing / Trousers',
            'QC 1 / Delicates',
            'QC 1 / Laundry',
            'QC 2',
            'Rug Partner',
            'Shelving - Bedlinen',
            'Shelving - Other',
            'Shelving - W&F',
            'Shoes',
            'SK - Long Rail',
            'SK - LOWER - IN',
            'SK - LOWER - OFFLOADED',
            'SK - LOWER - STORAGE',
            'SK - SHELVING',
            'SK - UPPER - IN',
            'SK - UPPER - OFFLOADED',
            'SK - UPPER - STORAGE',
            'Spotting / Delicates',
            'Spotting / Laundry',
            'Storage',
            'Tailoring',
            'VIP ARM',
            'VOID',
        ],
        filter: {
            first_name: '',
            last_name: '',
            order_id: '',
            sub_order: '',
            item: '',
            status: '',
            dest: '',
            location: '',
            prod_date_from: '',
            prod_date_to: '',
            deliv_date_from: '',
            deliv_date_to: '',
            skip: 0,
        }
    },
    mutations: {
        [SET_INVOICE_LIST]: (state, payload) => {
            state.invoice_list = payload.invoices;
            state.total_invoice_count = payload.total_count;
            state.filter.skip = state.invoice_list.length;
        },
        [SET_INVOICE_FILTER]: (state, payload) => { 
            state.invoice_list = [];
            state.total_invoice_count = 0;
            state.filter.status = payload.status.value;            
            state.filter.dest = payload.dest.value.join(',');            
            state.filter.location = payload.location.value.join(',');            
            state.filter.prod_date_from = payload.prod_date.value.start;            
            state.filter.prod_date_to = payload.prod_date.value.end;            
            state.filter.deliv_date_from = payload.deliv_date.value.start;            
            state.filter.deliv_date_to = payload.deliv_date.value.end;            
            state.filter.skip = 0;
        },
        [FILTER_INVOICE_LIST]: (state, payload) => { 
            state.invoice_list = payload.invoices;
            state.total_invoice_count = payload.total_count;
            state.filter.skip = state.invoice_list.length;            
        },
        // [SET_INVOICE_FILTER_FLAG]: (state, payload) => { state.filter.filter = payload },
        [ADD_MORE_INVOICE_TO_LIST]: (state, payload) => { 
            if(payload.invoices.length > 0){
                state.invoice_list = ( [ ...state.invoice_list, ...payload.invoices ]);
                state.filter.skip = state.invoice_list.length;
            }
        },
    },
    actions: {
        [SET_INVOICE_LIST]: async({ commit, dispatch, state } )=>{
            dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading data...'], {root: true});
            await axios.post('/get-all-invoices', state.filter).then(function (response) {
                commit(SET_INVOICE_LIST, response.data);
            })
            .catch(function (error) {
                if(typeof error.response !="undefined")
                dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'},{ root: true });
            }).finally(function(){
                dispatch(`${LOADER_MODULE}${HIDE_LOADER}`,{},{ root: true });
            });
        },
        [SET_INVOICE_FILTER]: async({ commit }, payload )=>{
            commit(SET_INVOICE_FILTER, payload )
        },
        [FILTER_INVOICE_LIST]: async({ commit, dispatch, state } )=>{
            dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Filtering data...'], {root: true});
            await axios.post('/get-all-invoices', state.filter).then(function (response) {
                commit(FILTER_INVOICE_LIST, response.data);
            })
            .catch(function (error) {
                if(typeof error.response !="undefined")
                dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'},{ root: true });
            }).finally(function(){
                dispatch(`${LOADER_MODULE}${HIDE_LOADER}`,{},{ root: true });
            });
        },
        [LOAD_MORE_INVOICE]: async ( { commit, dispatch, state } ) =>{
            dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading More...'], {root: true});
            await axios.post('/get-all-invoices', state.filter).then(function (response) {
                commit(ADD_MORE_INVOICE_TO_LIST, response.data);
            })
            .catch(function (error) {
                if(typeof error.response !="undefined")
                dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'},{ root: true });
            }).finally(function(){
                dispatch(`${LOADER_MODULE}${HIDE_LOADER}`,{},{ root: true });
            });            
        },
        [SET_INVOICE_FILTER_FLAG]: ({ commit }, payload)=>{
            commit(SET_INVOICE_FILTER_FLAG, payload);
        }
    },
    getters: {
        [GET_INVOICE_LIST]:state=> state.invoice_list,
        [GET_TOTAL_INVOICE_COUNT]:state=> state.total_invoice_count,
        [GET_LOADED_INVOICE_COUNT]:state=> state.invoice_list.length,
        [GET_INVOICE_STATUS]:state=> state.order_status,
        [GET_INVOICE_DESTINATION]:state=> state.destinations,
        [GET_INVOICE_LOCATION]:state=> state.locations,
    }
}