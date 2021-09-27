<template>
    <div v-if="showorderdetail" class="odv container ">

        <div class="order-detail-progressbar" :class="loaderclass"></div>
        <i class="icon-close" @click="close"></i>
        <div v-if="typeof ORDER['detail']!='undefined'" class="section1" >
        <svg width="30" height="40" class="pdficon" @click="featureunavailable('Pdf Invoice')">
            <image xlink:href="/images/pdficon.svg"  width="30" height="40"/>
        </svg>
        <h2 >&numero; {{ORDER.detail.id}}</h2> <tag  :name="ORDER.detail.Status" ></tag>
        </div>
        <transition name="popinout">
        <div v-if="typeof ORDER['detail']!='undefined'&&ORDER.detail.Status=='LATE'&&ORDER.detail.suggestedDeliveryDate==null&&!hasRoles(['cc'])" class="section-late-production-op row">
            <div class="col" style="padding-left:32px;">
                <b>This order is late</b>
                <br/>
                <span class="f14">Please suggest a delivery date</span>
            </div>
            <div class="col-5">

                <date-picker v-model="suggested_date" name="suggested_date" :available-dates="availabledates" :droppos="{top:'auto',right:'0',bottom:'auto',left:'auto',transformOrigin:'top right'}"></date-picker>
            </div>
            <div class="col-2">
                <button class="btn btn-dark btn-black" @click="setSuggestedDate">OK</button>
            </div>
        </div>

        <div v-else-if="typeof ORDER['detail']!='undefined'&&ORDER.detail.Status=='LATE'&&ORDER.detail.suggestedDeliveryDate!=null&&!showslots" class="section-late-production-op date-suggested row" :class="{cc:hasRoles(['cc','admin','Blanc Admin'])}">
            <div class="col">
                <b style="vertical-align: middle">New promised date suggested: {{formatDate(ORDER.detail.suggestedDeliveryDate)}}</b> <button v-if="hasRoles(['cc','admin','Blanc Admin'])" class="btn btn-outline-dark body_medium" @click="chooseSlot">Choose new slot</button>
            </div>
        </div>
        <div v-else-if="showslots" class="section-late-production-op date-suggested row"  :class="{cc:hasRoles(['cc','admin','Blanc Admin'])}">
            <div class="col" style="padding-left:32px;">
                <b style="margin-left: 0">This order is late</b>
                <br/>
                <span class="f14">Please suggest a delivery date</span>
            </div>
            <div class="col-6  p-0 d-flex justify-content-evenly">
                <date-picker v-model="cc_new_delivery_date" name="cc_new_delivery_date" :disabled-to-date="disabledtodate" :available-dates="availabledates" :droppos="{top:'auto',right:'0',bottom:'auto',left:'auto',transformOrigin:'top right'}"></date-picker>
                <time-slot-picker v-model="suggest_timeslot"   name="suggest_timeslot" :available-slots="availabletimeslot"></time-slot-picker>
            </div>
            <div class="col-1 p-0">
                <button class="btn btn-dark btn-black" @click="setNewDeliveryDate">OK</button>
            </div>
        </div>
            <div v-else-if="showNewDeliveryDateMsg" class="section-late-production-op date-suggested date-suggested-ok row" :class="{cc:hasRoles(['cc'])}">
                <div class="col"><b>New delivery date: {{ORDER.detail.PromisedDate}}</b></div>
            </div>
</transition>
            <div v-if="typeof ORDER['detail']!='undefined'"  class="row section2 align-items-center">
                <div class="col-9">
                <svg width="24" height="24" class="truckicon">
                    <image xlink:href="/images/truck.svg"  width="24" height="24"/>
                </svg>
                    <span class="body_medium valign-middle">Delivery</span>
                </div>
                <div class="col-3 text-center">
                    <tag  :name="ORDER.detail.paid" ></tag>
                </div>

            </div>
        <div v-if="typeof ORDER['detail']!='undefined'"  class="row section3">
            <div class="col-9">
                <span class="body_medium">Promised date: {{ORDER.detail.PromisedDate.toUpperCase()}}  <button type="button" class="btn-link-green body_regular"  @click="featureunavailable('Edit promised date')">Edit</button></span>
            </div>
            <div class="col-3 text-center body_bold">
               <b> {{formatPrice(ORDER.detail.Total)}}</b>
            </div>

        </div>
        <hr v-if="typeof ORDER['detail']!='undefined'" />
        <div  v-if="typeof ORDER['detail']!='undefined'" class="row section4">
            <div class="col">
                <span class="customername  body_bold  text-capitalize d-inline-block
">{{ORDER.detail.Name.replace(',','').toLowerCase()}} <button type="button" class="btn-link-green body_regular"  @click="featureunavailable('Edit customer')">Edit</button></span>
            </div>
            <div class="col">
                <tag   v-if="ORDER.detail.TypeDelivery=='DELIVERY'" :name="'B2C'" ></tag>
                <tag   v-else :name="'B2B'" ></tag>
            </div>
        </div>
        <div  v-if="typeof ORDER['detail']!='undefined'" class="row section5">
            <div class="col">
                <AddressFormat  :title="'Delivery address'" :address="ORDER.delivery" ></AddressFormat>
            </div>
            <div class="col">
                <AddressFormat :title="'Billing address'"  v-if="ORDER.billing!=null" :address="ORDER.billing" ></AddressFormat>
                <AddressFormat :title="'Billing address'" v-else :address="ORDER.delivery" ></AddressFormat>
            </div>
        </div>

        <div v-if="typeof ORDER['detail']!='undefined'" class="row section6">
            <div class="col" v-if="ORDER.detail.Phone!=''&&ORDER.detail.Phone!=null">
                <div class="row" v-for="(phone,index) in ORDER.detail.Phone">
                    <div class="col">
                        <div class="body_small_medium">Phone number {{index+1}}</div>
                        <div class="phone body_small">+{{phone.replace('|',' ')}}</div>
                    </div>
                </div>
            </div>
            <div v-else class="col">
                <div class="body_small_medium">Phone number</div>
                <div class="phone body_small">--</div>
            </div>
            <div class="col">
                <div class="row ">
                    <div class="col">
                        <div class="body_small_medium">Payment method</div>
                        <span class="body_small">--</span>
                    </div>
                    <div class="col">
                        <div class="body_small_medium">Payment details</div>
                        <span class="body_small">--</span>
                    </div>
                </div>
            </div>
        </div>
        <hr v-if="typeof ORDER['detail']!='undefined'"/>
        <div class="row">
            <div class="col">
                <order-detail-sub-order-items-table @show_conf="show_split_conf=true" :tabledef="itemsfields"  :id="'items_table'" v-if="typeof ORDER['detail']!='undefined'"></order-detail-sub-order-items-table>
            </div>
        </div>
        <div class="mt-3 mb-3 row" v-if="typeof ORDER['detail']!='undefined'">
            <div class="col-2">
                <button class="btn btn-outline-dark body_medium"  @click="featureunavailable('Open')">Open</button>
            </div>
            <div class="col-4">
                <button class="btn btn-outline-danger body_medium" @click="markaslate" v-if="ORDER['detail'].Status!='LATE'">Mark as late</button>
            </div>
        </div>
        <split-confirmation :show_conf="show_split_conf" @close="show_split_conf=false"></split-confirmation>
    </div>
</template>

<script>
    import {ref,computed,nextTick,watch} from 'vue';
    import {useRoute,useRouter} from 'vue-router';
    import {useStore} from 'vuex';
    import Tag from  '../miscellaneous/Tag'
    import AddressFormat from '../miscellaneous/AddressFormat';
    import DatePicker from '../miscellaneous/DatePicker'
    import TimeSlotPicker from '../miscellaneous/TimeSlotPicker';
    import OrderDetailSubOrderItemsTable from './OrderDetailSubOrderItemsTable'
    import {formatPrice,hasRoles,formatDate} from "../helpers/helpers";
    import SplitConfirmation from '../miscellaneous/SplitConfirmation'
    import {
        ORDERLIST_MODULE,
        ORDERLIST_GET_CURRENT_SELECTED,
        ORDERLIST_SELECT_CURRENT,
        ORDERDETAIL_MODULE,
        ORDERDETAIL_LOAD_DETAIL,
        ORDERDETAIL_GET_LOADER,
        ORDERDETAIL_SET_LOADER,
        TOASTER_MODULE,
        TOASTER_MESSAGE,
        ORDERDETAIL_GET_DETAILS,
        ORDERDETAIL_SET_DETAILS,
        ORDERLIST_LOADERMSG,
        ORDERLIST_MARK_AS_LATE,
        ORDERDETAIL_UPDATE_SUGGESTED_DELIVERY_DATE,
        ORDERLIST_UPDATE_SUGGESTED_DELIVERY_DATE,
        ORDERLIST_UPDATE_STATUS,
        ORDERLIST_NEW_DELIVERY_DATE,
        ORDERDETAIL_NEW_DELIVERY_DATE,
        ORDERDETAIL_UPDATE, ORDERLIST_REMOVE_ORDERS
    } from "../../store/types/types";



    export default {
        name: "OrderDetail",
        components:{Tag,AddressFormat,OrderDetailSubOrderItemsTable,DatePicker,TimeSlotPicker,SplitConfirmation},
        setup(){
            const route =useRoute();
            const router=useRouter();
            const store =useStore();

            const show_split_conf=ref(false);

            const itemsfields=ref({
                line_select:{
                    name:" ",
                    flex:"1",
                    sortable:false,
                    identifier:"infoitems_id",
                    type:'checkbox',
                },
                status:{
                    name:"Items",
                    flex:"3",
                    sortable:true,
                    type:'color',
                    css:"text-align:center",
                    header_align:"center",
                },
                typeitem:{
                    name:"",
                    flex:"10",
                    sortable:false
                },
                ItemTrackingKey:{
                    name:"Barcode n°",
                    flex:"10",
                    sortable:false,
                    type:'express'
                },
                brand:{
                    name:"Brand",
                    flex:"15",

                    sortable:true
                },
                colors:{
                    name:"Color",
                    flex:"8",
                    sortable:true,
                    type:'color',
                    css:"text-align:center",
                    header_align:"center",
                },


                station:{
                    name:"Station",
                    flex:"13",
                    sortable:true,
                    type:'tag',
                    header_align:"center",
                    css:"text-align:center",
                },
                priceTotal:{
                    name:"Price",
                    flex:"8",
                    sortable:true,
                    type:'price',
                    css:"text-align:right;"
                }
            });
            const CURRENT_SELECTED=computed(()=>{
                return store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_CURRENT_SELECTED}`];
            });
            const availableslots=ref([]);
            const availabledates=ref([]);
            const availabletimeslot=ref([]);
            const disabledtodate=ref('');


            const ORDER=computed(()=>{
                const o= store.getters[`${ORDERDETAIL_MODULE}${ORDERDETAIL_GET_DETAILS}`];

                if(typeof o.available_slots!=="undefined") {
                     availableslots.value=o.available_slots;
                    if(o.detail.TypeDelivery!="DELIVERY")
                        availabletimeslot.value=[1,3,5,6,7,9,11];
                if(Object.entries(o.available_slots).length>0){
                    availabletimeslot.value=[];
                   for(const date in  o.available_slots){
                       availabledates.value.push(date);

                   }

                }
                if(o.detail.suggestedDeliveryDate!=null){
                    let d=new Date(o.detail.suggestedDeliveryDate);
                    d.setDate(d.getDate()-1);

                    disabledtodate.value=`${d.getFullYear()}-${(d.getMonth()+1).toString().padStart(2,'0')}-${(d.getDate()).toString().padStart(2,'0')}`;
                }

                }
                return o;
            });


            if(CURRENT_SELECTED.value==''&&route.params.order_id>0){
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_SELECT_CURRENT}`,route.params.order_id)
            }
            const showorderdetail=computed(()=>{
                return (store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_CURRENT_SELECTED}`])&&route.params.order_id>0;
            });
            if(showorderdetail) {
                store.commit(`${ORDERDETAIL_MODULE}${ORDERDETAIL_SET_LOADER}`,'');
                nextTick(() => {
                    store.dispatch(`${ORDERDETAIL_MODULE}${ORDERDETAIL_LOAD_DETAIL}`, CURRENT_SELECTED.value).catch((error)=>{
                        if(typeof error.response!="undefined")
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'});
                    });;
                });
            }
            const featureunavailable=((feature)=>{
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:feature+' feature not yet implemented.',ttl:5,type:'success'});
            });

            const markaslate=(()=>{
                const order=[ORDER.value.detail.id];

                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOADERMSG}`,`Marking order as late, please wait...`);
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_MARK_AS_LATE}`,order).then(()=>{

                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'Order marked as late successfully.',ttl:5,type:'success'});
                    store.commit(`${ORDERDETAIL_MODULE}${ORDERDETAIL_UPDATE}`,{Status:'LATE'});
                }).catch((error)=>{
                    if(typeof error.response!="undefined")
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'});
                });

            });

            const suggested_date=ref('');
            const cc_new_delivery_date=ref('');

            watch(() => cc_new_delivery_date.value, (current_val, previous_val) => {
                if(ORDER.value.detail.TypeDelivery=="DELIVERY") {
                    if(Object.entries(availableslots.value).length>0) {
                        availabletimeslot.value=availableslots.value[current_val];
                    }

                }


            });

            const suggest_timeslot=ref(0);
            const setSuggestedDate=()=>{
                if(suggested_date.value=="") {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: `Please choose a date.`,
                        ttl: 5,
                        type: 'warn'
                    });
                    return false;
                }
                store.dispatch(`${ORDERDETAIL_MODULE}${ORDERDETAIL_UPDATE_SUGGESTED_DELIVERY_DATE}`,suggested_date.value).then(()=>{
                   store.commit(`${ORDERLIST_MODULE}${ORDERLIST_UPDATE_SUGGESTED_DELIVERY_DATE}`,{infoOrder_id:ORDER.value.detail.id,suggested_date:suggested_date.value});
                }).catch((error)=>{
                    if(typeof error.response!="undefined")
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'});
                });
            }
            const setNewDeliveryDate=()=>{
                if(cc_new_delivery_date.value=="") {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: `Please choose a new delivery date.`,
                        ttl: 5,
                        type: 'danger'
                    });
                }
                if(suggest_timeslot.value=="") {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: `Please choose a timeslot.`,
                        ttl: 5,
                        type: 'danger'
                    });
                }
                if(cc_new_delivery_date.value==""||suggest_timeslot.value=="")
                    return false;
                store.dispatch(`${ORDERDETAIL_MODULE}${ORDERDETAIL_NEW_DELIVERY_DATE}`,{PromisedDate:cc_new_delivery_date.value,timeslot:suggest_timeslot.value}).then((response)=>{

                    if(response.data.updated!==false) {
                        store.commit(`${ORDERDETAIL_MODULE}${ORDERDETAIL_UPDATE}`, {
                            PromisedDate: formatDate(cc_new_delivery_date.value, 'DAY DD/MM'),
                            Status: 'IN PROCESS'
                        });
                        store.commit(`${ORDERLIST_MODULE}${ORDERLIST_NEW_DELIVERY_DATE}`, {
                            infoOrder_id: ORDER.value.detail.id,
                            PromisedDate: formatDate(cc_new_delivery_date.value)
                        });
                       /* store.commit(`${ORDERLIST_MODULE}${ORDERLIST_UPDATE_STATUS}`, {
                            orderids: [ORDER.value.detail.id],
                            status: "IN PROCESS"
                        });*/
                        store.commit(`${ORDERLIST_MODULE}${ORDERLIST_REMOVE_ORDERS}`,  [ORDER.value.detail.id]);
                        showslots.value = false;
                        showNewDeliveryDateMsg.value = true;
                    }else{
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`Error: Unable to update. ${response.data.message}`,ttl:5,type:'danger'});
                    }
                }).catch((error)=>{
                    if(typeof error.response!="undefined")
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'});
                });
            }
            const showslots=ref(false);
            const showNewDeliveryDateMsg=ref(false);
            const chooseSlot=()=>{
                showslots.value=true;
            }

            return {
                showorderdetail,
                loaderclass:computed(()=>{
                    return store.getters[`${ORDERDETAIL_MODULE}${ORDERDETAIL_GET_LOADER}`];
                }),
                close:(()=>{
                    store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_SELECT_CURRENT}`,'');
                    store.commit(`${ORDERDETAIL_MODULE}${ORDERDETAIL_SET_DETAILS}`,{});
                    router.back();
                }),
                ORDER,
                featureunavailable,
                itemsfields,
                markaslate,
                suggested_date,
                cc_new_delivery_date: cc_new_delivery_date,
                suggest_timeslot,
                setSuggestedDate,
                formatDate,
                formatPrice,
                hasRoles,
                chooseSlot,
                showslots,
                availableslots,
                availabledates,
                availabletimeslot,
                setNewDeliveryDate,
                showNewDeliveryDateMsg,
                disabledtodate,
                show_split_conf
            }
        }
    }
</script>

<style scoped>
    .align-right{
        text-align: right;
    }
    .section1{
        margin-top:24px;
    }
    .section2,.section3{
        color:#47454B;
        font-weight: 400;
        margin-left: 3px;
        margin-top:31px;
    }
    .section3{
        margin-top:5px;
     }
    .section4{
        margin-bottom: 8px;
        margin-left: -2px;
        margin-right: -2px;
    }
    .section5{
        margin-bottom: 30px;
        margin-left: -2px;
        margin-right: -2px;
    }
    .section6{
        margin-left: -2px;
        margin-right: -2px;
    }
    .section6 .body_small_medium{
        color: #C3C3C3;
    }
    .truckicon{
        margin-right: 8px;
    }
    .pdficon{
        margin: 0 24px 0 6px;
        display: inline-block;
        vertical-align: middle;
        cursor: pointer;
    }
    h2{
        display: inline-block;
        margin: 0 36px 0 0;
        vertical-align: bottom;

    }
    .tag{
        vertical-align: bottom;
    }
    .odv{
        max-width: 684px;
        background: #FFF;
        height: calc(100% - var(--mainlogoheight));
        position: fixed;
        top: var(--mainlogoheight);
        overflow-y: auto;
        right: 0;
        z-index: 10001;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.18);
        padding: 0 20px;
    }
    .order-detail-progressbar{
        background: #FFF;
        height: 6px;
        width: 100%;
        display: block;
        position: absolute;
        margin: 0 -1.5rem;
    }
    .order-detail-progressbar:after{
        background: #42A71E;
        position: absolute;
        width: 0%;
        left: 0;
        content: "";
        height:6px;
    }

    .animate40:after{
        transition: width 2s ease;
        width: 40%;
        animation: pulse 500ms infinite;
    }
    .animate100:after{
        animation: ani100 0.5s ease forwards;
    }

    @keyframes ani100 {
        50%{
            width: 50%;
            opacity: 1;
        }
        60%{
            width: 50%;
            opacity: 1;
        }
       99%{
           width: 99%;
            opacity: 1;
        }
        100%{
            width: 100%;
            opacity: 0;
        }
    }

    @keyframes pulse {
        0%{
            opacity: 0.7;
        }
        100%{
            opacity: 1;
        }
    }

    .icon-close{
        top:24px;
        right: 24px;
    }
    hr{
      margin:  1.8rem -20px;
      background-color: #E0E0E0;
        opacity: 1;
    }
    .section-late-production-op{
        background: #F9D0D0;
        min-height: 84px;
        margin: 15px -20px 0 -20px;
        align-items: center;
    }
    .section-late-production-op b{
        font-weight: bold;
    }
    .date-suggested{
        background: rgba(239, 143, 0, 0.3);
        text-align: center;
    }
    .date-suggested-ok{
        background: rgba(66, 167, 30, 0.3);
        text-align: left;
    }
    .date-suggested.cc{
        text-align: left;
    }
    .date-suggested.cc b{
        margin:0 20px;
    }
    .btn-black{
        background: #000000;
        height: 40px;
        width: 46px;
        text-indent: -80px;
        overflow: hidden;
        position: relative;
        padding: inherit;
    }
    .btn-black:after{
        content: "";
        width: 11px;
        height: 17px;
        border-right: 2px solid #FFFFFF;
        border-bottom: 2px solid #FFFFFF;
        position: absolute;
        left: 50%;
        transform: translate(-50%) rotate(45deg);
    }
    .btn-black:hover{
        background: #47454B;
    }

</style>