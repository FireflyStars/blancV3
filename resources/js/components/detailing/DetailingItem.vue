<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <div class="container-fluid h-100 bg-color">
            <main-header></main-header>
            <div
                class="row d-flex align-content-stretch align-items-stretch flex-row hmax"
                style="z-index: 100"
            >
                <side-bar></side-bar>
                <div class="col main">
                    <bread-crumb :paths="paths"></bread-crumb>
                    <button class="pause-detailing-btn" @click="pauseDetailling">Pause detailing</button>
                    <div class="row">
                        <div class="col-8 left-panel">
                            <div class="new-order-text">New order n°{{ order_id }}</div>
                            <h2 class="subtitle">Choose item type</h2>
                            <div class="row">
                                <div class="col-6">
                                    <div class="label">What type of item is it?</div>
                                    <div class="position-relative input_search">
                                        <input
                                            type="text"
                                            ref="inputsearch"
                                            placeholder="Type the item name"
                                            v-model="search"
                                            @keyup.enter="submitSearch"
                                        />
                                        <span
                                            v-if="showbutton"
                                            @click="clearSearch"
                                            class="icon-close-position"
                                        >
                                            <i class="icon-close"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <detailing-departement
                                    :departements="departements"
                                    :itemDept="itemDept"
                                    @update-departement="chooseDepartement"
                                ></detailing-departement>
                            </div>
                        </div>
                        <detailing-right-panel
                            :customerName="customerName"
                            :item_price="item_price"
                            :item_id="item_id"
                            :step="step"
                        ></detailing-right-panel>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
<script>
import DetailingRightPanel from './DetailingRightPanel.vue';
import DetailingDepartement from './DetailingDepartement.vue';
import MainHeader from '../layout/MainHeader';
import BreadCrumb from '../layout/BreadCrumb';
import SideBar from '../layout/SideBar';
import { ref,onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useStore } from "vuex";
import {
    DETAILING_MODULE,
    INIT_DETAILING,
    UPDATE_DETAILING
} from "../../store/types/types";
import {LOADER_MODULE,SET_LOADER_MSG,DISPLAY_LOADER,HIDE_LOADER} from '../../store/types/types';

export default {
    name: "DetailingItem",
    components: { BreadCrumb, SideBar, MainHeader, DetailingRightPanel, DetailingDepartement },
    setup() {
        const router = useRouter();
        const route = useRoute();
        const store = useStore();
        const user = ref({});
        const search = ref({});
        const departements = ref({});
        const showSearch = ref(false);
        const showbutton = ref(false);
        const inputsearch = ref(null);
        const itemDept = ref(0);
        const order_id = ref(0);
        const item_id = ref(0);
        const detailingitem_id = ref(0);
        const detailingitem = ref({});
        const customerName = ref('');
        const item_price = ref(0);
        const step = ref(1);
        store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[true,'Please wait....']);

        order_id.value = route.params.order_id;
        item_id.value = route.params.item_id;
        const paths = ref([
            { name: 'All orders', route: 'LandingPage' },
            { name: 'Order n°' + order_id.value, route: 'NewOrder' },
            { name: 'Detailing item ' + item_id.value, route: 'DetailingItemType' }
        ]);
        watch(() => itemDept.value, (current_val, previous_val) => {
            itemDept.value = current_val;
        });
        store
            .dispatch(`${DETAILING_MODULE}${INIT_DETAILING}`, { detailingitem_id: detailingitem_id.value, order_id: order_id.value, item_id: item_id.value, search: "" })
            .then((response) => {
                user.value = response.data.user;
                search.value = response.data.search;
                departements.value = response.data.departements;
                customerName.value = response.data.user.name;
                detailingitem_id.value = response.data.detailingitem.id;
                detailingitem.value = response.data.detailingitem;
                itemDept.value = response.data.detailingitem.department_id;
                step.value = response.data.detailingitem.etape;

            })
            .finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });
        function submitSearch(e) {
            store
                .dispatch(`${DETAILING_MODULE}${INIT_DETAILING}`, { detailingitem_id: detailingitem_id.value, order_id: order_id.value, item_id: item_id.value, search: search.value })
                .then((response) => {
                    if (e.target.value) {
                        showSearch.value = true;
                        showbutton.value = true;
                        itemDept.value = response.data.searched_dept.dept_id;
                    } else {
                        showSearch.value = false;
                        showbutton.value = false;
                    }
                });

        };
        function clearSearch() {
            search.value = null;
            showSearch.value = false;
            showbutton.value = false;
        }
        
        function chooseDepartement(id) {
            itemDept.value = id;
            store.dispatch(`${DETAILING_MODULE}${UPDATE_DETAILING}`, { detailingitem_id: detailingitem_id.value, dept_id: itemDept.value })
                .then((response) => {
                    detailingitem.value = response.data.detailingitem;
                    itemDept.value = response.data.detailingitem.department_id;

                });
        }
        function pauseDetailling() {
            const status = detailingitem.value.status == 'In Process' ? 'Pause' : 'In Process'
            store.dispatch(`${DETAILING_MODULE}${UPDATE_DETAILING}`, { detailingitem_id: detailingitem_id.value, status: status })
                .then((response) => {
                    detailingitem.value = response.data.detailingitem;
                });

        }
        return {
            paths,
            user,
            search,
            departements,
            showSearch,
            inputsearch,
            showbutton,
            order_id,
            item_id,
            customerName,
            item_price,
            step,
            itemDept,
            detailingitem_id,
            detailingitem,
            submitSearch,
            clearSearch,
            chooseDepartement,
            pauseDetailling
        };
    },
}
</script>
<style scoped>
.container-fluid {
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 140%;
}
.main {
    padding: 50px 0px 50px 61px;
}
.breadcrumb {
    margin-bottom: 0px;
}
.left-panel {
    margin-top: 10px;
}
.new-order-text {
    display: flex;
    align-items: center;
    color: #868686;
    margin: 10px 0 10px 0;
}
/* Label*/
.label {
    height: 24px;
    font-size: 16px;
    line-height: 140%;
    color: #000000;
    margin: 10px 0 10px 0;
}
/* Search */
input {
    background: #f8f8f8 url(/images/search_gray.svg?9dab47b…) no-repeat center
        left 11px;
    height: 40px;
    padding-left: 45px;
    padding-right: 30px;
    width: 100%;
    height: 40px;
    margin-bottom: 12px;
    border: 0.5px solid #868686;
    border-radius: 4px;
}
input:focus-visible {
    outline: 0.5px #000000 solid;
    background-color: #ffffff;
}
.icon-close-position {
    position: absolute !important;
    top: 10px;
    right: 30px;
}
.icon-close:before {
    width: 16px;
    left: 4px;
    top: 2px;
}
.icon-close:after {
    width: 16px;
    top: 2px;
    left: -2px;
}
.pause-detailing-btn {
    padding: 10px;
    position: absolute;
    font-size: 16px !important;
    color: #000000;
    top: 75px;
    right: 15px;
    background: #ffffff;
    border: 1px solid #47454b;
    border-radius: 4px;
}
</style>
