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
                <multi-select-options v-model="preselection[ind]" :label="select.name" :options="select.options"></multi-select-options>
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

            const hasActiveFilters=computed(()=>{
                const filters= _.cloneDeep(store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_FILTER}`]);
                const allEmpty= Object.values(filters).every((element) => element.length===0);
                return !allEmpty;
            });

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
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_RESET_MULITCHECKED}`);
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_FILTER}`,{ customer: route.params.customerId ? route.params.customerId:window.sessionStorage.getItem('orders_customer') , search:route.params.value ? route.params.value : window.sessionStorage.getItem('search_value') , filter:preselection.value});
                toggleShow();
            }
            function cancel() {
                const filters=store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_FILTER}`];
                preselection.value=_.cloneDeep(filters);
                toggleShow();

            }
            const removefilter =()=>{
                detDate.value ={
                    start: '',
                    end: '',
                };
                delivDate.value ={
                    start: '',
                    end: '',
                };
                detDate.value ={
                    start: '',
                    end: '',
                };
                preselection.value = {}
                applyFilter()
            }
            return {
                showfilter,
                preselection,
                hasActiveFilters,
                prodDate,
                delivDate,
                data,
                detDate,
                toggleShow,
                applyFilter,
                cancel,
                removefilter,
            }

        },
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