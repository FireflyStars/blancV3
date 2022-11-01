<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <div class="container-fluid bg-color">
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
                                        <div class="row align-items-center">
                                            <div class="col color-col"><span v-if="item.first_color!=''" :style="'background:'+item.first_color" class="color-span"></span></div>
                                            <div class="col item-col text-gr">{{firstCap(item.typeitem)}}</div>
                                            <div class="col brand-col text-gr">{{item.brand}}</div>
                                            <div class="col barcode-col">{{item.tracking}}</div>
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
                                                            <span class="item-desc-text">{{item.size}}</span>
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
                                                            <div class="row mx-0 each-desc-row py-1 mt-1" v-for="(coef,fabric) in item.fabrics_arr">
                                                                <div class="col-10 item-desc-text">
                                                                    {{fabric}}
                                                                </div>
                                                                <div class="col-2 fabric-coef" v-if="coef > 0">
                                                                    {{coef}}%
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
                                                            <div class="col-2 text-align-right item-desc-text">&#163;{{price.toFixed(2)}}</div>
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
                                                <div class="row" v-if="(item.stains && item.stains.length > 0) || (item.damages && item.damages.length > 0)">
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
                                                <div class="row">
                                                    <div class="col-12 item-desc-text">No issues</div>
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
                                                        <span v-if="ds.price!='Price now' && ds.price!='Quote'">&#163;</span>{{ds.price}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Summary -->
                                <div class="row justify-content-end mt-3 mx-0">
                                    <div class="col-8 py-3" id="summary_div">
                                        <span class="summary-title">Order Total</span><span id="summary_item_count" v-if="created_date !=''">Placed on {{created_date}}</span>
                                        <div class="row px-0 mt-4 sub-total-text">
                                            <div class="col-4">Subtotal</div>
                                            <div class="col-5 sub-total-desc">{{order_items.length}} item<span v-if="order_items.length > 1">s</span> (Incl. VAT)</div>


                                            <div class="col-3 text-align-right">{{formatPrice(order.Subtotal)}}</div>

                                        </div>


                                        <div class="row px-0 mt-2 sub-total-text align-items-center" v-if="parseFloat(cust.discount) > 0 || parseFloat(order.AccountDiscount) > 0">
                                            <div class="col-4">Account Discount</div>
                                            <div class="col-5 sub-total-desc"> <span v-if="cust.discount > 0">{{cust.discount}}% (applied)</span></div>
                                            <div class="col-3 text-align-right"><span v-if="order.AccountDiscount > 0">-</span>{{formatPrice(order.AccountDiscount)}}</div>
                                        </div>
                                        <div class="row px-0 mt-2 sub-total-text" v-if="parseFloat(order.DiscountPerc) > 0 || parseFloat(order.OrderDiscount) > 0">
                                            <div class="col-4">Order Discount</div>
                                            <div class="col-5 sub-total-desc"> <span v-if="order.DiscountPerc > 0">{{order.DiscountPerc.toFixed()}}% (applied)</span></div>
                                            <div class="col-3 text-align-right"><span v-if="order.OrderDiscount > 0">-</span>{{formatPrice(order.OrderDiscount)}}</div>
                                        </div>
                                        <div class="row px-0 mt-2 sub-total-text" v-if="order.VoucherDiscount > 0">
                                            <div class="col-9">Voucher Discount</div>
                                            <div class="col-3 text-align-right">-{{formatPrice(order.VoucherDiscount)}}</div>
                                        </div>

                                        <div class="row px-0 mt-2 sub-total-text" v-if="order.ExpressCharge > 0">
                                            <div class="col-9">Express Charges</div>
                                            <div class="col-3 text-align-right">+{{formatPrice(order.ExpressCharge)}}</div>
                                        </div>
                                        <!--
                                        <div class="row px-0 mt-2 sub-total-text" v-if="order.bundles > 0">
                                            <div class="col-9">Bundles</div>
                                            <div class="col-3 text-align-right">-{{formatPrice(order.bundles)}}</div>
                                        </div>
                                        -->

                                        <div class="row px-0 sub-total-text" v-if="Object.keys(order_bundles).length > 0" id="bundles_bloc">
                                            <div class="col-12">
                                                <div class="row mt-2" v-for="(discount,name) in order_bundles">
                                                    <div class="col-9">{{name}}</div>
                                                    <div class="col-3 text-align-right">
                                                        -&#163;{{discount}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="row px-0 mt-1 pt-2 total-text" :class="{'mb-3':order.TypeDelivery=='DELIVERY'}">
                                            <div class="col-9">Total (inc discount)</div>
                                            <div class="col-3 text-align-right">{{formatPrice(order.SubtotalWithDiscount)}}</div><!--total_inc_vat-->
                                        </div>
                                         <div class="row px-0 mt-2 sub-total-text" v-if="order.FailedDelivery == 1 && order.TypeDelivery=='DELIVERY'">
                                            <div class="col-9">Failed delivery</div>
                                            <div class="col-3 text-align-right">+{{formatPrice(order.FailedDeliveryCharge)}}</div>
                                        </div>
                                        <div class="row px-0 mt-2 sub-total-text" v-if="order.DeliveryNowFee!=null && order.TypeDelivery=='DELIVERY'">
                                            <div class="col-9">Delivery fee</div>
                                            <div class="col-3 text-align-right">+{{formatPrice(order.DeliveryNowFee)}}</div>
                                        </div>
                                        <div class="row px-0 mt-2 sub-total-text" v-else-if="order.AutoDeliveryFee > 0 && order.TypeDelivery=='DELIVERY'">
                                            <div class="col-9">Delivery fee</div>
                                            <div class="col-3 text-align-right">+{{formatPrice(order.AutoDeliveryFee)}}</div>
                                        </div>
                                          <div class="row px-0 mt-2 sub-total-text" v-else :class="{'d-none':order.TypeDelivery!='DELIVERY'}">
                                            <div class="col-12">Free Delivery</div>
                                        </div>
                                        <div class="row px-0 mt-1 mb-3 pt-2  total-text" v-if="order.TypeDelivery=='DELIVERY'">
                                            <div class="col-9">Total (inc discount & delivery fees)</div>
                                            <div class="col-3 text-align-right">{{formatPrice(order.Total)}}</div>
                                        </div>
                                        <div class="row px-0 mt-2 sub-total-text">
                                            <div class="col-9">Total (excl VAT)</div>
                                            <div class="col-3 text-align-right">{{formatPrice(order.TotalExcVat)}}</div>
                                        </div>
                                          <div class="row px-0 mt-2 sub-total-text">
                                            <div class="col-9">Tax</div>
                                            <div class="col-3 text-align-right">{{formatPrice(order.TaxAmount)}}</div>
                                        </div>
                                             <div class="row px-0 mt-1 mb-3 pt-2  total-text">
                                            <div class="col-9">Total (inc vat)</div>
                                            <div class="col-3 text-align-right">{{formatPrice(order.Total)}}</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Credit / Balance -->
                                <div class="row justify-content-end mt-3 mx-0">
                                    <div class="col-8 py-3" id="credit_div">
                                        <div class="d-flex align-items-center">
                                            <img src="/images/icon_check.svg" class="paid_icon" v-if="amount_diff<=0"/>
                                             <img src="/images/unpaid_cross.svg" class="paid_icon" v-else/>
                                            <span class="summary-title" v-if="amount_diff<=0">Paid</span>
                                            <span class="summary-title" v-else>Pending payments</span>
                                        </div>
                                        <div class="row mt-4 px-0 py-1 sub-total-text">
                                        
                                            <div class="col-12" v-if="amount_paid_card.length > 0">
                                                <div class="row" v-for="a,i in amount_paid_card" :key="i">
                                                    <div class="col-9 px-0 payment-desc-text">Paid by Card ({{ a.type[0].toUpperCase() + a.type.slice(1)}} {{a.cardNumber.slice(-7)}}):</div>
                                                    <div class="col-3 text-align-right">&#163;{{a.montant}}</div>
                                                </div>
                                            </div>
                                            <div class="col-12 text-align-right" v-else>&#163;0.00</div>
                                        </div>

                                        <div class="row px-0 py-1 sub-total-text" v-if="amountPaidCredit(amount_paid_credit) > 0">
                                            <div class="col-12">
                                                <template v-for="a,i in amount_paid_credit" :key="i">
                                                <div class="row" v-if="a.montant>0">
                                                    <div class="col-9 px-0 payment-desc-text">Paid by cash credit:</div>
                                                    <div class="col-3 text-align-right">&#163;{{a.montant}}</div>
                                                </div>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="row px-0 py-1 sub-total-text" v-if="amount_without_credit > 0&&cust.credit_to_deduct!=0">
                                            <div class="col-4">Minus cash credit</div>
                                            <div class="col-8">
                                                <div class="row">
                                                    <div class="col-9 px-0 payment-desc-text"></div>
                                                    <div class="col-3 text-align-right"><span v-if="credit_to_deduct > 0">-</span>&#163;{{cust.credit_to_deduct}}</div>
                                                </div>
                                            </div>
                                        </div>

                                         <div class="row px-0 mt-4 py-2 balance-text">
                                            <div class="col-9">Outstanding order balance to pay</div>
                                            <div class="col-3 text-align-right">&#163;{{cust.amount_diff}}</div>
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

                                            <!-- Accordion for Order add ons -->
                                            <div class="accordion-item mx-2 mb-4">
                                                <h2 class="accordion-header" id="flush-headingOne">
                                                    <button
                                                        class="accordion-button collapsed gilroy-extra-bold"
                                                        type="button"
                                                        @click="openAccordionclick('orderaddons')" id="acdbtn_orderaddons"
                                                    >Order Add Ons</button>
                                                </h2>
                                                <div class="accordion-collapse collapse" id="acdpanel_orderaddons">
                                                    <div class="accordion-body d-table w-100 px-0 py-0">
                                                        <div class="accordion-content p-4 mt-3">
                                                            <div class="sidebar_title text-white">Order Add Ons</div>
                                                            <div class="row" v-for="(items,category) in addons">
                                                                <div class="col-12 addon-catname mt-3 mb-1" v-if="(order.TypeDelivery=='DELIVERY'&&category=='Delivery')||category!='Delivery'" >
                                                                    {{category}} Charges
                                                                </div>
                                                                <div class="col-12" v-if="(order.TypeDelivery=='DELIVERY'&&category=='Delivery')||category!='Delivery'" >
                                                                    <div class="row">
                                                                        <div class="col-6" v-for="a in items">
                                                                            <button class="each-addon-btn w-100 py-2"  @click="setOrderUpcharge(a.id)" :id="'upcharge_btn_'+a.id" :class="{'addon-selected':order_upcharges.includes(a.id)||(a.id==4&&order.DeliveryNowFee!=null),'is-express-upcharge':a.category_id==1}" :data-id="a.id">{{a.name}}
                                                                                <span v-if="a.type=='perc'">(+{{a.amount}}%)</span>
                                                                                <span v-else-if="a.type=='fixed'">(&#163;{{a.amount}})</span>
                                                                            </button>
                                                                        </div>
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
                                                            <div class="row">
                                                                <span class="sidebar_title text-white">Vouchers & Discount</span>

                                                                <div class="col-12">
                                                                    <div class="row pt-3" v-if="order_vouchers.length > 0">
                                                                        <span class="order-discount-text d-block mb-2">Campaign code applied</span>
                                                                        <div class="col-7" v-for="voucher in order_vouchers">
                                                                            <button class="each-order-voucher w-100 d-flex align-items-center position-relative" @click="promptRemoveVoucher(voucher.vouchers_id,voucher.code)"><span>{{voucher.code}}</span><img src="/images/icon_check.svg" class="position-absolute"/></button>
                                                                        </div>
                                                                    </div>

                                                                    <div v-else class="row mt-3 align-items-end">
                                                                        <div class="col-7">
                                                                            <span class="order-discount-text">Apply campaign code</span>
                                                                            <input type="text" id="voucher_discount" class="w-100 discount-input text-white" v-model="voucher_discount" placeholder="Coupon code"/>
                                                                        </div>
                                                                        <div class="col-3 pl-0">
                                                                            <button class="discount-input discount-btn px-3" @click="addVoucher">Apply</button>
                                                                        </div>
                                                                    </div>


                                                                    <div class="row mt-3 align-items-end">
                                                                        <div class="col-7">
                                                                            <span class="order-discount-text">Apply order discount</span>
                                                                            <input type="text" id="discount_perc" class="w-100 discount-input text-white" v-model="order_discount" placeholder="Discount amount (%)"/>
                                                                        </div>
                                                                        <div class="col-3 pl-0">
                                                                            <button class="discount-input discount-btn px-3" @click="setOrderDiscount">Apply</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--
                                            <div class="accordion-item mx-2 mb-4">
                                                <h2 class="accordion-header" id="flush-headingOne">
                                                    <button
                                                        class="accordion-button collapsed gilroy-extra-bold"
                                                        type="button"
                                                        @click="openAccordionclick('bundles')" id="acdbtn_bundles"
                                                    >Bundles</button>
                                                </h2>
                                                <div class="accordion-collapse collapse" id="acdpanel_bundles">
                                                    <div class="accordion-body d-table w-100 px-0 py-0">
                                                        <div class="accordion-content p-4 mt-3">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    Bundles content
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            -->
                                            <!-- Accordion for payment -->
                                            <div class="accordion-item mx-2 mb-4">
                                                <h2 class="accordion-header" id="flush-headingOne">
                                                    <button
                                                        class="accordion-button collapsed gilroy-extra-bold"
                                                        type="button"
                                                        @click="openAccordionclick('payment')" id="acdbtn_payment"
                                                    >Payment details</button>
                                                </h2>
                                                <div class="accordion-collapse collapse" id="acdpanel_payment">
                                                    <div class="accordion-body d-table w-100 px-0 py-0">
                                                        <div class="accordion-content p-4 mt-3">
                                                            <div class="row">
                                                                <div class="col-12 payment_title mb-4">
                                                                    <span class="sidebar_title text-white mb-3">Account credit details</span>
                                                                    <div class="row cust_credit_detail">
                                                                        <div class="col-7 cust_credit_desc">Account credit available</div>
                                                                        <div class="col-5 cust_credit_value text-white">&#163;{{cust_credit.toFixed(2)}}</div>
                                                                    </div>
                                                                    <div class="row cust_credit_detail">
                                                                        <div class="col-7 cust_credit_desc">Customer payment profile</div>
                                                                        <div class="col-5 cust_credit_value text-white"><span v-if="cust.OnAccount==1">On Account</span><span v-else>Pay as you go</span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 payment_title" :class="{'d-none':cust.OnAccount==1}">
                                                                    <span class="sidebar_title text-white mb-3">Card details <a href="javascript:void(0)" v-if="custcard" id="editcard" @click="setEditCard" :class="{'canceleditcard':editcard}"><span v-if="!editcard">Edit</span><span v-else>Cancel</span></a></span>
                                                                </div>

                                                                <payment ref="payment_comp" :custcard="custcard" :order_id="order_id" :cust="cust" :amounttopay="parseFloat(amount_to_pay)" @reload-checkout="closeEditCardAndReload" @complete-checkout="completeCheckout(false)"></payment>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </transition>
                                <!--
                                <div class="row justify-content-center">
                                    <div class="col-8">
                                        <button class="btn btn-outline-danger w-100" id="cancelTerminalBtn" @click="cancelTerminalRequest">Cancel Terminal request</button>
                                    </div>
                                </div>
                                -->
                            </div>
                        </div>
                        <div class="row justify-content-end py-4" id="last-row-btns">
                            <div class="col-4">
                                <div class="row align-items-center">
                                    <div class="col-6 px-4 text-align-center">
                                        <a href="javascript:void(0)" @click="redirectToDetailingList">Previous</a>
                                    </div>
                                    <div class="col-6 px-4">
                                        <button id="closeBtn" @click="redirectToOrderDetail" class="w-100 py-3" v-if="amount_diff<=0 && Object.keys(has_invoices).length > 0">Close</button>
                                        <button v-else id="completeBtn" class="w-100 py-3" @click="validatePayment" :disabled="editcard">Proceed</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </transition>

    <modal ref="no_payment_modal">
        <template #closebtn>
            <span class="close" id="addon_modal_close" @click="closeNoPaymentModal"></span>
        </template>
        <template #bheader>
            <div class="bmodal-header py-5 text-center">No payment method</div>
        </template>
        <template #bcontent>
        </template>
        <template #mbuttons>
            <div class="row mx-0 justify-content-center my-5 py-3">
                <div class="col-10">
                    <div class="row justify-content-center mb-4">
                        <div class="col-6">
                            <stripe-pay-now :user="cur_user" :order="order" :amounttopay="parseFloat(amount_to_pay)" @complete-checkout="completeCheckout(false)" @close-payment-modal="closePaymentAndShowLoading" @close-awaiting-payment="closeAwaitingPaymentModal" @set-terminal-pay="setTerminalPay" ref="stripePay"></stripe-pay-now>
                        </div>
                        <div class="col-6">
                            <button class="pay-btn w-100 py-3" @click="completeCheckout(true)">Pay later</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button id="payment_method_btn" class="w-100 py-3" @click="openPaymentAccordion">Add payment method</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </modal>

    <modal ref="awaiting_payment_modal">
        <template #closebtn>
            <span class="close" id="addon_modal_close" @click="closeAwaitingPaymentModal"></span>
        </template>
        <template #bheader>
            <div class="bmodal-header py-5 text-center">Awaiting payment</div>
        </template>
        <template #mbuttons>
            <div class="row justify-content-center">
                <div class="col-8">
                    <button class="btn btn-outline-danger w-100" id="cancelTerminalBtn" @click="cancelTerminalRequest">Cancel Terminal request</button>
                </div>
            </div>
        </template>
    </modal>

    <modal ref="remove_voucher_modal">
         <template #closebtn>
            <span class="close" id="rv_modal_close" @click="closeVoucherModal"></span>
        </template>
        <template #bheader>
            <div class="bmodal-header py-5 text-center">Do you want to remove the voucher {{cur_voucher_to_remove}} ?</div>
        </template>
        <template #mbuttons>
            <div class="row mx-0 justify-content-center my-5 py-3">
                <div class="col-3"><button class="btn btn-outline-success w-100" @click="removeVoucher(cur_id_voucher_to_remove,cur_voucher_to_remove)">Yes</button></div>
                <div class="col-2"></div>
                <div class="col-3"><button class="btn btn-outline-danger w-100" @click="closeVoucherModal">No</button></div>
            </div>
        </template>

    </modal>


    <modal ref="price_delivery_now_modal">
        <template #closebtn>
            <span class="close" id="price_delivery_now_modal_close" @click="closePriceDeliveryNowModal"></span>
        </template>
        <template #bheader>
            <div class="modal-header-deliverypricenow py-3 text-center">Price delivery now</div>
        </template>
        <template #bcontent>
        <div class="row mb-5 justify-content-center">

                <div class="col-4 form-group pt-3">
                        <label class="form-label body_medium">Enter Price</label>
                    <div class="input-group"><span class="input-group-text fw-bold" style="background: none;">£</span>
                        <input @keydown="handleAllowNumbers($event)" v-model="pricedeliverynow" style="border-left: medium none; fill: transparent;" type="number" step="0.1" class="form-control" placeholder="0.00"/>
                    </div>
                </div>


        </div>
        </template>
        <template #mbuttons>
            <div class="row justify-content-center gap-3 mb-5">
                <div class="col-4">
                    <button class="btn btn-outline-danger w-100"  @click="closePriceDeliveryNowModal">Close</button>
                </div>
                <div class="col-4">
                    <button class="btn btn-dark body_medium w-100"  @click="savePriceDeliveryNow">Save</button>
                </div>
            </div>
        </template>
    </modal>

</template>
<script>

import MainHeader from "../layout/MainHeader";
import BreadCrumb from "../layout/BreadCrumb";
import SideBar from "../layout/SideBar";
import { ref, onUpdated, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useStore } from "vuex";
import {formatPrice} from "../helpers/helpers";
import {
    LOADER_MODULE,
    DISPLAY_LOADER,
    HIDE_LOADER,
    TOASTER_MODULE,
    TOASTER_MESSAGE,
} from '../../store/types/types';
import Payment from '../miscellaneous/Payment.vue';
import Modal from '../miscellaneous/Modal.vue';
import StripePayNow from '../miscellaneous/StripePayNow.vue';

export default {
    name: "Checkout",
    components: { BreadCrumb, SideBar, MainHeader,Payment,Modal,StripePayNow},
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
        const order_discount = ref("");
        const sub_total = ref(0);
        const discount = ref(0);
        const total_with_discount = ref(0);
        const vat = ref(0);
        const total_inc_vat = ref(0);
        const total_exc_vat = ref(0);
        const custcard = ref({});
        const no_payment_modal = ref();
        const stripe_public_key = ref('');
        const cur_user = ref({});
        const payment_comp = ref();
        const editcard = ref(false);
        const order_balance = ref(0);
        const cust_credit = ref(0);
        order_id.value = route.params.order_id;
        const amount_to_pay = ref(0);
        const amount_paid = ref(0);
        const awaiting_payment_modal = ref();
        const amount_paid_card = ref(0);
        const amount_paid_credit = ref(0);
        const discount_perc = ref(0);
        const created_date = ref("");
        const credit_to_deduct = ref(0);
        const cust_discount = ref(0);
        const upcharges = ref(0);
        const bundles = ref(0);
        const order_without_upcharges = ref(0);
        const amount_without_credit = ref(0);
        const amount_diff = ref(0);
        const addons = ref([]);
        const order_upcharges = ref([]);
        const order_vouchers = ref([]);
        const discount_from_voucher = ref(0);
        const voucher_discount = ref('');

        const remove_voucher_modal = ref();
        const price_delivery_now_modal=ref();
        const cur_voucher_to_remove = ref('');
        const cur_id_voucher_to_remove = ref(0);
        const failed_delivery_price = ref(0);
        const price_plus_delivery = ref(0);
        const order_bundles = ref([]);
        const pricedeliverynow=ref("");
        const has_invoices = ref([]);
        const btn_disabled = ref(false);

        let bodytag=document.getElementsByTagName( 'body' )[0]
        bodytag.classList.remove('hide-overflowY');

        const terminal_pay = ref(0);
        const stripePay = ref();

        const setTerminalPay = ()=>{
            terminal_pay.value = 1;
        }


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
                if(res.data.order.DeliveryNowFee!='')
                pricedeliverynow.value=parseFloat(res.data.order.DeliveryNowFee).toFixed(2);
                booking.value = res.data.booking_details;
                address.value = res.data.address;
                sub_total.value = res.data.sub_total;
                discount.value = res.data.discount;
                total_with_discount.value = res.data.total_with_discount;
                total_inc_vat.value = res.data.total_inc_vat;
                total_exc_vat.value = res.data.total_exc_vat;
                vat.value = res.data.vat;
                custcard.value = res.data.custcard;
                stripe_public_key.value = res.data.stripe_public_key;
                cur_user.value = res.data.cur_user;
                order_balance.value = res.data.order.balance;
                cust_credit.value = res.data.cust.credit;

                amount_to_pay.value = res.data.order.TotalDue;//res.data.amount_to_pay;
                amount_paid.value = res.data.amount_paid;
                amount_paid_card.value = res.data.amount_paid_card;
                amount_paid_credit.value = res.data.amount_paid_credit;
                discount_perc.value = res.data.discount_perc;
                created_date.value = res.data.created_date;
                credit_to_deduct.value = res.data.credit_to_deduct;
                order_discount.value = res.data.order.DiscountPerc;
                cust_discount.value = res.data.cust_discount;
                upcharges.value = res.data.order_addon;
                bundles.value=res.data.bundles;
                order_without_upcharges.value = res.data.order_without_upcharges;
                amount_without_credit.value = res.data.amount_without_credit;
                amount_diff.value = res.data.amount_diff;
                addons.value = res.data.addons;
                order_upcharges.value = res.data.order_upcharges;
                order_vouchers.value = res.data.order_vouchers;
                discount_from_voucher.value = res.data.discount_from_voucher;
                failed_delivery_price.value = res.data.failed_delivery_price;
                price_plus_delivery.value = res.data.price_plus_delivery;
                order_bundles.value = res.data.order_bundles;
                has_invoices.value = res.data.has_invoices;
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
            if(typeof(string[0])!='undefined' && typeof(string[1])!='undefined'){
                return string[0].toUpperCase() + string.substring(1);
            }else{
                return "";
            }
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

        function setOrderDiscount(){
            let err = "";

            var reg = /^\d+$/;

            if(order_discount.value=='' || !reg.test(order_discount.value) || parseInt(order_discount.value) > 100){
                err = "Please enter a discount percent";
            }
            if(err!=''){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                {
                    message: err,
                    ttl: 3,
                    type: 'danger'
                });
            }else{
                axios.post('/set-checkout-discount',{
                    order_id:order_id.value,
                    discount:order_discount.value,
                    sub_total:sub_total.value,
                }).then((res)=>{

                }).catch((err)=>{
                    console.log(err);
                }).finally(()=>{
                    getCheckoutItems();
                });
            }
        }

        function redirectToDetailingList(){
            router.push('/order-content/'+order_id.value);
        }

        function completeCheckout(paylater){
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                true,
                "creating items....",
            ]);

            axios.post('/complete-checkout',{
                order_id:order_id.value,
                amount_to_pay:amount_to_pay.value,
                balance:order_balance.value,
                credit_to_deduct:credit_to_deduct.value,
                paylater:(paylater?1:0),
            }).then((res)=>{

                if(res.data.output && res.data.output.result=='ok'){
                    /*
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                    {
                        message: "Order items created",
                        ttl: 3,
                        type: 'success'
                    });
                    */
                   router.push('/order_details/'+order_id.value);
                }else{
                    if(terminal_pay.value==1){
                        stripePay.value.refundPayment();
                    }

                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                    {
                        message: "An error has occured during items creation",
                        ttl: 3,
                        type: 'danger'
                    });
                }

            }).catch((err)=>{
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                    {
                        message: JSON.stringify(err),
                        ttl: 3,
                        type: 'danger'
                    });

            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });
        }

        function validatePayment(){
            if(cust.value.OnAccount==1){
                completeCheckout(true);
            }else{
                if(editcard.value==true){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                        {
                            message: "Please save card details or cancel editing",
                            ttl: 3,
                            type: 'danger'
                        });
                }else{
                    let err = false;
                    if(amount_to_pay.value > 0 && cust.value.bycard==1 && !custcard.value){
                        err = true;
                        no_payment_modal.value.showModal();
                    }

                    if(!err){

                        if(custcard.value && cust.value.bycard==1 && typeof(custcard.value.id)!='undefined' && amount_to_pay.value > 0){
                            console.log('by card',amount_to_pay.value);
                            payment_comp.value.effectPayment('Pay')
                        }
                        else{
                            console.log('by credit',credit_to_deduct.value);

                            if(parseFloat(credit_to_deduct.value)==0){
                                no_payment_modal.value.showModal();
                            }else{
                                completeCheckout(false);
                            }
                        }
                    }
                }
            }
        }

        function closeEditCardAndReload(){
            if(editcard.value==true){
                setEditCard();
            }
            getCheckoutItems();
        }

        function closeNoPaymentModal(){
            no_payment_modal.value.closeModal();
        }

        function setEditCard(){
            if(editcard.value==false){
                editcard.value = true;
            }else{
                editcard.value = false;
            }

            payment_comp.value.setEditCard(editcard.value);
        }

        function openPaymentAccordion(){
            openAccordionclick('payment');
            closeNoPaymentModal();
        }

        function closePaymentAndShowLoading(){
            closeNoPaymentModal();
            awaiting_payment_modal.value.showModal();
        }

        function closeAwaitingPaymentModal(){
            awaiting_payment_modal.value.closeModal();
        }

        function redirectToOrderDetail(){
            axios.post('/validate-checkout-order',{
                order_id:order_id.value,
            }).then((res)=>{
                 if(res.data.output && res.data.output.result=='ok'){
                    router.push('/order_details/'+order_id.value);
                 }

            }).catch((err)=>{

            }).finally(()=>{

            });


        }

        function setOrderUpcharge(id){

            if(id==4){
                if(order.value.DeliveryNowFee!=null){
                    store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[
                true,
                "Delete price delivery now....",
            ]);

            axios.post('/save-price-delivery-now',{
                price:null,
                order_id:order_id.value
            }).then((res)=>{
                getCheckoutItems();
            }).catch((err)=>{

            }).finally(()=>{

            });
                }else{
                    price_delivery_now_modal.value.showModal();
                }
                return;
            }
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[
                true,
                "Setting upcharges....",
            ]);

            let el = document.getElementById('upcharge_btn_'+id);
            el.classList.toggle('addon-selected');

            let classes = Object.values(el.classList);
            let selected = false;
            let other_exp_ids = [];



            if(classes.includes('addon-selected')){
                selected = true;

                if(classes.includes('is-express-upcharge')){
                    let other_exp_els = Object.values(document.querySelectorAll('.is-express-upcharge:not(#upcharge_btn_'+id+')'));

                    if(other_exp_els.length > 0){
                        other_exp_els.forEach((v,i)=>{
                            let other_id = v.getAttribute('data-id');
                            other_exp_ids.push(other_id);
                            v.classList.remove('addon-selected');
                        });
                    }
                }
            }

            axios.post('/set-checkout-addon',{
                order_id:order_id.value,
                addon_id:id,
                exp_addons_to_remove:JSON.stringify(other_exp_ids),
                selected:selected,
            }).then((res)=>{
                console.log(res);
            }).catch((err)=>{

            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                getCheckoutItems();

            });
        }

        function promptRemoveVoucher(id,code){
            cur_voucher_to_remove.value = code;
            cur_id_voucher_to_remove.value = id;
            remove_voucher_modal.value.showModal();
        }

        function removeVoucher(id,code){
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[
                true,
                "Removing voucher....",
            ]);

            axios.post('/remove-order-voucher',{
                code:code,
                voucher_id:id,
                order_id:order_id.value
            }).then((res)=>{
                closeVoucherModal();
                if(res.data.deleted==1 || res.data.deleted==0){
                    getCheckoutItems();
                }

            }).catch((err)=>{

            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });
        }

        function closeVoucherModal(){
            cur_voucher_to_remove.value = '';
            cur_id_voucher_to_remove.value = 0;
            remove_voucher_modal.value.closeModal();
        }

        async function addVoucher(){

            if(!btn_disabled.value){
                btn_disabled.value = true;
                try {
                    await axios.post('/add-order-voucher',{
                        voucher:voucher_discount.value,
                        order_id:order_id.value,
                    }).then((res)=>{
                        let output = res.data.output;

                        if(output.err !=''){
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                                message: output.err,
                                ttl: 3,
                                type: 'danger'
                            });
                        }else{
                            getCheckoutItems();
                        }
                    }).catch(err=>{

                    }).finally(()=>{
                    });
                } finally {
                    btn_disabled.value = false;
                }
            }

        }

        function cancelTerminalRequest(){
             axios.post('/cancel-terminal-request',{})
                .then((res)=>{
                    console.log(res);
                }).catch((err)=>{

                }).finally(()=>{
                    awaiting_payment_modal.value.closeModal();
                })
        }

        const closePriceDeliveryNowModal=()=>{
            price_delivery_now_modal.value.closeModal();
        }

        const savePriceDeliveryNow=()=>{
               store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[
                true,
                "Saving price delivery now....",
            ]);

            axios.post('/save-price-delivery-now',{
                price:parseFloat(pricedeliverynow.value),
                order_id:order_id.value
            }).then((res)=>{
                closePriceDeliveryNowModal();
                getCheckoutItems();
            }).catch((err)=>{

            }).finally(()=>{

            });
        }

        const handleAllowNumbers = (e) => {//handle price format xxxxx.xx

        let s=e.target.value.split('.');
        if(typeof s[1]!="undefined"&&s[1].length==2){
             if (e.target.type === "number" && ["Backspace","Delete"].includes(e.key)!=true) {
                e.preventDefault();
            }
        }else if(e.target.value.includes('.')){
            if (e.target.type === "number" && ["0","1","2","3","4","5","6","7","8","9","Backspace","Delete"].includes(e.key)!=true) {
                e.preventDefault();
            }
        }else{
            if (e.target.type === "number" && ["0","1","2","3","4","5","6","7","8","9","Backspace","Delete","."].includes(e.key)!=true) {
                e.preventDefault();
            }
        }
        };

        const amountPaidCredit=(amount_paid_credit)=>{
            let x=_.cloneDeep(amount_paid_credit);
            let total=0;

            if(typeof x=="object")
            total= x.reduce((accumulator,a)=>{
                return accumulator+a.montant;
            },0);
           return total;
        }
        return {
            amountPaidCredit,
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
            order_discount,
            setOrderDiscount,
            sub_total,
            discount,
            total_with_discount,
            total_inc_vat,
            total_exc_vat,
            vat,
            custcard,
            getCheckoutItems,
            redirectToDetailingList,
            completeCheckout,
            validatePayment,
            no_payment_modal,
            closeNoPaymentModal,
            stripe_public_key,
            cur_user,
            payment_comp,
            editcard,
            setEditCard,
            closeEditCardAndReload,
            cust_credit,
            order_balance,
            amount_to_pay,
            openPaymentAccordion,
            amount_paid,
            awaiting_payment_modal,
            closePaymentAndShowLoading,
            closeAwaitingPaymentModal,
            amount_paid_card,
            amount_paid_credit,
            discount_perc,
            created_date,
            redirectToOrderDetail,
            credit_to_deduct,
            cust_discount,
            upcharges,
            bundles,
            order_without_upcharges,
            amount_without_credit,
            amount_diff,
            addons,
            setOrderUpcharge,
            order_upcharges,
            order_vouchers,
            discount_from_voucher,
            voucher_discount,
            promptRemoveVoucher,
            removeVoucher,
            remove_voucher_modal,
            closeVoucherModal,
            cur_voucher_to_remove,
            cur_id_voucher_to_remove,
            addVoucher,
            failed_delivery_price,
            price_plus_delivery,
            order_bundles,
            setTerminalPay,
            stripePay,
            cancelTerminalRequest,
            formatPrice,
            price_delivery_now_modal,
            closePriceDeliveryNowModal,
            savePriceDeliveryNow,
            pricedeliverynow,
            handleAllowNumbers,
            has_invoices,
            btn_disabled,
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
.title,
.summary-title{
    font-family: Gilroy;
    font-style: normal;
    font-weight: 800;
    color: #000000;
}

.title{
    font-size: 36px;
    line-height: 106%;
    padding: 10px;
}

.summary-title{
    font-size:32px;
    margin-right:10px;
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
.item-desc-text,
.fabric-coef{
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
#booking_log,
.sub-total-text{
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

#cust_email,
.order-discount-text{
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

.discount-input{
    height:40px;
    margin-top:0.25rem;
}

#discount_perc,
#voucher_discount{
    background:none;
    border:thin solid #fff;
    border-radius: 4px;
    text-indent:10px;
}

#discount_perc:focus,
#voucher_discount:focus{
    outline: none;
}

#discount_perc:-moz-placeholder{
    color:#fff;
}

.discount-btn{
    background:#fff;
    border-radius: 4px;
    border:none;
}

.discount-btn:hover{
    background: #42A71E;
    color:#fff;
}

#summary_div,
#credit_div{
    background:#fff;
    padding-left:3rem;
    padding-right:3rem;
}

#summary_item_count{
    font: normal 16px "Gotham Rounded";
    color:#868686;
}

.total-text,
.sub-total-text,
.balance-text{
    color:#47454B;
}

.total-text,
.balance-text{
    font:bold 22px "Gilroy";
}

.total-text{
    border-top: thin solid #c3c3c3;
}

.balance-text{
    border-top: thin solid #c3c3c3;
}

#last-row-btns *{
    font:normal 16px "Gotham Rounded";
}

#last-row-btns a{
    color:#47454B;
}

#last-row-btns a:hover{
    text-decoration: none;
}

#completeBtn{
    background: #42A71E;
    color:#fff;
    border:none;
    border-radius: 4px;
}

#completeBtn:hover{
    background: #333;
}

#closeBtn{
    background:#fff;
    border:thin solid #000;
    border-radius:4px;
}

#closeBtn:hover{
    background:#000;
    color:#fff;
}

.bmodal-header{
    font:bold 22px "Gilroy";
    color:#F4003D;
    background:#FFEFED;
}

.pay-btn,#payment_method_btn{
    font:normal 16px "Gotham Rounded";
    border-radius:4px;
}
.pay-btn{
    border:thin solid #47454B;
    background:#fff;
}

#payment_method_btn{
    border:none;
    background:#47454b;
    color:#fff;
}

.pay-btn:hover,#payment_method_btn:hover{
    color:#fff;
    background: #42A71E;
    border-color: #42A71E;
}

#editcard{
    font:normal 16px "Gotham Rounded";
    color:#42A71E;
}

#editcard.canceleditcard{
    color:#EB5757;
}

#editcard:hover{
    text-decoration: none;
}

.paid_icon{
    margin-right:0.5rem;
}

.payment_title .sidebar_title{
    border-bottom:0.5px solid #f8f8f8;
    display:block;
}

.cust_credit_desc{
    color: #afafaf;
}

.sub-total-desc,
.payment-desc-text{
    color:#c4c4c4;
}

.payment-desc-text{
    font:normal 15px "Gotham Rounded";
}

.addon-catname{
    color:#fff;
    font:normal 16px "Gotham Rounded";
}

.each-addon-btn{
    color:#fff;
    font:normal 16px "Gotham Rounded Light";
    border:thin solid #fff;
    border-radius: 4px;
    background:none;
}

.each-addon-btn:hover,
.each-addon-btn.addon-selected{
    color:#47454B;
    background: #fff;
}

.each-order-voucher{
    font:normal 16px "Gotham Rounded";
    text-align: left;
    padding-left:1rem;
    height:40px;
    background-color:#fff;
    border-radius:5px;
    border:none;
}

.each-order-voucher img{
    right: 0.5rem;
}

#bundles_bloc{
    /*
    border-bottom: thin solid #c3c3c3;
    */
}

#cancelTerminalBtn{
    background:#fff;
}

#cancelTerminalBtn:hover{
    background:#EB5757;
}
.modal-header-deliverypricenow{
    background-color:#F8F8F8 ;
    font:bold 22px "Gilroy";
    color:#141414;
}

#price_delivery_now_modal_close{
    top:23px;
}

/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
</style>
