<template>
    <transition
            enter-active-class="animate__animated animate__fadeIn"
    >
        <div class="container-fluid h-100 bg-color" v-if="showcontainer">
            <main-header></main-header>
            <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax" style="z-index:100">
            <side-bar></side-bar>
                <div class="col main-view p-5">
                    <h2>Component library</h2>

                    <div class="row">
                        <div class="col-3">

                            <select-options v-model="sel" placeholder="Choose a number" :options="[{value:'',display:''},{value:'1',display: 'One'},{value:'2',display:'Two'}]" name="select1" hint="Here is your hint."></select-options>
                        </div>
                        <div class="col p-2">
                            {{sel}}
                        </div>
                
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <select-options v-model="sel1" placeholder="Choose a number" :options="[{value:'',display:''},{value:'1',display: 'One'},{value:'2',display:'Two'}]" name="select2" hint="nice." :valid="true" label="Positive"></select-options>
                        </div>
                        <div class="col p-2">
                            {{sel1}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <select-options v-model="sel2" placeholder="Choose a number" :options="[{value:'',display:''},{value:'1',display: 'One'},{value:'2',display:'Two'}]" name="select3" hint="Oops." :valid="false" label="Negative"></select-options>
                        </div>
                        <div class="col p-2">
                            {{sel2}}
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-3">
                            <date-picker v-model="date" name="date" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Date picker" hint="Choose any timeslot"></date-picker>

                        </div>
                        <div class="col p-2">
                            {{date}}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-3">
                            <date-picker v-model="date2" name="date2" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Date picker" hint="Choose available timeslot" :available-dates="['2021-09-14','2021-09-15','2021-09-16','2021-09-18']"></date-picker>

                        </div>
                        <div class="col p-2">
                            {{date2}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <date-picker v-model="date3" name="date3" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Date picker" hint="disabled till 2021-09-10" disabled-to-date="2021-09-10"></date-picker>

                        </div>
                        <div class="col p-2">
                            {{date2}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <time-slot-picker placeholder="00-00 AM" v-model="slot"   name="timepick1" :available-slots="[1,5]" hint="How about a very long hint" label="Time"></time-slot-picker>
                        </div>
                        <div class="col p-2">
                            {{slot}}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-3">
                            <time-slot-picker placeholder="00-00 AM" v-model="slot2"   name="timepick2" :available-slots="[1,5]" hint="Oops its disabled" label="Time" :disabled="true"></time-slot-picker>
                        </div>
                        <div class="col p-2">
                            {{slot2}}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <tab-pane :tabs="{first:'First tab',second:'second tab'}">
                                <template v-slot:first>
                                    1st
                                </template>
                                <template v-slot:second>
                                    2nd
                                </template>
                            </tab-pane>
                        </div>
                         
                    </div>

                    <div class="row" style="padding-top:20px">
                        <div class="col">
                            <button @click="scanBarcode" >Scan Barcode</button>
                            <order-barcode  v-on:scan_Barcode="scanBarcode" class="back-layer" v-if="show_barcode" style="background: transparent;" ></order-barcode>
                            <div  v-if="show_barcode" class= "overlay">
                            </div>
                        </div>

                         
                    </div>
    
                    <div class="row search">
                        
                        <span class= "subtitle">Customer details</span>
                        <div  style="padding: 0;" >
                            <search  v-model="search" name="search" v-on:getCustomer="ClickCustomer" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Search a customer"  ></search>
                        </div>
                    
                     </div>


                    <div class="row recurring-form">
                      <div class="col-4 recurring">
                            <recurring-form  v-model="data"></recurring-form>
                      </div>
                       
                    </div>      
                         
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import {ref,onMounted,nextTick,watch} from 'vue';
    import MainHeader from '../layout/MainHeader';
    import SideBar from '../layout/SideBar'
    import SelectOptions from '../miscellaneous/SelectOptions'
    import TimeSlotPicker from '../miscellaneous/TimeSlotPicker'
    import DatePicker from '../miscellaneous/DatePicker'
    import TabPane from '../miscellaneous/TabPane'
    import {usePermission} from "../helpers/helpers";
    import {PERMISSIONS} from "../../store/types/permission_types";
    import Search from '../miscellaneous/Search';
    import OrderBarcode from '../miscellaneous/OrderBarcode'
    import RecurringForm from '../miscellaneous/RecurringForm'


    export default {
        name: "ComponentTest",
        components: { SideBar, MainHeader,SelectOptions,TimeSlotPicker,DatePicker,TabPane,Search,OrderBarcode, RecurringForm},
        setup(props,context){
            const showcontainer=ref(false);
            const show_barcode= ref(false);
            const Customer= ref('');
            const Scan= ref('');

            const sel=ref(1);
            const slot=ref(5);
            const slot2=ref(0);
            const sel1=ref(2);
            const sel2=ref('');
            const date=ref('');
            const date2=ref('2021-09-15');
            const date3=ref('2021-09-15');
            const search= ref('');
            const data= ref([]);

              watch(() => Scan.value, (current_val, previous_val) => {
              if(Scan.value == false) {
                  show_barcode.value= false;
              }else {
                  show_barcode.value= true;
              }

            });

            onMounted(()=>{
                nextTick(()=>{
                    showcontainer.value=true;

                });

            });
            const perm=usePermission;
            perm(PERMISSIONS.MARK_AS_LATE).then((res)=>{
               console.log(res);
            }).catch((err)=>{
                console.log(err)
            });
            perm(PERMISSIONS.MARK_AS_LATE).then((res)=>{
                console.log(res);
            }).catch((err)=>{
                console.log(err)
            });

            function scanBarcode(value){
                Scan.value = value;
            };

            function ClickCustomer(value){
                Customer.value = value;
                console.log('customer' ,value )
             };

               watch(() => data.value, (current_val, previous_val) => {
                 console.log('recurring_order', current_val)

            });

        

            
            return {

                showcontainer,
                sel,
                sel1,
                sel2,
                slot,
                slot2,
                date,
                date2,
                date3,
                search,
                show_barcode,
                scanBarcode,
                Customer,
                Scan,
                ClickCustomer,
                data

            }
        }
    }
</script>

<style scoped>

    .hmax{
        height: calc(100% - var(--mainlogoheight));
        padding-top:var(--mainlogoheight) ;
    }

    .auth-logo{
        height: var(--authlogoheight);
    }
    .back-layer{
        background:rgba(224, 224, 224,0.6);
        position: fixed;
        top: 0;
        left:0;
        height: 100%;
        width: 100%;
        z-index: 9999;
    }
    .search{
    background: #fff;
    width: 580px;
    height: 244px;
    box-shadow: 0px 4px 16px rgb(0 0 0 / 12%);
    border-radius: 5px;
    padding: 20px 32px 160px 32px;
    margin-top: 28px;
    position: relative;
    }
    .overlay{
        display: block;
        content: '';
        width: 100%;
        height: 100%;
        position: fixed;
        left: 0;
        top: 0;
        background: black;
        opacity: 0.6;
        cursor: pointer;
        z-index: 100;
    }
    .subtitle{
        height: 25px;
    }
    .recurring{
       margin: 20px;
       width: 540px;
    }
    .recurring-form{
        width: 580px;
        flex-wrap: nowrap;
    }

</style>