import {
    SET_DETAILING,
    GET_DETAILING,
    INIT_DETAILING,
    UPDATE_DETAILING,
    GET_DETAILING_LIST,
    SET_DETAILING_LIST
} from "../types/types";
import axios from "axios";

export const detailingItem = {
    namespaced: true,
    state: {
        loader: "",
        detailing_item: {},
        detailingList: {}
    },
    mutations: {
        [SET_DETAILING]: (state, payload) => state.detailing_item = payload,
        [SET_DETAILING_LIST]: (state, payload) => state.detailingList = payload
    },
    actions: {
        [INIT_DETAILING]: async ({ commit, state }, payload) => {
            return axios
                .post("/init-detailing", payload)
                .then((response) => {
                    if (response.data != null) {
                        commit(SET_DETAILING, response.data);
                    }
                    return Promise.resolve(response);
                })
                .catch((error) => {
                    return Promise.reject(error);
                })
                .finally(() => { });
        },
        [UPDATE_DETAILING]: async ({ commit, state }, payload) => {
            return axios
                .post("/update-detailing", payload)
                .then((response) => {
                    if (response.data != null) {
                        commit(SET_DETAILING, response.data);
                    }
                    return Promise.resolve(response);
                })
                .catch((error) => {
                    return Promise.reject(error);
                })
                .finally(() => { });
        },
        [GET_DETAILING_LIST]: async ({ commit, state }, payload) => {
            return axios
                .post("/get-detailing-list", payload)
                .then((response) => {
                    if (response.data != null) {
                        commit(SET_DETAILING_LIST, response.data);
                    }
                    return Promise.resolve(response);
                })
                .catch((error) => {
                    return Promise.reject(error);
                })
                .finally(() => { });
        },
    },
    getters: {
        [GET_DETAILING]: (state) => state.detailing_item,
        [GET_DETAILING_LIST]: (state) => state.detailingList,
    },
};
