<template>
<transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
    <div v-if="show_modal" class="bmodal-overlay">
        <div class="bmodal-container" id="myModal" v-if="show_modal">
            <slot name="closebtn">
                 <span class="close" @click="closeModal"></span>
            </slot>
            <slot name="bheader"></slot>
            <slot name="bcontent"></slot>
            <slot name="mbuttons"></slot>
        </div>
     </div>

</transition>
<!--
<transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
    <div class="modal d-block" id="modal" role="dialog" aria-labelledby="modal-header" aria-describedby="modal-body">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <slot name="header">
                        <h5 class="modal-title">Modal title</h5>
                        <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
                    </slot>
                </div>
                <div class="modal-body">
                    <slot name="body">
                        <p>Modal body text goes here.</p>
                    </slot>
                </div>
                <div class="modal-footer">
                    <slot name="footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </slot>
                </div>
            </div>
        </div>
    </div>
</transition>
-->

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
