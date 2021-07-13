import {
    ADD_TO_ALL_ORDER_LIST,
    GET_ALL_ORDER_LIST,
    LOAD_ALL_ORDER_LIST
} from "../types/types";

export const orderlist= {
    namespaced:true,
    state: {
            due_today:[],
            due_tommorrow:[],
            all_order:{
                selected:'',//currently selected line for displaying order detail
                checked:[], // currently check lines for possible mass action
                order_list:[{'id':1,'name':'lol'}], // order list
                skip:0, //show more
                take:10 //show more
            },
            customer_care:[],
            with_partner:[],

    },
    mutations: {
         [ADD_TO_ALL_ORDER_LIST]: (state, payload) => state.all_order.order_list=state.all_order.order_list.concat(payload),
        // [SET_LOADER_MSG]: (state, payload) => state.loader_msg = payload
    },
    actions: {
        [LOAD_ALL_ORDER_LIST]:({commit})=>{
            commit(ADD_TO_ALL_ORDER_LIST, [{id:2,name:'xxxx'}]);
        }
        // [DISPLAY_LOADER]: ({commit}, payload) => {
        //     commit(SET_SHOW_LOADER, payload[0]);
        //     if (typeof payload[1] != "undefined")
        //         commit(SET_LOADER_MSG, payload[1]);
        //
        // },
        // [HIDE_LOADER]: ({commit}) => {
        //     commit(SET_SHOW_LOADER, false);
        //     commit(SET_LOADER_MSG, 'Loading, please wait...');
        // }
    },
    getters: {
         [GET_ALL_ORDER_LIST]: state => state.all_order.order_list,
        // [GET_LOADER_MSG]: state => state.loader_msg,
    }
}