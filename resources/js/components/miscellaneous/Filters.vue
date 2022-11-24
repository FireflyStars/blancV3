<template>
    <button class="btn btn-outline-dark position-absolute filter body_small_bold" :class="{active:showfilter,hasfilters:hasActiveFilters}" @click="toggleShow">Filters <span></span></button>
    <transition name="trans-filter" >
    <div class="filters position-absolute" v-if="showfilter">
     <div  class="subtitle">
      <h2 class ="col-8">Filter by</h2>
      <span @click="removefilter()">Remove filters</span>
     </div>

        <div class="row" v-for="(select, ind) in filterDef.def" :key="ind">
            <div class="col" v-if="select.type == 'select'">
                <multi-select-options :name="select.name" :optionKey="ind" :options="select.options" @selected-options="selectedOptions"></multi-select-options>
                <!-- <div class="select" :class="{active: current_filter==ind}" @click="selectclick($event, ind)">{{select.name}}
                    <transition name="trans-filter" >
                        <div class="select-options" v-if="current_filter==ind" >
                            <span class="tick-all" @click="tickAllOptions(ind, select.options)">Tick all</span>
                            <check-box v-for="(option,index) in select.options"
                                :key="index"
                                @checkbox-clicked="checkboxclicked"
                                :id="index"
                                :name="ind"
                                :checked_checkbox="ind in preselection && preselection[ind].includes(index)">
                                {{option}}
                            </check-box>
                        </div>
                    </transition>
                </div>
                <div class="selected-options" v-if="ind in preselection && preselection[ind].length > 0">
                    <template v-for="(option, opIndex) in select.options">
                        <div class="selected-option-item" v-if="preselection[ind].includes(opIndex)">
                            {{ option.toLowerCase() }} 
                            <svg @click="removeOption(ind, opIndex)" class="ms-2 cursor-pointer" width="9" height="8" viewBox="0 0 9 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.02217 0.277731C0.798925 0.0597667 0.43697 0.0597667 0.213723 0.277731C-0.00952425 0.495696 -0.00952428 0.849086 0.213723 1.06705L3.19464 3.97743L0.167435 6.933C-0.0558118 7.15096 -0.0558117 7.50435 0.167435 7.72232C0.390682 7.94028 0.752637 7.94028 0.975884 7.72232L4.00309 4.76675L7.02741 7.7195C7.25065 7.93747 7.61261 7.93747 7.83585 7.7195C8.0591 7.50154 8.0591 7.14815 7.83585 6.93019L4.81153 3.97743L7.78957 1.06986C8.01281 0.851899 8.01281 0.498508 7.78957 0.280544C7.56632 0.0625794 7.20436 0.0625795 6.98112 0.280544L4.00309 3.18811L1.02217 0.277731Z" fill="black"/>
                            </svg>
                        </div>
                    </template>
                </div> -->
            </div>
            <div class="col" v-if="select.type == 'datepicker' && select.id == 'det_date'">
                <div class="from-group mb-3">
                    <date-range-picker
                        v-model="detDate"
                        :name="select.id"
                        :placeholder="select.name"
                        :droppos="{ top:'auto', right: 0, bottom:'auto', left:'auto', transformOrigin:'top right'}"
                        :color="'#000000'"
                        :font="'16px'"
                        ref="date_picker_det"
                        >
                    </date-range-picker>
                </div>
            </div>
            <div class="col" v-if="select.type == 'datepicker' && select.id == 'prod_date'">
                <div class="from-group mb-3">
                    <date-range-picker
                        v-model="prodDate"
                        :name="select.id"
                        :placeholder="select.name"
                        :droppos="{ top:'auto', right: 0, bottom:'auto', left:'auto', transformOrigin:'top right'}"
                        :color="'#000000'"
                        :font="'16px'"
                        ref="date_picker_prod"
                        >
                    </date-range-picker>
                </div>
            </div>
            <div class="col" v-if="select.type == 'datepicker' && select.id == 'deliv_date'">
                <div class="from-group">
                    <date-range-picker
                        v-model="delivDate"
                        :name="select.id"
                        :placeholder="select.name"
                        :droppos="{ top:'auto', right: 0, bottom:'auto', left:'auto', transformOrigin:'top right'}"
                        :color="'#000000'"
                        :font="'16px'"
                        ref="date_picker_deliv"
                        >
                    </date-range-picker>
                </div>
            </div>
        </div>
        <div class="row buttons mb-3">
            <div class="col text-center"><button class="btn btn-link  body_regular" @click="cancel">Cancel</button></div>
            <div class="col text-right"><button class="btn btn-dark body_medium" @click="applyFilter">Apply</button></div>
        </div>
    </div>
    </transition>
</template>

<script>
    import {ref,computed, watch} from 'vue';
    import DateRangePicker from '../miscellaneous/DateRangePicker';
    import CheckBox from '../miscellaneous/CheckBox';
    import MultiSelectOptions from '../miscellaneous/MultiSelectOptions';
    import {useRoute} from 'vue-router';
    import {useStore} from 'vuex';
    import {ORDERLIST_FILTER, ORDERLIST_GET_FILTER, ORDERLIST_MODULE,ORDERLIST_RESET_MULITCHECKED} from "../../store/types/types";
    export default {
        name: "Filters",
        props:['filterDef', 'data'],
        components:{CheckBox, DateRangePicker, MultiSelectOptions},
        setup(props){
            const showfilter=ref(false);
            const current_filter=ref('');
            const detDate=ref({
                start: '',
                end: '',
            });
            const prodDate=ref({
                start: '',
                end: '',
            });
            const delivDate=ref({
                start: '',
                end: '',
            });
            const preselection=ref({});

            const store=useStore();

            const toggleShow=(()=>showfilter.value=!showfilter.value);
            const data = ref('');
            data.value = props.data;
            const route = useRoute();

            // function selectclick(event, sel) {
            //     if(current_filter.value != sel) {
            //         current_filter.value = sel;
            //     }else if(current_filter.value==sel){
            //         if ( event.target.matches(".select") || !event.target.closest(".select-options")){
            //             current_filter.value='';
            //         }
            //     }
            // }
            // document.addEventListener('click', (event)=>{
            //     if(!event.target.matches(".select") && !event.target.closest(".select-options")){
            //         current_filter.value='';
            //     }
            // });
            const hasActiveFilters=computed(()=>{
                const filters= _.cloneDeep(store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_FILTER}`]);
                const allEmpty= Object.values(filters).every((element) => element.length===0);
                return !allEmpty;
            });


            // function checkboxclicked(check,id,name) {
            //     if(check)
            //     if(name in preselection.value) {
            //         preselection.value[name].push(id);
            //     }else {
            //         preselection.value[name] = [];
            //         preselection.value[name].push(id);
            //     }

            //     if(!check)
            //         if(name in preselection.value) {
            //             preselection.value[name]= preselection.value[name].filter(item=>item!=id);
            //         }
            // }
            function applyFilter() {
                if(prodDate.value.start !='' && prodDate.value.end !='')
                    preselection.value['infoitems.ProdDate'] = [prodDate.value.start, prodDate.value.end];
                else
                    delete preselection.value['infoitems.ProdDate']
                if(delivDate.value.start !='' && delivDate.value.end !='')
                    preselection.value['infoitems.DelivDate'] = [delivDate.value.start, delivDate.value.end];
                else
                    delete preselection.value['infoitems.DelivDate']
                if(detDate.value.start !='' && detDate.value.end !='')
                    preselection.value['infoOrder.DetDate'] = [detDate.value.start, detDate.value.end];
                else
                    delete preselection.value['infoOrder.DetDate']
                console.log(preselection);
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_RESET_MULITCHECKED}`);
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_FILTER}`,{ customer: route.params.customerId , search:route.params.value , filter:preselection.value});
                current_filter.value='';
                toggleShow();
            }
            function cancel() {
                current_filter.value='';
                const filters=store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_FILTER}`];
                preselection.value=_.cloneDeep(filters);
                toggleShow();

            }
            function removedata(){
                preselection.value = {}
            }
            const selectedOptions =( options, ind )=>{
                preselection.value[ind] = options;
            }
            return {
                showfilter,
                current_filter,
                preselection,
                hasActiveFilters,
                prodDate,
                delivDate,
                data,
                detDate,
                toggleShow,
                applyFilter,
                cancel,
                removedata,
                selectedOptions
            }

        },
        methods:{
            removefilter(){
                this.$refs.date_picker_deliv.resetFilter()
                this.$refs.date_picker_det.resetFilter()
                this.$refs.date_picker_prod.resetFilter()
                this.removedata()
                this.applyFilter()
           }
        }
    }
</script>

<style scoped>
    .text-right{
        text-align: right;
    }
    .filters .buttons button{
        width: 130px;
        height: 56px;
    }


    .filters .buttons{
        margin-top: 51px;
    }
    .filter{
        top:-62px;
        right: 22px;
        padding: 10px 16px;
    }
    .filter.hasfilters:not(:hover){
        background:#42A71E ;
        color:#FFF;
        border-color: #42A71E;
    }
    .filter.hasfilters.active{
        background: #47454B;
        border-color: #47454B;
    }

    .filter:focus{
        box-shadow: none;
    }
    .filter span{
        background: #47454B;
        width: 14px;
        height: 3px;
        border-radius: 5px;
        display: inline-block;
        position: relative;
        vertical-align: middle;
        left: 4px;
        margin: 0 5px 0 20px;
    }
    .filter span:before{
        background: #47454B;
        content: " ";
        width: 22px;
        height: 3px;
        border-radius: 5px;
        display: block;
        top: -6px;
        left: 50%;
        position: absolute;
        transform: translate(-50%);
    }
    .filter span:after{
        background: #47454B;
        content: " ";
        width: 7px;
        height: 3px;
        border-radius: 5px;
        display: block;
        left: 50%;
        position: absolute;
        transform: translate(-50%);
        top: 6px;

    }
    .filter.hasfilters:not(:hover) span,.filter.hasfilters:not(:hover) span:before,.filter.hasfilters:not(:hover) span:after{
        background: #FFF;
    }
    .filter:hover span, .filter:hover span:before, .filter:hover span:after,
    .filter.active span, .filter.active span:before, .filter.active span:after
    {
        background: #FFF;
    }
    .filters{
        right: 22px;
        background: #F8F8F8;
        box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.12);
        width:458px;
        min-height:445px;
        max-height: 800px;
        overflow-y: auto;        
        top:-8px;
        z-index: 2;
        transform-origin: top center;
        padding:0 46px;
    }
    .filters h2{
        margin:30px 0 20px 0;
    }
    .filters span{
        margin:30px 0 20px 0;
        font-size: 16px;
        font-weight: normal;
        font-family: 'Gotham Rounded Light';
        text-decoration: underline;
        cursor: pointer;
    }
    .subtitle{
        display: flex;
    }
</style>