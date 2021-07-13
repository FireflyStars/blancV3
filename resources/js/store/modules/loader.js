import {SET_SHOW_LOADER,SET_LOADER_MSG,GET_LOADER_MSG,GET_SHOW_LOADER,DISPLAY_LOADER,HIDE_LOADER} from '../types/types'
//#1first method can be imported by import * as loader from './modules/loader'
// export const state={
//     show_loader:false,
//     loader_msg:'Loading, please wait...',
// };
// export const mutations={
//     [SET_SHOW_LOADER]:(state,payload) => state.show_loader=payload,
//     [SET_LOADER_MSG]:(state,payload) => state.loader_msg=payload
// };
// export const actions={
//     [DISPLAY_LOADER]:({commit},payload) =>{
//         commit(SET_SHOW_LOADER,payload[0]);
//         if(typeof payload[1]!="undefined")
//             commit(SET_LOADER_MSG,payload[1]);
//
//     },
//     [HIDE_LOADER]:({commit})=>{
//         commit(SET_SHOW_LOADER,false);
//         commit(SET_LOADER_MSG,'Loading, please wait...');
//     }
// };
// export const getters={
//     [GET_SHOW_LOADER]:state=>state.show_loader,
//     [GET_LOADER_MSG]:state=>state.loader_msg,
// };



//#2 second method can be imported by import loader  from './modules/loader'
// export default {
//     state: {
//         show_loader: false,
//         loader_msg: 'Loading, please wait...',
//     },
//     mutations: {
//         [SET_SHOW_LOADER]: (state, payload) => state.show_loader = payload,
//         [SET_LOADER_MSG]: (state, payload) => state.loader_msg = payload
//     },
//     actions: {
//         [DISPLAY_LOADER]: ({commit}, payload) => {
//             commit(SET_SHOW_LOADER, payload[0]);
//             if (typeof payload[1] != "undefined")
//                 commit(SET_LOADER_MSG, payload[1]);
//
//         },
//         [HIDE_LOADER]: ({commit}) => {
//             commit(SET_SHOW_LOADER, false);
//             commit(SET_LOADER_MSG, 'Loading, please wait...');
//         }
//     },
//     getters: {
//         [GET_SHOW_LOADER]: state => state.show_loader,
//         [GET_LOADER_MSG]: state => state.loader_msg,
//     }
// }


//#3 second method can be imported by import { loader }  from './modules/loader'
export const loader= {
    namespaced:true,
    state: {
        show_loader: false,
        loader_msg: 'Loading, please wait...',
    },
    mutations: {
        [SET_SHOW_LOADER]: (state, payload) => state.show_loader = payload,
        [SET_LOADER_MSG]: (state, payload) => state.loader_msg = payload
    },
    actions: {
        [DISPLAY_LOADER]: ({commit}, payload) => {
            commit(SET_SHOW_LOADER, payload[0]);
            if (typeof payload[1] != "undefined")
                commit(SET_LOADER_MSG, payload[1]);

        },
        [HIDE_LOADER]: ({commit}) => {
            commit(SET_SHOW_LOADER, false);
            commit(SET_LOADER_MSG, 'Loading, please wait...');
        }
    },
    getters: {
        [GET_SHOW_LOADER]: state => state.show_loader,
        [GET_LOADER_MSG]: state => state.loader_msg,
    }
}