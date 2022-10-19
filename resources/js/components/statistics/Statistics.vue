<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <div class="container-fluid h-100 bg-color">
            <main-header></main-header>
            <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax" style="z-index: 100">
                <side-bar></side-bar>
                <div class="col main-view p-0">
                    <h2 class="ms-0">Statistics</h2>
                    <div class="container-fluid orderlist-tabs d-flex align-items-center">
                        <!-- <div class="orderlist-tab body_bold" :class="{ 'active': tab == 'sales' }" @click="setTab('sales')">Sales</div> -->
                        <div class="orderlist-tab body_bold active" @click="setTab('production')">Sales V3</div>
                    </div>
                    <div class="container-stat position-relative mb-10 p-0">
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
            compareStartDate: `${today.getFullYear()}-${today.getMonth()}-${today.getDate()}`,
            compareEndDate: `${today.getFullYear()}-${today.getMonth()}-${today.getDate()}`,
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
<style scoped>
.main-view {
  margin: 63px 10px 0;
}
</style>
