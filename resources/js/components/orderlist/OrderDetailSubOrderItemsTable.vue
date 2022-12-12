<template>
    <div class="container-fluid position-relative">
        <section class="itemlist-table " v-if="Object.entries(ITEM_LIST).length !== 0">
            <div class="suborder" v-for="(ITEMS,suborder) in ITEM_LIST" :key="suborder">
                <transition-group tag="div" class="position-relative" name="list" appear>
                    <div class="suborderheader" v-if="Object.entries(ITEM_LIST).length !== 0">
                        <span class="col-9  subordernum body_small_medium">
                            Sub order {{suborder}}
                        </span>
                        <div class="col-3 list-options-suborder" >
                            <div class='column1'  >
                                <img  class="img-arrow" src="/images/flesh.png" />
                            </div>
                            <div class='column2' >
                                <img  class="img-arrow" @click="setSubOrderFulfilled(ITEMS[0].InvoiceID , ITEMS[0].Invoice_Status)" src="/images/check.png" />
                            </div>   
                            <div class='column3' >
                                <img  class="img-arrow" src="/images/download.png" @click="openModal(ITEMS[0].InvoiceID)" />
                            </div>
                            <div class='column4' >
                                <img  class="img-arrow" @click="OpenSubOrderOptions(suborder)"  src="/images/menu.png"   :class="{ active: show === suborder }"/>   
                            </div>
                        </div>
                    </div>

                    <SubOrderOptions @free-reclean="freeReClean" @re-assign="reAssign"  v-if="show === suborder && open_options" :suborder=suborder :user=user :items="Object.entries(ITEMS)" :item_selected="Object.entries(MULTI_CHECKED)" :invoice_id="ITEMS[0].InvoiceID" :invoice_Status="ITEMS[0].Invoice_Status" :ListTrackingKey="Object.entries(MULTI_HSL_CHECKED)"></SubOrderOptions>
                    
                        <header v-if="Object.entries(ITEM_LIST).length !== 0">
                            <div class="tcol noselect"  v-for="(col,index) in tabledef" :key="index" :style="{flex:col.flex,'text-align':col.header_align}" :class="{'sortable': col.sortable,'check-box': col.type=='checkbox'}" >{{col.name}}
                                <check-box v-if="col.type=='checkbox'&& ITEMS.length>0" :checked_checkbox="typeof MULTI_CHECKED[suborder]!=='undefined'&&ITEMS.length==MULTI_CHECKED[suborder].length"  @checkbox-clicked="checkboxallclicked" :name="suborder"></check-box>
                            </div>
                        </header>
                    <div class="trow" v-for="ITEM in ITEMS" :key="ITEM.infoitems_id">
                        <template v-for="(col,index) in tabledef" :key="index">
                            <div class="tcol"   :style="{flex:col.flex}" :class="{'check-box': col.type=='checkbox',[index]:true}"  @click="selectrow(ITEM.infoitems_id,index ,ITEMS[0].InvoiceID)"  >
                                <check-box v-if="col.type=='checkbox'" :checked_checkbox="typeof MULTI_CHECKED[suborder]!=='undefined'&&MULTI_CHECKED[suborder].includes(ITEM.infoitems_id)" :id="ITEM.infoitems_id"   @checkbox-clicked="checkboxclicked" :name="suborder" :trackingkey="ITEM.ItemTrackingKey"></check-box>
                                <tag v-else-if="col.type=='tag'" :name="ITEM[index]" :style="{backgroundColor:'transparent',border:'1px solid #000000',color:'#000000'}"></tag>
                                <color-tag :style="col.css" v-else-if="col.type=='color'" :colors="ITEM[index].toLowerCase()"></color-tag>
                                <span v-else :style="col.css" class="tool-tip"  :class="{body_small_medium:col.name=='',body_small:col.name!=''}" :data-tooltip="preprocess(col,ITEM[index])">{{preprocess(col,ITEM[index])}}</span>
                            </div>                         
                        </template>
                    </div>
                </transition-group>
            </div>
        </section>
        <section class="nodata p-2" v-if="Object.entries(ITEM_LIST).length === 0">
           <button v-if="(status == 'RECURRING' || status == 'SCHEDULED') && hideButton(order)" class="detail-btn detail-btn-detail-order text-center" @click="EditOrder()"> Detail order </button>
           <p v-if="status != 'RECURRING' && status != 'SCHEDULED' ">No items available.</p>
        </section>
        <!-- <transition name="trans-batch-actions">
            <div class=" batch-actions" v-if="Object.entries(MULTI_CHECKED).length !== 0"><button class="btn btn-outline-dark body_medium"  @click="show_split_conf">Split</button><button class="btn btn-outline-dark body_medium"  @click="featureunavailable('Delete items')">Delete</button></div>
        </transition> -->
         <FulfillConfirmation  :invoice_id= [invoiceId] :show_conf="show_model_Fulfil" @close="show_model_Fulfil=false"></FulfillConfirmation>
    </div>
    <ItemDetail @close="OpenitemDetails = false" class="modal-item" v-if = "OpenitemDetails" :item_id = ItemId :invoiceId = invoiceId ></ItemDetail>
    <qz-print ref="qz_printer"></qz-print>
</template>

<script>
    import {useRouter,useRoute} from 'vue-router'
    import {ref,computed } from 'vue';
    import {useStore} from 'vuex';
    import QzPrint from "../QzPrint";

    import {
        ORDERLIST_MODULE,
        LOADER_MODULE,
        GET_SHOW_LOADER,
        ORDERLIST_LOADERMSG,
        ORDERLIST_RESET_MULITCHECKED,
        TOASTER_MODULE,
        TOASTER_MESSAGE,
        ORDERLIST_CANCEL_ORDERS,
        ORDERLIST_LOAD_TAB,
        ORDERLIST_MARK_AS_LATE,
        ORDERDETAIL_MODULE,
        ORDERDETAIL_GET_DETAILS,
        ORDERDETAIL_MULTI_ITEMS_CHECKED,
        ORDERDETAIL_MULTI_ITEMS_UNCHECKED, 
        ORDERDETAIL_GET_ALL_ITEMS_MULITCHECKED,
        ORDERDETAIL_GET_ALL_HSL_MULITCHECKED,
        DISPLAY_LOADER,
        HIDE_LOADER,
        ASSEMBLY_HOME_MODULE,
        SET_SELECTED_NAV
    } from '../../store/types/types';
    import Tag from  '../miscellaneous/Tag'
    import CheckBox from '../miscellaneous/CheckBox'
    import ColorTag from "../miscellaneous/ColorTag";
    import SubOrderOptions from "../miscellaneous/SubOrderOptions";
    import FulfillConfirmation from "../miscellaneous/FulfillConfirmation";
    import ItemDetail from "../assembly/ItemDetail";

    export default {
        name: "OrderDetailSubOrderItemsTable",
        props:['tabledef',"tab","id" , "status" , "user" , "order"],
        components:{ColorTag, Tag,CheckBox, SubOrderOptions, QzPrint , FulfillConfirmation , ItemDetail},
        emits: ['freeReclean', 'show_conf', 'close', 'reAssign'],
        setup(props,context){
            const router = useRouter();
            const store=useStore();
            const route = useRoute();
            const orderId = ref(0);
            const open_options = ref(false);
            const show = ref('');
            const show_model_Fulfil = ref(false);
            const qz_printer = ref(null);
            const invoiceId = ref('');
            const ItemId = ref('');
            const ListTrackingKey = ref([]);
            const OpenitemDetails = ref(false);

            const ITEM_LIST=computed(()=>{
                return store.getters[`${ORDERDETAIL_MODULE}${ORDERDETAIL_GET_DETAILS}`].items;
            });

            const MULTI_CHECKED=computed(()=>{
                return store.getters[`${ORDERDETAIL_MODULE}${ORDERDETAIL_GET_ALL_ITEMS_MULITCHECKED}`];
            });
             const MULTI_HSL_CHECKED=computed(()=>{
                return store.getters[`${ORDERDETAIL_MODULE}${ORDERDETAIL_GET_ALL_HSL_MULITCHECKED}`];
            });

            const hideButton=((order)=>{

                if(new Date(order.order_left_date) <= new Date()){
                             return true;
                 }
            });


            function preprocess(def,val) {

                if(typeof def.type!="undefined"&&def.type=="price"){

                    return "£"+(val!=0?val.toFixed(2):0);
                }
                return val;
            }

            function selectrow(id,colname,invoice_ID){
                OpenitemDetails.value = false
                store.dispatch(`${ASSEMBLY_HOME_MODULE}${SET_SELECTED_NAV}`, "OrderDetails")
                if(colname=='line_select') return;
                  setTimeout(function(){  
                   ItemId.value = id
                   OpenitemDetails.value = true
                   invoiceId.value = invoice_ID
               }  
              , 5)   
            }

            function checkboxclicked(check,id , name ,order ,trackingkey) {
                if(check===true){
                    store.dispatch(`${ORDERDETAIL_MODULE}${ORDERDETAIL_MULTI_ITEMS_CHECKED}`,{infoitems_id:id,suborder:name,trackingkey:trackingkey});
                    ListTrackingKey.value.push(trackingkey)
                }
                if(check===false){
                    store.dispatch(`${ORDERDETAIL_MODULE}${ORDERDETAIL_MULTI_ITEMS_UNCHECKED}`,{infoitems_id:id,suborder:name,trackingkey:trackingkey});
                    ListTrackingKey.value.splice(ListTrackingKey.value.findIndex((z) => { return z === trackingkey }), 1);
                }
                close()
            }
            function checkboxallclicked(check,id,suborder,trackingkey) {
                const   list=_.cloneDeep(ITEM_LIST.value[suborder]);
                        list.forEach((item) => {
                            if(check) {
                                store.dispatch(`${ORDERDETAIL_MODULE}${ORDERDETAIL_MULTI_ITEMS_CHECKED}`, {
                                    infoitems_id: item.infoitems_id,
                                    suborder,
                                    suborder,
                                    trackingkey:item.ItemTrackingKey
                                })
                            }else{
                                store.dispatch(`${ORDERDETAIL_MODULE}${ORDERDETAIL_MULTI_ITEMS_UNCHECKED}`, {
                                    infoitems_id: item.infoitems_id,
                                    suborder,
                                    suborder,
                                    trackingkey:item.ItemTrackingKey
                                })
                    
                            }
                        });
              
                close()
            }

            const close=()=>{
                context.emit('close');
                open_options.value=false;
                OpenitemDetails.value = false;
            }
            const featureunavailable=((feature)=>{
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:feature+' feature not yet implemented.',ttl:5,type:'success'});
            });
            const cancelorders =(()=>{
               /* store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOADERMSG}`,`Cancelling ${MULTI_CHECKED.value.length} order(s), please wait...`);
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_CANCEL_ORDERS}`,MULTI_CHECKED.value).then(()=>{
                    if(ORDER_LIST.value.length==0){
                            store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOAD_TAB}`,{tab:props.id,name:props.tab.name});
                    }
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'Order(s) cancelled successfully.',ttl:5,type:'success'});
                    store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_RESET_MULITCHECKED}`);
                }).catch((error)=>{
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'});
                });
*/
            });

            function EditOrder(){
                orderId.value = route.params.order_id
                router.push('/order-content/'+orderId.value);
            }

            const show_split_conf=()=>{
                context.emit("show_conf");
            }
        
            function setSubOrderFulfilled(suborderid , Invoice_Status){
                if(Invoice_Status == "FULFILLED"){
                    show_model_Fulfil.value = false
                }else {
                    show_model_Fulfil.value = true
                }
                  
                  invoiceId.value = suborderid
            }

            function OpenSubOrderOptions(idx){
                this.open_options = !this.open_options
                if(this.open_options == true)  {
                    show.value = idx 
                
                } else {
                    show.value = ''
                }
                  
            }

            const freeReClean = (itemId, subOrder)=>{
                open_options.value = false;
                context.emit('freeReclean', itemId, subOrder);
            }
            const reAssign = (suborder, invoice_id)=>{
                open_options.value = false;
                context.emit('reAssign', suborder, invoice_id);
            }
          

            return {
                route,

                MULTI_CHECKED,
                MULTI_HSL_CHECKED,
                ITEM_LIST,

                preprocess,
                selectrow,
                checkboxclicked,
                show_split_conf,

                checkboxallclicked,
                featureunavailable,
                cancelorders,
                EditOrder,
                setSubOrderFulfilled,
                OpenSubOrderOptions,
                open_options,
                qz_printer,
                show,
                show_model_Fulfil,
                invoiceId,
                close,
                OpenitemDetails,
                ItemId,
                ListTrackingKey,
                hideButton,
                freeReClean,
                reAssign
            }
            
        },

        methods:{
        openModal(InvoiceID){
            this.OpenitemDetails = false
            this.$refs.qz_printer.loadPrinterModal(InvoiceID , ".odv")
        }
    },   
 
    }
</script>

<style scoped>
    .suborder{
        background-color: #F8F8F8;
        box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.25);
        margin-bottom: 15px;
        display: inline-block;
        width: 100%;
        padding:15px 0 20px 0;
    }
    .subordernum{
        font-size: 14px;
        font-weight: 500;
        color:#868686;
        margin: 0 0 14px 13px;
       
    }
    .suborderheader{
      display: flex;
    }
    .container-fluid{
        padding: 0px;
    }
    .current_sel{
        position: relative;
        z-index:10000;
        background: rgb(247, 251, 246);
        box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.25);
    }
    .detail-btn-detail-order{
        background: #42A71E;
        color: #FFFFFF;
        width: 225px;
        border-radius: 4px;
        height: 40px;
        border-color: #42A71E;           
    }
    /*list transitions*/
    .list-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .list-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .list-enter-active{
        transition: all 1s ease;
    }

    .list-leave-from{
        opacity: 1;
        transform: scale(1);
    }
    .list-leave-to{
        opacity: 0;
        transform: scale(0.6);
    }
    .list-leave-active{
        transition: all 1s ease;
        position: absolute;
        width: 100%;
    }
    .list-move{
        transition:all 0.9s ease;
    }
    .check-box{
        padding:20px 22px
    }
    .nodata{
        background: #FFFFFF;
        min-height: 350px;
        display: flex;
        align-items: center;justify-content: center;
    }
    .batch-actions{
        background: #EEEEEE;
        box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.25);
        padding: 1rem 0;
        position: sticky;
        bottom: 0;
        transform-origin: bottom center;
        z-index: 1;
    }
    .batch-actions button{
        margin-left: 1rem;

    }

    .trans-batch-actions-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-batch-actions-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .trans-batch-actions-enter-active{
        transition: all ease 0.2s;
    }
    .trans-batch-actions-leave-from{
        opacity: 1;
        transform: scale(1);
    }
    .trans-batch-actions-leave-to{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-batch-actions-leave-active{
        transition: all ease 0.2s;
    }
.tcol.Status,.tcol.paid{
    text-align: center;
}
.img-arrow{
    margin :0 5px;
}
span.tool-tip.body_small {
    margin-left: 6px;
}
.tag {
    max-width: 130px;
}
    .list-options-suborder{
        display: flex;
    }
    .column1 {
    width: 30px;
    margin: 0;
    position: relative;
    }
    .column2 {
    width: 30px;
    margin: 0;
    position: relative;
    }
    .column3 {
    width: 30px;
    margin: 0;
    position: relative;
    }
    .column4 {
    width: 30px;
    margin: 0;
    position: relative;
    }
    .column1:after {
        content: "..";
        position: absolute;
        right: 0%;
        color: #FFFFFF;
        font-size: 10px;
        background: #000;
        padding: 3px;
        top: -20px;
        opacity: 0;
        height: 19px;
        width: max-content;
        size: -3px;
        font-weight: 100;
        text-size-adjust: 8px;
        font-family: "Gotham Rounded";
  }
  .column2:after {
    content: "Fulfill SubOrder.";
    position: absolute;
    right: 0%;
        color: #FFFFFF;
        font-size: 10px;
        background: #000;
        padding: 3px;
        top: -20px;
        opacity: 0;
        height: 19px;
        width: max-content;
        size: -3px;
        font-weight: 100;
        text-size-adjust: 8px;
        font-family: "Gotham Rounded";
  }
  .column3:after {
    content: "Print ticket(s).";
    position: absolute;
    right: 0%;
        color: #FFFFFF;
        font-size: 10px;
        background: #000;
        padding: 3px;
        top: -20px;
        opacity: 0;
        height: 19px;
        width: max-content;
        size: -3px;
        font-weight: 100;
        text-size-adjust: 8px;
        font-family: "Gotham Rounded";
  }
  .column4:after {
    content: " Split, offload & more...";
    position: absolute;
    right: 0%;
        color: #FFFFFF;
        font-size: 10px;
        background: #000;
        padding: 3px;
        top: -20px;
        opacity: 0;
        height: 19px;
        width: max-content;
        size: -3px;
        font-weight: 100;
        text-size-adjust: 8px;
        font-family: "Gotham Rounded";
  }
 
  
.column1:hover:after {
    opacity: 1;
}
.column2:hover:after {
    opacity: 1;
}
.column3:hover:after {
    opacity: 1;
}
.column4:hover:after {
    opacity: 1;
}

</style>