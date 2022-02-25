import {
    RESET_ASSEMBLY_STATE,
    SET_ASSEMBLY_STATE,
    GET_ASSEMBLY_GROUPED_POSTES,
    GET_ASSEMBLY_MAIN_STATES,
    GET_STATS_TODAY,
    GET_STATS_TOMORROW,
    GET_STATS_OVERDUE,
    GET_STATS_LATER,
    SET_ASSEMBLY_INVOICE,
    GET_ASSEMBLY_INVOICE,
    GET_STATS_TOTAL,
} from "../types/types";
export const assemblyHome = {
    namespaced: true,
    state: {
        postes:     "",
        item:       {},
        reason:     {},
        item_damage:[],
        item_action:null,
        item_preference:[],
        item_post:      [],
        cur_poste:      {},
        history:        null,
        picto:          null,
        types_picto:    null,
        assembly:       [],
        groupedpostes:  [],
        mainAssemblyStats:[],
        stats_today:    null,
        stats_tomorrow: null,
        stats_overdue:  null,
        stats_later:    null,
        all_postes:     [],
        statut_conveyor:[],
        all_tailoring_postes:[],
        current_poste:      {},
        in_tailoring_postes:[],
        invoice_list:       [],
    },
    mutations: {
        [RESET_ASSEMBLY_STATE]: (state) => { 
            state.postes            =   "";
            state.item              =   {};
            state.item_damage       =   [];
            state.item_action       =   [];
            state.item_preference   =   [];
            state.item_post         =   {};
            state.cur_poste         =   {};
            state.history           =   null;
            state.picto             =   null;
            state.types_picto       =   null;
            state.assembly          =   [];
            state.groupedpostes     =   {};
            state.mainAssemblyStats =   [];
            state.stats_total       =   null;
            state.stats_today       =   null;
            state.stats_tomorrow    =   null;
            state.stats_overdue     =   null;
            state.stats_later       =   null; 
        },
        [SET_ASSEMBLY_STATE]: (state, payload) => { 
            state.postes            =   "";
            state.item              =   {};
            state.item_damage       =   [];
            state.item_action       =   [];
            state.item_preference   =   [];
            state.item_post         =   {};
            state.cur_poste         =   {};
            state.history           =   null;
            state.picto             =   null;
            state.types_picto       =   null;
            state.assembly          =   [];
            state.groupedpostes     =   payload.main_stats;
            state.mainAssemblyStats =   payload.grouped_postes;
            state.stats_total       =   payload.stats_total;
            state.stats_today       =   payload.stats_today;
            state.stats_tomorrow    =   payload.stats_tomorrow;
            state.stats_overdue     =   payload.stats_overdue;
            state.stats_later       =   payload.stats_later; 
        },
        [SET_ASSEMBLY_INVOICE]: (state, payload) => { 
            state.invoice_list      =   payload
        },
    },
    actions: {
        [RESET_ASSEMBLY_STATE]: ({ commit }) => {
            commit(RESET_ASSEMBLY_STATE);
        },
        [SET_ASSEMBLY_INVOICE]: ({ commit }, payload) => {
            commit(SET_ASSEMBLY_INVOICE, payload);
        },
        [SET_ASSEMBLY_STATE]: ({ commit }, payload) => {
            commit('SET_ASSEMBLY_STATE', payload);
        },
    },
    getters: {
        [GET_ASSEMBLY_GROUPED_POSTES]:(state)   => state.groupedpostes,
        [GET_ASSEMBLY_MAIN_STATES]:(state)      => state.mainAssemblyStats,
        [GET_STATS_TOTAL]:(state)               => state.stats_total,
        [GET_STATS_TODAY]:(state)               => state.stats_today,
        [GET_STATS_TOMORROW]:(state)            => state.stats_tomorrow,
        [GET_STATS_OVERDUE]:(state)             => state.stats_overdue,
        [GET_STATS_LATER]:(state)               => state.stats_later,
        [GET_ASSEMBLY_INVOICE]:(state)          => state.invoice_list,
    },
};
