<template>

    <div class="options position-absolute">
        <div class="row">

            <div class="col-12 row-option" @click="selectSplit()" >
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

    </div>
</template>

<script>
    import {ref} from 'vue';
    import DateRangePicker from '../miscellaneous/DateRangePicker';
    import CheckBox from '../miscellaneous/CheckBox';
    import {TOASTER_MODULE, TOASTER_MESSAGE} from "../../store/types/types";
    import {useStore} from 'vuex';
    export default {
        name: "SubOrderOptions",
        props:['items' ,'invoice_id' ],
        components:{CheckBox, DateRangePicker},
        setup(props){
           const store=useStore();
           const listItems =ref([]);


            props.items.forEach(item => {
                
                listItems.value.push(item.infoitems_id);

            });


           function selectSplit(){
   
               console.log("selectSplit")
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
               selectSplit
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
        min-height: 336px;
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
   
    .custom-trans-enter-active{
       transition: opacity .5s;
       color:yellow !important;
    }

    .custom-trans-leave-active{
       transition: opacity .5s;
       transform-origin: top center !important;
       color:red !important;
    }
   
    .custom-trans-enter-from{
         opacity: 0;
         color: green !important;
    }
  
    .custom-trans-leave{
        opacity: 0;
        color:aqua !important;
        
    }
     div.options.position-absolute list-leave-active{
       transition: opacity .5s;
       transform-origin: top center !important;
       color:red !important;
    }
   



</style>