<template>
    <div class="select" :class="{active: showOption}" @click="selectclick($event, optionKey)">{{label}}
        <transition name="trans-filter" >
            <div class="select-options" v-if="showOption" >
                <span class="tick-all" @click="tickAllOptions(options)">Tick all</span>
                <check-box v-for="(option, index) in options"
                    :key="option"
                    @checkbox-clicked="checkboxclicked"
                    :id="option"
                    :name="'checkbox'"
                    :checked_checkbox="selectedOptions.includes(option)">
                    {{option}}
                </check-box>
            </div>
        </transition>
    </div>
    <div class="selected-options" v-if="selectedOptions.length > 0">
        <template v-for="(option, opIndex) in options">
            <div class="selected-option-item" v-if="selectedOptions.includes(option)">
                {{ option }} 
                <svg @click="removeOption(option)" class="ms-2 cursor-pointer" width="9" height="8" viewBox="0 0 9 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.02217 0.277731C0.798925 0.0597667 0.43697 0.0597667 0.213723 0.277731C-0.00952425 0.495696 -0.00952428 0.849086 0.213723 1.06705L3.19464 3.97743L0.167435 6.933C-0.0558118 7.15096 -0.0558117 7.50435 0.167435 7.72232C0.390682 7.94028 0.752637 7.94028 0.975884 7.72232L4.00309 4.76675L7.02741 7.7195C7.25065 7.93747 7.61261 7.93747 7.83585 7.7195C8.0591 7.50154 8.0591 7.14815 7.83585 6.93019L4.81153 3.97743L7.78957 1.06986C8.01281 0.851899 8.01281 0.498508 7.78957 0.280544C7.56632 0.0625794 7.20436 0.0625795 6.98112 0.280544L4.00309 3.18811L1.02217 0.277731Z" fill="black"/>
                </svg>
            </div>
        </template>
    </div>
</template>

<script>
    import { ref } from 'vue';
    import CheckBox from './CheckBox';
    export default {
        name: "MultiSelectOptions",
        props:{
            label: String,
            options: Object,
            modelValue: Array,
            optionKey: String,
        },
        // emits: [ 'selected-options' ], 
        components:{ CheckBox },
        setup(props, context){
            const showOption = ref(false);
            const selectedOptions = ref([]);
            const selectclick = (event, sel) =>{
                if( !showOption.value ) {
                    showOption.value = true;
                }else{
                    if ( event.target.matches(".select") || !event.target.closest(".select-options")){
                        showOption.value = false;
                    }
                }
            }
            document.addEventListener('click', (event)=>{
                if(!event.target.matches(".select") && !event.target.closest(".select-options")){
                    showOption.value = false;
                }
            });
            const checkboxclicked = (check , id, name) =>{
                if(check){
                    selectedOptions.value.push(id);
                    // if(name in selectedOptions.value) {
                    // }else {
                    //     selectedOptions.value = [];
                    //     selectedOptions.value.push(id);
                    // }
                }else{
                    // console.log(name in selectedOptions.value)
                    selectedOptions.value= selectedOptions.value.filter(item=>item!=id);
                    // if(name in selectedOptions.value) {
                    // }
                }
                // context.emit('selected-options', selectedOptions, props.optionKey);
                context.emit('update:modelValue', selectedOptions);
            }        
            const removeOption = (filterItem)=>{
                selectedOptions.value = selectedOptions.value.filter((item)=>{
                    return item != filterItem;
                })
                // context.emit('selected-options', selectedOptions, props.optionKey);
                context.emit('update:modelValue', selectedOptions);
            }
            const tickAllOptions = (options)=>{
                selectedOptions.value = [];
                for (const key in options) {
                    selectedOptions.value.push(key);
                }
                // context.emit('selected-options', selectedOptions, props.optionKey);
                context.emit('update:modelValue', selectedOptions);
            }                
            return{
                selectclick,
                removeOption,
                tickAllOptions,
                checkboxclicked,
                selectedOptions,
                showOption
            }
        }
    }
</script>

<style scoped>
    .trans-filter-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-filter-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .trans-filter-enter-active{
        transition: all ease 0.2s;
    }
    .trans-filter-leave-from{
        opacity: 1;
        transform: scale(1);
    }
    .trans-filter-leave-to{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-filter-leave-active{
        transition: all ease 0.2s;
    }
.select{
        background: #FFFFFF;
        border: 0.5px solid #E0E0E0;
        box-sizing: border-box;
        border-radius: 5px;
        padding: 0 36px 0 16px;
        height: 40px;
        font-size: 16px;
        display: flex;
        cursor: pointer;
        align-items: center;
        position: relative;
        margin-bottom: 10px;

    }
    .select.active{
        margin-right: -2px;
        margin-left: -2px;
        background: #EEEEEE;
        border: 2px solid #000000;
    }
    .select-options{
        position: absolute;
        width: 100%;
        left: 0;
        top: 46px;
        background: #FFF;
        box-shadow: inset 0px 0px 4px rgba(37, 40, 43, 0.12);
        max-height: 250px;
        z-index: 1;
        overflow-y: auto;
        transform-origin: top center;
    }
    .select:after,.select:before{
        content: " ";
        height: 3px;
        display: block;
        width: 13px;
        background: #868686;
        border-radius: 10px;
        transform: rotate(40deg);
        right:22px;
        position: absolute;
    }
    .select.active:after,.select.active:before{
    background: #000000;
    }
    .select:after{
        transform: rotate(-40deg);
        right: 13px;
    }
    .selected-options{
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }
    .selected-option-item{
        font-size: 12px !important;
        padding: 10px 15px;
        border-radius: 20px;
        background: #E0E0E0;
        display: flex;
        align-items: center;
        margin: 0 5px 5px 0 !important;
        text-transform: capitalize;
    }
    .tick-all{
        font-family: 'Gotham Rounded';
        font-style: normal;
        font-weight: 325;
        font-size: 16px;
        line-height: 19px;
        text-decoration-line: underline;
        color: #42A71E;        
        cursor: pointer;
        position: absolute;
        margin: 0 !important;
        right: 10px;
        top: 10px;        
    }
</style>