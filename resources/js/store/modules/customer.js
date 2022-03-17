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
    SET_CUSTOMER_SELECTED_TAB
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
        filter: {
            skip: 0,
            selected_nav: 'CustomerList',
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
        },
        [SET_CURRENT_SELECTED_CUSTOMER]: (state, payload)=>{
            state.current_selected = payload;
        },
        [SET_CUSTOMER_SELECTED_TAB]: (state, payload)=>{
            state.filter.selected_nav = payload;
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
        },
        [SET_CURRENT_SELECTED_CUSTOMER]: ({ commit }, payload)=>{
            commit(SET_CURRENT_SELECTED_CUSTOMER, payload);
        },
        [SET_CUSTOMER_SELECTED_TAB]: ( { commit }, payload)=>{
            commit(SET_CUSTOMER_SELECTED_TAB, payload)
        }
    },
    getters:{
        [GET_CUSTOMER_LIST]: (state) => state.customer_list,
        [GET_LOADED_CUSTOMER_COUNT]: (state) => state.customer_list.length,
        [GET_TOTAL_CUSTOMER_COUNT]: (state) => state.total_customer_count,
        [GET_CURRENT_SELECTED_CUSTOMER]: (state) => state.current_selected,
        [GET_ALL_SELECTED_CUSTOMER]: (state) => state.multi_selected,
    }
}