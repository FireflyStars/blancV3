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
    NEWORDER_CUR_CUSTOMER,
    NEWORDER_GET_ALL_TIMESLOTS,
    NEW_ORDER_GET_TRANCHE_POSTCODE,
    NEW_ORDER_TRANCHE_POSTCODE,
    NEW_ORDER_SET_TRANCHE_POSTCODE,
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
        },
        timeslot_def:[
            {
                value:1,
                display:'8-10 am',
                available:false
            },
            {
                value:3,
                display:'10-12 pm',
                available:false
            },
            {
                value:5,
                display:'12-2 pm',
                available:false
            },
            {
                value:7,
                display:'2-4 pm',
                available:false
            },
            {
                value:9,
                display:'4-6 pm',
                available:false
            },
            {
                value:11,
                display:'6-8 pm',
                available:false
            },
            {
                value:13,
                display:'8-8 pm',
                available:true
            }
        ],
        tranches:[],
    },
    mutations:{
        [NEWORDER_PRELOAD_FORM_SET]:(state,payload)=>{
            state.form.TypeDeliveries=payload.delivery_methods;
        },
        [NEWORDER_PRELOAD_ORDER_CUSTOMER_SET]:(state,payload)=>{
            state.order.infoCustomer=payload;
        },
        [NEW_ORDER_TRANCHE_POSTCODE]:(state,payload)=>{
            state.tranches = payload;
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
                commit(NEWORDER_PRELOAD_ORDER_CUSTOMER_SET,response.data.customer);
                return Promise.resolve(response);
            })
                .catch((error) => {
                    return Promise.reject(error);
                }).finally(() => {
                  //  dispatch(`${LOADER_MODULE}${HIDE_LOADER}`, {}, {root: true});
                });
        },
        [NEW_ORDER_SET_TRANCHE_POSTCODE]:async ({commit,dispatch},payload)=>{
            dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading tranche'], {root: true});
            return axios.post('/get-tranche-by-postcode',{
                postcode: payload
            }).then((response)=>{
                commit(NEW_ORDER_TRANCHE_POSTCODE,response.data.available_slots);
                return Promise.resolve(response);

                //console.log(response);
            }).catch((error)=>{
                return Promise.reject(error);
            }).finally(()=>{
                dispatch(`${LOADER_MODULE}${HIDE_LOADER}`, {}, {root: true});
            });
        }



    },
    getters:{
        [NEWORDER_PRELOAD_FORM_GET]:state=>state.form,
        [NEWORDER_PRELOAD_ORDER_GET]:state=>state.order,
        [NEWORDER_CUR_CUSTOMER]:state=>state.order.infoCustomer,
        [NEWORDER_GET_ALL_TIMESLOTS]:state=>state.timeslot_def,
        [NEW_ORDER_GET_TRANCHE_POSTCODE]:state=>state.tranches,
    }
}
