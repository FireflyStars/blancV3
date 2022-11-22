import {
    LOADER_MODULE, 
    DISPLAY_LOADER,
    HIDE_LOADER,
    GET_CUSTOMER_LIST,
    SET_CUSTOMER_LIST,
    LOAD_MORE_CUSTOMER,
    GET_LOADED_CUSTOMER_COUNT,
    GET_TOTAL_CUSTOMER_COUNT,
    ADD_MORE_CUSTOMER_TO_LIST,
    GET_CURRENT_SELECTED_CUSTOMER,
    GET_ALL_SELECTED_CUSTOMER,
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    SET_CURRENT_SELECTED_CUSTOMER,
    SET_CUSTOMER_SELECTED_TAB,
    SET_CUSTOMER_FILTER,
    REMOVE_CUSTOMER_FILTER,
    FILTER_CUSTOMER_LIST,
    SET_CUSTOMER_DETAIL,
    LOAD_CUSTOMER_DETAIL,
    GET_CUSTOMER_DETAIL,
    GET_LOADER_CLASS,
    SET_LOADER_CLASS
}
from '../types/types';
export const Customer = {
    namespaced: true,
    state:{
        selected_nav: 'CustomerList',
        customer_list: [],
        total_customer_count: '',
        current_selected: '',
        multi_selected: [],      
        customer_detail: {
            name: ''
        },  
        loader: 'animate40',
        filter: {
            skip: 0,
            selected_nav: 'CustomerList',
            customer_type: '',
            customer_location: '',
            invoice_preference: '',
            total_spent: '',
            last_order_start: '',
            last_order_end: '',
            Customername :'',
        }
    },
    mutations:{
        [ADD_MORE_CUSTOMER_TO_LIST]: (state, payload)=>{
            if(payload.customers.length > 0){
                state.customer_list = ( [ ...state.customer_list, ...payload.customers ]);
                state.filter.skip = state.customer_list.length;
            }
        },
        [SET_CUSTOMER_LIST]: (state, payload)=>{
            state.total_customer_count = payload.total_count;
            state.customer_list = payload.customers;
            state.filter.skip = state.customer_list.length;    
        },
        [SET_CURRENT_SELECTED_CUSTOMER]: (state, payload)=>{
            state.current_selected = payload;
        },
        [SET_CUSTOMER_SELECTED_TAB]: (state, payload)=>{
            state.filter.selected_nav = payload;
        },
        [SET_CUSTOMER_FILTER]: (state, payload)=>{
            state.filter.skip = 0; 
            if(payload.Customername){
                state.filter.Customername = payload.Customername.value;
            }else {
                state.filter.customer_type = payload.customer_type.value;
                state.filter.customer_location = payload.customer_location.value;
                state.filter.invoice_preference = payload.invoice_preference.value;
                state.filter.total_spent = payload.total_spent.value;
                state.filter.last_order_start = payload.last_order.value.start;
                state.filter.last_order_end = payload.last_order.value.end;
            } 
        },
        [REMOVE_CUSTOMER_FILTER]: (state, payload)=>{    

                state.filter.customer_type = '';
                state.filter.customer_location = '';
                state.filter.invoice_preference = '';
                state.filter.total_spent = '';
                state.filter.last_order_start = '';
                state.filter.last_order_end = '';    
        },
        [SET_CUSTOMER_DETAIL]:(state, payload)=>{
            state.customer_detail = payload;
        },
        [SET_LOADER_CLASS]: (state, payload) => state.loader = payload,
    },
    actions:{
        [LOAD_MORE_CUSTOMER]: async ({ commit, dispatch, state })=>{
            dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading More...'], {root: true});
            await axios.post('/get-all-customers', state.filter).then(function (response) {
                commit(ADD_MORE_CUSTOMER_TO_LIST, response.data);
            })
            .catch(function (error) {
                if(typeof error.response !="undefined")
                dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'},{ root: true });
            }).finally(function(){
                dispatch(`${LOADER_MODULE}${HIDE_LOADER}`,{},{ root: true });
            });
        },
        [SET_CUSTOMER_LIST]: async ({ commit, dispatch, state })=>{
            state.filter.Customername = '';
            state.filter.skip = 0 ;
            dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading customer data...'], {root: true});
            await axios.post('/get-all-customers', state.filter).then(function (response) {
                commit(SET_CUSTOMER_LIST, response.data);
            })
            .catch(function (error) {
                if(typeof error.response !="undefined")
                dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'},{ root: true });
            }).finally(function(){
                dispatch(`${LOADER_MODULE}${HIDE_LOADER}`,{},{ root: true });
            });
        },
        [SET_CURRENT_SELECTED_CUSTOMER]: ({ commit }, payload)=>{
            commit(SET_CURRENT_SELECTED_CUSTOMER, payload);
        },
        [SET_CUSTOMER_SELECTED_TAB]: ( { commit }, payload)=>{
            commit(SET_CUSTOMER_SELECTED_TAB, payload)
        },
        [SET_CUSTOMER_FILTER]: ( { commit } , payload)=>{
            commit(SET_CUSTOMER_FILTER, payload);
        },
        [REMOVE_CUSTOMER_FILTER]: ( { commit } , payload)=>{
            commit(REMOVE_CUSTOMER_FILTER, payload);
        },
        [FILTER_CUSTOMER_LIST]: async ( { commit, dispatch, state })=>{
            dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Filtering customer data...'], {root: true});
            await axios.post('/get-all-customers', state.filter).then(function (response) {
                commit(SET_CUSTOMER_LIST, response.data);
            })
            .catch(function (error) {
                if(typeof error.response !="undefined")
                dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'},{ root: true });
            }).finally(function(){
                dispatch(`${LOADER_MODULE}${HIDE_LOADER}`,{},{ root: true });
            });
        },
        [SET_CUSTOMER_DETAIL]: ( {commit} , payload)=>{
            
            commit(SET_CUSTOMER_DETAIL, payload);
        },
        [LOAD_CUSTOMER_DETAIL]: async ( {commit, dispatch}, payload )=>{
            commit(SET_LOADER_CLASS,'animate40');
            await axios.post('/get-customer-detail', { customer_id: payload }).then(function (response) {
                console.log(response.data);
                commit(SET_CUSTOMER_DETAIL, response.data);
            })
            .catch(function (error) {
                if(typeof error.response !="undefined")
                dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'},{ root: true });
            }).finally(()=>{
                commit(SET_LOADER_CLASS,'animate40 animate100');
            });
        }
    },
    getters:{
        [GET_CUSTOMER_LIST]: state => state.customer_list,
        [GET_LOADED_CUSTOMER_COUNT]: state => state.customer_list.length,
        [GET_TOTAL_CUSTOMER_COUNT]: state => state.total_customer_count,
        [GET_CURRENT_SELECTED_CUSTOMER]: state => state.current_selected,
        [GET_ALL_SELECTED_CUSTOMER]: state => state.multi_selected,
        [GET_CUSTOMER_DETAIL]: state => state.customer_detail,
        [GET_LOADER_CLASS]: state => state.loader,
    }
}