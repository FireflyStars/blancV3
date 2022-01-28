import {
    DISPLAY_LOADER,
    HIDE_LOADER,
    LOADER_MODULE,
    NEWORDER_GET_CUSTOMER,
    NEWORDER_PRELOAD_FORM,
    NEWORDER_PRELOAD_FORM_GET,
    NEWORDER_PRELOAD_FORM_SET,
    NEWORDER_PRELOAD_ORDER_CUSTOMER_SET,
    NEWORDER_PRELOAD_ORDER_GET,
    NEWORDER_CUR_CUSTOMER
} from "../types/types";
import axios from "axios";

export const neworder= {
    namespaced:true,

    state:{
       order:{
           infoCustomer: null,
           infoOrder_id: null,
           TypeDelivery: null,
       },
        form:{
           TypeDeliveries:[]
        }
    },
    mutations:{
        [NEWORDER_PRELOAD_FORM_SET]:(state,payload)=>{
            state.form.TypeDeliveries=payload.delivery_methods;
        },
        [NEWORDER_PRELOAD_ORDER_CUSTOMER_SET]:(state,payload)=>{
            state.order.infoCustomer=payload;
        },


    },
    actions:{
        [NEWORDER_PRELOAD_FORM]:async ({commit,dispatch},payload)=>{
            dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Preloading new order form'], {root: true});
            return axios.get('/preload-order-form-info', {


            }).then((response) => {
                commit(NEWORDER_PRELOAD_FORM_SET,response.data);
                return Promise.resolve(response);
            })
                .catch((error) => {
                    return Promise.reject(error);
                }).finally(() => {
                    dispatch(`${LOADER_MODULE}${HIDE_LOADER}`, {}, {root: true});
                });
        },
        [NEWORDER_GET_CUSTOMER]:async ({commit,dispatch},payload)=>{
          //  dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Fetching customer details...'], {root: true});
            return axios.post('/customerdetails', {
                CustomerID:payload.CustomerID

            }).then((response) => {
                //console.log(response.data);
                commit(NEWORDER_PRELOAD_ORDER_CUSTOMER_SET,response.data);
                return Promise.resolve(response);
            })
                .catch((error) => {
                    return Promise.reject(error);
                }).finally(() => {
                  //  dispatch(`${LOADER_MODULE}${HIDE_LOADER}`, {}, {root: true});
                });
        }


    },
    getters:{
        [NEWORDER_PRELOAD_FORM_GET]:state=>state.form,
        [NEWORDER_PRELOAD_ORDER_GET]:state=>state.order,
        [NEWORDER_CUR_CUSTOMER]:state=>state.order.infoCustomer,
    }
}
