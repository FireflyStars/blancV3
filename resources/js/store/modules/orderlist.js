
import {
    ALL_ORDER_ADD_TO_LIST,
    ALL_ORDER_GET_LIST,
    ALL_ORDER_LOAD_LIST,
    ALL_ORDER_SHOWMORE_LIST,
    ALL_ORDER_SET_CURRENT_SELECTED,
    ALL_ORDER_GET_CURRENT_SELECTED,
    ALL_ORDER_SELECT_CURRENT,
    ALL_ORDER_GET_ALL_ORDER_MULITCHECKED,
    ALL_ORDER_MULITCHECKED,
    ALL_ORDER_SET_ALL_ORDER_MULITCHECKED,
    ALL_ORDER_MULITUNCHECKED,
    DISPLAY_LOADER,HIDE_LOADER,LOADER_MODULE
} from "../types/types";

import axios from 'axios';

export const orderlist= {
    namespaced:true,
    state: {
            due_today:[],
            due_tommorrow:[],
            all_order:{
                currently_selected:'',//currently selected line for displaying order detail
                multi_checked:[], // currently check lines for possible mass action ex like batch delete...
                order_list:[], // order list
                skip:0, //show more
                take:10 //show more
            },
            customer_care:[],
            with_partner:{
                currently_selected:'',//currently selected line for displaying order detail
                multi_checked:[], // currently check lines for possible mass action ex like batch delete...
                order_list:[], // order list
                skip:0, //show more
                take:10 //show more
            },

    },
    mutations: {
         [ALL_ORDER_ADD_TO_LIST]: (state, payload) => state.all_order.order_list=state.all_order.order_list.concat(payload),
        [ALL_ORDER_SHOWMORE_LIST]:(state,payload) =>{
             state.all_order.skip=payload.skip;
        },
        [ALL_ORDER_SET_CURRENT_SELECTED]:(state,payload) =>{
            state.all_order.multi_checked=  state.all_order.multi_checked.filter(item => item !== state.all_order.currently_selected);//remove previous from multichecked
            state.all_order.currently_selected=payload;

        },
        [ALL_ORDER_SET_ALL_ORDER_MULITCHECKED]:(state,payload)=>{

             if(payload.add)// add from multi_checked
            state.all_order.multi_checked.push(payload.id);
             if(!payload.add) // remove from multi_checked
                 state.all_order.multi_checked=  state.all_order.multi_checked.filter(item => item !== payload.id);

        }

    },
    actions: {

        [ALL_ORDER_LOAD_LIST]:async({commit,state,dispatch},payload)=>{

            if(typeof payload!="undefined"&&payload.showmore){
                commit(ALL_ORDER_SHOWMORE_LIST,{skip:state.all_order.skip+state.all_order.take});
                dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[true,'loading more, please wait...'],{ root: true });
            }else{
                dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[true,'loading orderlist, please wait...'],{ root: true });
            }



           return axios.post('/getorderlist', {
                skip: state.all_order.skip,
                take: state.all_order.take
            })
                .then(function (response) {
                    commit(ALL_ORDER_ADD_TO_LIST, response.data);
                    console.log(response);
                })
                .catch(function (error) {
                    console.log(error);
                }).finally(function(){
                dispatch(`${LOADER_MODULE}${HIDE_LOADER}`,{},{ root: true });
            });


        },
        [ALL_ORDER_SELECT_CURRENT]:({commit},payload)=>{
            commit(ALL_ORDER_SET_CURRENT_SELECTED,payload);
        },
        [ALL_ORDER_MULITCHECKED]:({commit},payload)=>{
            commit(ALL_ORDER_SET_ALL_ORDER_MULITCHECKED,{id:payload,add:true});
        },
        [ALL_ORDER_MULITUNCHECKED]:({commit},payload)=>{
            commit(ALL_ORDER_SET_ALL_ORDER_MULITCHECKED,{id:payload,add:false});
        },
    },
    getters: {
         [ALL_ORDER_GET_LIST]: state => state.all_order.order_list,
         [ALL_ORDER_GET_CURRENT_SELECTED]:state => state.all_order.currently_selected,
         [ALL_ORDER_GET_ALL_ORDER_MULITCHECKED]:state=>state.all_order.multi_checked,
        // [GET_LOADER_MSG]: state => state.loader_msg,
    }
}