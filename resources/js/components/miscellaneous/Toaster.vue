<template>

        <transition-group tag="div" class="toast-wrap" name="list" appear>
        <div class="toaster" v-for="(toast,index) in toasts" :key="index" :class="toast.type" @click="dismiss(toast.id)">
           {{toast.message}}
        </div>
        </transition-group>


</template>

<script>
    import {TOASTER_GET_ALL, TOASTER_MODULE, TOASTER_REMOVE_TOAST} from '../../store/types/types';
    import {computed} from 'vue';
    import {useStore} from 'vuex';
    export default {
        name: "Toaster",
        setup(){
            const dismiss=((id)=>{
                store.commit(`${TOASTER_MODULE}${TOASTER_REMOVE_TOAST}`,id);
            });
            const store=useStore();
            const toasts=computed(()=>store.getters[`${TOASTER_MODULE}${TOASTER_GET_ALL}`]);
             return {
                 dismiss,
                 toasts
             }
        }
    }
</script>

<style scoped>
    .toast-wrap{
        position: fixed;top: 75px; left:50%;transform: translate(-50%); height: auto;width: 100%;max-width:480px; z-index: 4;display: flex;align-items: center;justify-content: center;
        flex-direction: column;
    }
 .toaster{
        width: 100%;
     padding: 10px 20px;
     color: white;
     position: relative;
     border-radius: 10px;
     box-shadow: 1px 3px 5px rgba(0,0,0,0.2);
     margin-bottom: 10px;
     text-align: center;
    cursor: pointer;
 }
    .toaster.danger{
        background:rgba(235, 87, 87, 0.9);
    }
    .toaster.success{
        background:rgba(66, 167, 30, 0.9);
    }

    .list-enter-from{
        opacity: 0;
        /*transform: scale(0.6);*/
        transform: translateY(-60px);
    }
    .list-enter-to{
        opacity: 1;
        /*transform: scale(1);*/
        transform: translateY(0);
    }
    .list-enter-active{
        transition: all 0.3s ease;
    }

    .list-leave-from{
        opacity: 1;
        /*transform: scale(1);*/
        transform: translateY(0);
    }
    .list-leave-to{
        opacity: 0;
       /* transform: scale(0.6);*/
        transform: translateY(-60px);
    }
    .list-leave-active{
        transition: all 0.3s ease;
       /*8 position: absolute;*/
        width: 100%;
    }
    .list-move{
        transition:all 0.9s ease;
    }
</style>