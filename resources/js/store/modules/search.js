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


} from "../types/types";
import axios from 'axios';

export const search= {
    namespaced:true,
  
    state:{
        loader:'',
        PerPageOrder:'',
        PerPageUser:'',
        PerPageEmails:'',
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
            }
    
     
    },
    getters: {
        [CUSTOMER_GET_LOADER]:state=>state.loader,
        [CUSTOMER_GET_LIST]:state=>state.listcustomers,
        [CUSTOMEREMAILS_GET_LIST]:state=>state.listemailscustomers,
        [CUSTOMERORDERS_GET_LIST]:state=>state.listorderscustomers,
       

    }
}