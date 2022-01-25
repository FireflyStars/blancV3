<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <div class="container-fluid h-100 bg-color">
            <main-header></main-header>
            <div
                class="row d-flex align-content-stretch align-items-stretch flex-row hmax"
                style="z-index: 100"
            >
                <side-bar></side-bar>
                <div class="col main-view p-5">
                    <h2>Statistics</h2>
                    <div class="container-fluid orderlist-tabs d-flex align-items-center">
                        <div class="orderlist-tab body_bold active">Sales</div>
                    </div>
                    <div class="container-stat position-relative mb-10">
                        <div class="row">
                            <div class="col-10 mb-10">
                                <statisticsFilters :filterVal="filterVal"></statisticsFilters>
                            </div>
                        </div>
                        <div class="row pb-4">
                            <div class="col-sm-4">
                                <div class="container-fluid col-12 bg-white py-4 mb-3">
                                    <div class="row text-bold">
                                        <div class="col-8">Total Sales (by channel)</div>
                                        <div class="col-2 float-right">£200</div>
                                        <i class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                        <div class="col-1 px-0 float-right">50%</div>
                                    </div>

                                    <div class="row">
                                        <div class="row text-bold">Stores</div>
                                        <div class="bar-progress">
                                            <div class="first-bar">
                                                <span>
                                                    £200
                                                    <!-- <i class="icon-arrow-white-up"></i> -->
                                                </span>
                                            </div>
                                            <div class="second-bar">£200</div>
                                            <div class="third-bar">£200</div>
                                            <div class="fourth-bar">£200</div>
                                        </div>
                                        <div class="row text-center text-under-bar">
                                            <div class="label-under-bar">MB</div>
                                            <div class="label-under-bar">NH</div>
                                            <div class="label-under-bar">CH</div>
                                            <div class="label-under-bar">SK</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="row text-bold">Deliveries</div>
                                        <div class="bar-progress">
                                            <div class="first-bar">£200</div>
                                            <div class="fourth-bar">£200</div>
                                        </div>
                                        <div class="row col-7 text-center text-under-bar">
                                            <div class="label-under-bar">B2C</div>
                                            <div class="label-under-bar">B2B</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container-fluid col-12 bg-white py-4 mb-3">
                                    <div class="row body_bold">
                                        <div class="col-8">Average Order Value</div>
                                        <div class="col-2 float-right">£200</div>
                                        <i class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                        <div class="col-1 px-0 float-right">50%</div>
                                    </div>
                                    <div class="row">
                                        <div class="row">
                                            <div class="col-5">Stores</div>
                                            <div class="col-2 float-right width-fit-content">£200</div>
                                            <i class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">Delivery</div>
                                            <div class="col-2 float-right width-fit-content">£200</div>
                                            <i class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">B2B</div>
                                            <div class="col-2 float-right width-fit-content">£200</div>
                                            <i class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">B2C</div>
                                            <div class="col-2 float-right width-fit-content">£200</div>
                                            <i class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="container-fluid col-12 bg-white py-4 mb-3">
                                    <div class="row body_bold">
                                        <div class="col-8">Total Items (by service)</div>
                                        <div class="col-2 float-right">£200</div>
                                        <i class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                        <div class="col-1 px-0 float-right">50%</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row chart-title-row">
                                            <div class="col-6 text-align-right">Services</div>
                                            <div class="col-6 text-align-center pl-0">Today</div>
                                        </div>
                                        <div
                                            v-for="(a, i) in stats_today"
                                            class="row chart-row align-items-center"
                                            id="stats_today"
                                        >
                                            <div
                                                class="col-6 text-right each-dept-name pl-0"
                                            >{{ i }}</div>
                                            <div class="col-6">
                                                <div class="row">
                                                    <div
                                                        v-if="a > 0"
                                                        class="chart-bar"
                                                        :style="{
                                                            width:
                                                                parseInt(a) *
                                                                parseFloat(
                                                                    width_scale
                                                                ) +
                                                                '%',
                                                            background:
                                                                top_3_today[i]
                                                                    ? top_3_today[
                                                                    i
                                                                    ]
                                                                    : '#e7e7e7',
                                                        }"
                                                    ></div>
                                                    <div
                                                        class="col-2 today-count"
                                                        :class="{
                                                            ' pl-0': a === 0,
                                                        }"
                                                    >{{ a }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="container-fluid col-12 bg-white py-4 mb-3">
                                    <div class="row text-bold">
                                        <div class="col-8">Sales from 1st time Customers</div>
                                        <div class="col-2 float-right">£200</div>
                                        <i class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                        <div class="col-1 px-0 float-right">50%</div>
                                    </div>

                                    <div class="row">
                                        <div class="row text-bold">Stores</div>
                                        <div class="bar-progress">
                                            <div class="first-bar">£200</div>
                                            <div class="second-bar">£200</div>
                                            <div class="third-bar">£200</div>
                                            <div class="fourth-bar">£200</div>
                                        </div>
                                        <div class="row col-10 text-center text-under-bar">
                                            <div class="label-under-bar">MB</div>
                                            <div class="label-under-bar">NH</div>
                                            <div class="label-under-bar">CH</div>
                                            <div class="label-under-bar">SK</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="row text-bold">Deliveries</div>
                                        <div class="bar-progress">
                                            <div class="first-bar">£200</div>
                                            <div class="second-bar">£200</div>
                                            <div class="fourth-bar">£200</div>
                                        </div>
                                        <div class="row col-10 text-center text-under-bar">
                                            <div class="label-under-bar">APP</div>
                                            <div class="label-under-bar">CC</div>
                                            <div class="label-under-bar">B2B</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container-fluid col-12 bg-white py-4 mb-3">
                                    <div class="row body_bold">
                                        <div class="col-8">New Sign Ups</div>
                                        <div class="col-2 float-right">£200</div>
                                        <i class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                        <div class="col-1 px-0 float-right">50%</div>
                                    </div>
                                    <div class="row">
                                        <div class="row">
                                            <div class="col-5">Stores</div>
                                            <div class="col-2 float-right width-fit-content">22</div>
                                            <i class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">Delivery</div>
                                            <div class="col-2 float-right width-fit-content">136</div>
                                            <i class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">B2B</div>
                                            <div class="col-2 float-right width-fit-content">10</div>
                                            <i class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">B2C</div>
                                            <div class="col-2 float-right width-fit-content">1</div>
                                            <i class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div>
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
                                        <div class="col-2 float-right">£200</div>
                                        <i class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                        <div class="col-1 px-0 float-right">50%</div>
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
                                            v-for="(a, i) in stats_today"
                                            class="row chart-row align-items-center"
                                            id="stats_today"
                                        >
                                            <div
                                                class="col-2 text-right each-dept-name pl-0"
                                            >{{ i }}</div>
                                            <div class="col-4">
                                                <div class="row">
                                                    <div
                                                        v-if="a > 0"
                                                        class="chart-bar"
                                                        :style="{
                                                            width:
                                                                parseInt(a) *
                                                                parseFloat(
                                                                    width_scale
                                                                ) +
                                                                '%',
                                                            background:
                                                                top_3_today[i]
                                                                    ? top_3_today[
                                                                    i
                                                                    ]
                                                                    : '#e7e7e7',
                                                        }"
                                                    ></div>
                                                    <div
                                                        class="col-2 today-count"
                                                        :class="{
                                                            ' pl-0': a === 0,
                                                        }"
                                                    >{{ a }}</div>
                                                </div>
                                            </div>
                                            <div
                                                class="col-2 text-align-center each-dept-name pl-0"
                                            >{{ a }}</div>
                                            <div
                                                class="col-2 text-align-center each-dept-name pl-0"
                                            >{{ a }}</div>
                                            <div
                                                class="col-2 text-align-center each-dept-name pl-0"
                                            >{{ a }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="container-fluid col-12 bg-white py-4 mb-3">
                                    <div class="row body_bold">
                                        <div class="col-8">Key figures</div>
                                    </div>
                                    <div class="row">
                                        <div class="row">
                                            <div class="col-5">YoY sales Growth</div>
                                            <div class="col-3 float-right width-fit-content">22</div>
                                            <i class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">CLV</div>
                                            <div class="col-3 float-right width-fit-content">£136</div>
                                            <i class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">CAC</div>
                                            <div class="col-3 float-right width-fit-content">£10</div>
                                            <i class="col-1 bi bi-arrow-up green px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">Total cust.</div>
                                            <div class="col-3 float-right width-fit-content">23,000</div>
                                            <i class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">Total active cust.</div>
                                            <div class="col-3 float-right width-fit-content">15,000</div>
                                            <i class="col-1 bi bi-arrow-down red px-0 width-auto"></i>
                                            <div class="col-2 px-0 float-right">50%</div>
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
import { ref, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useStore } from "vuex";
import {
    STATISTICS_MODULE,
    STATISTICS_LOAD_LIST,
} from "../../store/types/types";
import StatisticsFilters from "../miscellaneous/StatisticsFilters";
export default {
    name: "Statistics",
    components: { SideBar, MainHeader, StatisticsFilters },
    setup(props,context){
        const store = useStore();
        const stats_today = ref({});
        const top_3_today = ref({});
        const width_scale = ref(0);
        const selectedValue = ref("");
        const filterVal = ref({});
        filterVal.value = {
            customFilter:'',
            startDate:'',
            endDate:'',
            dateRangeType:''
        };
        const router = useRouter();
        const route = useRoute();
        const route_name = ref(route.name);
        watch(
            () => route.name,
            (current_val, previous_val) => {
                route_name.value = current_val;
            }
        );
        watch(() => filterVal.value, (current_val, previous_val) => {
            console.log('filterVal', current_val)
        });
        store
            .dispatch(`${STATISTICS_MODULE}${STATISTICS_LOAD_LIST}`, filterVal.value)
            .then((response) => {
                stats_today.value = response.data.stats_today;
                top_3_today.value = response.data.top_3_today;
                width_scale.value = response.data.scale;
            });
        return {
            selectedValue,
            filterVal,
            stats_today,
            top_3_today,
            width_scale,
            route_name,
            router,
        };
    },
};
</script>

<style scoped></style>
