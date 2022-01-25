import {
    SET_STATISTICS,
    GET_STATISTICS,
    STATISTICS_LOAD_LIST,
} from "../types/types";
import axios from "axios";

export const statistics = {
    namespaced: true,
    state: {
        loader: "",
        statistics: {},
    },
    mutations: {
        [SET_STATISTICS]: (state, payload) => (state.statistics = payload),
    },
    actions: {
        [STATISTICS_LOAD_LIST]: async ({ commit, state }, payload) => {
            return axios
                .post("/get-statistics", payload)
                .then((response) => {
                    if (response.data != null) {
                        commit(SET_STATISTICS, response.data);
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
        [GET_STATISTICS]: (state) => state.statistics,
    },
};
