<template>
    <div @click="togglechkbox" class="chkbox_wrap d-flex align-items-center"><span class="chkbox" :class="{checked:check}" ></span><label class="noselect body_regular ms-2 text-nowrap"><slot></slot></label></div>
</template>

<script>
    import {ref,watch} from 'vue';
    export default {
        name: "CheckBox",
        props:['modelValue','id','checked_checkbox','name','order','trackingkey'],
        setup(props,{ emit }){
            const check=ref(false);
            
            check.value=props.checked_checkbox;

            function togglechkbox(){
                check.value=!check.value;
                emit('checkbox-clicked', check.value,props.id,props.name,props.order,props.trackingkey)
                emit('update:modelValue', check.value);
            }
            watch(() => props.checked_checkbox, (current_val, previous_val) => {
                check.value=current_val;
            });
            watch(() =>check.value, (current_val, previous_val) => {

               // emit('checkbox-clicked', current_val,props.id,props.name)
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
    span.chkbox,thead span.chkbox,header span.chkbox{
        border:#868686 2px solid;
    }
    span.chkbox.checked,header span.chkbox.checked{
        background: #47454B;
        border:none;
    }
    .chkbox_wrap{
        cursor: pointer;
    }
    span.chkbox{
        margin: 0;
    }
    .filters span.chkbox{
        margin: 6px 22px 6px 22px;
    }
    .filters .chkbox_wrap{
        display: flex;
    }
    .chkbox_wrap label{
        margin: 0 10px;
    }
    .filters .chkbox_wrap label{
        margin: 6px 22px 6px 0;
        font-weight: lighter;
        overflow: hidden;
        text-overflow: ellipsis;        
    }
</style>