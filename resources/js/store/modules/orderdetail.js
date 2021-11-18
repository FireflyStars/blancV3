import {
    ORDERDETAIL_SET_LOADER,
    ORDERDETAIL_LOAD_DETAIL,
    ORDERDETAIL_GET_LOADER,
    ORDERDETAIL_SET_DETAILS,
    ORDERDETAIL_GET_DETAILS,
    ORDERDETAIL_MULTI_ITEMS_CHECKED,
    ORDERDETAIL_MULTI_ITEMS_ADD,
    ORDERDETAIL_MULTI_ITEMS_UNCHECKED,
    ORDERDETAIL_MULTI_ITEMS_REMOVE,
    ORDERDETAIL_GET_ALL_ITEMS_MULITCHECKED,
    ORDERDETAIL_UPDATE_SUGGESTED_DELIVERY_DATE,
    ORDERDETAIL_SPLIT,
    ORDERDETAIL_NEW_DELIVERY_DATE,
    ORDERDETAIL_UPDATE, ORDERDETAIL_SET_ITEMS,

} from "../types/types";
import axios from "axios";


export const orderdetail= {
    namespaced:true,

    state:{
        loader:'',
        orderdetail:{

        },
        selected_items:{

        }
    },
    mutations: {
        [ORDERDETAIL_SET_LOADER]: (state, payload) => state.loader = payload,
        [ORDERDETAIL_SET_DETAILS]: (state,payload) =>state.orderdetail=payload,
        [ORDERDETAIL_MULTI_ITEMS_ADD]:(state, payload)=>{
            if(typeof state.selected_items[payload.suborder]==="undefined")
                state.selected_items[payload.suborder]=[];
            if(state.selected_items[payload.suborder].includes(payload.infoitems_id)===false)
            state.selected_items[payload.suborder].push(payload.infoitems_id);
        },
        [ORDERDETAIL_MULTI_ITEMS_REMOVE]:(state, payload)=>{

            state.selected_items[payload.suborder]=state.selected_items[payload.suborder].filter(item=>item!==payload.infoitems_id);
            if(state.selected_items[payload.suborder].length==0){
                delete state.selected_items[payload.suborder];
            }
        },



        [ORDERDETAIL_UPDATE]:(state, payload)=>{
            for(const prop in payload){
                if(typeof state.orderdetail.detail[prop]!=="undefined")
                    state.orderdetail.detail[prop]=payload[prop];
            }

        },
        [ORDERDETAIL_SET_ITEMS]:(state,payload)=>{
            state.orderdetail.items=payload;
        }
    }
    ,
    actions:{
        [ORDERDETAIL_SPLIT]:async ({commit,state})=>{
            commit(ORDERDETAIL_SET_LOADER,'animate40');


            return axios.post('/splititems', {
                infoOrder_id:state.orderdetail.detail.id,
                items:state.selected_items

            }).then( (response)=>{
                if(response.data.items!=null){

                    commit(ORDERDETAIL_SET_ITEMS,response.data.items);

                }
                return Promise.resolve(response);
            })
                .catch((error)=>{
                    return Promise.reject(error);
                }).finally(()=>{
                    commit(ORDERDETAIL_SET_LOADER,'animate40 animate100');
                });
        },

        [ORDERDETAIL_LOAD_DETAIL]:async ({commit},infoOrder_id)=>{
            commit(ORDERDETAIL_SET_LOADER,'animate40');


            return axios.post('/getorderdetail', {
                infoOrder_id:infoOrder_id

            }).then( (response)=>{
                if(response.data.order.detail!=null){

                        commit(ORDERDETAIL_SET_DETAILS,response.data.order);



                }
                return Promise.resolve(response);
            })
                .catch((error)=>{
                    return Promise.reject(error);
                }).finally(()=>{
                    commit(ORDERDETAIL_SET_LOADER,'animate40 animate100');
                });

        },
        [ORDERDETAIL_MULTI_ITEMS_CHECKED]:({commit}, payload)=>commit(ORDERDETAIL_MULTI_ITEMS_ADD,payload),
        [ORDERDETAIL_MULTI_ITEMS_UNCHECKED]:({commit}, payload)=>commit(ORDERDETAIL_MULTI_ITEMS_REMOVE,payload),
        [ORDERDETAIL_UPDATE_SUGGESTED_DELIVERY_DATE]:async ({commit,state},payload)=>{

            return axios.post('/suggestdate', {
                infoOrder_id:state.orderdetail.detail.id,
                suggested_delivery_date:payload

            }).then( (response)=>{
                if(response.data.updated!==false){

                    commit(ORDERDETAIL_UPDATE,{suggestedDeliveryDate:payload});

                }
                return Promise.resolve(response);
            })
                .catch((error)=>{
                    return Promise.reject(error);
                }).finally(()=>{

                });
        },
        [ORDERDETAIL_NEW_DELIVERY_DATE]:async({commit,state},payload)=>{
            return axios.post('/newdeliverydate', {
                infoOrder_id:state.orderdetail.detail.id,
                PromisedDate:payload.PromisedDate,
                timeslot:payload.timeslot

            }).then( (response)=>{
                if(response.data.updated!==false){

                    commit(ORDERDETAIL_UPDATE,{suggestedDeliveryDate:null});

                }
                return Promise.resolve(response);
            })
                .catch((error)=>{
                    return Promise.reject(error);
                }).finally(()=>{

                });
        }

    },
    getters:{
        [ORDERDETAIL_GET_LOADER]:state=>state.loader,
        [ORDERDETAIL_GET_DETAILS]:state=>state.orderdetail,
        [ORDERDETAIL_GET_ALL_ITEMS_MULITCHECKED]:state=>state.selected_items,
    }
}