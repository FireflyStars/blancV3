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
                <div class="select" :class="{active: current_filter==ind}" @click="selectclick(ind)">{{select.name}}
                    <transition name="trans-filter" >
                        <div class="select-options" v-if="current_filter==ind" >
                            <check-box v-for="(option,index) in select.options" :key="index" @checkbox-clicked="checkboxclicked" :id="index" :name="ind" :checked_checkbox="ind in preselection && preselection[ind].includes(index)">{{option}}</check-box>
                        </div>
                    </transition>
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
                        >
                    </date-range-picker>
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
    import {ref,computed, watch} from 'vue';
    import DateRangePicker from '../miscellaneous/DateRangePicker';
    import CheckBox from '../miscellaneous/CheckBox';
    import {useStore} from 'vuex';
    import {ORDERLIST_FILTER, ORDERLIST_GET_FILTER, ORDERLIST_MODULE,ORDERLIST_RESET_MULITCHECKED} from "../../store/types/types";
    export default {
        name: "Filters",
        props:['filterDef', 'data'],
        components:{CheckBox, DateRangePicker},
        setup(props){
            const showfilter=ref(false);
            const current_filter=ref('');
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


            function selectclick(sel) {
                if(current_filter.value != sel) {
                    current_filter.value = sel;
                }else if(current_filter.value==sel){
                    current_filter.value='';
                }
            }
            const hasActiveFilters=computed(()=>{
                const filters= _.cloneDeep(store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_FILTER}`]);
                const allEmpty= Object.values(filters).every((element) => element.length===0);
                return !allEmpty;
            });

            // watch(() =>prodDate.value, (current_val, previous_val) => {
            //     preselection.value['infoitems.ProdDate'] = [current_val.start, current_val.end];
            // });
            // watch(() => delivDate.value, (current_val, previous_val) => {
            //     preselection.value['infoitems.DelivDate'] = [current_val.start, current_val.end];
            // });

            function checkboxclicked(check,id,name) {
                console.log(check,id,name);
                if(check)
                if(name in preselection.value) {
                    preselection.value[name].push(id);
                }else {
                    preselection.value[name] = [];
                    preselection.value[name].push(id);
                }

                if(!check)
                    if(name in preselection.value) {
                        preselection.value[name]= preselection.value[name].filter(item=>item!=id);
                    }
            }
            function applyFilter() {
                if(prodDate.value.start !='' && prodDate.value.end !='')
                    preselection.value['infoitems.ProdDate'] = [prodDate.value.start, prodDate.value.end];
                else
                    delete preselection.value['infoitems.ProdDate']
                if(delivDate.value.start !='' && delivDate.value.end !='')                    
                    preselection.value['infoitems.DelivDate'] = [delivDate.value.start, delivDate.value.end];
                else
                    delete preselection.value['infoitems.DelivDate']
                console.log(preselection);
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_RESET_MULITCHECKED}`);
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_FILTER}`,_.cloneDeep(preselection.value));
                current_filter.value='';
                toggleShow();
            }
            function cancel() {
                current_filter.value='';
                const filters=store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_FILTER}`];
                preselection.value=_.cloneDeep(filters);
                toggleShow();

            }
            function removefilter(){
              preselection.value = {}
            }
            return {
                showfilter,
                current_filter,
                selectclick,
                toggleShow,
                checkboxclicked,
                preselection,
                applyFilter,
                cancel,
                hasActiveFilters,
                prodDate,
                delivDate,
                removefilter,
                data
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
    }
    .subtitle{
        display: flex;
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
        margin-bottom: 20px;

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

</style>