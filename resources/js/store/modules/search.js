import {
    CUSTOMERLIST_SET_DETAILS,
    CUSTOMERLIST_LOAD_DETAILS,
    CUSTOMERLIST_SET_LOADER,
    CUSTOMERLIST_GET_DETAILS,
    CUSTOMERLIST_GET_LOADER,
    CUSTOMEREMAILS_SET_LIST,
    CUSTOMEREMAILS_LOAD_LIST,
    CUSTOMEREMAILS_GET_LIST,
    CUSTOMERORDERS_SET_LIST,
    CUSTOMERORDERS_LOAD_LIST,
    CUSTOMERORDERS_GET_LIST

} from "../types/types";
import axios from 'axios';

export const search= {
    namespaced:true,
  
    state:{
        loader:'',
        listcustomers:{

        },
        listemailscustomers:{

        },
        listorderscustomers:{

        },
    },
    mutations: {
        [CUSTOMERLIST_SET_DETAILS]: (state,payload) =>state.listcustomers=payload,
        [CUSTOMEREMAILS_SET_LIST]: (state,payload) =>state.listemailscustomers=payload,
        [CUSTOMERORDERS_SET_LIST]: (state,payload) =>state.listorderscustomers=payload,
        [CUSTOMERLIST_SET_LOADER]: (state, payload) => state.loader = payload,
       
    },
    actions: {
        [CUSTOMERLIST_LOAD_DETAILS]:async ({commit},query)=>{
            commit(CUSTOMERLIST_SET_LOADER,'animate40');

            return axios.post('/SearchCustomerByName', query)
            .then( (response)=>{
                console.log(response)
                if(response.data.customers.data!=null){
                        commit(CUSTOMERLIST_SET_DETAILS ,response.data.customers.data);
                }
                return Promise.resolve(response.data.customers.data);
            })
                .catch((error)=>{
                    return Promise.reject(error);
                }).finally(()=>{
                    commit(CUSTOMERLIST_SET_LOADER,'animate40 animate100');
                });

        },

        [CUSTOMEREMAILS_LOAD_LIST]:async ({commit},query)=>{

            return axios.post('/SearchCustomerByEmail', query)
            .then( (response)=>{
                console.log(response)
                if(response.data.customers_emails.data!=null){
                        commit(CUSTOMEREMAILS_SET_LIST ,response.data.customers_emails.data);
                }
                return Promise.resolve(response.data.customers_emails.data);
            })
                .catch((error)=>{
                    return Promise.reject(error);
                }).finally(()=>{
                   
                });

        },

        [CUSTOMERORDERS_LOAD_LIST]:async ({commit},query)=>{

            return axios.post('/SearchCustomerByOrder', query)
            .then( (response)=>{
                if(response.data.customers_orders.data!=null){
                        commit(CUSTOMERORDERS_SET_LIST ,response.data.customers_orders.data);
                }
                return Promise.resolve(response.data.customers_orders.data);
            })
                .catch((error)=>{
                    return Promise.reject(error);
                }).finally(()=>{
                });

        },
    },
    getters: {
        [CUSTOMERLIST_GET_LOADER]:state=>state.loader,
        [CUSTOMERLIST_GET_DETAILS]:state=>state.listcustomers,
        [CUSTOMEREMAILS_GET_LIST]:state=>state.listemailscustomers,
        [CUSTOMERORDERS_GET_LIST]:state=>state.listorderscustomers,
       

    }
}