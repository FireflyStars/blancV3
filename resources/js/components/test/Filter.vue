<template>
    <button class="btn btn-outline-dark filter position-relative body_small_bold" :class="{active:showfilter}" @click="toggleShow">Filters <span></span></button>
    <transition name="trans-filter" >
        <div class="filters position-absolute" v-if="showfilter">
            <h2 class="subtitle">Filter by</h2>
            <div class="row">
                <div class="mb-2" :class="{ 'col-6': filterItem.type == 'input', 'col-12': filterItem.type != 'input'}" v-for="(filterItem, key) in filterDef" :key="key">
                    <div class="form-group" v-if="filterItem.type == 'input'">
                        <input type="text" :placeholder="filterItem.label" v-model="filterItem.value" :id="filterItem.label" class="form-control form-control-sm">
                    </div>
                    <div class="form-group" v-if="filterItem.id == 'sub_order_status'">
                        <Multiselect :label="filterItem.label" :placeholder="filterItem.label" v-model="filterItem.value" :mode="filterItem.mode" :options="orderStatus" />
                    </div>
                    <div class="form-group" v-if="filterItem.id == 'destination'">
                        <Multiselect :label="filterItem.label" :placeholder="filterItem.label" v-model="filterItem.value" :mode="filterItem.mode" :options="destinations" />
                    </div>
                    <div class="form-group" v-if="filterItem.id == 'location'">
                        <Multiselect :label="filterItem.label" :placeholder="filterItem.label" v-model="filterItem.value" :mode="filterItem.mode" :options="locations" />
                    </div>
                    <div class="form-group " v-if="filterItem.type == 'datepicker'">
                        <label  :for="filterItem.label">{{ filterItem.label }} :</label>
                        <div class="d-flex justify-content-between">
                            <date-picker v-model="filterItem.value.from" @update:modelValue="newValue => filterItem.value.from = newValue" :name="filterItem.id+'_from'" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="From" :disabled-from-date="startDisabledtodate"></date-picker>
                            <date-picker v-model="filterItem.value.to" @update:modelValue="newValue => filterItem.value.to = newValue" :name="filterItem.id+'_to'" :droppos="{top:'auto',right:'0',bottom:'auto',left:'auto', transformOrigin:'top right'}" label="To" :disabled-from-date="endDisabledtodate"></date-picker>
                        </div>
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
    import { ref, computed } from 'vue';
    import {
        INVOICE_MODULE, 
        SET_INVOICE_FILTER,
        FILTER_INVOICE_LIST,
        GET_INVOICE_STATUS, 
        GET_INVOICE_DESTINATION, 
        GET_INVOICE_LOCATION,                
    } from '../../store/types/types';
    import CheckBox from '../miscellaneous/CheckBox';
    import { useStore } from 'vuex';
    import DatePicker from '../miscellaneous/DatePicker';
    import Multiselect from '@vueform/multiselect';
    export default {
        name: "Filters",
        props:['filterDef'],
        emits: ['update:filterDef'],
        components:{ 
            CheckBox,
            DatePicker,
            Multiselect
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
                store.dispatch(`${INVOICE_MODULE}${SET_INVOICE_FILTER}`, props.filterDef);
                store.dispatch(`${INVOICE_MODULE}${FILTER_INVOICE_LIST}`);
            }
            const cancel = ()=>{
                toggleShow();
            }
            const toggleShow = ()=>{
                showfilter.value =! showfilter.value;
            }
            const orderStatus = computed(()=>{
                return store.getters[`${INVOICE_MODULE}${GET_INVOICE_STATUS}`];
            })

            const destinations = computed(()=>{
                return store.getters[`${INVOICE_MODULE}${GET_INVOICE_DESTINATION}`];
            })

            const locations = computed(()=>{
                return store.getters[`${INVOICE_MODULE}${GET_INVOICE_LOCATION}`];
            })            
            return {
                showfilter,
                startDisabledtodate,
                endDisabledtodate,
                orderStatus,
                destinations,
                locations,                
                applyFilter,
                cancel,
                toggleShow
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
        z-index: 2;
        transform-origin: top center;
        padding:0 46px;
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