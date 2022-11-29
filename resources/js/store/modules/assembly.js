import {
    RESET_ASSEMBLY_STATE,
    SET_ASSEMBLY_STATE,
    GET_ASSEMBLY_GROUPED_POSTES,
    GET_ASSEMBLY_MAIN_STATES,
    GET_STATS_TODAY,
    GET_STATS_TOMORROW,
    GET_STATS_OVERDUE,
    GET_STATS_LATER,
    SET_INVOICE_LIST,
    GET_INVOICE_LIST,
    GET_STATS_TOTAL,
    INVOICELIST_GET_CURRENT_SELECTED,
    INVOICELIST_SET_CURRENT_SELECTED,
    INVOICELIST_SET_ALL_SELECTED,
    INVOICELIST_GET_ALL_SELECTED,
    INVOICELIST_SET_MULTI_UNCHECKED,
    INVOICE_RESET_MULITCHECKED,
    LOAD_MORE_INVOICE,
    GET_LOADED_INVOICE_COUNT,
    ADD_MORE_INVOICE_TO_LIST,
    GET_TOTAL_INVOICE_COUNT,
    LOADER_MODULE,
    TOASTER_MODULE,
    DISPLAY_LOADER,
    TOASTER_MESSAGE,
    HIDE_LOADER,
    SET_SELECTED_NAV,
    GET_SELECTED_NAV,
    GET_USER_ROLE,
    SET_USER_ROLE

} from "../types/types";
export const assemblyHome = {
    namespaced: true,
    state: {
        postes:     "",
        selected_nav: 'AssemblyHome', //InvoiceList
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
        current_selected: '',
        multi_selected: [],      
        total_invoice_count: 0,  
        skip: 0,
        user_role: 0
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
        [SET_INVOICE_LIST]: (state, payload) => {
            state.invoice_list = payload.invoices;
            state.total_invoice_count = payload.total_count;
            state.skip = state.invoice_list.length;

        },        
        [ADD_MORE_INVOICE_TO_LIST]: (state, payload) => { 
            if(payload.invoices.length > 0){
                state.invoice_list = ( [ ...state.invoice_list, ...payload.invoices ]);
                state.skip = state.invoice_list.length;
            }
        },
        [INVOICELIST_SET_CURRENT_SELECTED]:(state, payload) =>{
            state.multi_selected    =   state.multi_selected.filter(item => item !== state.current_selected);//remove previous from multichecked
            state.current_selected  =   payload;
            let bodytag=document.getElementsByTagName( 'body' )[0]
            if(payload==''){
                bodytag.className='';
            }else{
                bodytag.classList.add('hide-overflowY');
            }
        },
        [INVOICELIST_SET_ALL_SELECTED]:(state, payload)=>{
             if(payload.add)// add from multi_checked
                state.multi_selected.push( payload.id );
             if(!payload.add) // remove from multi_checked
                state.multi_selected  =  state.multi_selected.filter(item => item !== payload.id);
        },        
        [INVOICE_RESET_MULITCHECKED]:(state)=>{
                state.multi_selected = [];
        },        
        [SET_SELECTED_NAV]:(state, payolad)=>{
                state.selected_nav = payolad;
        },        
        [SET_USER_ROLE]:(state, user_role)=>{
                state.user_role = user_role;
        },        
    },
    actions: {
        [RESET_ASSEMBLY_STATE]: ({ commit }) => {
            commit(RESET_ASSEMBLY_STATE);
        },
        [SET_INVOICE_LIST]: ({ commit }, payload) => {
            commit(SET_INVOICE_LIST, payload);
        },
        [SET_ASSEMBLY_STATE]: ({ commit }, payload) => {
            commit('SET_ASSEMBLY_STATE', payload);
        },
        [LOAD_MORE_INVOICE]: async ( { commit, dispatch }, payload ) =>{
            dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading More...'], {root: true});
            await axios.post('/partner-details', payload).then(function (response) {
                commit(ADD_MORE_INVOICE_TO_LIST, response.data);
            })
            .catch(function (error) {
                if(typeof error.response !="undefined")
                dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'},{ root: true });
            }).finally(function(){
                dispatch(`${LOADER_MODULE}${HIDE_LOADER}`,{},{ root: true });
            });            
        },
        [INVOICELIST_SET_CURRENT_SELECTED]:({commit}, payload)=>{
            commit(INVOICELIST_SET_CURRENT_SELECTED,payload);
        },
        [INVOICELIST_SET_ALL_SELECTED]:({ commit, getters }, payload)=>{
            if(!getters.INVOICELIST_GET_ALL_SELECTED.includes(payload))
            commit(INVOICELIST_SET_ALL_SELECTED, { id:payload, add:true } );
        },
        [INVOICELIST_SET_MULTI_UNCHECKED]:({ commit }, payload)=>{
            commit(INVOICELIST_SET_ALL_SELECTED, { id:payload, add:false } );
        },
        [INVOICE_RESET_MULITCHECKED]:({ commit })=>{
            commit(INVOICE_RESET_MULITCHECKED);
        },
        [SET_SELECTED_NAV]:({ commit }, payload)=>{
            commit(SET_SELECTED_NAV, payload);
        },
        [SET_USER_ROLE]:({ commit }, user_role)=>{
            commit(SET_USER_ROLE, user_role);
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
        [GET_INVOICE_LIST]:(state)          => state.invoice_list,
        [INVOICELIST_GET_CURRENT_SELECTED]:state=> state.current_selected,
        [INVOICELIST_GET_ALL_SELECTED]:state=> state.multi_selected,        
        [GET_LOADED_INVOICE_COUNT]:state=> state.invoice_list.length,
        [GET_TOTAL_INVOICE_COUNT]:state=> state.total_invoice_count,
        [GET_SELECTED_NAV]:state=> state.selected_nav,
        [GET_USER_ROLE]:state=> state.user_role,
    },
};
