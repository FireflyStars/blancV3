<template>
<button class="btn btn-outline-success" @click="showModal" v-if="showBtn">Show Modal</button>
<transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">

    <div v-if="show_modal" class="bmodal-overlay">
        <div class="bmodal-container" id="myModal" v-if="show_modal">
            <span class="close" @click="closeModal"></span>
        </div>
     </div>

</transition>
</template>

<script>
import {ref} from 'vue';

export default {
    name: "Modal",
    emits:['close-modal'],
    props:{
        showBtn:Boolean
    },
    setup(props, context){
        const show_modal = ref(false);

        const closeModal = ()=>{
            show_modal.value = false;
              //document.getElementById('myModal').classList.add('hide');

            context.emit('close-modal');
        }

        const showModal = ()=>{
            show_modal.value = true;
            //document.getElementById('myModal').classList.add('show');

        }
        return {
            show_modal,
            closeModal,
            showModal
        }
    }
};
</script>
<style scoped>

</style>
