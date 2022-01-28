import {
    SET_DETAILING,
    GET_DETAILING,
    INIT_DETAILING,
    UPDATE_DETAILING,
} from "../types/types";
import axios from "axios";

export const detailingItem = {
    namespaced: true,
    state: {
        loader: "",
        statistics: {},
    },
    mutations: {
        [SET_DETAILING]: (state, payload) => (state.statistics = payload),
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
    },
    getters: {
        [GET_DETAILING]: (state) => state.statistics,
    },
};
