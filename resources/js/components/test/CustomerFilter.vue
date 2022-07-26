<template>
    <button class="btn btn-outline-dark filter position-relative body_small_bold" :class="{active:showfilter}" @click="toggleShow">Filters <span></span></button>
    <transition name="trans-filter" >
        <div class="filters position-absolute" v-if="showfilter">
            <div  class="subtitle">
                <h2 class ="col-8">Filter by</h2>
                <span @click="removefilter()">Remove filters</span>
            </div>
            <div class="row">
                <div class="mb-2 col-12" v-for="(filterItem, key) in filterDef" :key="key">
                    <div class="form-group" v-if="filterItem.type == 'select'">
                        <select-options v-model="filterItem.value" :placeholder="filterItem.label" :options="filterItem.options" :name="key" :valid="true" :label="''"></select-options>
                    </div>
                    <div class="form-group " v-if="filterItem.type == 'datepicker'">
                        <KeepAlive>
                            <date-range-picker 
                                v-model="filterItem.value" 
                                :name="key"
                                :placeholder="filterItem.label"
                                :droppos="{ top:'auto', right: 0, bottom:'auto', left:'auto', transformOrigin:'top right'}" 
                                :disabled-from-date="startDisabledtodate">
                            </date-range-picker>
                        </KeepAlive>
                    </div>
                </div>
            </div>
            <div class="row buttons mb-4">
                <div class="col text-center"><button class="btn btn-link  body_regular" @click="cancel">Cancel</button></div>
                <div class="col text-right"><button class="btn btn-dark body_medium" @click="applyFilter">Apply</button></div>
            </div>
        </div>
    </transition>
</template>

<script>
    import { ref, computed } from 'vue';
    import {
        CUSTOMER_MODULE, 
        SET_CUSTOMER_FILTER,
        FILTER_CUSTOMER_LIST,
        REMOVE_CUSTOMER_FILTER
    } from '../../store/types/types';
    import { useStore } from 'vuex';
    import SelectOptions from '../test/SelectOptions';
    import DateRangePicker from '../miscellaneous/DateRangePicker';
    export default {
        name: "CustomerFilter",
        props:['filterDef'],
        emits: ['update:filterDef'],
        components:{ 
            DateRangePicker,
            SelectOptions
        },
        setup( props ){
            const store = useStore();
            const current = new Date();
            const currentDate = `${current.getFullYear()}-${current.getMonth()+1}-${current.getDate()}`;
            const startDisabledtodate = ref(currentDate);
            const endDisabledtodate = ref(currentDate);
            const showfilter=ref(false);
            const applyFilter = ()=>{
                toggleShow();
                store.dispatch(`${CUSTOMER_MODULE}${SET_CUSTOMER_FILTER}`, props.filterDef);
                store.dispatch(`${CUSTOMER_MODULE}${FILTER_CUSTOMER_LIST}`);
            }
            const cancel = ()=>{
                toggleShow();
            }
            const toggleShow = ()=>{
                showfilter.value =! showfilter.value;
            }
            function removefilter(){

                store.dispatch(`${CUSTOMER_MODULE}${REMOVE_CUSTOMER_FILTER}`);
                store.dispatch(`${CUSTOMER_MODULE}${FILTER_CUSTOMER_LIST}`);
                toggleShow();
            }
            return {
                showfilter,
                startDisabledtodate,
                endDisabledtodate,
                applyFilter,
                cancel,
                toggleShow,
                removefilter
                
                
            }
        },
        methods:{
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
        right: 0;
        background: #F8F8F8;
        box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.12);
        width:458px;
        min-height:445px;
        z-index: 20000;
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
<style src="@vueform/multiselect/themes/default.css"></style>