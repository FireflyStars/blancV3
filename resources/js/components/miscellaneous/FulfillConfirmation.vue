<template>
    <transition
            enter-active-class="animate__animated animate__fadeIn"
            leave-active-class="animate__animated animate__fadeOut"
    >
    <div v-if="show" class="confirmation-overlay">
        <div class="confirmation">
            <i class="icon-close" @click="close"></i>
            <div class="confirmation-title">
                <span class="subtitle">Fulfill</span>
            </div>
            <div class="confirmation-msg body_regular"><p>Do you want to Fulfill Sub-Order(s) </p></div>
            <div class="confirmation-btn">
                <button class="btn btn-outline-danger body_medium" style="margin-right: 59px" @click="close">No, cancel</button>
                <button class="btn btn-dark body_medium" style="" @click="Fulfill">Yes, fulfill</button>
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
        name: "FulfillConfirmation",
        props:["show_conf",
            "invoice_id"],
        setup(props,context){
            const show=ref(false);
            const store=useStore();
            watch(() => props.show_conf, (toval, fromval) => {
                show.value=toval;
            });
            const close=()=>{
                context.emit('close');
                show.value=false;
            }

            function Fulfill(){
                props.invoice_id.forEach(function(invoice,i) {
                   axios.post('/setInvoiceFulfilled',{
                   invoice_id: invoice,
                   nextpost:28
                    }).then((res)=>{
                        if( res.data.status_message == "ok"){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'Success',ttl:5,type:'success'});
                            if(i ==  props.invoice_id.length -1){
                                location.reload();
                            }
                        }else {
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${res.data.status_message}`,ttl:5,type:'danger'});
                        }

                         close();
                    }).catch((error)=>{
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'});
                    })
                });
                close()
            }
            return {
                show,
                close,
                Fulfill
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
        z-index: 1000;
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
