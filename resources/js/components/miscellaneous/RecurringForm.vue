<template>
    <div class="fields-wrapper">

        <ul class="days-of-week">
            <li v-for="(day, index) in slotsByDay " :key="index">
                <a @click="setSlots(day.value,$event)"  class="day" :class="{disabled:!day.selected, selected:day.selected}">
                    <span>{{day.value.slice(8,9)}}</span>
                </a>
            </li>
        </ul>



        <ul  v-if="reccuring.length > 0" class="time-slot">
             <li v-if="reccuring.length < 2">
                     <time-slot-picker placeholder="Time" class="data-picker"  v-model= reccuring[0].slot  v-bind:name= reccuring[0].value :available-slots="reccuring[0].available"  label="Select a slot" :isRecurring="true"></time-slot-picker>
             </li>


             <div v-if="reccuring.length < 5" class="time-slot">

                    <li v-if="reccuring[0] && reccuring.length > 1">
                    <time-slot-picker placeholder="Time" class="data-picker"    v-model= reccuring[0].slot  v-bind:name= reccuring[0].value :available-slots="reccuring[0].available"  label="Select first slot" :isRecurring="true"></time-slot-picker>
                    </li>

                    <li v-if="reccuring[1]">
                        <time-slot-picker placeholder="Time" class="data-picker" v-model= reccuring[1].slot  v-bind:name= reccuring[1].value :available-slots="reccuring[1].available"  label="Select second slot" :isRecurring="true"></time-slot-picker>
                    </li>

                    <li v-if="reccuring[2]">
                            <time-slot-picker placeholder="Time" class="data-picker"  v-model= reccuring[2].slot  v-bind:name= reccuring[2].value :available-slots="reccuring[2].available"  label="Select third slot" :isRecurring="true"></time-slot-picker>
                    </li>

                    <li v-if="reccuring[3]">
                        <time-slot-picker placeholder="Time" class="data-picker"   v-model= reccuring[3].slot  v-bind:name= reccuring[3].value :available-slots="reccuring[3].available"  label="Select 4th slot" :isRecurring="true"></time-slot-picker>
                    </li>

             </div>


             <div v-if="reccuring.length > 4" class="time-slot">

                    <li>
                        <time-slot-picker placeholder="Time" class="data-picker"   v-model= reccuring[0].slot   v-bind:name= reccuring[0].value :available-slots="reccuring[0].available"  label="Select first slot" :isRecurring="true"></time-slot-picker>
                    </li>

                    <li v-if="reccuring[3]">
                        <time-slot-picker placeholder="Time" class="data-picker"  v-model= reccuring[3].slot  v-bind:name= reccuring[3].value :available-slots="reccuring[3].available"  label="Select 4th slot" :isRecurring="true"></time-slot-picker>
                    </li>

                    <li v-if="reccuring[1]">
                        <time-slot-picker placeholder="Time" class="data-picker"  v-model= reccuring[1].slot  v-bind:name= reccuring[1].value :available-slots="reccuring[1].available"  label="Select second slot" :isRecurring="true"></time-slot-picker>
                    </li>

                    <li v-if="reccuring[4]">
                        <time-slot-picker placeholder="Time" class="data-picker"  v-model= reccuring[4].slot    v-bind:name= reccuring[4].value :available-slots="reccuring[4].available"  label="Select 5th slot" :isRecurring="true"></time-slot-picker>
                    </li>

                    <li v-if="reccuring[2]">
                        <time-slot-picker placeholder="Time" class="data-picker"  v-model= reccuring[2].slot   v-bind:name= reccuring[2].value :available-slots="reccuring[2].available"  label="Select third slot" :isRecurring="true"></time-slot-picker>
                    </li>

                    <li v-if="reccuring[5]">
                        <time-slot-picker placeholder="Time" class="data-picker"  v-model= reccuring[5].slot v-bind:name= reccuring[5].value :available-slots="reccuring[5].available"  label="Select 6th slot" :isRecurring="true"></time-slot-picker>
                    </li>
             </div>

        </ul>
    </div>

</template>
<script>

import {ref, watch} from 'vue';
import TimeSlotPicker from '../miscellaneous/TimeSlotPicker';
import {useStore} from 'vuex';
import axios from 'axios';
import {
    LOADER_MODULE,
    DISPLAY_LOADER,
    HIDE_LOADER,
} from "../../store/types/types";

export default ({
    name: "RecurringForm",
    components: {TimeSlotPicker},
     props: {
            modelValue: Array,
            postcode:String || null,
            cust:Object||null,
        },

    setup(props,context) {


        const store = useStore();
         const reccuring= ref([]);
         const available_by_postcode = ref([]);
         const cust_slots = ref([]);



         const slotsByDay=ref([
                {
                    value:'DeliveryMon',
                    selected:false,
                    slot: 0,
                    available:[],
                },
                {
                    value:'DeliveryTu',
                    selected:false,
                    slot:0,
                     available:[],
                },
                {
                    value:'DeliveryWed',
                    selected:false,
                    slot:0,
                    available:[],
                },
                {
                    value:'DeliveryTh',
                    selected:false,
                    slot:0,
                    available:[],
                },
                {
                    value:'DeliveryFri',
                    selected:false,
                    slot:0,
                    available:[],
                },
                {
                    value:'DeliverySat',
                    selected:false,
                    slot:0,
                    available:[],
                }
            ]);

        if(props.postcode !=''){
            loadCustTranches();
        }


        function loadCustTranches(){
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Fetching slots'], {root: true});
            axios.post('/get-slots-by-day',{postcode:props.postcode})
                .then((res)=>{
                    let tranches = res.data.tranches;
                    let keys = Object.keys(tranches);
                    keys.forEach(function(v,i){
                        let index = slotsByDay.value.findIndex((z) => { return z.value === v });
                        slotsByDay.value[index].available = tranches[v];

                        if(props.cust){
                            let cust_slot = JSON.parse(props.cust[v]);
                            if(cust_slot.length > 0){
                                slotsByDay.value[index].slot = cust_slot[0];
                                slotsByDay.value[index].selected = true;
                            }
                        }
                    });

                    if(props.cust){
                        let selected_arr = [];
                        slotsByDay.value.forEach(function(v,i){
                            if(v.selected){
                                selected_arr.push(slotsByDay.value[i]);
                            }
                        });
                        reccuring.value=selected_arr;
                    }

                }).catch((err)=>{

                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);

                });
        }



        function setSlots(day){
            slotsByDay.value.forEach((slotDay,index)=>{

                if(slotDay.value == day){
                    if(slotDay.selected == true){
                        slotDay.selected = false;
                        slotDay.slot = 0;
                    }
                     else if(slotDay.selected == false){
                        slotDay.selected = true;
                    }
                }

            });

            reccuring.value =  slotsByDay.value.filter(function (el) {
                return el.selected == true;
            });
        }


        watch(()=>_.cloneDeep(reccuring.value),(current_val,previous_val)=>{
            context.emit("update:modelValue",current_val);
        });

        function returnedData(arr){
            loadCustTranches();
        }


    return {
            setSlots,
            slotsByDay,
            reccuring,
            returnedData,
            cust_slots,
        }
    }
})

</script>

<style scoped>
   .fields-wrapper {
        padding: 15px;
    }

     .days-of-week {
          display: flex;
          list-style-type:none;
          padding:0;
          margin: 0;
     }
     .days-of-week li{
         padding-right: 14px;
     }

    .day {
        width: 38px;
        height: 38px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
        background-color: #E0E0E0;
        transition: .3s all;
        text-decoration: none;
    }

    .day:hover{
        cursor:pointer;
    }

    .selected {
        font-family: GothamRoundedBook;
        font-size: 14px;
        line-height: 140%;
        color: white;
        background-color: #42A71E;
    }

    .disabled {
        background-color: #E0E0E0;
        color:#47454B;
    }
    .time-slot{
        display: flex;
        flex-wrap: wrap;
        padding: 23px 0 0 0;
        width: 100%;
        list-style: none;
    }
    /* .time-slot li:nth-child(2n+1) .data-picker {
    margin-right: 10px;
    } */
    .time-slot li:nth-child(2n+2) {
    margin-left: 25px;
    }
    .time-slot li {
    width: 156px;
    padding-bottom: 24px;

    }
    .time-slot .data-picker {
    width: 156px;
    padding-bottom: 24px;

    }
</style>
