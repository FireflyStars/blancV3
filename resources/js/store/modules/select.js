import {GET_CURRENT_SELECT,SET_CURRENT_SELECT} from '../types/types'

export const select= {
    namespaced:true,
    state: {
        current_clicked: '',
    },
    mutations: {
        [SET_CURRENT_SELECT]: (state, payload) => state.current_clicked = payload,
    },
    actions: {

    },
    getters: {
        [GET_CURRENT_SELECT]: state => state.current_clicked,

    }
}