<template>

    <div class="options position-absolute">
        <div class="row">
            <div  v-if="btn_split_show" class="col-12 row-option"  @click="selectSplit()" >
                <img class="img-arrow" src="/images/split.png" />
                <span>Split</span>
            </div>
            <div class="col-12 row-option" >
                <img class="img-arrow" src="/images/offload.png" />
                <span>Offload</span>
            </div>
            <div class="col-12 row-option" @click="freeReclean">
                <img class="img-arrow" src="/images/bubbles.svg" />
                <span>Free reclean</span>
            </div>
            <div class="col-12 row-option" @click="reAssign">
                <img class="img-arrow" src="/images/reassign.svg" />
                <span>Reassign</span>
            </div>
            <div class="col-12 row-option">
                <img class="img-arrow" src="/images/link.svg" />
                <span>Copy link</span>
            </div>
             <!-- <div class="col-12 row-option" >
                <img class="img-arrow" src="/images/garbage.png" />
                <span>Delete</span>
            </div> -->
            <div class="col-12 row-option" v-if="(user == 1 || user == 4) && invoice_Status!='VOID'"  @click="VoidSubOrder()">
                <img class="img-arrow" src="/images/erase.png" />
                <span>Void</span>
            </div>
        </div>
        <NewSplitConfirmation :items="items" :item_selected="listItems" :invoice_id="invoice_id"  :suborder="suborder" :ListTrackingKey="ListHSL" :show_conf="show_split_conf" @close="show_split_conf=false"></NewSplitConfirmation>
        <void-confirmation :invoice_id="invoice_id" :suborder="suborder" :show_conf="show_void_conf" @close="show_void_conf=false" ></void-confirmation>

    </div>
</template>

<script>
    import {ref} from 'vue';
    import {
        // ORDERDETAIL_MODULE, 
        // ORDERDETAIL_GET_ALL_ITEMS_MULITCHECKED,
        TOASTER_MODULE,
        TOASTER_MESSAGE,
    } from "../../store/types/types";
    import {useStore} from 'vuex';
    import NewSplitConfirmation from '../miscellaneous/NewSplitConfirmation';
    import VoidConfirmation from '../miscellaneous/VoidConfirmation';
    export default {
        name: "SubOrderOptions",
        props:['items' ,'invoice_id','item_selected','suborder','invoice_Status','user' ,'ListTrackingKey'],
        components:{ NewSplitConfirmation , VoidConfirmation},
        emits: ['freeReclean', 'close', 'reAssign'],
        setup(props , context){
            const store=useStore();
            const btn_split_show = ref(false)
            const show_split_conf=ref(false);
            const show_void_conf=ref(false);
            const listItems =ref([]);
            const ListHSL =ref([]);
            props.item_selected.forEach(item => {
                if (item[0] == props.suborder){
                   listItems.value.push(item[1])
                }
            });  

             props.ListTrackingKey.forEach(item => {
                if (item[0] == props.suborder){
                   ListHSL.value.push(item[1])
                }
            });

            if(listItems.value.length > 0 && props.items.length > 1 && props.invoice_Status != "FULFILLED" ){
              btn_split_show.value = true
            } else {
              btn_split_show.value = false
            }

            function selectSplit(){
             show_split_conf.value = true
            }
               
            const close=()=>{
                context.emit('close');
            }  
            
            function VoidSubOrder(){
              show_void_conf.value = true
            }
            const freeReclean =()=>{
                if(listItems.value.length == 0){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'please select a item.',ttl:5,type:'danger'});
                }else if(listItems.value.length > 1){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'you can only select one item.',ttl:5,type:'danger'});
                }else{
                    context.emit('freeReclean', listItems.value[0][0], props.suborder)
                }
            }
            const reAssign =()=>{
                context.emit('reAssign', props.suborder, props.invoice_id)
            }
            return {
                show_split_conf,
                btn_split_show,
                listItems,
                show_void_conf,
                ListHSL,
                VoidSubOrder,
                selectSplit,
                close,
                freeReclean,
                reAssign
            }
        }
    }
</script>

<style scoped>

    .row-option{
        height: 56px;
        padding: 10px 32px;
        cursor: pointer;
    }
    .row-option:hover{
        background-color: #EEEEEE;
    }
    .options{
        right: 0px;
        background: #F8F8F8;
        box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.12);
        width: 298px;
        min-height: auto;
        z-index: 2;
        transform-origin: top center;
        padding:0 10px;
    }
    img.img-arrow {
    margin-right: 25px;
    width: 25px;
    height: auto;
    object-fit: contain;
    object-position: center;
    }

      .custom-trans-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }

</style>