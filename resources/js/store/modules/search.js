import {
    CUSTOMER_SET_LIST,
    CUSTOMER_LOAD_LIST,
    CUSTOMER_SET_LOADER,
    CUSTOMER_GET_LIST,
    CUSTOMER_GET_LOADER,
    CUSTOMEREMAILS_SET_LIST,
    CUSTOMEREMAILS_GET_LIST,
    CUSTOMERORDERS_SET_LIST,
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
        [CUSTOMER_SET_LIST]: (state,payload) =>state.listcustomers=payload,
        [CUSTOMEREMAILS_SET_LIST]: (state,payload) =>state.listemailscustomers=payload,
        [CUSTOMERORDERS_SET_LIST]: (state,payload) =>state.listorderscustomers=payload,
        [CUSTOMER_SET_LOADER]: (state, payload) => state.loader = payload,
       
    },
    actions: {
        [CUSTOMER_LOAD_LIST]:async ({commit},query ,PerPage)=>{
            commit(CUSTOMER_SET_LOADER,'animate40');

            return axios.post('/SearchCustomer', query , PerPage)
            .then( (response)=>{
                console.log(response)
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
    },
    getters: {
        [CUSTOMER_GET_LOADER]:state=>state.loader,
        [CUSTOMER_GET_LIST]:state=>state.listcustomers,
        [CUSTOMEREMAILS_GET_LIST]:state=>state.listemailscustomers,
        [CUSTOMERORDERS_GET_LIST]:state=>state.listorderscustomers,
       

    }
}