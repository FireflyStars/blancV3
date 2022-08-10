<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <div class="container-fluid h-100 bg-color">
            <main-header></main-header>
            <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax" style="z-index: 100">
                <side-bar></side-bar>
                <div class="col main-view p-0">
                    <h2 class="ms-0">Statistics</h2>
                    <div class="container-fluid orderlist-tabs d-flex align-items-center">
                        <div class="orderlist-tab body_bold" :class="{ 'active': tab == 'sales' }" @click="setTab('sales')">Sales</div>
                        <div class="orderlist-tab body_bold" :class="{ 'active': tab == 'production' }" @click="setTab('production')">Sales V3</div>
                    </div>
                    <div class="container-stat position-relative mb-10" v-if="tab == 'sales'">
                        <div class="row">
                            <div class="col-10 mb-10">
                                <statisticsFilters :filterVal="filterVal" @update:filterVal="newValue => filterVal = newValue"></statisticsFilters>
                            </div>
                        </div>
                        <div class="row pb-4">
                            <div class="col-sm-3">
                                <div class="container-fluid col-12 px-2 bg-white py-4 mb-3">
                                    <div class="d-flex w-100 fw-bold flex-nowrap">
                                        <div class="col-8 p-0 text-nowrap">Total Sales (by channel)</div>
                                        <div class="col-2 float-right">£{{ stats.total_sales }} </div>
                                        <div class="col-2 d-flex flex-nowrap" v-if="stats.total_sales >= stats.last_total_sales && stats.last_total_sales > 0 && stats.last_total_sales">
                                            <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                            <span>{{ ( (stats.total_sales / stats.last_total_sales -1)*100 ).toFixed(0) +'%' }}</span>
                                        </div>
                                        <div class="col-2 d-flex flex-nowrap" v-if="stats.total_sales < stats.last_total_sales && stats.last_total_sales > 0 && stats.last_total_sales">
                                            <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                            <span>{{ ( (stats.total_sales / stats.last_total_sales -1)*100) .toFixed(0) +'%' }}</span>
                                        </div>
                                        <div class="col-2 d-flex flex-nowrap" v-if="stats.last_total_sales == 0 && stats.total_sales == 0">
                                            0%
                                        </div>
                                        <div class="col-2 d-flex flex-nowrap" v-if="stats.last_total_sales == 0">
                                            --
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap w-100">
                                        <div class="d-flex w-100 fst-itali">Stores</div>
                                        <div class="d-flex w-100">
                                            <div class="col-3 d-flex flex-nowrap justify-content-center" v-if="stats.mb >= stats.last_mb && stats.last_mb > 0 && stats.last_mb">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.mb / stats.last_mb - 1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 d-flex flex-nowrap justify-content-center" v-if="stats.mb < stats.last_mb && stats.last_mb > 0 && stats.last_mb">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.mb / stats.last_mb - 1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_mb == 0 && stats.mb == 0">
                                                <!-- <i class="bi bi-arrow-down red px-0 width-auto"></i> -->
                                                <span>0%</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_mb == 0 && stats.mb !=0">
                                                --
                                            </div>

                                            <div class="col-3 d-flex flex-nowrap justify-content-center" v-if="stats.nh >= stats.last_nh && stats.last_nh > 0 && stats.last_nh">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.nh / stats.last_nh -1 )*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 d-flex flex-nowrap justify-content-center" v-if="stats.nh < stats.last_nh && stats.last_nh > 0 && stats.last_nh">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.nh / stats.last_nh - 1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_nh == 0 && stats.nh ==0">
                                                <span>0%</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_nh == 0 && stats.nh !=0">
                                                --
                                            </div>

                                            <div class="col-3 d-flex flex-nowrap justify-content-center" v-if="stats.ch >= stats.last_ch && stats.last_ch > 0 && stats.last_ch">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.ch / stats.last_ch -1 )*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 d-flex flex-nowrap justify-content-center" v-if="stats.ch < stats.last_ch && stats.last_ch > 0 && stats.last_ch">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.ch / stats.last_ch -1 )*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_ch == 0 && stats.ch ==0">
                                                <span>0%</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_ch == 0 && stats.ch !=0">
                                                --
                                            </div>                                            

                                            <div class="col-3 d-flex flex-nowrap justify-content-center" v-if="stats.sk >= stats.last_sk && stats.last_sk > 0 && stats.last_sk">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.sk / stats.last_sk -1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 d-flex flex-nowrap justify-content-center" v-if="stats.sk < stats.last_sk && stats.last_sk > 0 && stats.last_sk">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.sk / stats.last_sk - 1 ) *100).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_sk == 0 && stats.sk ==0">
                                                <!-- <i class="bi bi-arrow-down red px-0 width-auto"></i> &nbsp; -->
                                                <span>0%</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_sk == 0 && stats.sk !=0">
                                                --
                                            </div>
                                        </div>
                                        <div class="d-flex w-100 text-center">
                                            <div class="first-bar col-3">£{{ stats.mb }} </div>
                                            <div class="second-bar col-3">£{{ stats.nh }}</div>
                                            <div class="third-bar col-3">£{{ stats.ch }}</div>
                                            <div class="fourth-bar col-3">£{{ stats.sk }}</div>
                                        </div>
                                        <div class="d-flex w-100 text-center">
                                            <div class="col-3">MB</div>
                                            <div class="col-3">NH</div>
                                            <div class="col-3">CH</div>
                                            <div class="col-3">SK</div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap w-100">
                                        <div class="d-flex w-100 fst-itali">Deliveries</div>
                                        <div class="d-flex w-100 text-center">
                                            <div class="col-6 d-flex flex-nowrap" v-if="stats.b2c >= stats.last_b2c && stats.last_b2c > 0 && stats.last_b2c">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.b2c / stats.last_b2c -1 )*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-6 d-flex flex-nowrap" v-if="stats.b2c < stats.last_b2c && stats.last_b2c > 0 && stats.last_b2c">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.b2c / stats.last_b2c -1 )*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-6 text-center" v-if="stats.last_b2c == 0 && stats.b2c ==0">
                                                <!-- <i class="bi bi-arrow-down red px-0 width-auto"></i> &nbsp; -->
                                                <span>0%</span>
                                            </div>
                                            <div class="col-6 text-center" v-if="stats.last_b2c == 0 && stats.b2c !=0">
                                                --
                                            </div>                                            

                                            <div class="col-6 d-flex flex-nowrap" v-if="stats.b2b >= stats.last_b2b && stats.last_b2b > 0 && stats.last_b2b">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.b2b / stats.last_b2b -1 )*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-6 d-flex flex-nowrap" v-if="stats.b2b < stats.last_b2b && stats.last_b2b > 0 && stats.last_b2b">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.b2b / stats.last_b2b - 1) *100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-6 text-center" v-if="stats.last_b2b == 0 && stats.b2b ==0">
                                                <!-- <i class="bi bi-arrow-down red px-0 width-auto"></i> &nbsp; -->
                                                <span>0%</span>
                                            </div>
                                            <div class="col-6 text-center" v-if="stats.last_b2b == 0 && stats.b2b !=0">
                                                --
                                            </div>
                                        </div>
                                        <div class="d-flex w-100 text-center">
                                            <div class="first-bar col-6">£{{ stats.b2c }}</div>
                                            <div class="fourth-bar col-6">£{{ stats.b2b }}</div>
                                        </div>
                                        <div class="d-flex w-100 text-center">
                                            <div class="col-6">B2C</div>
                                            <div class="col-6">B2B</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container-fluid col-12 px-2 bg-white py-4 mb-3">
                                    <div class="d-flex w-100 body_bold">
                                        <div class="col-8 text-nowrap">Average Order Value</div>
                                        <div class="col-2 float-right">£{{ stats.avg_total_order }}</div>
                                        <div class="col-2 d-flex flex-nowrap" v-if="stats.avg_total_order >= stats.last_avg_total_order && stats.last_avg_total_order > 0 && stats.last_avg_total_order">
                                            <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                            <span>{{ ( (stats.avg_total_order / stats.last_avg_total_order - 1)*100 ).toFixed(0) +'%' }}</span>
                                        </div>
                                        <div class="col-2 d-flex flex-nowrap" v-if="stats.avg_total_order < stats.last_avg_total_order && stats.last_avg_total_order > 0 && stats.last_avg_total_order">
                                            <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                            <span>{{ ( (stats.avg_total_order / stats.last_avg_total_order - 1)*100 ).toFixed(0) +'%' }}</span>
                                        </div>
                                        <div class="col-2 text-center" v-if="stats.last_avg_total_order == 0 && stats.avg_total_order ==0">
                                            <!-- <i class="bi bi-arrow-down red px-0 width-auto"></i> &nbsp; -->
                                            <span>0%</span>
                                        </div>
                                        <div class="col-2 text-center" v-if="stats.last_avg_total_order == 0 && stats.avg_total_order !=0">
                                            --
                                        </div>
                                    </div>
                                    <div class="d-flex w-100 flex-wrap">
                                        <div class="d-flex w-100">
                                            <div class="col-5">Stores</div>
                                            <div class="col-4 float-right width-fit-content">£{{ stats.avg_store_order }}</div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.avg_store_order >= stats.last_avg_store_order && stats.last_avg_store_order > 0 && stats.last_avg_store_order">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.avg_store_order / stats.last_avg_store_order -1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.avg_store_order < stats.last_avg_store_order && stats.last_avg_store_order > 0 && stats.last_avg_store_order">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.avg_store_order / stats.last_avg_store_order -1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_avg_store_order == 0 && stats.avg_store_order ==0">
                                                <!-- <i class="bi bi-arrow-down red px-0 width-auto"></i> &nbsp; -->
                                                <span>0%</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_avg_store_order == 0 && stats.avg_store_order !=0">
                                                --
                                            </div>
                                        </div>
                                        <div class="d-flex w-100">
                                            <div class="col-5">Delivery</div>
                                            <div class="col-4 float-right width-fit-content">£{{ stats.avg_delivery_order }}</div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.avg_delivery_order >= stats.last_avg_delivery_order && stats.last_avg_delivery_order > 0 && stats.last_avg_delivery_order">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.avg_delivery_order / stats.last_avg_delivery_order - 1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.avg_delivery_order < stats.last_avg_delivery_order && stats.last_avg_delivery_order > 0 && stats.last_avg_delivery_order">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.avg_delivery_order / stats.last_avg_delivery_order - 1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_avg_delivery_order == 0 && stats.avg_delivery_order ==0">
                                                <!-- <i class="bi bi-arrow-down red px-0 width-auto"></i> &nbsp; -->
                                                <span>0%</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_avg_delivery_order == 0 && stats.avg_delivery_order !=0">
                                                --
                                            </div>                                            
                                        </div>
                                        <div class="d-flex w-100">
                                            <div class="col-5">B2B</div>
                                            <div class="col-4 float-right width-fit-content">£{{ stats.avg_b2b_order }}</div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.avg_b2b_order >= stats.last_avg_b2b_order && stats.last_avg_b2b_order > 0 && stats.last_avg_b2b_order">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.avg_b2b_order / stats.last_avg_b2b_order- 1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.avg_b2b_order < stats.last_avg_b2b_order && stats.last_avg_b2b_order > 0 && stats.last_avg_b2b_order">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.avg_b2b_order / stats.last_avg_b2b_order-1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_avg_b2b_order == 0 && stats.avg_b2b_order ==0">
                                                <!-- <i class="bi bi-arrow-down red px-0 width-auto"></i> &nbsp; -->
                                                <span>0%</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_avg_b2b_order == 0 && stats.avg_b2b_order !=0">
                                                --
                                            </div>
                                        </div>
                                        <div class="d-flex w-100">
                                            <div class="col-5">B2C</div>
                                            <div class="col-4 float-right width-fit-content">£{{ stats.avg_b2c_order }}</div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.avg_b2c_order >= stats.last_avg_b2c_order && stats.last_avg_b2c_order > 0 && stats.last_avg_b2c_order">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.avg_b2c_order / stats.last_avg_b2c_order-1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.avg_b2c_order < stats.last_avg_b2c_order && stats.last_avg_b2c_order > 0 && stats.last_avg_b2c_order">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.avg_b2c_order / stats.last_avg_b2c_order -1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_avg_b2c_order == 0 && stats.avg_b2c_order ==0">
                                                <!-- <i class="bi bi-arrow-down red px-0 width-auto"></i> &nbsp; -->
                                                <span>0%</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_avg_b2c_order == 0 && stats.avg_b2c_order !=0">
                                                --
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 p-0">
                                <div class="container-fluid col-12 px-2 bg-white py-4 mb-3">
                                    <div class="row body_bold">
                                        <div class="col-4">Services</div>
                                        <div class="col-4 text-center">Sales (£)</div>
                                        <div class="col-4 text-center">Sales (# items)</div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-4"></div>
                                        <div class="col-4 d-flex">
                                            <div class="col-6">Stores</div>
                                            <div class="col-6">Deliveries</div>
                                        </div>
                                        <div class="col-4 d-flex">
                                            <div class="col-6">Stores</div>
                                            <div class="col-6">Deliveries</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4 border-bottom">
                                            <p class="mb-0" v-for="(item, index) in stats.dep_list" :key="index">
                                                {{ item.DepartmentName }}
                                            </p>
                                        </div>
                                        <div class="col-4 d-flex border-bottom">
                                            <div class="col-6">
                                                <p class="mb-0" v-for="(item, index) in stats.sale_store_by_dep" :key="index">
                                                    {{ item }}
                                                </p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-0" v-for="(item, index) in stats.sale_delivery_by_dep" :key="index">
                                                    {{ item }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-4 d-flex border-bottom">
                                            <div class="col-6">
                                                <p class="mb-0" v-for="(item, index) in stats.item_store_by_dep" :key="index">
                                                    {{ item }}
                                                </p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-0" v-for="(item, index) in stats.item_delivery_by_dep" :key="index">
                                                    {{ item }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4 fw-bold">
                                            Total
                                        </div>
                                        <div class="col-4 d-flex">
                                            <div class="col-6">{{ stats.total_sale_store_by_dep }}</div>
                                            <div class="col-6">{{ stats.total_sale_delivery_by_dep }}</div>
                                        </div>
                                        <div class="col-4 d-flex">
                                            <div class="col-6">{{ stats.total_item_store_by_dep }}</div>
                                            <div class="col-6">{{ stats.total_item_delivery_by_dep }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="container-fluid col-12 px-2 bg-white py-4 mb-3">
                                    <div class="d-flex w-100 flex-wrap">
                                        <div class="d-flex w-100">
                                            <div class="col-6 p-0 text-nowrap">Booking</div>
                                            <div class="col-3 float-right">{{ stats.total_booking }}</div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.total_booking >= stats.last_total_booking && stats.last_total_booking > 0 && stats.last_total_booking">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.total_booking / stats.last_total_booking-1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.total_booking < stats.last_total_booking && stats.last_total_booking > 0 && stats.last_total_booking">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.total_booking / stats.last_total_booking-1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_total_booking == 0 && stats.total_booking == 0">
                                                <!-- <i class="bi bi-arrow-down red px-0 width-auto"></i> -->
                                                <span>0%</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_total_booking == 0 && stats.total_booking !=0">
                                                --
                                            </div>
                                        </div>
                                        <div class="d-flex w-100 text-center">
                                            <div class="col-4">
                                                <div class="col-12 d-flex flex-nowrap" v-if="stats.app_booking >= stats.last_app_booking && stats.last_app_booking > 0 && stats.last_app_booking">
                                                    <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                    <span>{{ ( (stats.app_booking / stats.last_app_booking-1)*100 ).toFixed(0) +'%' }}</span>
                                                </div>
                                                <div class="col-12 d-flex flex-nowrap" v-if="stats.app_booking < stats.last_app_booking && stats.last_app_booking > 0 && stats.last_app_booking">
                                                    <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                    <span>{{ ( (stats.app_booking / stats.last_app_booking-1)*100 ).toFixed(0) +'%' }}</span>
                                                </div>
                                                <div class="col-12 text-center" v-if="stats.last_app_booking == 0 && stats.app_booking == 0">
                                                    <span>0%</span>
                                                </div>
                                                <div class="col-12 text-center" v-if="stats.last_app_booking == 0 && stats.app_booking !=0">
                                                    --
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="col-12 d-flex flex-nowrap" v-if="stats.pos_booking >= stats.last_pos_booking && stats.last_pos_booking > 0 && stats.last_pos_booking">
                                                    <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                    <span>{{ ( (stats.pos_booking / stats.last_pos_booking-1)*100 ).toFixed(0) +'%' }}</span>
                                                </div>
                                                <div class="col-12 d-flex flex-nowrap" v-if="stats.pos_booking < stats.last_pos_booking && stats.last_pos_booking > 0 && stats.last_pos_booking">
                                                    <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                    <span>{{ ( (stats.pos_booking / stats.last_pos_booking-1)*100 ).toFixed(0) +'%' }}</span>
                                                </div>
                                                <div class="col-12 text-center" v-if="stats.last_pos_booking == 0 && stats.pos_booking == 0">
                                                    <span>0%</span>
                                                </div>
                                                <div class="col-12 text-center" v-if="stats.last_pos_booking == 0 && stats.pos_booking !=0">
                                                    --
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="col-12 d-flex flex-nowrap" v-if="stats.rec_booking >= stats.last_rec_booking && stats.last_rec_booking > 0 && stats.last_rec_booking">
                                                    <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                    <span>{{ ( (stats.rec_booking / stats.last_rec_booking-1)*100 ).toFixed(0) +'%' }}</span>
                                                </div>
                                                <div class="col-12 d-flex flex-nowrap" v-if="stats.rec_booking < stats.last_rec_booking && stats.last_rec_booking > 0 && stats.last_rec_booking">
                                                    <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                    <span>{{ ( (stats.rec_booking / stats.last_rec_booking-1)*100 ).toFixed(0) +'%' }}</span>
                                                </div>
                                                <div class="col-12 text-center" v-if="stats.last_rec_booking == 0 && stats.rec_booking == 0">
                                                    <span>0%</span>
                                                </div>
                                                <div class="col-12 text-center" v-if="stats.last_rec_booking == 0 && stats.rec_booking !=0">
                                                    --
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex w-100 text-center">
                                            <div class="first-bar col-4">{{ stats.app_booking }}</div>
                                            <div class="second-bar col-4">{{ stats.pos_booking }}</div>
                                            <div class="fourth-bar col-4">{{ stats.rec_booking }}</div>
                                        </div>
                                        <div class="d-flex w-100 text-center">
                                            <div class="col-4">APP</div>
                                            <div class="col-4">POS</div>
                                            <div class="col-4">RECURING</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container-fluid col-12 px-2 bg-white py-4 mb-3">
                                    <div class="d-flex w-100 body_bold">
                                        <div class="col-6">New Sign Ups</div>
                                        <div class="col-3 float-right width-fit-content">{{ stats.total_new_signup }}</div>
                                        <div class="col-3 d-flex flex-nowrap" v-if="stats.total_new_signup >= stats.last_total_new_signup && stats.last_total_new_signup > 0 && stats.last_total_new_signup">
                                            <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                            <span>{{ ( (stats.total_new_signup / stats.last_total_new_signup-1)*100 ).toFixed(0) +'%' }}</span>
                                        </div>
                                        <div class="col-3 d-flex flex-nowrap" v-if="stats.total_new_signup < stats.last_total_new_signup && stats.last_total_new_signup > 0 && stats.last_total_new_signup">
                                            <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                            <span>{{ ( (stats.total_new_signup / stats.last_total_new_signup-1)*100 ).toFixed(0) +'%' }}</span>
                                        </div>
                                        <div class="col-3 text-center" v-if="stats.last_total_new_signup == 0 && stats.total_new_signup ==0">
                                            <span>0%</span>
                                        </div>
                                        <div class="col-3 text-center" v-if="stats.last_total_new_signup == 0 && stats.total_new_signup !=0">
                                            --
                                        </div>                                        
                                    </div>
                                    <div class="d-flex w-100 flex-wrap">
                                        <div class="d-flex w-100">
                                            <div class="col-6">Stores</div>
                                            <div class="col-3 float-right width-fit-content">{{ stats.stores_new_signup }}</div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.stores_new_signup >= stats.last_stores_new_signup && stats.last_stores_new_signup > 0 && stats.last_stores_new_signup">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.stores_new_signup / stats.last_stores_new_signup-1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.stores_new_signup < stats.last_stores_new_signup && stats.last_stores_new_signup > 0 && stats.last_stores_new_signup">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.stores_new_signup / stats.last_stores_new_signup-1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_stores_new_signup == 0 && stats.stores_new_signup ==0">
                                                <span>0%</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_stores_new_signup == 0 && stats.stores_new_signup !=0">
                                                --
                                            </div>                                            
                                        </div>
                                        <div class="d-flex w-100">
                                            <div class="col-6">Delivery</div>
                                            <div class="col-3 float-right width-fit-content">{{ stats.delivery_new_signup }}</div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.delivery_new_signup >= stats.last_delivery_new_signup && stats.last_delivery_new_signup > 0 && stats.last_delivery_new_signup">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.delivery_new_signup / stats.last_delivery_new_signup-1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.delivery_new_signup < stats.last_delivery_new_signup && stats.last_delivery_new_signup > 0 && stats.last_delivery_new_signup">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.delivery_new_signup / stats.last_delivery_new_signup-1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_delivery_new_signup == 0 && stats.delivery_new_signup ==0">
                                                <span>0%</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_delivery_new_signup == 0 && stats.delivery_new_signup !=0">
                                                --
                                            </div>
                                        </div>
                                        <div class="d-flex w-100">
                                            <div class="col-6">B2B</div>
                                            <div class="col-3 float-right width-fit-content">{{ stats.b2b_new_signup }}</div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.b2b_new_signup >= stats.last_b2b_new_signup && stats.last_b2b_new_signup > 0 && stats.last_b2b_new_signup">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.b2b_new_signup / stats.last_b2b_new_signup-1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.b2b_new_signup < stats.last_b2b_new_signup && stats.last_b2b_new_signup > 0 && stats.last_b2b_new_signup">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.b2b_new_signup / stats.last_b2b_new_signup-1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_b2b_new_signup == 0 && stats.b2b_new_signup ==0">
                                                <span>0%</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_b2b_new_signup == 0 && stats.b2b_new_signup !=0">
                                                --
                                            </div>                                            
                                        </div>
                                        <div class="d-flex w-100">
                                            <div class="col-6">B2C</div>
                                            <div class="col-3 float-right width-fit-content">{{ stats.b2c_new_signup }}</div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.b2c_new_signup >= stats.last_b2c_new_signup && stats.last_b2c_new_signup > 0 && stats.last_b2c_new_signup">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.b2c_new_signup / stats.last_b2c_new_signup-1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.b2c_new_signup < stats.last_b2c_new_signup && stats.last_b2c_new_signup > 0 && stats.last_b2c_new_signup">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.b2c_new_signup / stats.last_b2c_new_signup-1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_b2c_new_signup == 0 && stats.b2c_new_signup ==0">
                                                <span>0%</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_b2c_new_signup == 0 && stats.b2c_new_signup !=0">
                                                --
                                            </div>                                            
                                        </div>
                                        <div class="d-flex w-100">
                                            <div class="col-6">APP</div>
                                            <div class="col-3 float-right width-fit-content">{{ stats.app_new_signup }}</div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.app_new_signup >= stats.last_app_new_signup && stats.last_app_new_signup > 0 && stats.last_app_new_signup">
                                                <i class="bi bi-arrow-up green px-0 width-auto"></i>
                                                <span>{{ ( (stats.app_new_signup / stats.last_app_new_signup-1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 d-flex flex-nowrap" v-if="stats.app_new_signup < stats.last_app_new_signup && stats.last_app_new_signup > 0 && stats.last_app_new_signup">
                                                <i class="bi bi-arrow-down red px-0 width-auto"></i>
                                                <span>{{ ( (stats.app_new_signup / stats.last_app_new_signup -1)*100 ).toFixed(0) +'%' }}</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_app_new_signup == 0 && stats.app_new_signup ==0">
                                                <span>0%</span>
                                            </div>
                                            <div class="col-3 text-center" v-if="stats.last_app_new_signup == 0 && stats.app_new_signup !=0">
                                                --
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-stat position-relative mb-10 p-0" v-else>
                        <chart></chart>
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
import Chart from "./Chart.vue";

export default {
    name: "Statistics", 
    components: { SideBar, MainHeader, StatisticsFilters, Chart },
    setup(props,context){
        const store = useStore();
        const stats = ref({});
        const stats_today = ref({});
        const top_3_today = ref({});
        const width_scale = ref(0);
        const selectedValue = ref("");
        const tab = ref('production');
        const filterVal = ref({});
        const today = new Date();
        filterVal.value = {
            customFilter: 0,
            startDate: `${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()}`,
            endDate:`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()}`,
            dateRangeType:'Last Month',
            compareMode: 'month',
            compareCustomFilter: false,
            compareStartDate: `${today.getFullYear()-1}-${today.getMonth()+1}-${today.getDate()}`,
            compareEndDate: `${today.getFullYear()-1}-${today.getMonth()+1}-${today.getDate()}`,
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
                    stats.value = response.data.stats;
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
        });
        store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading data...']);
        store.dispatch(`${STATISTICS_MODULE}${STATISTICS_LOAD_LIST}`, filterVal.value).then((response) => {
            stats.value = response.data.stats;
        }).finally(()=>{
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
        });
        const setTab = (tabValue)=>{
            tab.value = tabValue;
        }
        return {
            statistics : computed(() => store.getters[`${STATISTICS_MODULE}${GET_STATISTICS}`]),
            selectedValue,
            filterVal,
            stats_today,
            top_3_today,
            width_scale,
            route_name,
            router,
            stats,
            tab,
            setTab
        };
    },

    methods: {
        ...mapActions({
            STATISTICS_LOAD_LIST: `${STATISTICS_MODULE}${STATISTICS_LOAD_LIST}`,
        }),
    }
};
</script>

<style>
.main-view {
  margin: 63px 10px 0;
}
</style>
