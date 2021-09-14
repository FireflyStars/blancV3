import {SIDEBAR_GET_SLIDEIN, SIDEBAR_SET_SLIDEIN} from "../types/types";

export const sidebar= {
    namespaced:true,
    state: {
        slidein: false,
    },
    mutations: {
        [SIDEBAR_SET_SLIDEIN]: (state) => state.slidein = !state.slidein,
    },
    actions: {

    },
    getters: {
        [SIDEBAR_GET_SLIDEIN]: state => state.slidein,

    }
}