<template>
    <div v-if="showorderdetail" class="odv container">
        <div class="order-detail-progressbar" :class="loaderclass"></div>
        <i class="icon-close" @click="close"></i>
               <div class="detail-header position-fixed " v-if="(typeof ORDER['detail']!='undefined')">
                 <div class = "d-flex align-items-center justify-content-between">
                    <div class="detail-title text-capitalize" >
                        <div class ="name_customer">{{ORDER.detail.Name.toLowerCase()}}</div>
                        <span class="ms-3 cursor-pointer" style="font-size: 12px; line-height: 14px; color: #42A71E; text-decoration: underline;" @click="EditCustomer(ORDER.detail.id)">View</span>
                    </div>

                    <div class="detail-close-section d-flex align-items-center">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="transform: translateY(8px);">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M20.8867 11.1437C20.8543 11.0873 20.8279 11.0274 20.8081 10.9652L19.8615 8.00091C19.7421 7.62706 19.4011 7.45233 19.0165 7.45233H16.8865V6.22331H19.0165C19.7857 6.22331 20.648 6.70463 20.8867 7.45233L21.7857 9.90358L22.8774 12.8132C22.941 12.9861 23 13.1118 23 13.2737V15.4265C23 16.4304 22.2041 17.2443 21.2222 17.2443H2.77778C1.79594 17.2443 1 16.4304 1 15.4265V6.87706C1 5.87311 1.79594 5.00586 2.77778 5.00586H15.6716C16.4309 5.00586 16.8865 5.44681 16.8865 6.22331L16.2663 6.5591C16.2663 6.28458 15.94 6.22331 15.6716 6.22331H2.77778C2.28686 6.22331 2.20968 6.37509 2.20968 6.87706V15.4265C2.20968 15.9285 2.28686 16.0183 2.77778 16.0183H21.2222C21.7131 16.0183 21.7857 15.9285 21.7857 15.4265V13.4761C21.7857 13.3425 21.7589 13.2103 21.707 13.0873L20.8867 11.1437Z" fill="white"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16.8881 6.20459V10.0537C16.8881 10.2998 16.996 10.4982 17.2935 10.4982H21.6709V11.7591H17.2935C16.3977 11.7591 15.6743 10.7894 15.6743 10.0537V6.20459L16.8881 6.20459Z" fill="white"/>
                        <path d="M8.18364 16.3276C8.18364 17.3401 7.37958 18.1609 6.38772 18.1609C5.39586 18.1609 4.5918 17.3401 4.5918 16.3276C4.5918 15.3151 5.39586 14.4943 6.38772 14.4943C7.37958 14.4943 8.18364 15.3151 8.18364 16.3276Z" fill="white"/>
                        <circle cx="6.38779" cy="16.3276" r="2.1666" stroke="white"/>
                        <path d="M19.8575 16.3276C19.8575 17.3401 19.0534 18.1609 18.0615 18.1609C17.0697 18.1609 16.2656 17.3401 16.2656 16.3276C16.2656 15.3151 17.0697 14.4943 18.0615 14.4943C19.0534 14.4943 19.8575 15.3151 19.8575 16.3276Z" fill="white"/>
                        <circle cx="18.0611" cy="16.3276" r="2.1666" stroke="white"/>
                        </svg>
                        <span  class="cust-location-name ms-1">{{ORDER.detail.TypeDelivery}}</span>
                        <svg class="detail-close ms-4" width="12" height="12" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" @click="close">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.78812 0.297179C1.3976 -0.0953162 0.764438 -0.0953162 0.373917 0.297179C-0.0166053 0.689674 -0.0166053 1.32604 0.373916 1.71853L5.58834 6.9593L0.292891 12.2815C-0.0976305 12.674 -0.0976304 13.3104 0.292891 13.7029C0.683413 14.0954 1.31657 14.0954 1.7071 13.7029L7.00254 8.38065L12.293 13.6978C12.6835 14.0903 13.3166 14.0903 13.7072 13.6978C14.0977 13.3053 14.0977 12.6689 13.7072 12.2765L8.41675 6.9593L13.6261 1.72358C14.0167 1.33109 14.0167 0.694726 13.6261 0.302231C13.2356 -0.0902646 12.6025 -0.0902643 12.2119 0.302231L7.00254 5.53795L1.78812 0.297179Z" fill="white"/>
                        </svg>
                    </div>
                </div>
                <div class = "mt-1 d-flex align-items-center">
                    <div class="cust-type-icon rounded-pill">
                            {{ORDER.detail.cust_type }}
                    </div>
                    <div class="cust-account-icon rounded-pill">
                            {{ ORDER.detail.OnAccount == '1' ? "On Account" : "PAY AS YOU GO" }}
                    </div>
                </div>
                <div class = "mt-1 d-flex align-items-center">
                    <img  @click="removeLinkedAccount(ORDER.detail.id)" v-if="ORDER.detail.account_type == 'Sub'" src="/images/link.png"/>
                    <div class="text-type">
                        {{ORDER.detail.CompanyName}}
                    </div>
                </div>
            </div>

        <div v-if="(typeof ORDER['detail']!='undefined')"  class="row section2 align-items-center">

             <div v-if="(typeof ORDER['detail']!='undefined')" class=" col-7 section1">
                <h2 >&numero; {{ORDER.detail.order_id}}<button v-if="ORDER['detailingitemlist'].length !== 0" type="button" class="btn-link-green body_regular"  @click='EditOrder(ORDER.detail.order_id)'>Edit</button></h2>
                <div class=" text-center">
                    <tag :name="ORDER.detail.Status" style="margin-right: 10px;"></tag>
                    <tag :name="ORDER.detail.paid"></tag>
               </div>

            </div>
            <div class="col-5 text-center sectionPrice">
                <svg width="30" height="40" class="pdficon" @click="featureunavailable('Pdf Invoice')">
                    <image xlink:href="/images/pdficon.svg"  width="30" height="40"/>
                </svg>
                <div class="total-section mt-2">

                            <div class="total d-flex">
                                <div class="col-8 px-2 text-end PriceTotal">{{formatPrice(ORDER.detail.Total)}}</div>
                                <div class="col-4 px-2 d-flex align-items-center">Total</div>
                            </div>
                            <div class="total d-flex">
                                <div class="col-8 px-2 text-end PriceTotal">{{ORDER.totalitems}}</div>
                                <div class="col-4 px-2 d-flex align-items-center">Pieces</div>
                            </div>
                </div>
                <!-- <div>
                <div class="col-3 text-center body_bold">
                  <b> {{formatPrice(ORDER.detail.Total)}}</b> Total
                </div>
                <div class="col-3 text-center body_bold">
                  <b> {{ORDER.detailingitemlist.length}}</b> Pieces
                </div>
                </div> -->
            </div>
        </div>

        <transition name="popinout">
            <div v-if="typeof ORDER['detail']!='undefined'&&ORDER.detail.Status=='LATE'&&ORDER.detail.suggestedDeliveryDate==null&&!hasRoles(['cc'])" class="section-late-production-op row">
                <div class="col" style="padding-left:32px;">
                    <b>This order is late</b>
                    <br/>
                    <span class="f14" v-if="typeof ORDER['detail']!='undefined'&&ORDER.detail.TypeDelivery=='DELIVERY'">Please enter new delivery date</span>
                    <span class="f14" v-else>Please enter new collection date</span>
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
                    <span :class="{'d-none':setDeliveryDate}">
                    <b style="margin-left: 0">This order is late</b>
                    <br/>
                    </span>
                    <span class="f14" v-if="typeof ORDER['detail']!='undefined'&&ORDER.detail.TypeDelivery=='DELIVERY'">Please enter new delivery date</span>
                    <span class="f14" v-else>Please enter new collection date</span>
                </div>
                <div class="col-6  p-0 d-flex justify-content-evenly">
                    <date-picker v-model="cc_new_delivery_date" name="cc_new_delivery_date" :disabled-to-date="disabledtodate" :available-dates="availabledates" :droppos="{top:'auto',right:'0',bottom:'auto',left:'auto',transformOrigin:'top right'}"></date-picker>
                    <time-slot-picker  v-if="typeof ORDER['detail']!='undefined'&&ORDER.detail.TypeDelivery=='DELIVERY'" placeholder="00-00 AM" v-model="suggest_timeslot"   name="suggest_timeslot" :available-slots="availabletimeslot"></time-slot-picker>
                </div>
                <div class="col-1 p-0">
                    <button class="btn btn-dark btn-black" @click="setNewDeliveryDate">OK</button>
                </div>
            </div>
            <div v-else-if="showNewDeliveryDateMsg" class="section-late-production-op date-suggested date-suggested-ok row" :class="{cc:hasRoles(['cc'])}">
                <div class="col">
                    <b v-if="ORDER.detail.TypeDelivery=='DELIVERY'">New delivery date: {{ORDER.detail.PromisedDate}}</b>
                    <b v-else>New collection date: {{ORDER.detail.PromisedDate}}</b>
                    </div>
            </div>
        </transition>

        <transition name="popinout">
            <div v-if="typeof ORDER['detail']!='undefined'&&!ORDER.detail.alreadypickuped&&ORDER.detail.Status=='LATE'&&ORDER.detail.suggestedDeliveryDate==null&&!hasRoles(['cc'])" class="section-late-production-op row">
                <div class="col" style="padding-left:32px;">
                    <b>This order is late</b>
                    <br/>
                    <span class="f14">Please enter new pickup date</span>
                </div>
                <div class="col-5">

                    <date-picker v-model="suggested_date" name="suggested_date" :available-dates="availabledates" :droppos="{top:'auto',right:'0',bottom:'auto',left:'auto',transformOrigin:'top right'}"></date-picker>
                </div>
                <div class="col-2">
                    <button class="btn btn-dark btn-black" @click="setSuggestedDate">OK</button>
                </div>
            </div>
            <div v-else-if="typeof ORDER['detail']!='undefined'&&!ORDER.detail.alreadypickuped&&ORDER.detail.Status=='LATE'&&ORDER.detail.suggestedDeliveryDate!=null&&!showslots" class="section-late-production-op date-suggested row" :class="{cc:hasRoles(['cc','admin','Blanc Admin'])}">
                <div class="col">
                    <b style="vertical-align: middle">New pickup date suggested: {{formatDate(ORDER.detail.suggestedDeliveryDate)}}</b> <button v-if="hasRoles(['cc','admin','Blanc Admin'])" class="btn btn-outline-dark body_medium" @click="chooseSlot">Choose new slot</button>
                </div>
            </div>
            <div v-else-if="showslotspickup" class="section-late-production-op date-suggested row"  :class="{cc:hasRoles(['cc','admin','Blanc Admin'])}">
                <div class="col" style="padding-left:32px;">
                    <span :class="{'d-none':setDeliveryDate}">
                    <b style="margin-left: 0">This order is late</b>
                    <br/>
                    </span>
                    <span class="f14">Please enter new pickup date</span>
                </div>
                <div class="col-6  p-0 d-flex justify-content-evenly">
                    <date-picker v-model="cc_new_pickup_date" name="cc_new_pickup_date" :disabled-to-date="disabledtodate" :available-dates="availabledates" :droppos="{top:'auto',right:'0',bottom:'auto',left:'auto',transformOrigin:'top right'}"></date-picker>
                    <time-slot-picker placeholder="00-00 AM" v-model="suggest_timeslot_pickup"   name="suggest_timeslot_pickup" :available-slots="availabletimeslot"></time-slot-picker>
                </div>
                <div class="col-1 p-0">
                    <button class="btn btn-dark btn-black" @click="setNewPickupDate">OK</button>
                </div>
            </div>
            <!-- <div v-else-if="showNewDeliveryDateMsg" class="section-late-production-op date-suggested date-suggested-ok row" :class="{cc:hasRoles(['cc'])}">
                <div class="col"><b>New delivery date: {{ORDER.detail.PromisedDate}}</b></div>
            </div> -->
        </transition>

            <div class="order-brief-info-section mt-2" v-if="(typeof ORDER['detail']!='undefined')">
                <div class="d-flex">
                    <div class="col-6"  >
                        <p class="order-sub-title m-0">
                            {{ORDER.detail.order_left_text}} <span v-if=" (ORDER['detail'].Status =='RECURRING' || ORDER['detail'].Status =='SCHEDULED') && ORDER['detail'].DatePickup != '--' " class="ms-2 cursor-pointer text-underline"  @click="featureunavailable('Edit Pickup')">Edit</span>
                        </p>
                        <p class="m-0">{{ORDER.detail.order_left_date }}</p>
                        <p class="m-0">{{ORDER.detail.order_left_time}}</p>
                    </div>
                    <div class="col-6 ps-5 border-left">
                        <p class="order-sub-title m-0">
                            {{ORDER.detail.order_right_text}} <span  class="ms-2 cursor-pointer text-underline" v-if=" (ORDER['detail'].Status !='FULFILLED'&&ORDER['detail'].Status !='DELETE'&&ORDER['detail'].Status !='VOID' )" @click="showDeliverySlots">Edit</span>
                        </p>
                         <p class="mb-0" v-if="!updatedelverydate">{{ORDER.detail.order_right_date }}</p>
                         <p class="mb-0" v-if="updatedelverydate">{{formatOrderDate(cc_new_delivery_date)}}</p>
                         <p class="mb-0" >{{ORDER.detail.order_right_time}}</p>
                    </div>
                </div>
            </div>
            <div  class = "booking mt-2" v-if="(typeof ORDER['booking']!='undefined') && ORDER.booking != null">
                <span>Booked by <b>{{ORDER.booking.name}}</b>, on {{ORDER.booking.CreatedDate}} @ {{ORDER.booking.time}}</span>
            </div>

        <hr v-if="(typeof ORDER['detail']!='undefined')" />
        <!-- <div  v-if="(typeof ORDER['detail']!='undefined')" class="row section4">

            <div class="accordion-container">
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <div class="col-12 accordion-header accordion-button collapsed" id="flush-headingOne"
                                type="button"
                                @click="openAccordionclick()"
                                :class="{ opened: instAcc === true}"
                        >
                            <span class="col-10 customername  body_bold  text-capitalize d-inline-block">
                                {{ORDER.detail.Name.replace(',','').toLowerCase()}}
                                <button type="button" class="btn-link-green body_regular" @click="EditCustomer(ORDER.detail.id)">Edit</button>
                            </span>
                            <div class="col-2">
                                    <tag  v-if="ORDER.detail.TypeDelivery=='DELIVERY'" :name="'B2C'" ></tag>
                                    <tag  v-else :name="'B2B'" ></tag>
                              </div>


                        </div>
                        <div
                            id="flush-collapseOne"
                            class="accordion-collapse collapse"
                            :class="{ show: instAcc === true }"
                        >
                            <div class="accordion-body">

                                    <div  v-if="(typeof ORDER['detail']!='undefined')" class="row section5">
                                            <div class="col">
                                                <AddressFormat :title="'Delivery address'" :address="ORDER.delivery" ></AddressFormat>
                                            </div>
                                            <div class="col">
                                            <AddressFormat :title="'Billing address'"  v-if="ORDER.billing!=null" :address="ORDER.billing" ></AddressFormat>
                                            <AddressFormat :title="'Billing address'" v-else :address="ORDER.delivery" ></AddressFormat>
                                        </div>
                                    </div>

                            <div v-if="(typeof ORDER['detail']!='undefined')" class="row section6">
                                <div class="col" v-if="ORDER.detail.Phone!=''&&ORDER.detail.Phone!=null">
                                    <div class="row" v-for="(phone,index) in ORDER.detail.Phone" :key="index">
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
                           </div>
                        </div>
                    </div>

                </div>
            </div>



            <div class="col">
                <tag  v-if="ORDER.detail.TypeDelivery=='DELIVERY'" :name="'B2C'" ></tag>
                <tag  v-else :name="'B2B'" ></tag>
            </div>
        </div> -->
        <!-- <div  v-if="(typeof ORDER['detail']!='undefined')" class="row section5">
            <div class="col">
                <AddressFormat :title="'Delivery address'" :address="ORDER.delivery" ></AddressFormat>
            </div>
            <div class="col">
                <AddressFormat :title="'Billing address'"  v-if="ORDER.billing!=null" :address="ORDER.billing" ></AddressFormat>
                <AddressFormat :title="'Billing address'" v-else :address="ORDER.delivery" ></AddressFormat>
            </div>
        </div> -->


        <!-- <hr v-if="(typeof ORDER['detail']!='undefined')"/> -->
        <div class="row">
            <div class="col">
                <order-detail-sub-order-items-table @close="show_split_conf=false" @show_conf="show_split_conf=true" :tabledef="itemsfields" :id="'items_table'" :user="ORDER['user']" :status="ORDER['detail'].Status" v-if="(typeof ORDER['detail']!='undefined')">
                </order-detail-sub-order-items-table>
            </div>
        </div>
        <div class="mt-3 mb-5 row" v-if="(typeof ORDER['detail']!='undefined')">
            <div class="col-4 options_btn" v-if="ORDER['items'].length !== 0">
                <button class="btn btn-outline-dark body_medium" @click="openModal(ORDER['detail'].order_id)">Print ticket(s)</button>
            </div>

            <div class="col-4 options_btn" v-if="ORDER['items'].length !== 0">
                <button class="btn btn-outline-danger body_medium" @click="markaslate" v-if="ORDER['detail'].Status!='LATE'">Mark as late</button>
            </div>
             <div class="col-4 options_btn" v-if="ORDER['items'].length === 0">
                <button class="btn btn-outline-dark body_medium" @click="openModal(ORDER['detail'].order_id)">Print ticket(s)</button>
            </div>
             <div class="col-4 options_btn" v-if="ORDER['items'].length === 0 && (ORDER['detail'].Status =='RECURRING' || ORDER['detail'].Status =='SCHEDULED' )">
                <button class="btn btn-outline-danger body_medium" @click="CancelBooking">Cancel booking</button>
            </div>

            <div class="col-4 options_btn">
                <mini-checkout :order_id="parseInt($route.params.order_id)" @reload-order-detail="reloadOrderDetail" v-if="showFulfillBtn"></mini-checkout>
            </div>

            <div class="col-1 options_btn">
                <button @click="openOptions()" class="btn btn-outline-dark body_medium menu"><span>...</span></button>
                <OrderOptions v-if="show_options_btn" :user="ORDER['user']" :order="ORDER['detail']" :items="ORDER['items']"></OrderOptions>
            </div>
            </div>

        <split-confirmation :show_conf="show_split_conf" @close="show_split_conf=false"></split-confirmation>
        <CancelBookingConfirmation v-if= "showModelCancelBooking" :show_modal="showModelCancelBooking" @close="showModelCancelBooking=false" :order = ORDER.detail></CancelBookingConfirmation>
        <qz-print ref="qz_printer"></qz-print>
    </div>
</template>

<script>
    import {ref,computed,nextTick,watch,onBeforeMount} from 'vue';
    import {useRoute,useRouter} from 'vue-router';
    import {useStore} from 'vuex';
    import Tag from  '../miscellaneous/Tag'
    import AddressFormat from '../miscellaneous/AddressFormat';
    import DatePicker from '../miscellaneous/DatePicker'
    import TimeSlotPicker from '../miscellaneous/TimeSlotPicker';
    import OrderDetailSubOrderItemsTable from './OrderDetailSubOrderItemsTable'
    import {formatPrice,hasRoles,formatDate} from "../helpers/helpers";
    import SplitConfirmation from '../miscellaneous/SplitConfirmation';
    import CancelBookingConfirmation from '../miscellaneous/CancelBookingConfirmation';
    import OrderOptions from '../miscellaneous/OrderOptions';
    import QzPrint from "../QzPrint";
    import MiniCheckout from '../miscellaneous/MiniCheckout.vue';
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
        ORDERDETAIL_NEW_PICKUP_DATE,
        ORDERDETAIL_UPDATE, ORDERLIST_REMOVE_ORDERS,
        LOADER_MODULE,
        DISPLAY_LOADER,
        HIDE_LOADER
    } from "../../store/types/types";



    export default {
        name: "OrderDetail",
        components:{Tag,AddressFormat,OrderDetailSubOrderItemsTable,DatePicker,TimeSlotPicker,SplitConfirmation ,OrderOptions , CancelBookingConfirmation , QzPrint,MiniCheckout},
        setup(){
            const route =useRoute();
            const router=useRouter();
            const store =useStore();

            const show_split_conf=ref(false);
            const show_options_btn=ref(false);

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
            const orderId = ref(0);
            const instAcc = ref(false);
            const showModelCancelBooking = ref(false);
            const newdeliverydate = ref('')
            const order =  ref({});
            const showFulfillBtn = ref(true);


            const ORDER=computed(()=>{
                const o= store.getters[`${ORDERDETAIL_MODULE}${ORDERDETAIL_GET_DETAILS}`];

                order.value = o.detail;

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
                    }).finally(()=>{
                        //console.log('order status',order.value.Status);

                        if(typeof(order.value.Status) !='undefined' && ['DELIVERED','FULFILLED'].includes(order.value.Status)){
                            showFulfillBtn.value = false;
                        }
                    });
                });
            }
            const featureunavailable=((feature)=>{
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:feature+' feature not yet implemented.',ttl:5,type:'success'});
            });

            const markaslate=(()=>{
                const order=[ORDER.value.detail.order_id];

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
            const cc_new_pickup_date=ref('');
            const updatedelverydate=ref(false)

            watch(() => cc_new_delivery_date.value, (current_val, previous_val) => {
                console.log(current_val != previous_val)
                if(current_val != previous_val ){
                        updatedelverydate.value = true
                }

                if(ORDER.value.detail.TypeDelivery=="DELIVERY") {
                    if(Object.entries(availableslots.value).length>0) {
                        availabletimeslot.value=availableslots.value[current_val];
                    }

                }


            });

            watch(() => cc_new_pickup_date.value, (current_val, previous_val) => {
                if(ORDER.value.detail.TypeDelivery=="DELIVERY") {
                    if(Object.entries(availableslots.value).length>0) {
                        availabletimeslot.value=availableslots.value[current_val];
                    }

                }


            });

            const suggest_timeslot=ref(0);
            const suggest_timeslot_pickup = ref(0);
            const newtimeslotdelivery = ref(0);
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
                   store.commit(`${ORDERLIST_MODULE}${ORDERLIST_UPDATE_SUGGESTED_DELIVERY_DATE}`,{infoOrder_id:ORDER.value.detail.order_id,suggested_date:suggested_date.value});
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
                if(suggest_timeslot.value==""&&typeof ORDER.value.detail!='undefined'&&ORDER.value.detail.TypeDelivery=='DELIVERY') {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: `Please choose a timeslot.`,
                        ttl: 5,
                        type: 'danger'
                    });
                }
                if(cc_new_delivery_date.value==""||(suggest_timeslot.value==""&&typeof ORDER.value.detail!='undefined'&&ORDER.value.detail.TypeDelivery=='DELIVERY'))
                    return false;
                store.dispatch(`${ORDERDETAIL_MODULE}${ORDERDETAIL_NEW_DELIVERY_DATE}`,{PromisedDate:cc_new_delivery_date.value,timeslot:suggest_timeslot.value}).then((response)=>{

                    if(response.data.updated!==false) {
                        store.commit(`${ORDERDETAIL_MODULE}${ORDERDETAIL_UPDATE}`, {
                            PromisedDate: formatDate(cc_new_delivery_date.value, 'DAY DD/MM'),
                            Status: 'In Process'
                        });
                        store.commit(`${ORDERLIST_MODULE}${ORDERLIST_NEW_DELIVERY_DATE}`, {
                            infoOrder_id: ORDER.value.detail.order_id,
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


            const setNewPickupDate=()=>{

                if(cc_new_pickup_date.value=="") {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: `Please choose a new pickup date.`,
                        ttl: 5,
                        type: 'danger'
                    });
                }
                if(suggest_timeslot_pickup.value==""&&typeof ORDER.value.detail!='undefined'&&ORDER.value.detail.TypeDelivery=='DELIVERY') {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: `Please choose a timeslot.`,
                        ttl: 5,
                        type: 'danger'
                    });
                }
                // if(cc_new_pickup_date.value > cc_new_delivery_date.value ) {
                //     store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                //         message: `Please a new delivery date.`,
                //         ttl: 5,
                //         type: 'danger'
                //     });
                //     //showDeliverySlots();
                // }

                if(cc_new_pickup_date.value==""||(suggest_timeslot_pickup.value==""&&typeof ORDER.value.detail!='undefined'&&ORDER.value.detail.TypeDelivery=='DELIVERY'))
                    return false;

                    if(cc_new_delivery_date.value != ""){

                             newdeliverydate.value = cc_new_delivery_date
                    }else {
                             newdeliverydate.value = ORDER.value.detail.DateDeliveryAsk
                    }

                     if(suggest_timeslot.value != ""){

                             newtimeslotdelivery.value = suggest_timeslot.value
                    }else {
                             newtimeslotdelivery.value = ORDER.value.detail.TimeDelivery
                    }


                store.dispatch(`${ORDERDETAIL_MODULE}${ORDERDETAIL_NEW_PICKUP_DATE}`,{PickupDate:cc_new_pickup_date.value, deliveryDate:newdeliverydate.value, timeslotPickup:suggest_timeslot_pickup.value ,timeslotDelivery:suggest_timeslot.value }).then((response)=>{
                    console.log("respppppp" , response)

                    // if(response.data.updated!==false) {
                    //     store.commit(`${ORDERDETAIL_MODULE}${ORDERDETAIL_UPDATE}`, {
                    //         PromisedDate: formatDate(cc_new_pickup_date.value, 'DAY DD/MM'),
                    //         Status: 'In Process'
                    //     });
                    //     store.commit(`${ORDERLIST_MODULE}${ORDERLIST_NEW_DELIVERY_DATE}`, {
                    //         infoOrder_id: ORDER.value.detail.order_id,
                    //         PromisedDate: formatDate(cc_new_pickup_date.value)
                    //     });
                    //    /* store.commit(`${ORDERLIST_MODULE}${ORDERLIST_UPDATE_STATUS}`, {
                    //         orderids: [ORDER.value.detail.id],
                    //         status: "IN PROCESS"
                    //     });*/
                    //     store.commit(`${ORDERLIST_MODULE}${ORDERLIST_REMOVE_ORDERS}`,  [ORDER.value.detail.id]);
                    //     showslots.value = false;
                    //     showNewDeliveryDateMsg.value = true;
                    // }else{
                    //     store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`Error: Unable to update. ${response.data.message}`,ttl:5,type:'danger'});
                    // }
                }).catch((error)=>{
                    if(typeof error.response!="undefined")
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'});
                });
            }

            const showslots=ref(false);
            const showslotspickup=ref(false);
            const showNewDeliveryDateMsg=ref(false);
            const chooseSlot=()=>{
                showslots.value=true;
            }

            function EditCustomer(customerId){

                  router.push({
                        name:'ViewCustomer',
                        params: {
                            customer_id:customerId,
                        },
                    });
            }

            function EditOrder(order_id){
                orderId.value = order_id
                router.push('/checkout/'+orderId.value);
            }
             function openAccordionclick(i) {

                instAcc.value = !instAcc.value;

            }
            function openOptions(){
               show_options_btn.value = !show_options_btn.value
            }

            const setDeliveryDate = ref(false);

            function showDeliverySlots(){
                showslots.value = true;
                setDeliveryDate.value = true;
            }
            function showPickupSlots(){
                showslotspickup.value = true;
                setDeliveryDate.value = true;
            }
            function CancelBooking(){
                showModelCancelBooking.value = true
            }
            function formatOrderDate(date_txt){
                   let weekdays = [];
                weekdays[0] = 'Sunday';
                weekdays[1] = 'Monday';
                weekdays[2] = 'Tuesday';
                weekdays[3] = 'Wednesday';
                weekdays[4] = 'Thursday';
                weekdays[5] = 'Friday';
                weekdays[6] = 'Satuday';

                let dt = new Date(date_txt);

                let day_num =  dt.getDay();
                let dt_dd = String(dt.getDate()). padStart(2, '0');
                let dt_mm = dt.toLocaleString('default', { month: 'long' });;
                let dt_yy = String(dt.getFullYear() + 1). padStart(2, '0');

                return weekdays[day_num]+" "+dt_dd+" "+dt_mm+" "+dt_yy;
            }

             // handler when you unlink sub account from linked accounts
             const removeLinkedAccount = (id)=>{

               axios.post('/unlink-Account', {
                        customer_id: id,
                    }).then((res)=>{
                        if(res.data.message == "OK"){
                            location.reload();
                        } else {
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, { message: res.data.error , ttl:5, type:'danger' });
                        }
                    }).catch((errors)=>{
                        console.log(errors)
                    });

            }


            const reloadOrderDetail = ()=>{
                console.log('reload called from mini checkout');

                store.commit(`${ORDERDETAIL_MODULE}${ORDERDETAIL_SET_LOADER}`,'');
                nextTick(() => {
                    store.dispatch(`${ORDERDETAIL_MODULE}${ORDERDETAIL_LOAD_DETAIL}`, CURRENT_SELECTED.value).catch((error)=>{
                        if(typeof error.response!="undefined")
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'});
                    }).finally(()=>{
                        if(typeof(order.value.Status) !='undefined' &&  ['DELIVERED','FULFILLED'].includes(order.value.Status)){
                            showFulfillBtn.value = false;
                        }
                    })
                });

            }

            return {
                showorderdetail,
                loaderclass:computed(()=>{
                    return store.getters[`${ORDERDETAIL_MODULE}${ORDERDETAIL_GET_LOADER}`];
                }),
                close:(()=>{
                    store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_SELECT_CURRENT}`,'');
                    store.commit(`${ORDERDETAIL_MODULE}${ORDERDETAIL_SET_DETAILS}`,{});
                    //router.back();
                    router.push({
                        name:'LandingPage',
                        params: {
                        customerId:route.params.customerId,
                        value:route.params.value,
                       },
                    });
                }),
                ORDER,
                featureunavailable,
                itemsfields,
                markaslate,
                suggested_date,
                cc_new_delivery_date: cc_new_delivery_date,
                cc_new_pickup_date:cc_new_pickup_date,
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
                show_split_conf,
                EditCustomer,
                EditOrder,
                openAccordionclick,
                instAcc,
                show_options_btn,
                openOptions,
                setDeliveryDate,
                showDeliverySlots,
                showModelCancelBooking,
                CancelBooking,
                showPickupSlots,
                showslotspickup,
                setNewPickupDate,
                newdeliverydate,
                suggest_timeslot_pickup,
                newtimeslotdelivery,
                updatedelverydate,
                formatOrderDate,
                removeLinkedAccount,
                reloadOrderDetail,
                order,
                showFulfillBtn,
            }
        },
        methods:{
        openModal(order_id){
            this.$refs.qz_printer.loadPrinterOrderModal(order_id)
        }
    }
    }
</script>

<style scoped>
.accordion-button {
    background: #ffffff;
    box-sizing: border-box;
    border-radius: 6px;
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 19.6px;
    color: #000000;
    height: 40px;
}
.accordion-item {
    /* margin: 10px; */
    border-radius: 6px;
    border: none;
}
.accordion-flush .accordion-item .accordion-button {
    background-color: #F8F8F8;
    box-shadow: inset 0px -1px 0px rgb(168 168 168 / 25%);
    margin-bottom: 15px;
    height: 44px;
    border-radius: 10px;
    }
.accordion-body {
    background: #f8f8f8;
}
.options_btn{
    position: relative;
    width: auto;
}
.menu{
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
}
.menu span{
    font-size: 26px;
    line-height: 26px;
    height: 26px;
    display: inline-block;
    justify-content: center;
    align-items: center;
    margin-top: -9px;
}
.align-right{
        text-align: right;
    }
    .section1{
        margin-top:30px;
    }
    .section2,.section3{
        color:#47454B;
        font-weight: 400;
        height: auto;
        margin: 0;
        padding-top: 77px;
        padding-bottom: 15px;
    }
    .section3{
        margin-top:5px;
     }
    .section4{
        margin-bottom: 8px;
        /* margin-left: -2px;
        margin-right: -2px; */
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
    .booking{
        font-size : 12px;
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
    .detail-header{
        padding: 20px 30px 15px 30px;
        height: 99px;
        background: #47454B;
        width: 684px;
        z-index: 2;
        margin: 0 -1.25rem;
    }
    .detail-title{
        font-size: 20px;
        line-height: 110%;
        color: #FFFFFF;
        font-family: "Gotham Rounded";
        font-style: normal;
        font-weight: 400;
        display:flex;
    }
     .order-brief-info-section{
            padding: 15px 30px;
            background: #FFFFFF;
            box-shadow: 0px 0px 6px rgba(153, 153, 153, 0.25);
            border-radius: 5px;

     }
      .order-brief-info-section p{
                font-family: 'Gotham Rounded Book';
                font-size: 12px;
                line-height: 14px;}

      .order-brief-info-section p span{
                    font-size: 8px;
                    line-height: 10px;
                    color: #42A71E;
                }
            .order-sub-title{
                font-family: 'Gotham Rounded' !important;
                font-size: 12px;
                line-height: 14px;
                color: #000000;
                font-weight: 400;
            }
            .border-left{
                border-left: 1px solid #E0E0E0;
            }

             .cust-type-icon{
                padding: 2px 18px;
                font-size: 12px;
                line-height: 140%;
                width: 60px;
                height: 20px;
                background: rgba(212, 221, 247, 0.7);
                color: #47454B;
                margin-right: 10px;

            }
            .cust-account-icon{
                padding: 2px 18px;
                font-size: 12px;
                line-height: 140%;
                width: auto;
                height: 20px;
                background: linear-gradient(0deg, rgba(251, 248, 185, 0.5), rgba(251, 248, 185, 0.5)), rgba(251, 248, 185, 0.5);
                color: #47454B;
                margin-right: 10px;
            }
            .cust-location-name{
                font-size: 16px;
                line-height: 140%;
                color: #F8F8F8;
                font-family: "Gotham Rounded";
                font-style: normal;
                font-weight: 400;
                transform: translateY(8px);

            }
            .detail-close{
                    transform: translate3d(20px, -10px, 0px);
            }
            .text-center{
                display: flex;
                margin-top: 10px
            }
            .sectionPrice{
                    display: flex;
                    border-left: 4px solid green;
                    height: 60px;
                    margin-top: 30px;
                    align-items: center;
                    background: #ECECEC;
            }
            .text-type{
                color:#FFFFFF;
                text-overflow: ellipsis;
                max-width: 400px;
                overflow: hidden;
                white-space: nowrap;
                padding-left: 3px;
            }
            .total div:first-child{
                    font-size: 20px;
                    line-height: 140%;
                    color: #47454B;
                }
                .total div:last-child{
                    font-size: 16px;
                    line-height: 140%;
                    color: #47454B;
                }
                .PriceTotal{
                    font-family: 'Gotham Rounded';
                    font-style: normal;
                    font-weight: 400;
                    font-size: 20px;
                    line-height: 140%;
                }
                .name_customer{
                    white-space: nowrap;
                    overflow: hidden;
                    max-width: 400px;
                    text-overflow: ellipsis;
                }
                .cursor-pointer{
                    font-size: 8px;
                    line-height: 10px;
                    color: #42A71E;
                    text-decoration: underline;
                    font-family: 'Gotham Rounded';
                }
                .unlink:hover{
                    fill: #42A71E;
                }
</style>
