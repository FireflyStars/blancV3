import {
    RESET_ASSEMBLY_STATE
} from "../types/types";
import axios from "axios";

export const assemblyHome = {
    namespaced: true,
    state: {
        postes: "",
        item:{},
        reason:{},
        item_damage:[],
        item_action:null,
        item_preference:[],
        item_post:[],
        cur_poste: {},
        history: null,
        picto:null,
        types_picto:null,
        assembly:[],
        groupedpostes:[],
        mainAssemblyStats:[],
        stats_today:null,
        stats_tomorrow:null,
        stats_overdue:null,
        stats_later:null,
        all_postes:[],
        statut_conveyor:[],
        all_tailoring_postes:[],
        current_poste:{},
        in_tailoring_postes:[],
    },
    mutations: {
        [RESET_ASSEMBLY_STATE]: (state) => { 
            state.postes= "";
            state.item={};
            state.item_damage=[];
            state.item_action=[];
            state.item_preference=[];
            state.item_post={};
            state.cur_poste= {};
            state.history= null;
            state.picto=null;
            state.types_picto=null;
            state.assembly=[];
            state.groupedpostes={};
            state.mainAssemblyStats=[];
            state.stats_today=null;
            state.stats_tomorrow=null;
            state.stats_overdue=null;
            state.stats_later=null; 
        },
    },
    actions: {
        [RESET_ASSEMBLY_STATE]: ({ commit, state }) => {
            commit(RESET_ASSEMBLY_STATE);
        },
    },
    getters: {
    },
};
