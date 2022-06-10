<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <div class="container-fluid h-100 bg-color">
            <!--<main-header></main-header>-->
            <div
                class="row d-flex align-content-stretch align-items-stretch flex-row hmax"
                style="z-index: 100"
            >
                <side-bar></side-bar>
                <div class="col main">

                    <bread-crumb :paths="paths"></bread-crumb>

                    <div class="content">

                        <div class="row">

                            <div class="col-8 px-4">
                                <span class="order-no my-3 d-block">New order n°{{ order_id }}</span>
                                <h1 class="title">Order Summary</h1>

                                <div class="col-12 py-2" id="items-table-header">
                                <div class="row mx-0 py-2" >
                                    <div class="col color-col">Color</div>
                                    <div class="col item-col">Item</div>
                                    <div class="col brand-col">Brand</div>
                                    <div class="col barcode-col">Barcode</div>
                                    <div class="col services-col">Services</div>
                                    <div class="col price-col">Price</div>
                                    <div class="col expand-col"></div>
                                </div>

                                <div class="row mx-0 each-row-item" v-for="(item,id) in order_items">
                                    <div class="col-12 each-item-row py-3" :id="'item_row'+id" @click="toggleRowDetail(id)">
                                        <div class="row">
                                            <div class="col color-col"><span v-if="item.first_color!=''" :style="'background:'+item.first_color" class="color-span"></span></div>
                                            <div class="col item-col text-gr">{{firstCap(item.typeitem)}}</div>
                                            <div class="col brand-col text-gr">{{item.brand}}</div>
                                            <div class="col barcode-col">{{item.ItemTrackingKey}}</div>
                                            <div class="col services-col">
                                                <span v-for="service in item.services" class="each-item-service d-table px-2">{{service}}</span>
                                            </div>
                                            <div class="col price-col text-gr"><span>&#163;{{item.priceTotal}}</span></div>
                                            <div class="col expand-col">
                                                <img src="/images/picto_arrow.svg" class="each-item-chevron"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 py-3 each-row-detail d-none mb-3" :id="'row_detail_'+id">
                                        <div class="row justify-content-center pb-4 mb-4 each-desc-block mx-4">
                                            <div class="col-10">
                                                <div class="item-desc-heading mb-3">
                                                    <span>Description</span>
                                                    <a href="javascript:void(0)" @click="goToDetailing(item.order_id,item.detailingitem_id,2)">Edit</a>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-4">
                                                            <div class="item-sub-heading">Item type</div>
                                                            <span class="item-desc-text">{{firstCap(item.typeitem)}}</span>
                                                        </div>
                                                        <div class="mb-4">
                                                            <div class="item-sub-heading">Size</div>
                                                            <span class="item-desc-text">{{item.Size}}</span>
                                                        </div>
                                                        <div class="mb-4">
                                                            <div class="item-sub-heading">Colour & pattern</div>
                                                            <span class="item-desc-text" v-if="item.all_colors.length > 0">
                                                                <span v-for="color in item.all_colors" class="color-span" :style="'background:'+color"></span>
                                                            </span>
                                                            <span class="item-desc-text" v-if="item.Patterns && item.Patterns !='None'">
                                                                {{item.Patterns}}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                         <div class="mb-4">
                                                            <div class="item-sub-heading">Brand</div>
                                                            <div class="row mx-0 each-desc-row py-1 mt-1">
                                                                <div class="col-10 item-desc-text">
                                                                    {{item.brand}}
                                                                </div>
                                                                <div class="col-2 brand-coef-perc" v-if="item.brand_coef_perc > 0">+{{item.brand_coef_perc}}%</div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-4">
                                                            <div class="item-sub-heading">Item fabric</div>
                                                            <div class="row mx-0 each-desc-row py-1 mt-1">
                                                                <div class="col-10 item-desc-text">
                                                                    {{item.Fabrics}}
                                                                </div>
                                                                <div class="col-2">
                                                                    <!-- Fabric COEF to confirm -->
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-4">
                                                            <div class="item-sub-heading">Condition</div>
                                                            <span class="item-desc-text">{{firstCap(item.generalState)}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 mt-4" v-if="Object.keys(item.complexities_arr).length > 0">
                                                        <div class="item-sub-heading">Complexities</div>
                                                        <div class="row each-desc-row py-1 my-1" v-for="(price,comp) in item.complexities_arr">
                                                            <div class="col-10 item-desc-text">{{comp}}</div>
                                                            <div class="col-2 text-align-right item-desc-text">&#163;{{price}}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row justify-content-center pb-4 mb-4 each-desc-block mx-4">
                                            <div class="col-10">
                                                <div class="item-desc-heading mb-3">
                                                    <span>Issues</span>
                                                    <a href="javascript:void(0)" @click="goToDetailing(item.order_id,item.detailingitem_id,10)">Edit</a>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6" v-if="item.stains && item.stains.length > 0">
                                                        <div class="mb-4">
                                                            <div class="item-sub-heading">Stains</div>
                                                        </div>
                                                        <div class="row justify-content-center mb-1" v-for="(stain,index) in item.stains">
                                                            <div class="col-1">
                                                                <span class="each-issue-index stain-issue-index">{{index+1}}</span></div>
                                                            <div class="col-11">
                                                                <span v-if="stain.id_issue > 0 && issues[stain.id_issue]">
                                                                    {{issues[stain.id_issue].stain}}&nbsp;
                                                                </span>
                                                                <span v-else>Stain </span>
                                                                <span v-if="zones[stain.id_zone]">{{zones[stain.id_zone]}}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-6" v-if="item.damages && item.damages.length > 0">
                                                        <div class="mb-4">
                                                            <div class="item-sub-heading">Damages</div>
                                                        </div>
                                                        <div class="row justify-content-center mb-1" v-for="(damage,index) in item.damages">
                                                            <div class="col-1">
                                                                <span class="each-issue-index damage-issue-index">{{index+1}}</span>
                                                            </div>
                                                            <div class="col-11">
                                                                <span v-if="damage.id_issue > 0 && issues[damage.id_issue]">
                                                                    {{issues[damage.id_issue].damage}}&nbsp;
                                                                </span>
                                                                <span v-if="zones[damage.id_zone]">{{zones[damage.id_zone]}}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row justify-content-center pb-4 mb-4 each-desc-block mx-4" id="services_block">
                                            <div class="col-10">
                                                <div class="item-desc-heading mb-3">
                                                    <span>Services</span>
                                                    <a href="javascript:void(0)" @click="goToDetailing(item.order_id,item.detailingitem_id,11)">Edit</a>
                                                </div>
                                                <div class="row each-desc-row mb-2 each-detailed-service-row py-1" v-for="ds in item.detailed_services">
                                                    <div class="col-10">
                                                        {{ds.name}}
                                                    </div>
                                                    <div class="col-2 text-align-right">
                                                        &#163;{{ds.price}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>


                            </div>
                            <div class="col-4 checkout-sidebar pt-4">
                                <transition enter-active-class="animate__animated animate__fadeIn">
                                    <div class="accordion-container">
                                        <div class="accordion accordion-flush" id="accordionFlushExample">
                                            <!-- Accordion for Customer details -->
                                            <div class="accordion-item mx-2 mb-4">
                                                <h2 class="accordion-header" id="flush-headingOne">
                                                    <button
                                                        class="accordion-button collapsed gilroy-extra-bold"
                                                        type="button"
                                                        @click="openAccordionclick('custdetails')" id="acdbtn_custdetails"
                                                    >Customer details</button>
                                                </h2>
                                                <div class="accordion-collapse collapse" id="acdpanel_custdetails">
                                                    <div class="accordion-body d-table w-100 px-0 py-0">
                                                        <div class="accordion-content p-4 mt-3">
                                                            <div class="text-white">
                                                                <div class="row mb-3" v-if="cust && cust.id">
                                                                    <div class="col-7">
                                                                        <span id="cust_name">{{cust.Name}}</span>
                                                                        <a href="javascript:void(0)" id="customer_link" @click="redirectToCustDetail(cust.id)">Edit</a>
                                                                    </div>
                                                                    <div class="col-5">
                                                                        <span class="cust-tag py-1 px-1 mb-1" v-if="cust.cust_type!=''">{{cust.cust_type}}</span>
                                                                        <!--
                                                                        <span class="cust-tag py-1 px-2 mb-1">Frequent Flyer</span>
                                                                        -->
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-7">
                                                                        <div class="item-sub-heading">Email</div>
                                                                            <a :href="'mailto:'+cust.EmailAddress" id="cust_email" v-if="cust.EmailAddress">{{cust.EmailAddress.toLowerCase()}}</a>
                                                                    </div>
                                                                     <div class="col-5">
                                                                        <div class="item-sub-heading">Phone number</div>
                                                                        <div class="row" v-if="cust.phone_num && cust.phone_num.length > 0">
                                                                            <div class="col-12" v-for="phone in cust.phone_num">
                                                                                {{phone}}
                                                                            </div>
                                                                        </div>
                                                                     </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Accordion for Order details -->
                                            <div class="accordion-item mx-2 mb-4">
                                                <h2 class="accordion-header" id="flush-headingOne">
                                                    <button
                                                        class="accordion-button collapsed gilroy-extra-bold"
                                                        type="button"
                                                        @click="openAccordionclick('orderdetails')" id="acdbtn_orderdetails"
                                                    >Order details</button>
                                                </h2>
                                                <div class="accordion-collapse collapse" id="acdpanel_orderdetails">
                                                    <div class="accordion-body d-table w-100 px-0 py-0">
                                                        <div class="accordion-content p-4 mt-3">
                                                            <div class="row align-items-center mb-3" v-if="order">
                                                                <div class="col-6">
                                                                    <span class="sidebar_title text-white">Order details</span>
                                                                    <a href="javascript:void(0)" id="order_link">Edit</a>
                                                                </div>
                                                                <div class="col-6 text-align-right">
                                                                        <img src="/images/picto_store.svg" class="delivery-method-icon store_collecion_icon"  v-if="order.deliverymethod=='in_store_collection'">

                                                                        <img src="/images/truck_white.svg" class="delivery-method-icon"  v-if="['home_delivery','delivery_only','recurring'].includes(order.deliverymethod)">

                                                                        <span class="text-white delivery-method-text">
                                                                            <span v-if="order.deliverymethod=='in_store_collection'">
                                                                               In Store Collection
                                                                            </span>
                                                                            <span v-if="order.deliverymethod=='home_delivery'">
                                                                                Home Delivery
                                                                            </span>
                                                                            <span v-if="order.deliverymethod=='delivery_only'">
                                                                                Delivery only
                                                                            </span>
                                                                            <span v-if="order.deliverymethod=='recurring'">
                                                                                Recurring
                                                                            </span>
                                                                        </span>
                                                                </div>
                                                            </div>

                                                            <div class="col-12" >
                                                                <div class="row mb-3 mx-0 align-items-center each-booking-log-row py-1">
                                                                    <div class="col-2 pl-0">
                                                                        <span class="sidebar_title text-white">Slot</span>
                                                                    </div>
                                                                    <div class="col-10 text-white text-align-right pr-0" id="booking_log">
                                                                        <!--
                                                                        <pre>{{booking}}</pre>
                                                                        -->
                                                                        Booked by @{{booking.user}} on
                                                                        <span v-if="order.deliverymethod=='in_store_collection'">
                                                                             {{booking.dropoff_date}} @{{booking.dropoff_time}}
                                                                        </span>
                                                                        <span v-if="['home_delivery','delivery_only','recurring'].includes(order.deliverymethod)">
                                                                            {{booking.creation_date}} @ {{booking.creation_time}}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="row text-white" v-if="order.deliverymethod=='recurring'">
                                                                    <div class="col-6 pr-0 mb-3" v-for="slot in booking.recurring">
                                                                        <div class="row mx-0 align-items-center">
                                                                            <div class="col-2 pl-0">
                                                                                <img src="/images/calendar_white.svg" class="each-booking-calendar-icon"/>
                                                                            </div>
                                                                            <div class="col-10 pl-0">
                                                                                {{slot.day}}<br/>{{slot.tranche}}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row text-white" v-else>
                                                                    <div class="col-6 pr-0" v-if="order.deliverymethod!='delivery_only'">
                                                                        <div class="item-sub-heading">
                                                                            <span v-if="order.deliverymethod=='in_store_collection'">
                                                                                Dropoff time
                                                                            </span>
                                                                            <span v-else-if="order.deliverymethod=='home_delivery'">
                                                                                Pickup time
                                                                            </span>
                                                                        </div>
                                                                        <div class="row mx-0 align-items-center">
                                                                            <div class="col-2 pl-0">
                                                                                <img src="/images/calendar_white.svg" class="each-booking-calendar-icon"/>
                                                                            </div>
                                                                            <div class="col-10 pl-0">
                                                                                <span v-if="order.deliverymethod=='in_store_collection'">
                                                                                    {{booking.dropoff_day}} {{booking.dropoff_date}}<br/>{{booking.dropoff_time}}
                                                                                </span>
                                                                                <span v-else-if="order.deliverymethod=='home_delivery'">
                                                                                    {{booking.pickup_day}} {{booking.pickup_date}}<br/>{{booking.pickup_time}}
                                                                                </span>
                                                                                </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6 pr-0">
                                                                        <div class="item-sub-heading">
                                                                            <span v-if="order.deliverymethod=='in_store_collection'">
                                                                            Pickup time
                                                                            </span>
                                                                            <span v-else-if="['home_delivery','delivery_only'].includes(order.deliverymethod)">
                                                                            Delivery time
                                                                            </span>
                                                                        </div>
                                                                         <div class="row mx-0 align-items-center">
                                                                            <div class="col-2 pl-0">
                                                                                <img src="/images/calendar_white.svg" class="each-booking-calendar-icon"/>
                                                                            </div>
                                                                            <div class="col-10 pl-0">
                                                                                <span v-if="order.deliverymethod=='in_store_collection'">
                                                                                {{booking.pickup_day}} {{booking.pickup_date}}<br/>{{booking.pickup_time}}
                                                                                </span>
                                                                                <span v-else-if="['home_delivery','delivery_only'].includes(order.deliverymethod)">
                                                                                    {{booking.delivery_day}} {{booking.delivery_date}}<br/>{{booking.delivery_time}}
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 mt-4" v-if="order.deliverymethod !='in_store_collection'">
                                                                <div class="row mb-3 mx-0 align-items-center each-booking-log-row py-1">
                                                                    <div class="col-12 sidebar_title text-white px-0">Details</div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-5">
                                                                        <div class="item-sub-heading">Delivery address</div>
                                                                        <div class="row text-white addr-bloc">
                                                                            <div class="col-12" v-if="address">
                                                                                <span v-if="address.address1">{{address.address1}}</span>
                                                                                <span v-if="address.address2"><br/>{{address.address2}}</span><br/>{{address.postcode}}, {{address.Town}}
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                    <div class="col-7">
                                                                        <div class="item-sub-heading">Delivery instructions</div>
                                                                        <div class="row mx-0 text-white mb-3">
                                                                            <div class="col-12 py-2 mt-1" id="delivery_instruction">{{cust.commentDelivery}}</div>
                                                                        </div>
                                                                        <!--
                                                                        <div class="item-sub-heading">Alternate Delivery Contact</div>
                                                                        -->
                                                                    </div>
                                                                </div>
                                                            </div>




                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Accordion for Vouchers -->
                                            <div class="accordion-item mx-2 mb-4">
                                                <h2 class="accordion-header" id="flush-headingOne">
                                                    <button
                                                        class="accordion-button collapsed gilroy-extra-bold"
                                                        type="button"
                                                        @click="openAccordionclick('vouchers')" id="acdbtn_vouchers"
                                                    >Vouchers & Discounts</button>
                                                </h2>
                                                <div class="accordion-collapse collapse" id="acdpanel_vouchers">
                                                    <div class="accordion-body d-table w-100 px-0 py-0">
                                                        <div class="accordion-content p-4 mt-3">
                                                            Voucher details goes here
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </transition>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
<script>

import MainHeader from "../layout/MainHeader";
import BreadCrumb from "../layout/BreadCrumb";
import SideBar from "../layout/SideBar";
import { ref, onMounted, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useStore } from "vuex";
import {
    LOADER_MODULE,
    DISPLAY_LOADER,
    HIDE_LOADER
} from '../../store/types/types';

export default {
    name: "Checkout",
    components: { BreadCrumb, SideBar, MainHeader},
    setup() {
        const router = useRouter();
        const route = useRoute();
        const store = useStore();

        const order_id = ref(0);
        const order_items = ref([]);
        const zones = ref([]);
        const issues = ref([]);
        const cust = ref({});
        const order = ref({});
        const booking = ref([]);
        const address = ref({});

        order_id.value = route.params.order_id;

        const paths = ref([
            { name: "Order", route: "LandingPage" },
            { name: "New order", route: "NewOrder" },
            { name: "Checkout", route: "" },
        ]);


        function getCheckoutItems(){
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                true,
                "Loading details....",
            ]);

            axios.post('/get-checkout-items',{
                order_id:order_id.value,
            }).then((res)=>{
                order_items.value = res.data.items;
                zones.value = res.data.zones;
                issues.value = res.data.issues;
                cust.value = res.data.cust;
                order.value = res.data.order;
                booking.value = res.data.booking_details;
                address.value = res.data.address;
            }).catch((err)=>{

            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });
        }
        getCheckoutItems();

        function openAccordionclick(id) {
            let acdbtn = document.getElementById('acdbtn_'+id);

            let acdpanel = document.getElementById('acdpanel_'+id);
            acdbtn.classList.toggle('opened');
            acdpanel.classList.toggle('show');
        }

        function toggleRowDetail(id){


            let el = document.getElementById('item_row'+id);
            el.classList.toggle('opened');

            let detail_el = document.getElementById('row_detail_'+id);
            detail_el.classList.toggle('d-none');
        }

        function firstCap(string){
            return string[0].toUpperCase() + string.substring(1);
        }

        function goToDetailing(order_id,detailingitem_id,etape){
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                true,
                "Loading details....",
            ]);

            axios.post('/change-detailing-etape',{
                detailingitem_id:detailingitem_id,
                etape:etape,
            }).then((res)=>{
                //if(res.data.detailingitem && res.data.updated){
                    router.push('/detailing_item/' + order_id + '/' + detailingitem_id);
                //}
            }).catch((err)=>{

            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });
        }

        function redirectToCustDetail(id){
            router.push('/customer-detail/'+id);
        }

        return {
            order_id,
            paths,
            order_items,
            openAccordionclick,
            toggleRowDetail,
            firstCap,
            zones,
            issues,
            goToDetailing,
            cust,
            redirectToCustDetail,
            order,
            booking,
            address,
        }

    },
}
</script>
<style scoped>

    .order-no {
    font-family: Gilroy;
    font-style: normal;
    font-weight: 800;
    font-size: 20px;
    line-height: 110%;
    display: flex;
    align-items: center;
    color: #868686;
    padding: 10px;
}
.title {
    font-family: Gilroy;
    font-style: normal;
    font-weight: 800;
    font-size: 36px;
    line-height: 106%;
    color: #000000;
    padding: 10px;
}

.checkout-sidebar,
.accordion-collapse,
.accordion-body,
.accordion-header{
    background:#47454B;
}

.checkout-sidebar{
    min-height: 70vh;
}

.accordion-item{
    border-radius: 5px;
}

.accordion-container {
    font-family: Gotham Rounded;
}
.opened:after {
    /* background-image: url("../../../../resources/img/accordion_arrow.png");
    background-repeat: no-repeat !important; */
    transform: rotate(180deg);
}
.accordion-body-title {
    display: flex;
    align-items: flex-end;
    line-height: 140%;
    color: #868686;
    font-family: "Gilroy";
}

.accordion-content{
    border:thin solid #fff;
    border-radius:3px;
}

.accordion-button{
    background:#e0e0e0 !important;
    border:thin solid #eee !important;
    border-radius:3px !important;
    padding:0.5rem !important;
}

.each-item-row{
    background:#fff;
}

.color-col{
    max-width: 7%;
}

.item-col{
    max-width:15%;
}

.brand-col{
    max-width:15%;
}

.barcode-col{
    max-width: 13%;
}

.price-col{
    max-width: 10%;
}

.expand-col{
    max-width:5%;
}

.each-item-service{
    border: 1px solid #333;
    float:left;
    border-radius: 10px;
    margin-right: 0.5rem;
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
}

.each-item-row{
    border:thin solid #e0e0e0;
}

.each-item-row:hover{
    background: #e0e0e0;
    cursor:pointer;
}

.each-row-detail{
    background: #fff;
    border:thin solid #e0e0e0;
    border-top:none;
}

.each-item-row img,
.each-row-detail{
    transition: transform 0.2s ease-in-out;
}

.each-item-row.opened img{
   transform: rotate(180deg);
}

.item-desc-heading span{
    font:bold 22px "Gilroy";
    margin-right:10px;
}

.item-desc-heading a,
#customer_link,
#order_link{
    font:normal 16px "Gotham Rounded";
    color:#42A71E;
    text-decoration: underline;
}

.color-span{
    display: inline-block;
    width: 20px !important;
    height: 20px;
    border-radius: 50%;
    margin: 0 2px;
    border: 1px solid #f8f8f8;
    box-sizing: border-box;
    filter: drop-shadow(0px 0px 4px rgba(80, 80, 80, 0.2));
}

.item-sub-heading,
.item-desc-text{
    font:normal 16px "Gotham Rounded";
}

.item-desc-text{
    display: block;
}

.item-sub-heading{
    color:#868686;
}

.item-desc-text .color-span{
    margin:5px 10px 5px 0 !important;
}

.each-desc-row{
    background:#f8f8f8;
    border-radius:10px;
}

.brand-coef-perc{
    font:normal 16px "Gotham Rounded";
    color:#B69968;
}

.each-desc-block:not(#services_block){
    border-bottom:thin solid #e0e0e0;
}

.each-detailed-service-row
{
 font:normal 16px "Gotham Rounded";
}


.delivery-method-text,
#booking_log{
    font:normal 16px "Gotham Rounded";
}

.each-issue-index{
    display: flex;
    width: 20px;
    height: 20px;
    line-height: 1em;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    font:normal 14px "Gotham Rounded";
    color:#fff;
}

.stain-issue-index{
    background: #EF8F00;
}

.damage-issue-index{
    background: #EB5757;
}

.text-gr{
    font-family: "Gotham Rounded";
}

#cust_name,
.sidebar_title{
    font:bold 22px "Gilroy";
    margin-right:10px;
}

.cust-tag{
    border:thin solid #fff;
    font:normal 12px "Gotham Rounded";
    border-radius:3px;
    display:table;
    float:left;
    margin-right:0.5rem;
}

#cust_email{
    font:normal 16px "Gotham Rounded";
    color:#fff;
}

.delivery-method-icon{
    height:20px;
}

.store_collection_icon{
    margin-right:10px;
}

.each-booking-log-row{
    border-bottom:thin solid #fff;
}

.each-booking-calendar-icon{
    margin-left:-5px;
}

#delivery_instruction{
    border:thin solid #fff;
    border-radius:3px;
    min-height: 4em;
}

</style>
