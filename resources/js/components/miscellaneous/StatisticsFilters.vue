<template>
    <div class="row">
        <div class="col-1 width-fit-content">
            <button class="btn btn-outline-dark filter body_small_bold" :class="{active:showfilter,hasfilters:hasActiveFilters}" @click="toggleShow">
                <i class="bi bi-calendar"></i> {{customFilter? 'Custom':selectedValue}}
            </button>
        </div>
        <div class="col-10 custom-filter-text">
            <p v-if="customFilter">{{customFilterText}}</p>
        </div>
    </div>
    <transition name="trans-filter" >
    <div class="filters position-absolute" v-if="showfilter" v-click-outside="onClickOutside">
        <h2 class="subtitle">Date range</h2>
        <div class="row">
            <switch-btn v-model="customFilter" label-left="Custom Filter" label-right="" ></switch-btn>
        </div>
        <div class="row mt-10"  v-if="!customFilter">
            <div class="col-10">
                <select-options v-model="selectedValue" :options="dateRange" name="select-date-range"  classnames="black-border"></select-options>
            </div>
        </div>
        <div v-if="customFilter">
            <div class="row mt-10">
                <div class="col-3">
                    <date-picker v-model="startDate" @update:modelValue="newValue => startDate = newValue" name="start_date" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Starting" :disabled-from-date="startDisabledtodate"></date-picker>
                </div>
            </div>
            <div class="row mt-10">
                <div class="col-3">
                    <date-picker v-model="endDate" @update:modelValue="newValue => endDate = newValue" name="end_date" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Ending" :disabled-from-date="endDisabledtodate"></date-picker>
                </div>
            </div>
        </div>
        <div class="row buttons">
            <div class="col text-center"><button class="btn btn-link  body_regular" @click="cancel">Cancel</button></div>
            <div class="col text-right"><button class="btn btn-dark body_medium" @click="applyFilter">Apply</button></div>
        </div>
    </div>
    </transition>
</template>

<script>
    import {ref,computed} from 'vue';
    import CheckBox from '../miscellaneous/CheckBox';
    import {useStore} from 'vuex';
    import SelectOptions from '../miscellaneous/SelectOptions';
    import DatePicker from '../miscellaneous/DatePicker';
    import SwitchBtn from '../miscellaneous/SwitchBtn';
    import vClickOutside from 'click-outside-vue3';
    export default {
        name: "StatisticsFilters",
        directives: {
            clickOutside: vClickOutside.directive
        },           
        props:['filterVal'],
        emits: ['update:filterVal'],
        components:{CheckBox,SelectOptions,DatePicker,SwitchBtn},
         setup(props,context){
            let monthNames =["Jan","Feb","Mar","Apr",
                      "May","Jun","Jul","Aug",
                      "Sep", "Oct","Nov","Dec"];
    
            const current = new Date();
            const currentDate = `${current.getFullYear()}-${current.getMonth()+1}-${current.getDate()}`;
            const showfilter=ref(false);
            const customFilter=ref(false);
            const selectedValue =ref('Today');
            const startDate=ref(currentDate);
            const endDate=ref(currentDate);
            const startDisabledtodate = ref(currentDate);
            const endDisabledtodate = ref(currentDate);
            const customFilterText = ref('');
            const store=useStore();
            const toggleShow=(()=>showfilter.value=!showfilter.value);

            const hasActiveFilters=computed(()=>{
                return false;
            });
            const dateRange = ref([]);
            dateRange.value = [
                { value: "Today", display: "Today" },
                { value: "Yesterday", display: "Yesterday" },
                { value: "Last 7 days", display: "Last 7 days" },
                { value: "Last 90 days", display: "Last 90 days" },
                { value: "Last Month", display: "Last Month" },
                { value: "Year to date", display: "Year to date" },
                { value: "4th Quarter", display: "4th Quarter ("+`${current.getFullYear()-1}`+")" },
                { value: "3rd Quarter", display: "3rd Quarter ("+`${current.getFullYear()-1}`+")" },
                { value: "2nd Quarter", display: "2nd Quarter ("+`${current.getFullYear()-1}`+")" },
                { value: "1st Quarter", display: "1st Quarter ("+`${current.getFullYear()-1}`+")" }
            ];
            function applyFilter() {
                const data = {
                    customFilter:customFilter.value,
                    startDate:startDate.value,
                    endDate:endDate.value,
                    dateRangeType:selectedValue.value
                };
                customFilterText.value=`Compared From ${new Date(startDate.value).getDate()} ${monthNames[new Date(startDate.value).getMonth()]}, ${new Date(startDate.value).getFullYear()} To ${new Date(endDate.value).getDate()} ${monthNames[new Date(endDate.value).getMonth()]}, ${new Date(endDate.value).getFullYear()}`;
                context.emit("update:filterVal", data);
                // store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_RESET_MULITCHECKED}`);
                // store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_FILTER}`,_.cloneDeep(preselection.value));
                // current_filter.value='';
                toggleShow();
            }

            function cancel() {
                // current_filter.value='';
                // const filters=store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_FILTER}`];
                // preselection.value=_.cloneDeep(filters);
                toggleShow();
            }

            return {
                toggleShow,
                applyFilter,
                cancel,
                dateRange,
                showfilter,
                customFilter,
                hasActiveFilters,
                selectedValue,
                startDate,
                startDisabledtodate,
                endDate,
                endDisabledtodate,
                customFilterText
            }
        },
        methods:{
            onClickOutside (event) {
                this.showfilter = false;
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
        margin-bottom: 51px;
    }
    .filter{
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
    .filter.hasfilters:not(:hover) span,.filter.hasfilters:not(:hover) span:before,.filter.hasfilters:not(:hover) span:after{
        background: #FFF;
    }
    .filter:hover span, .filter:hover span:before, .filter:hover span:after,
    .filter.active span, .filter.active span:before, .filter.active span:after
    {
        background: #FFF;
    }
    .filters{
        background: #F8F8F8;
        box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.12);
        width:444px;
        min-height:250px;
        z-index: 2;
        transform-origin: top center;
        padding:0 46px;
    }
    .bi.bi-calendar{
        font-size: 20px;
    }
    .filters h2{
        margin:30px 0 20px 0;
    }

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
        border: 1px solid #000000;;
        box-sizing: border-box;
        border-radius: 5px;
        padding: 0 36px 0 16px;
        height: 40px;
        font-size: 16px;
        display: flex;
        cursor: pointer;
        align-items: center;
        position: relative;
        margin-bottom: 30px;

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
        max-height: 168px;
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
    .custom-filter-text{
        margin-top: 12px;
        font-size: 14px;
    }

</style>