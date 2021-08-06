<template>
    <span class="chkbox" :class="{checked:check}" @click.stop="togglechkbox"></span>
</template>

<script>
    import {ref,watch} from 'vue';
    export default {
        name: "CheckBox",
        props:['id','checked_checkbox'],
        setup(props,{ emit }){
            const check=ref(false);

            check.value=props.checked_checkbox;

            function togglechkbox(){
                check.value=!check.value;

            }
            watch(() => props.checked_checkbox, (current_val, previous_val) => {
                check.value=current_val;
            });
            watch(() =>check.value, (current_val, previous_val) => {

                emit('checkbox-clicked', current_val,props.id)
            })
            return {
                check,
                togglechkbox
            }
        }
    }
</script>

<style scoped>
    span.chkbox{
        width: 20px;
        height: 20px;
        display: inline-block;
        position: relative;
    }
    span.chkbox.checked{
        background: #47454B;
        border:none;
    }
    span.chkbox:after{
      opacity: 0;
        position: absolute;
        content: " ";
        left: 6px;
        top: 2px;
        width: 8px;
        height: 13px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }
    span.chkbox.checked:after{

        opacity: 1;
        transition: opacity 0.3s ease-out;
        -webkit-transition: opacity 0.3s ease-out;
        -moz-transition:  opacity 0.3s ease-out;
    }
</style>