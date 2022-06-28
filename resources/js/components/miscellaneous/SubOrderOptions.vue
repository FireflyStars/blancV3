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
            <div class="col-12 row-option" row-option>
                <img class="img-arrow" src="/images/link.svg" />
                <span>Copy link</span>
            </div>
             <div class="col-12 row-option" >
                <img class="img-arrow" src="/images/arrow.png" />
                <span>Reassign</span>
            </div>
             <div class="col-12 row-option" >
                <img class="img-arrow" src="/images/garbage.png" />
                <span>Delete</span>
            </div>
            <div class="col-12 row-option" >
                <img class="img-arrow" src="/images/erase.png" />
                <span>Void</span>
            </div>
        </div>
        <NewSplitConfirmation :items="item_selected" :suborder="suborder" :show_conf="show_split_conf" @close="show_split_conf=false"></NewSplitConfirmation>

    </div>
</template>

<script>
    import {ref} from 'vue';
    import {TOASTER_MODULE, TOASTER_MESSAGE} from "../../store/types/types";
    import {useStore} from 'vuex';
     import NewSplitConfirmation from '../miscellaneous/NewSplitConfirmation'
    export default {
        name: "SubOrderOptions",
        props:['items' ,'invoice_id','item_selected','suborder' ],
        components:{ NewSplitConfirmation},
        setup(props){
           const store=useStore();
           const listItems =ref([]);
           const btn_split_show = ref(false)
           const show_split_conf=ref(false);

            if(listItems.value.length > 1 && props.items.length != listItems.value.length){
              btn_split_show.value = true
            } else {
              btn_split_show.value = false
            }
            
            props.item_selected.forEach(item => {
                
               listItems.value.push(item[1])

            });            



           function selectSplit(){
               show_split_conf.value = true
              

               axios.post('/SplitSubOrder',{
                   suborderid: props.invoice_id ,
                   items:listItems.value
               }).then((res)=>{
                   console.log("response" , res)
                 // store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'Success.',ttl:5,type:'success'});

               }).catch((error)=>{
                 console.log("error" , error)
                // store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'});
               })
           
           }
            return {
               selectSplit,
               show_split_conf
            }
        }
    }
</script>

<style scoped>

    .row-option{
        height: 56px;
        padding: 10px 32px;
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