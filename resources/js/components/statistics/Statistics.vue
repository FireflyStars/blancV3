<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <div class="container-fluid h-100 bg-color">
            <main-header></main-header>
            <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax" style="z-index: 100">
                <side-bar></side-bar>
                <div class="col main-view p-5">
                    <h2>Statistics</h2>
                    <div class="container-fluid orderlist-tabs d-flex align-items-center">
                        <div class="orderlist-tab body_bold active">Sales</div>
                    </div>
                    <div class="container-stat position-relative mb-10">
                        <div class="row">
                            <div class="col-10 mb-10">
                                <statisticsFilters :filterVal="filterVal" @update:filterVal="newValue => filterVal = newValue"></statisticsFilters>
                            </div>
                        </div>
                        <div class="row pb-4">
                            <div class="col-sm-4">
                                <div class="container-fluid col-12 bg-white py-4 mb-3">
                                    <div class="row fw-bold">
                                        <div class="col-6 p-0 text-nowrap">Total Sales (by channel)</div>
                                        <div class="col-4 float-right">£{{ statistique.total_sales }} </div>
                                        <i v-if="statistique.total_sales >= statistique.last_total_sales && statistique.last_total_sales !=0" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                        <i v-else-if="statistique.total_sales < statistique.last_total_sales && statistique.last_total_sales !=0" class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                        <div class="col-1 px-0 float-right" v-if="statistique.last_total_sales && statistique.last_total_sales !=0">{{ statistique.last_total_sales !=0 ? ((( statistique.total_sales*100 )/statistique.last_total_sales).toFixed(0) +'%' ) : '--'  }}</div>
                                    </div>

                                    <div class="row">
                                        <div class="row fst-itali">Stores</div>
                                        <div class="row text-center">
                                            <div class="first-bar col-3">£{{ statistique.mb }} </div>
                                            <div class="second-bar col-3">£{{ statistique.nh }}</div>
                                            <div class="third-bar col-3">£{{ statistique.ch }}</div>
                                            <div class="fourth-bar col-3">£{{ statistique.sk }}</div>
                                        </div>
                                        <div class="row text-center">
                                            <div class="col-3">MB</div>
                                            <div class="col-3">NH</div>
                                            <div class="col-3">CH</div>
                                            <div class="col-3">SK</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="row fst-itali">Deliveries</div>
                                        <div class="row text-center">
                                            <div class="first-bar col-6">£{{ statistique.b2c }}</div>
                                            <div class="fourth-bar col-6">£{{ statistique.b2b }}</div>
                                        </div>
                                        <div class="row text-center">
                                            <div class="col-6">B2C</div>
                                            <div class="col-6">B2B</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container-fluid col-12 bg-white py-4 mb-3">
                                    <div class="row body_bold">
                                        <div class="col-8">Average Order Value</div>
                                        <div class="col-2 float-right">£{{ statistique.avg_total_order }}</div>
                                        <i v-if="statistique.avg_total_order >= statistique.last_avg_total_order && statistique.last_avg_total_order && statistique.last_avg_total_order !=0" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                        <i v-else-if="statistique.last_avg_total_order && statistique.last_avg_total_order !=0" class="col-1 bi bi-arrow-down text-danger px-0 width-auto"></i>
                                        <div class="col-1 px-0 float-right" v-if="statistique.last_avg_total_order && statistique.last_avg_total_order !=0">{{ statistique.last_avg_total_order > 0 ? ((statistique.avg_total_order*100)/statistique.last_avg_total_order).toFixed(0) +'%' : '--' }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="row">
                                            <div class="col-5">Stores</div>
                                            <div class="col-4 float-right width-fit-content">£{{ statistique.avg_store_order }}</div>
                                            <i v-if="statistique.avg_store_order >= statistique.last_avg_store_order && statistique.last_avg_store_order && statistique.last_avg_store_order != 0" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <i v-else-if="statistique.last_avg_store_order && statistique.last_avg_store_order != 0" class="col-1 bi bi-arrow-down text-danger px-0 width-auto"></i>
                                            <div class="col-1 px-0 float-right" v-if="statistique.last_avg_store_order && statistique.last_avg_store_order != 0">{{ statistique.last_avg_store_order > 0 ? ((statistique.avg_store_order*100)/statistique.last_avg_store_order).toFixed(0) + '%' : '--' }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">Delivery</div>
                                            <div class="col-4 float-right width-fit-content">£{{ statistique.avg_delivery_order }}</div>
                                            <i v-if="statistique.avg_delivery_order > statistique.last_avg_delivery_order && statistique.last_avg_delivery_order && statistique.last_avg_delivery_order !=0" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <i v-else-if="statistique.last_avg_delivery_order && statistique.last_avg_delivery_order !=0" class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-1 px-0 float-right" v-if="statistique.last_avg_delivery_order && statistique.last_avg_delivery_order !=0">{{ statistique.last_avg_delivery_order > 0 ? ((statistique.avg_delivery_order*100)/statistique.last_avg_delivery_order).toFixed(0)+'%' : '--' }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">B2B</div>
                                            <div class="col-4 float-right width-fit-content">£{{ statistique.avg_b2b_order }}</div>
                                            <i v-if="statistique.avg_b2b_order >= statistique.last_avg_b2b_order && statistique.last_avg_b2b_order && statistique.last_avg_b2b_order !=0" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <i v-else-if="statistique.last_avg_b2b_order && statistique.last_avg_b2b_order !=0" class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-1 px-0 float-right" v-if="statistique.last_avg_b2b_order && statistique.last_avg_b2b_order !=0">{{ statistique.last_avg_b2b_order > 0 ? ((statistique.avg_b2b_order*100)/statistique.last_avg_b2b_order).toFixed(0)+'%' :'--' }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">B2C</div>
                                            <div class="col-4 float-right width-fit-content">£{{ statistique.avg_b2c_order }}</div>
                                            <i v-if="statistique.avg_b2c_order >= statistique.last_avg_b2c_order && statistique.last_avg_b2c_order && statistique.last_avg_b2c_order !=0" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <i v-else-if="statistique.last_avg_b2c_order && statistique.last_avg_b2c_order !=0" class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-1 px-0 float-right" v-if="statistique.last_avg_b2c_order && statistique.last_avg_b2c_order !=0">{{ statistique.last_avg_b2c_order > 0 ? ((statistique.avg_b2c_order*100)/statistique.last_avg_b2c_order).toFixed(0)+'%' : '--' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="container-fluid col-12 bg-white py-4 mb-3">
                                    <div class="row body_bold">
                                        <div class="col-8">Total Items (by service)</div>
                                        <div class="col-2 float-right">£{{ statistique.service_items_total }}</div>
                                        <i v-if="statistique.service_items_total >= statistique.last_service_items_total && statistique.last_service_items_total && statistique.last_service_items_total !=0" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                        <i v-else-if="statistique.last_service_items_total && statistique.last_service_items_total !=0" class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                        <div class="col-1 px-0 float-right" v-if="statistique.last_service_items_total && statistique.last_service_items_total !=0">{{ statistique.last_service_items_total > 0 ? ((statistique.service_items_total*100)/statistique.last_service_items_total).toFixed(0) +'%' : '--' }}</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row chart-title-row">
                                            <div class="col-6 text-align-right">Services</div>
                                            <div class="col-6 text-align-center pl-0">Today</div>
                                        </div>
                                        <div
                                            v-for="(item, i) in statistique.service_items"
                                            class="row chart-row align-items-center"
                                            id="stats_today"
                                            :key="i"
                                        >
                                            <div
                                                class="col-6 text-right each-dept-name pl-0"
                                            >{{ item.DepartmentName }}</div>
                                            <div class="col-6">
                                                <div class="row">
                                                    <!-- <div
                                                        v-if="item.total > 0"
                                                        class="chart-bar"
                                                        :style="{
                                                            width: parseInt(item.total) * 0.01 + '%',
                                                            background: '#e7e7e7',
                                                        }"
                                                    ></div> -->
                                                    <div
                                                        class="w-100 today-count text-center"
                                                        :class="{' pl-0': item.total === 0,}"
                                                    >{{ item.total.toFixed(0) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="container-fluid col-12 bg-white py-4 mb-3">
                                    <div class="row fw-bold">
                                        <div class="col-8 p-0 text-nowrap">Sales from 1st time Customers</div>
                                        <div class="col-2 float-right">£{{ statistique.first_time_total_sales }}</div>
                                        <i v-if="statistique.first_time_total_sales >= statistique.last_first_time_total_sales && statistique.last_first_time_total_sales && statistique.last_first_time_total_sales !=0" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                        <i v-else-if="statistique.last_first_time_total_sales && statistique.last_first_time_total_sales !=0" class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                        <div class="col-1 px-0 float-right" v-if="statistique.last_first_time_total_sales && statistique.last_first_time_total_sales !=0">{{ statistique.last_first_time_total_sales > 0 ? ((statistique.first_time_total_sales*100)/statistique.last_first_time_total_sales).toFixed(0)+'%' :'--' }}</div>
                                    </div>

                                    <div class="row">
                                        <div class="row fw-italic">Stores</div>
                                        <div class="row text-center">
                                            <div class="first-bar col-3">£{{ statistique.first_time_mb }}</div>
                                            <div class="second-bar col-3">£{{ statistique.first_time_nh }}</div>
                                            <div class="third-bar col-3">£{{ statistique.first_time_ch }}</div>
                                            <div class="fourth-bar col-3">£{{ statistique.first_time_sk }}</div>
                                        </div>
                                        <div class="row text-center">
                                            <div class="col-3">MB</div>
                                            <div class="col-3">NH</div>
                                            <div class="col-3">CH</div>
                                            <div class="col-3">SK</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="row fw-italic">Deliveries</div>
                                        <div class="row">
                                            <div class="first-bar col-4">£{{ statistique.first_time_app }}</div>
                                            <div class="second-bar col-4">£200{{ statistique.first_time_normal }}</div>
                                            <div class="fourth-bar col-4">£{{ statistique.first_time_b2b }}</div>
                                        </div>
                                        <div class="row text-center">
                                            <div class="col-4">APP</div>
                                            <div class="col-4">CC</div>
                                            <div class="col-4">B2B</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container-fluid col-12 bg-white py-4 mb-3">
                                    <div class="row">
                                        <div class="row">
                                            <div class="col-8 p-0 text-nowrap">Booking</div>
                                            <div class="col-2 float-right">£{{ statistique.total_booking }}</div>
                                            <i v-if="statistique.total_booking >= statistique.last_total_booking && statistique.last_total_booking && statistique.last_total_booking !=0" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <i v-else-if="statistique.last_total_booking && statistique.last_total_booking !=0" class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-1 px-0 float-right" v-if="statistique.last_total_booking && statistique.last_total_booking !=0">{{ statistique.last_total_booking > 0 ? ((statistique.total_booking*100)/statistique.last_total_booking).toFixed(0)+'%' : '--' }}</div>
                                        </div>
                                        <div class="row text-center">
                                            <div class="first-bar col-4">{{ statistique.app_booking }}</div>
                                            <div class="second-bar col-4">{{ statistique.pos_booking }}</div>
                                            <div class="fourth-bar col-4">{{ statistique.rec_booking }}</div>
                                        </div>
                                        <div class="row text-center">
                                            <div class="col-4">APP</div>
                                            <div class="col-4">POS</div>
                                            <div class="col-4">RECURING</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container-fluid col-12 bg-white py-4 mb-3">
                                    <div class="row body_bold">
                                        <div class="col-8">New Sign Ups</div>
                                        <div class="col-2 float-right width-fit-content">{{ statistique.total_new_signup }}</div>
                                        <i v-if="statistique.total_new_signup >= statistique.last_total_new_signup && statistique.last_total_new_signup && statistique.last_total_new_signup!=0" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                        <i v-else-if="statistique.last_total_new_signup && statistique.last_total_new_signup!=0" class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                        <div class="col-1 px-0 float-right" v-if="statistique.last_total_new_signup && statistique.last_total_new_signup!=0">{{ ((statistique.total_new_signup*100)/statistique.last_total_new_signup).toFixed(0) }}%</div>
                                    </div>
                                    <div class="row">
                                        <div class="row">
                                            <div class="col-5">Stores</div>
                                            <div class="col-2 float-right width-fit-content">{{ statistique.stores_new_signup }}</div>
                                            <i v-if="statistique.stores_new_signup >= statistique.last_stores_new_signup && statistique.last_stores_new_signup && statistique.last_stores_new_signup!=0" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <i v-else-if="statistique.last_stores_new_signup && statistique.last_stores_new_signup!=0" class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-1 px-0 float-right" v-if="statistique.last_stores_new_signup && statistique.last_stores_new_signup!=0">{{ ((statistique.stores_new_signup*100)/statistique.last_stores_new_signup).toFixed(0) }}%</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">Delivery</div>
                                            <div class="col-2 float-right width-fit-content">{{ statistique.delivery_new_signup }}</div>
                                            <i v-if="statistique.delivery_new_signup >= statistique.last_delivery_new_signup" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <i v-else-if="statistique.last_total_new_signup && statistique.last_total_new_signup!=0" class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-1 px-0 float-right" v-if="statistique.last_total_new_signup && statistique.last_total_new_signup!=0">{{ statistique.last_total_new_signup > 0 ? ((statistique.delivery_new_signup*100)/statistique.last_delivery_new_signup).toFixed(0) +'%' : '--'}}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">B2B</div>
                                            <div class="col-2 float-right width-fit-content">{{ statistique.b2b_new_signup }}</div>
                                            <i v-if="statistique.b2b_new_signup >= statistique.last_b2b_new_signup && statistique.last_b2b_new_signup && statistique.last_b2b_new_signup!=0" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <i v-else-if="statistique.last_b2b_new_signup && statistique.last_b2b_new_signup!=0" class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-1 px-0 float-right" v-if="statistique.last_b2b_new_signup && statistique.last_b2b_new_signup!=0">{{ statistique.last_b2b_new_signup > 0 ? ((statistique.b2b_new_signup*100)/statistique.last_b2b_new_signup).toFixed(0)+'%' : '--' }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">B2C</div>
                                            <div class="col-2 float-right width-fit-content">{{ statistique.b2c_new_signup }}</div>
                                            <i v-if="statistique.b2c_new_signup >= statistique.last_b2c_new_signup && statistique.last_b2c_new_signup && statistique.last_b2c_new_signup !=0" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <i v-else-if="statistique.last_b2c_new_signup && statistique.last_b2c_new_signup !=0" class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-1 px-0 float-right" v-if="statistique.last_b2c_new_signup && statistique.last_b2c_new_signup !=0">{{ statistique.last_b2c_new_signup > 0 ? ((statistique.b2c_new_signup*100)/statistique.last_b2c_new_signup).toFixed(0) + '%' : '--'}}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">APP</div>
                                            <div class="col-2 float-right width-fit-content">{{ statistique.app_new_signup }}</div>
                                            <i v-if="statistique.app_new_signup >= statistique.last_app_new_signup && statistique.last_app_new_signup && statistique.last_app_new_signup !=0" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <i v-else-if="statistique.last_app_new_signup && statistique.last_app_new_signup !=0" class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-1 px-0 float-right" v-if="statistique.last_app_new_signup && statistique.last_app_new_signup !=0">{{ ((statistique.app_new_signup*100)/statistique.last_app_new_signup).toFixed(0) }}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-stat position-relative mt-10">
                        <div class="row pb4">
                            <div class="col-sm-8">
                                <div class="container-fluid col-12 bg-white py-4 mb-3">
                                    <div class="row body_bold">
                                        <div class="col-8">Total Items (by service)</div>
                                        <div class="col-2 float-right">£{{ statistique.services_group_total }}</div>
                                        <i v-if="statistique.services_group_total >= statistique.last_services_group_total" class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                        <i v-else class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                        <div class="col-1 px-0 float-right">{{ (statistique.services_group_total*100)/statistique.last_services_group_total }}%</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row chart-title-row">
                                            <div class="col-2"></div>
                                            <div class="col-4 pl-0">TOTAL</div>
                                            <div class="col-2 text-align-center pl-0">Stores</div>
                                            <div class="col-2 text-align-center pl-0">App</div>
                                            <div class="col-2 text-align-center pl-0">B2B</div>
                                        </div>
                                        <div
                                            v-for="(group, i) in statistique.services_group"
                                            class="row chart-row align-items-center"
                                            id="stats_today"
                                            :key="i"
                                        >
                                            <div
                                                class="col-2 text-right each-dept-name pl-0"
                                            >{{ group.DepartmentName }}</div>
                                            <div class="col-4">
                                                <div class="row">
                                                    <!-- <div
                                                        v-if="a > 0"
                                                        class="chart-bar"
                                                        :style="{
                                                            width: parseInt(a) * parseFloat(width_scale) +'%',
                                                            background: top_3_today[i]? top_3_today[i]: '#e7e7e7',
                                                        }"
                                                    ></div> -->
                                                    <!-- <div
                                                        class="col-2 today-count"
                                                        :class="{' pl-0': group.total === 0,}"
                                                    >{{  }}</div> -->
                                                </div>
                                            </div>
                                            <div
                                                class="col-2 text-align-center each-dept-name pl-0"
                                            >{{ group.total.toFixed(0) }}</div>
                                            <div
                                                class="col-2 text-align-center each-dept-name pl-0"
                                            >{{ group.total.toFixed(0) }}</div>
                                            <div
                                                class="col-2 text-align-center each-dept-name pl-0"
                                            >{{ group.total.toFixed(0) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 d-none">
                                <div class="container-fluid col-12 bg-white py-4 mb-3">
                                    <div class="row body_bold">
                                        <div class="col-8">Key figures</div>
                                    </div>
                                    <div class="row">
                                        <div class="row">
                                            <div class="col-5">YoY sales Growth</div>
                                            <div class="col-3 float-right width-fit-content">22</div>
                                            <!-- <i class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div> -->
                                        </div>
                                        <div class="row">
                                            <div class="col-5">CLV</div>
                                            <div class="col-3 float-right width-fit-content">£136</div>
                                            <!-- <i class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div> -->
                                        </div>
                                        <div class="row">
                                            <div class="col-5">CAC</div>
                                            <div class="col-3 float-right width-fit-content">£10</div>
                                            <!-- <i class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div> -->
                                        </div>
                                        <div class="row">
                                            <div class="col-5">Total cust.</div>
                                            <div class="col-3 float-right width-fit-content">23,000</div>
                                            <!-- <i class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div> -->
                                        </div>
                                        <div class="row">
                                            <div class="col-5">Total active cust.</div>
                                            <div class="col-3 float-right width-fit-content">15,000</div>
                                            <!-- <i class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
import SideBar from "../layout/SideBar";
import MainHeader from "../layout/MainHeader";
import { ref, watch , computed} from "vue";
import { useRouter, useRoute } from "vue-router";
import { useStore, mapActions } from "vuex";
import {
    STATISTICS_MODULE,
    GET_STATISTICS,
    LOADER_MODULE,
    DISPLAY_LOADER,
    HIDE_LOADER,
    STATISTICS_LOAD_LIST,
} from "../../store/types/types";
import StatisticsFilters from "../miscellaneous/StatisticsFilters";

export default {
    name: "Statistics", 
    components: { SideBar, MainHeader, StatisticsFilters },
    setup(props,context){
        const store = useStore();
        const statistique = ref({});
        const stats_today = ref({});
        const top_3_today = ref({});
        const width_scale = ref(0);
        const selectedValue = ref("");
        const filterVal = ref({});
        const today = new Date();
        filterVal.value = {
            customFilter: 0,
            startDate: `${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()}`,
            endDate:`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()}`,
            dateRangeType:'Today'
        };
        const router = useRouter();
        const route = useRoute();
        const route_name = ref(route.name);
        watch(() => route.name, (current_val, previous_val) => {
                route_name.value = current_val;
            }
        );
        watch(() => filterVal.value, (current_val, previous_val) => {
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading data...']);
            store.dispatch(`${STATISTICS_MODULE}${STATISTICS_LOAD_LIST}`, current_val)
                .then((response) => {
                    console.log(response.data);
                    statistique.value = response.data.statistique;
                    // stats_today.value = response.data.statistique.stats_today;
                    // top_3_today.value = response.data.statistique.top_3_today;
                    // width_scale.value = response.data.statistique.scale;
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
        });
        store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading data...']);
        store
            .dispatch(`${STATISTICS_MODULE}${STATISTICS_LOAD_LIST}`, filterVal.value)
            .then((response) => {
                console.log(response.data);
                statistique.value = response.data.statistique;
                // stats_today.value = response.data.statistique.stats_today;
                // top_3_today.value = response.data.statistique.top_3_today;
                // width_scale.value = response.data.statistique.scale;                
                // console.log(response.data.statistique)
            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });
        return {
            statistics : computed(() => store.getters[`${STATISTICS_MODULE}${GET_STATISTICS}`]),
            selectedValue,
            filterVal,
            stats_today,
            top_3_today,
            width_scale,
            route_name,
            router,
            statistique
        };
    },

    methods: {
        ...mapActions({
            STATISTICS_LOAD_LIST: `${STATISTICS_MODULE}${STATISTICS_LOAD_LIST}`,
        }),
        onClickOutside (event) {

        }        
    }
};
</script>

<style scoped></style>
