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
            <div class="confirmation-msg body_regular"><p>You are about to split an item from this order</p></div>
            <div class="confirmation-btn">
                <button class="btn btn-outline-danger body_medium" style="margin-right: 59px" @click="close">Cancel</button>
                <button class="btn btn-dark body_medium" style="" @click="split">Split</button>
            </div>
        </div>

    </div>
    </transition>
</template>

<script>

    import {watch ,ref} from 'vue';
    import {useStore} from 'vuex';
    import {ORDERDETAIL_MODULE, ORDERDETAIL_SPLIT} from "../../store/types/types";
    export default {
        name: "SplitConfirmation",
        props:{
            show_conf:{
                type:Boolean
            }
        },
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
            const split=()=>{
                close();
                store.dispatch(`${ORDERDETAIL_MODULE}${ORDERDETAIL_SPLIT}`);
            }
            return {
                show,
                close,
                split
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