<template>
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
</template>

<script>
export default {
    name: "Modal",
    emits:['close-modal'],
    setup(props, context){
        const closeModal = ()=>{
            context.emit('close-modal');
        }
        return {
            closeModal
        }  
    }
};
</script>