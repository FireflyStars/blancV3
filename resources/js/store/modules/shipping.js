import {
    SHIPPING_MODULE,
    GET_PARTNERS,
    SET_PARTNERS,
    SHIPPING_LOAD_LIST,
} from "../types/types";
import axios from 'axios';

export const shipping = {
    namespaced:true,
    state:{
        partners:{},
    },
    getters:{
        [GET_PARTNERS]: (state) => state.partners,
    },
    mutations:{
        [SET_PARTNERS]: (state,payload) => (state.partners = payload),
    },
    actions:{
        [SHIPPING_LOAD_LIST]:async ({ commit, state }, payload) => {
            return axios
                .post("/get-shipping-partners", payload)
                .then((response) => {
                    if (response.data != null) {
                        commit(SET_PARTNERS, response.data.partners);
                    }
                    return Promise.resolve(response);
                })
                .catch((error) => {
                    return Promise.reject(error);
                })
                .finally(() => { });
        }
    }
}
