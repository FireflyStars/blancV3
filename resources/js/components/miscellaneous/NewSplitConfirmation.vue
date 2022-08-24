<template>
    <transition
            enter-active-class="animate__animated animate__fadeIn"
            leave-active-class="animate__animated animate__fadeOut"
    >
    <div v-if="show" class="confirmation-overlay">
        <div class="confirmation">
            <i class="icon-close" @click="close"></i>
            <div class="confirmation-title">
                <span class="subtitle">Split</span>
            </div>
            <span>{{url}}</span>
            <div class="confirmation-msg body_regular">
                <p>Do you want to split items
                <br>
                {{listItems}}
                <br>
                OUT OF sub-order {{suborder}}</p></div>
            <div class="confirmation-btn">
                <button class="btn btn-outline-danger body_medium" style="margin-right: 59px" @click="close">No, Cancel</button>
                <button class="btn btn-dark body_medium" style="" @click="split">Yes ,Split</button>
            </div>
        </div>

    </div>
    </transition>
</template>

<script>

    import {watch ,ref} from 'vue';
    import {useStore} from 'vuex';
    import {TOASTER_MODULE, TOASTER_MESSAGE} from "../../store/types/types";
    export default {
        name: "NewSplitConfirmation",
        props:{
            show_conf:{
                type:Boolean
            },
            suborder :String,
            items :Array,
            invoice_id: String,
            item_selected:Array,
            url: String,
        },
        setup(props,context){
            const show=ref(false);
            const store=useStore();
            const listItems = ref('');
 
            watch(() => props.show_conf, (toval, fromval) => {
                show.value=toval;
            });
              const regex = /\,(?=[^,]*$)/g;
              var reg =/[ ,-]/g;
         
              listItems.value = props.item_selected.map(String);
              if(listItems.value.length > 0){
                 listItems.value = listItems.value[0].replace(reg , ', ').replace(regex, ' and ');
              }
              
            const close=()=>{
                context.emit('close');
                show.value=false;
            }
            
           function split(){
               axios.post('/SplitSubOrder',{
                   invoice_id: props.invoice_id ,
                   items:props.item_selected
               }).then((res)=>{
                        url.value = res.data.url 
                        if( res.data.status_message == "OK"){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'Success',ttl:5,type:'success'});
                             location.reload();
                        }else {
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${res.data.status_message}`,ttl:5,type:'danger'});
                        }
                       
                     close();
                    }).catch((error)=>{
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'});
                    })
                  close()
           }
            return {
                show,
                close,
                split,
                listItems
            }
        }
    }
</script>

<style scoped>
    .confirmation-overlay{
        background:rgba(0, 0, 0,0.7);
        position: fixed;
        top: var(--mainlogoheight);
        right:0;
        height: calc(100% - var(--mainlogoheight));
        width: 684px;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .confirmation-title{
        background-color: #E0E0E0;
        min-height: 94px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .confirmation{
        position: relative;
        text-align: center;background-color: #fff;width: 545px; display: block; border-radius: 8px;
        overflow: hidden;
        padding-bottom: 24px;
         }
    .icon-close{
        top:24px;
        right: 30px;
    }
    .btn{
        min-width: 164px;
        padding-top: 17px;
        padding-bottom:17px;
    }
    .confirmation-msg{
        display: flex;
        align-items: center;
        justify-content: center;
        height: 223px;
    }
    .confirmation-msg p{
        max-width: 290px;
        margin: 0;
    }
</style>