import {
    ITEM_DETAIL_SET_LOADER,
    ITEM_DETAIL_GET_LOADER,
    ITEM_DETAIL_SET_DETAIL,
    ITEM_DETAIL_GET_DETAIL,
    ITEM_DETAIL_LOAD_DETAIL
} from "../types/types";
import axios from "axios";


export const itemDetail= {
    namespaced: true,

    state:{
        loader:'animate40',
        itemdetail:{
            breif_info: {
                id: '',
            },
            location_history: []
        },
        selected_items:{

        }
    },
    mutations: {
        [ITEM_DETAIL_SET_LOADER]: (state, payload) => state.loader = payload,
        [ITEM_DETAIL_SET_DETAIL]: (state, payload) => state.itemdetail = payload,
    },
    actions:{
        [ITEM_DETAIL_LOAD_DETAIL]: async ({commit},item_id)=>{
            commit(ITEM_DETAIL_SET_LOADER,'animate40');
            return await axios.post('/getitemdetail', {
                item_id: item_id
            }).then( (response)=>{
                commit(ITEM_DETAIL_SET_DETAIL, response.data.item_detail);
                return Promise.resolve(response);
            })
            .catch((error)=>{
                return Promise.reject(error);
            }).finally(()=>{
                commit(ITEM_DETAIL_SET_LOADER,'animate40 animate100');
            });
        },
        [ITEM_DETAIL_SET_DETAIL]: ( { commit }, payload )=>{
            commit(ITEM_DETAIL_SET_DETAIL, payload);
        }
    },
    getters:{
        [ITEM_DETAIL_GET_LOADER]:state=>state.loader,
        [ITEM_DETAIL_GET_DETAIL]:state=>state.itemdetail,
    }
}