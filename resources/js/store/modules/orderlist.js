
import {
    ORDERLIST_ADD_TO_LIST,
    ORDERLIST_SHOWMORE_LIST,
    ORDERLIST_SET_CURRENT_SELECTED,
    ORDERLIST_GET_CURRENT_SELECTED,
    ORDERLIST_SELECT_CURRENT,
    ORDERLIST_GET_ALL_ORDER_MULITCHECKED,
    ORDERLIST_MULITCHECKED,
    ORDERLIST_SET_ALL_ORDER_MULITCHECKED,
    ORDERLIST_MULITUNCHECKED,
    DISPLAY_LOADER,
    HIDE_LOADER,
    LOADER_MODULE,
    ORDERLIST_CURRENTTAB,
    ORDERLIST_SET_CURRENTTAB,
    ORDERLIST_GET_LIST,
    ORDERLIST_LOAD_LIST,
    ORDERLIST_SORT,
    ORDERLIST_GET_SORT,
    ORDERLIST_SET_SORT,
    ORDERLIST_SET_LIMIT,
    ORDERLIST_RESET_ORDERLIST,
    ORDERLIST_SET_LOADERMSG,
    ORDERLIST_LOADERMSG,
    ORDERLIST_FILTER,
    ORDERLIST_GET_FILTER,
    ORDERLIST_SET_FILTER,
    ORDERLIST_RESET_MULITCHECKED,
    ORDERLIST_SET_MULITCHECKED,
    ORDERLIST_CANCEL_ORDERS,
    ORDERLIST_UPDATE_STATUS,
    ORDERLIST_REMOVE_ORDERS,
    ORDERLIST_LOAD_TAB, TOASTER_MODULE, TOASTER_MESSAGE, ORDERLIST_MARK_AS_LATE
} from "../types/types";

import axios from 'axios';

export const orderlist= {
    namespaced:true,
    state: {
            current_tab:'',
            loader_msg:'Loading...',
            due_today:{
                currently_selected:'',//currently selected line for displaying order detail
                multi_checked:[], // currently check lines for possible mass action ex like batch delete...
                order_list:[], // order list
                skip:0, //show more
                take:10, //show more
                sort:[],//sort col
                filters:{},//filters
            },
            due_tomorrow:{
                currently_selected:'',//currently selected line for displaying order detail
                multi_checked:[], // currently check lines for possible mass action ex like batch delete...
                order_list:[], // order list
                skip:0, //show more
                take:10, //show more
                sort:[],//sort col
                filters:{},//filters
            },
            all_orders:{
                currently_selected:'',//currently selected line for displaying order detail
                multi_checked:[], // currently check lines for possible mass action ex like batch delete...
                order_list:[], // order list
                skip:0, //show more
                take:10, //show more
                sort:[],//sort col
                filters:{},//filters
            },
            customer_care:{
                currently_selected:'',//currently selected line for displaying order detail
                multi_checked:[], // currently check lines for possible mass action ex like batch delete...
                order_list:[], // order list
                skip:0, //show more
                take:10, //show more
                sort:[],//sort col
                filters:{},//filters
            },
            with_partner:{
                currently_selected:'',//currently selected line for displaying order detail
                multi_checked:[], // currently check lines for possible mass action ex like batch delete...
                order_list:[], // order list
                skip:0, //show more
                take:10, //show more
                sort:[],//sort col
                filters:{},//filters
            },

    },
    mutations: {
         [ORDERLIST_ADD_TO_LIST]: (state, payload) => state[state.current_tab].order_list=state[state.current_tab].order_list.concat(payload),
        [ORDERLIST_SHOWMORE_LIST]:(state, payload) =>{
             state[state.current_tab].skip=payload.skip;
        },
        [ORDERLIST_SET_CURRENT_SELECTED]:(state, payload) =>{
            state[state.current_tab].multi_checked=  state[state.current_tab].multi_checked.filter(item => item !== state[state.current_tab].currently_selected);//remove previous from multichecked
            state[state.current_tab].currently_selected=payload;

        },
        [ORDERLIST_SET_ALL_ORDER_MULITCHECKED]:(state, payload)=>{

             if(payload.add)// add from multi_checked
            state[state.current_tab].multi_checked.push(payload.id);
             if(!payload.add) // remove from multi_checked
                 state[state.current_tab].multi_checked=  state[state.current_tab].multi_checked.filter(item => item !== payload.id);

        },
        [ORDERLIST_CURRENTTAB]:(state,payload)=>state.current_tab=payload.tab,
        [ORDERLIST_SET_SORT]:(state, payload) =>state[state.current_tab].sort=payload,
        [ORDERLIST_SET_LIMIT]:(state,payload) =>{
            state[state.current_tab].skip=payload.skip;
            state[state.current_tab].take=payload.take;
        },
        [ORDERLIST_RESET_ORDERLIST]:state=>state[state.current_tab].order_list=[],
        [ORDERLIST_SET_LOADERMSG]:(state,payload)=>state.loader_msg=payload,
        [ORDERLIST_SET_FILTER]:(state,payload)=>state[state.current_tab].filters=payload,
        [ORDERLIST_SET_MULITCHECKED]:(state,payload)=>state[state.current_tab].multi_checked=payload,
        [ORDERLIST_UPDATE_STATUS]:(state,payload)=>{

                payload.orderids.forEach(id=>{
                    state[state.current_tab].order_list.forEach(order=>{
                        if(order.id===id)
                            order.Status=payload.status;
                    })
                })
        },
        [ORDERLIST_REMOVE_ORDERS]:(state,payload)=>{
            state[state.current_tab].order_list=state[state.current_tab].order_list.filter(order=>!payload.includes(order.id));
        }
    },
    actions: {

        [ORDERLIST_LOAD_LIST]:async({commit,state,dispatch},payload)=>{

            if(typeof payload!="undefined"&&payload.showmore){
                commit(ORDERLIST_SHOWMORE_LIST,{skip:state[state.current_tab].skip+state[state.current_tab].take});
                dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[true,state.loader_msg],{ root: true });
            }else{
                dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[true,state.loader_msg],{ root: true });
                commit(ORDERLIST_RESET_ORDERLIST);
            }



           return axios.post('/getorderlist', {
                skip: state[state.current_tab].skip,
                take: state[state.current_tab].take,
               current_tab:state.current_tab,
               sort:state[state.current_tab].sort,
               filters:state[state.current_tab].filters,
            })
                .then(function (response) {
                    commit(ORDERLIST_ADD_TO_LIST, response.data);
                    console.log(response);
                })
                .catch(function (error) {
                    dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'},{ root: true });
                }).finally(function(){
                dispatch(`${LOADER_MODULE}${HIDE_LOADER}`,{},{ root: true });
            });


        },
        [ORDERLIST_SELECT_CURRENT]:({commit}, payload)=>{
            commit(ORDERLIST_SET_CURRENT_SELECTED,payload);
        },
        [ORDERLIST_MULITCHECKED]:({commit,getters}, payload)=>{
            if(!getters.ORDERLIST_GET_ALL_ORDER_MULITCHECKED.includes(payload))
            commit(ORDERLIST_SET_ALL_ORDER_MULITCHECKED,{id:payload,add:true});
        },
        [ORDERLIST_MULITUNCHECKED]:({commit}, payload)=>{
            commit(ORDERLIST_SET_ALL_ORDER_MULITCHECKED,{id:payload,add:false});
        },
        [ORDERLIST_SET_CURRENTTAB]:({commit},payload)=>{
            commit(ORDERLIST_CURRENTTAB,{tab:payload});
        },
        [ORDERLIST_SORT]:({commit,state,dispatch,getters},payload)=>{
            let sortcols=getters.ORDERLIST_GET_SORT;

            if (sortcols.some(e => {
                if(e[payload] === '') {
                    e[payload] = 'ASC'
                }else if(e[payload] === 'ASC') {
                    e[payload] = 'DESC'
                }else if(e[payload] === 'DESC') {
                    e[payload] = '';

                    sortcols=   sortcols.filter((item)=>item[payload]!=='');
                }
                return e[payload] === 'ASC'||e[payload] === 'DESC'||e[payload] === '';
            })){
                //console.log('entered',e);
                //if(sortcols[payload]==='ASC'){
                //    sortcols[payload]='DESC';
               // }
            }else{
                const obj={};
                obj[payload]='ASC';
                sortcols.push(obj);

            }
            commit(ORDERLIST_SET_SORT,sortcols);
            commit(ORDERLIST_SET_LIMIT,{skip:0,take:10});
            commit(ORDERLIST_SET_LOADERMSG,'Sorting...');
            dispatch(ORDERLIST_LOAD_LIST);

        },
        [ORDERLIST_LOADERMSG]:({commit},payload)=>{
            commit(ORDERLIST_SET_LOADERMSG,payload);
        },
        [ORDERLIST_FILTER]:({commit,dispatch},payload)=>{
            commit(ORDERLIST_SET_FILTER,payload);
            commit(ORDERLIST_SET_LIMIT,{skip:0,take:10});
            commit(ORDERLIST_SET_LOADERMSG,'Applying Filters...');
            dispatch(ORDERLIST_LOAD_LIST);
        },
        [ORDERLIST_RESET_MULITCHECKED]:({commit})=>{
            commit(ORDERLIST_SET_MULITCHECKED,[]);
        },
        [ORDERLIST_CANCEL_ORDERS]:async({commit,dispatch,state},payload)=>{
            dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[true,state.loader_msg],{ root: true });
            return axios.post('/cancelorders', {
               orderids:payload

            }).then( (response)=>{
                    if(response.data.done=='ok'){
                        if(state.current_tab=='all_orders'){
                           commit(ORDERLIST_UPDATE_STATUS,{status:'DELETE',orderids:payload});
                        }else{
                            commit(ORDERLIST_REMOVE_ORDERS,payload);
                        }
                    }
                    return Promise.resolve(response);
                })
                .catch((error)=>{
                    return Promise.reject(error);
                }).finally(()=>{
                    dispatch(`${LOADER_MODULE}${HIDE_LOADER}`,{},{ root: true });
                });
        },
        [ORDERLIST_MARK_AS_LATE]:async({commit,dispatch,state},payload)=>{
            dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[true,state.loader_msg],{ root: true });
            return axios.post('/markaslate', {
                orderids:payload

            }).then( (response)=>{
                if(response.data.done=='ok'){

                        commit(ORDERLIST_UPDATE_STATUS,{status:'LATE',orderids:payload});

                }
                return Promise.resolve(response);
            })
                .catch((error)=>{
                    return Promise.reject(error);
                }).finally(()=>{
                    dispatch(`${LOADER_MODULE}${HIDE_LOADER}`,{},{ root: true });
                });
        },
        [ORDERLIST_LOAD_TAB]:({commit,dispatch},payload)=>{
            dispatch(ORDERLIST_SET_CURRENTTAB,payload.tab);
            commit(ORDERLIST_RESET_ORDERLIST);
            commit(ORDERLIST_SET_LIMIT,{skip:0,take:10});
            dispatch(ORDERLIST_LOADERMSG, `Loading ${payload.name.toLowerCase()}...`);
            dispatch(ORDERLIST_LOAD_LIST);
        }
    },
    getters: {

        [ORDERLIST_GET_CURRENT_SELECTED]:state => state[state.current_tab].currently_selected,
        [ORDERLIST_GET_ALL_ORDER_MULITCHECKED]: state=>state[state.current_tab].multi_checked,
        [ORDERLIST_GET_LIST]:state =>state[state.current_tab].order_list,
        [ORDERLIST_GET_SORT]:state =>state[state.current_tab].sort,
        [ORDERLIST_GET_FILTER]:state=> state[state.current_tab].filters,
    }
}