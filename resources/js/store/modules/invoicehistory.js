import { INVOICE_HISTORY_LOAD, INVOICE_HISTORY_GET_LIST, LOADER_MODULE, DISPLAY_LOADER, TOASTER_MODULE, TOASTER_MESSAGE, HIDE_LOADER, INVOICE_HISTORY_SHOWMORE_LIST, INVOICE_HISTORY_RESET_LIST, INVOICE_HISTORY_ADD_TO_LIST} from '../types/types'



//#3 second method can be imported by import { loader }  from './modules/loader'
export const invoicehistory= {
    namespaced:true,
    state: {
        invoices: [],
        skip:0, //show more
        take:10, //show more
        loader_msg:'Loading invoice histories...',
    },
    mutations: {
        [INVOICE_HISTORY_ADD_TO_LIST]: (state, payload) => state.invoices=state.invoices.concat(payload),
        [INVOICE_HISTORY_SHOWMORE_LIST]:(state, payload) =>{
            state.skip=payload.skip;
       },
 
       [INVOICE_HISTORY_RESET_LIST]:state=>state.invoices=[],
    },
    actions: {
       
        [INVOICE_HISTORY_LOAD]: async({commit,state,dispatch},payload) => {
            if(typeof payload!="undefined"&&payload.showmore){
                commit(INVOICE_HISTORY_SHOWMORE_LIST,{skip:state.skip+state.take});
                dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[true,state.loader_msg],{ root: true });
            }else{
                dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[true,state.loader_msg],{ root: true });
                commit(INVOICE_HISTORY_RESET_LIST);
            }

        
            return axios.post('/getinvoicehistories', {
                skip: state.skip,
                take: state.take,
                CustomerID:payload.CustomerID
            })
                .then(function (response) {
                    commit(INVOICE_HISTORY_ADD_TO_LIST, response.data);

                })
                .catch(function (error) {
                    if(typeof error.response !="undefined")
                    dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'},{ root: true });
                }).finally(function(){
                dispatch(`${LOADER_MODULE}${HIDE_LOADER}`,{},{ root: true });
            });

        },

    },
    getters: {
        [INVOICE_HISTORY_GET_LIST]: state => state.invoices,
    }
}