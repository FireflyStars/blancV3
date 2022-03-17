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
    TOASTER_MODULE,
    TOASTER_MESSAGE,
}
from '../types/types';
export const Customer = {
    namespaced: true,
    state:{
        selected_nav: 'AllCustomers',
        customer_list: [],
        total_customer_count: '',
        filter: {
            skip: 0,
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
            console.log(payload)
            state.total_customer_count = payload.total_count;
            state.customer_list = payload.customers;
            state.filter.skip = state.customer_list.length;
        }
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
        }
    },
    getters:{
        [GET_CUSTOMER_LIST]: (state) => state.customer_list,
        [GET_LOADED_CUSTOMER_COUNT]: (state) => state.customer_list.length,
        [GET_TOTAL_CUSTOMER_COUNT]: (state) => state.total_customer_count,
    }
}