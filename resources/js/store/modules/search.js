import {
    CUSTOMER_SET_LIST,
    CUSTOMER_LOAD_LIST,
    CUSTOMER_SET_LOADER,
    CUSTOMER_GET_LIST,
    CUSTOMER_GET_LOADER,
    CUSTOMEREMAILS_SET_LIST,
    CUSTOMEREMAILS_GET_LIST,
    CUSTOMERORDERS_SET_LIST,
    CUSTOMERORDERS_GET_LIST,
    CUSTOMER_SEARCH_LOAD_LIST,
    CUSTOMER_GET_SEARCH_LIST,
    CUSTOMER_SEARCH_SET_LIST,
    CUSTOMER_SEARCH_SET_COUNT,
    CUSTOMER_GET_SEARCH_COUNT


} from "../types/types";
import axios from 'axios';

export const search= {
    namespaced:true,
  
    state:{
        loader:'',
        PerPageOrder:'',
        PerPageUser:'',
        PerPageEmails:'',
        PerPage:'',
        countcustomers:'',
        listcustomers:{

        },
        listemailscustomers:{

        },
        listorderscustomers:{

        },
        listsearchcustomers:{

        },
    },
    mutations: {
        [CUSTOMER_SET_LIST]: (state,payload) =>state.listcustomers=payload,
        [CUSTOMEREMAILS_SET_LIST]: (state,payload) =>state.listemailscustomers=payload,
        [CUSTOMERORDERS_SET_LIST]: (state,payload) =>state.listorderscustomers=payload,
        [CUSTOMER_SET_LOADER]: (state, payload) => state.loader = payload,
        [CUSTOMER_SEARCH_SET_LIST]: (state,payload) =>state.listsearchcustomers=payload,
        [CUSTOMER_SEARCH_SET_COUNT]: (state,payload) =>state.countcustomers=payload,
    },
    actions: {
        [CUSTOMER_LOAD_LIST]:async ({commit,state},payload )=>{

            commit(CUSTOMER_SET_LOADER,'animate40');
           
            if(payload.showmore == 'search_name'){
                state.PerPageUser = parseInt(state.PerPageUser) + 10;
            }
            if(payload.showmore == 'search_email'){
                state.PerPageEmails = parseInt(state.PerPageEmails) + 10;    
            }
            if(payload.showmore == 'search_order'){
                state.PerPageOrder = parseInt(state.PerPageOrder) + 10;
            }
            else if(payload.showmore == undefined) {
                state.PerPageUser = '3';
                state.PerPageEmails = '3';
                state.PerPageOrder = '3';
            }


            return axios.post('/SearchCustomer', {
                query:payload.query ,
                PerPageOrder:state.PerPageOrder,
                PerPageUser:state.PerPageUser, 
                PerPageEmails:state.PerPageEmails})
            .then( (response)=>{

                if(response.data.customers.data!=null){
                        commit(CUSTOMER_SET_LIST ,response.data.customers.data);
                        commit(CUSTOMEREMAILS_SET_LIST ,response.data.customers_emails.data);
                        commit(CUSTOMERORDERS_SET_LIST ,response.data.customers_orders.data);
                }
                return Promise.resolve(response);
            })
                .catch((error)=>{
                    return Promise.reject(error);
                }).finally(()=>{
                    commit(CUSTOMER_SET_LOADER,'animate40 animate100');
                });
            },

    
            [CUSTOMER_SEARCH_LOAD_LIST]:async ({commit,state},payload )=>{
                commit(CUSTOMER_SET_LOADER,'animate40');

                if(typeof payload!="undefined"&&payload.showmore)
                {
                    state.PerPage = parseInt(state.PerPage) + 10;
                }else{
                    state.PerPage = '4';
                }
             

                return axios.post('/SearchByCustomer', {
                    query:payload.query ,
                    PerPage:state.PerPage})
                .then( (response)=>{
                    if(response.data!=null){
                            commit(CUSTOMER_SEARCH_SET_LIST ,response.data.data);
                            commit(CUSTOMER_SEARCH_SET_COUNT ,(response.data.total - state.PerPage));
                            
                    }
                    return Promise.resolve(response);
                })
                    .catch((error)=>{
                        return Promise.reject(error);
                    }).finally(()=>{
                        commit(CUSTOMER_SET_LOADER,'animate40 animate100');
                    });
                }
    
     
    },
    getters: {
        [CUSTOMER_GET_LOADER]:state=>state.loader,
        [CUSTOMER_GET_LIST]:state=>state.listcustomers,
        [CUSTOMEREMAILS_GET_LIST]:state=>state.listemailscustomers,
        [CUSTOMERORDERS_GET_LIST]:state=>state.listorderscustomers,
        [CUSTOMER_GET_SEARCH_LIST]:state=>state.listsearchcustomers,
        [CUSTOMER_GET_SEARCH_COUNT]:state=>state.countcustomers,
       

    }
}