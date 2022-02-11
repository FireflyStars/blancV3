<template>
    <select-options :placeholder="placeholder" :options="timeslot_def" v-model="timeslot" :name="name" :classnames="'timeslotpicker'" :hint="hint" :label="label" :disabled="disabled" :valid="valid">
        <div class="timeslot body_small_bold" v-for="(time,index) in timeslot_def" :class="{disabled:!time.available,current:time.value==timeslot}" @click="selectTimeSlot(time.value)">{{time.display}}</div>
    </select-options>
</template>

<script>
    import SelectOptions from './SelectOptions';
    import {ref,watch} from 'vue';
    import {useStore} from 'vuex';
    import {SELECT_MODULE, SET_CURRENT_SELECT} from "../../store/types/types";
    export default {
        name: "TimeSlotPicker",
        components:{SelectOptions},
        props: {
            modelValue: Number,
            availableSlots: Array,
            name:{
                type: String,
                required: true
                },
            hint: String,
            label:String,
            disabled:Boolean,
            valid:Boolean|null,
            placeholder:String,
        },
        setup(props,context){
            const timeslot=ref(0);
            const store=useStore();
            timeslot.value=props.modelValue;
            const timeslot_def=ref([
                {
                    value:1,
                    display:'8-10 am',
                    available:false
                },
                {
                    value:3,
                    display:'10-12 pm',
                    available:false
                },
                {
                    value:5,
                    display:'12-2 pm',
                    available:false
                },
                {
                    value:7,
                    display:'2-4 pm',
                    available:false
                },
                {
                    value:9,
                    display:'4-6 pm',
                    available:false
                },
                {
                    value:11,
                    display:'6-8 pm',
                    available:false
                },
                {
                    value:13,
                    display:'8-8 pm',
                    available:true
                }
            ]);
            watch(()=>props.availableSlots,(current_val,previous_val)=>{
                timeslot_def.value.forEach(slot=>{
                    slot.available=false;
                    if(current_val.includes(slot.value))
                        slot.available=true;
                });
            });
            timeslot_def.value.forEach(slot=>{
                if(props.availableSlots.includes(slot.value))
                    slot.available=true;
            });
            const selectTimeSlot=(value)=>{
                if( timeslot.value==value) {
                    timeslot.value = 0;
                }else {
                    timeslot.value = value;
                }
                context.emit("update:modelValue",timeslot.value);
                store.commit(`${SELECT_MODULE}${SET_CURRENT_SELECT}`,'');
            }
            watch(()=>props.modelValue,(current_val,previous_val)=>{
                timeslot.value = current_val;
            });

            return {
                timeslot_def,
                timeslot,
                selectTimeSlot,

            }
        }
    }
</script>

<style scoped>

    .timeslot{
        border: 1px solid #000000;
        border-radius: 5px;
        text-align: center;
        line-height: 36px;
        vertical-align: middle;
        width: 81px;
        height: 36px;
        margin: 20px auto;
        transition: background-color 0.2s ease-in-out;
    }
    .timeslot.disabled{
        pointer-events: none;
        background: #F8F8F8;
        border-radius: 5px;
        border:1px solid #F8F8F8;
        color:#C3C3C3;
    }
    .timeslot:not(.disabled):not(.current):hover{
        background:#EEEEEE;
    }
    .timeslot.current{
        background: #42A71E;
        color:#FFFFFF;
        border-color:#42A71E;
    }

</style>
