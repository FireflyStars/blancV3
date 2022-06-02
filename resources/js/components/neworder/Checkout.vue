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
                                            <div class="col item-col">{{firstCap(item.typeitem)}}</div>
                                            <div class="col brand-col">{{item.brand}}</div>
                                            <div class="col barcode-col">{{item.ItemTrackingKey}}</div>
                                            <div class="col services-col">
                                                <span v-for="service in item.services" class="each-item-service d-table px-2">{{service}}</span>
                                            </div>
                                            <div class="col price-col">{{item.priceTotal}}</div>
                                            <div class="col expand-col">
                                                <img src="/images/picto_arrow.svg" class="each-item-chevron"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 py-3 each-row-detail d-none" :id="'row_detail_'+id">
                                        <div class="row justify-content-center">
                                            <div class="col-10">
                                                <div class="item-desc-heading mb-3">
                                                    <span>Description</span>
                                                    <a href="javascript:void(0)">Edit</a>
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
                                                            </div>
                                                        </div>
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
                                            <div class="accordion-item mx-4 mb-4">
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
                                                            Customer details goes here
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Accordion for Order details -->
                                            <div class="accordion-item mx-4 mb-4">
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
                                                            Order details goes here
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Accordion for Vouchers -->
                                            <div class="accordion-item mx-4 mb-4">
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

        order_id.value = route.params.order_id;

        const paths = ref([
            { name: "Order", route: "LandingPage" },
            { name: "New order", route: "" },
            { name: "Checkout", route: "" },
        ]);


        function getCheckoutItems(){
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                true,
                "Loadind details....",
            ]);

            axios.post('/get-checkout-items',{
                order_id:order_id.value,
            }).then((res)=>{
                order_items.value = res.data.items;
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

        return {
            order_id,
            paths,
            order_items,
            openAccordionclick,
            toggleRowDetail,
            firstCap,
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
.accordion-body{
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
    max-width: 15%;
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

.item-desc-heading a{
    font:normal 16px "Gotham Rounded";
    color:#42A71E;
    text-decoration: underline;
}

.color-span{
    display: inline-block;
    width: 15px !important;
    height: 15px;
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

</style>
