import {
    SCAN_GET_ITEMS,
    SCAN_GET_BAGS,
    SCAN_SET_ITEMS,
    SCAN_SET_BAGS,
    SCAN_ITEM

} from "../types/types";
import axios from 'axios';

export const scan= {
    namespaced:true,
  
    state:{
        loader:'',
        
        details_bags:{

        },
        details_items:{

        },
       
    },
    mutations: {
        [SCAN_SET_ITEMS]: (state,payload) =>state.details_items=payload,
        [SCAN_SET_BAGS]: (state,payload) =>state.details_bags=payload,
      
    },
    actions: {
        [SCAN_ITEM]:async ({commit,state},payload )=>{
            return axios.post('/ScanItemAndBag', {
                info_tracking:payload.info_tracking })
            .then( (response)=>{
                if(response.data!=null){
                    if(response.data.bag != undefined){
                        commit(SCAN_SET_BAGS ,response.data.bag);
                    } else if(response.data.item != undefined){
                        commit(SCAN_SET_ITEMS ,response.data.item);
                    }
                }
                return Promise.resolve(response);
            })
                .catch((error)=>{
                    return Promise.reject(error);
                }).finally(()=>{
                  
                });
            },
            
    },
    getters: {
        [SCAN_GET_ITEMS]:state=>state.details_items,
        [SCAN_GET_BAGS]:state=>state.details_bags,
    }
}