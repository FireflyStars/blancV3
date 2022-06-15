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
                    <button
                        class="pause-detailing-btn"
                        @click="detailingitem.status == 'In Process'?show_pause_popup = true:pauseDetailling()"
                        v-if="detailingitem.status == 'In Process' || detailingitem.status == 'Pause'"
                    >{{ detailingitem.status == 'In Process' ? 'Pause detailing' : 'Resume detailing' }}</button>
                    <div class="row container-detailing">
                        <div class="col-8 left-panel">
                            <div class="new-order-text">New order n°{{ order_id }}</div>
                            <h2 class="subtitle" v-if="detailingitem.etape === 1">Choose item type</h2>
                            <h2 class="subtitle" v-else-if="detailingitem.etape === 2">Describe item</h2>
                            <h2
                                class="subtitle"
                                v-else-if="[3, 4, 5, 6, 7, 8].includes(detailingitem.etape)"
                            >Describe {{ item_description.typeitem_name }}</h2>
                            <h2
                                class="subtitle"
                                v-else-if="detailingitem.etape === 9"
                            >Select item complexities</h2>
                            <h2
                                class="subtitle"
                                v-else-if="detailingitem.etape === 10"
                            >Describe item issuses</h2>
                            <h2 class="subtitle mb-5" v-else-if="detailingitem.etape==11">
                                Select the desired service(s)
                            </h2>
                            <div
                                class="row"
                                v-if="detailingitem.etape === 1 || detailingitem.etape === 2"
                            >
                                <div class="col-6">
                                    <div class="label">What type of item is it?</div>
                                    <div class="position-relative">
                                        <input
                                            type="text"
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
                                <detailing-item-departement
                                    v-if="detailingitem.etape === 1"
                                    :departements="detailingData.departements"
                                    :itemDept="itemDept"
                                    @update-departement="chooseDepartement"
                                ></detailing-item-departement>
                                <detailing-item-type
                                    v-else-if="detailingitem.etape === 2"
                                    :categories="detailingData.categories"
                                    :typeitems="detailingData.typeitems"
                                    :detailingitem="detailingitem"
                                    @save-type-item="saveItemDetails"
                                    @go-to-step="backPreviousStep"
                                ></detailing-item-type>
                                <detailing-item-description
                                    v-else-if="detailingitem.etape > 2 && detailingitem.etape <= 8"
                                    :detailingData="detailingData"
                                    :detailingitem="detailingitem"
                                    :item_description="item_description"
                                    @save-item-description="saveItemDetails"
                                    @go-to-step="backPreviousStep"
                                ></detailing-item-description>
                                <detailing-item-complexities
                                    v-if="detailingitem.etape === 9"
                                    :detailingData="detailingData"
                                    :detailingitem="detailingitem"
                                    @save-item-complexities="saveItemDetails"
                                    @go-to-step="backPreviousStep"
                                ></detailing-item-complexities>
                                <detailing-item-issues
                                    v-if="detailingitem.etape === 10"
                                    :detailingData="detailingData"
                                    :detailingitem="detailingitem"
                                    :typeitemPicto="item_description.typeitem_picto"
                                    @save-item-issues="saveItemDetails"
                                    @go-to-step="backPreviousStep"
                                ></detailing-item-issues>
                                <detailing-services
                                    v-if="detailingitem.etape == 11"
                                    :detailingitem="detailingitem"
                                    :main_services="main_services"
                                    :cleaning_services="cust_cleaning_services"
                                    :tailoring_services="tailoring_services"
                                    @save-item-services="saveItemDetails"
                                    @go-to-step="backPreviousStep"
                                ></detailing-services>
                            </div>
                        </div>
                        <detailing-right-panel
                            :customerName="customerName"
                            :item_description="item_description"
                            :detailingitem="detailingitem"
                            :cleaning_services="cust_cleaning_services"
                            :step="step"
                            @update-cleaning-price="saveItemDetails"
                            ref="right_panel_cmp"
                        ></detailing-right-panel>
                    </div>
                </div>
            </div>
        </div>
    </transition>
    <transition
        enter-active-class="animate__animated animate__fadeIn"
        leave-active-class="animate__animated animate__fadeOut"
    >
        <div v-if="show_pause_popup" class="popup-pause">
            <div class="popup-container">
                <div class="popup-title">
                    Are you sure you want to pause item's detailing ?
                    <i
                        class="icon-close popup-close"
                        @click="show_pause_popup = !show_pause_popup"
                    ></i>
                </div>
                <div class="popup-body">
                    <div class="popup-label">
                        You are about to quit this detailing.
                        <br />The status of the detailing will be "partial" as some information is still missing.
                    </div>
                    <div class="row button-row">
                        <button class="col btn btn-link" @click="show_pause_popup = false">cancel</button>
                        <button class="col btn popup-btn" @click="pauseDetailling">Pause detailing</button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
<script>
import DetailingRightPanel from './DetailingRightPanel.vue';
import DetailingItemDepartement from './DetailingItemDepartement.vue';
import DetailingItemType from './DetailingItemType.vue';
import DetailingItemDescription from './DetailingItemDescription.vue';
import DetailingItemComplexities from './DetailingItemComplexities.vue';
import DetailingServices from './DetailingServices.vue';
import MainHeader from '../layout/MainHeader';
import BreadCrumb from '../layout/BreadCrumb';
import SideBar from '../layout/SideBar';
import { ref, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useStore } from "vuex";
import {
    DETAILING_MODULE, INIT_DETAILING, UPDATE_DETAILING,
    TOASTER_MESSAGE, TOASTER_MODULE,
    LOADER_MODULE, SET_LOADER_MSG, DISPLAY_LOADER, HIDE_LOADER
} from "../../store/types/types";
import DetailingItemIssues from './DetailingItemIssues.vue';

export default {
    name: "DetailingItem",
    components: { BreadCrumb, SideBar, MainHeader, DetailingRightPanel, DetailingItemDepartement, DetailingItemType, DetailingItemDescription, DetailingItemComplexities, DetailingItemIssues,DetailingServices },
    setup() {
        const router = useRouter();
        const route = useRoute();
        const store = useStore();
        const user = ref({});
        const search = ref("");
        const departements = ref({});
        const detailingData = ref([]);
        const showSearch = ref(false);
        const showbutton = ref(false);
        const itemDept = ref(0);
        const order_id = ref(0);
        const item_id = ref(0);
        const detailingitem_id = ref(0);
        const detailingitem = ref({});
        const item_description = ref({});
        const customerName = ref('');
        const step = ref(1);
        const show_pause_popup = ref(false);
        const cust_cleaning_services = ref({});
        const main_services = ref({});
        const right_panel_cmp = ref();
        const tailoring_services = ref({});

        store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Please wait....']);

        order_id.value = route.params.order_id;
        item_id.value = route.params.item_id;
        const paths = ref([
            { name: 'All orders', route: 'LandingPage' },
            { name: 'Order n°' + order_id.value, route: 'DetailingItemList' },
            { name: 'Detailing item ' + item_id.value, route: 'DetailingItem' }
        ]);
        watch(() => itemDept.value, (current_val, previous_val) => {
            itemDept.value = current_val;
        });

        function initDetailing(){
        store
            .dispatch(`${DETAILING_MODULE}${INIT_DETAILING}`, { detailingitem_id: detailingitem_id.value, order_id: order_id.value, item_id: item_id.value, search: "" })
            .then((response) => {
                if (response.data.user) {
                    user.value = response.data.user;
                    search.value = response.data.search;
                    customerName.value = response.data.user.name;
                    detailingitem_id.value = response.data.detailingitem.id;
                    detailingitem.value = response.data.detailingitem;
                    itemDept.value = response.data.detailingitem.department_id;
                    step.value = response.data.detailingitem.etape;
                    detailingData.value = response.data.detailing_data;
                    item_description.value = response.data.item_description;
                    main_services.value = response.data.main_services;
                    cust_cleaning_services.value = response.data.cust_cleaning_services;
                    tailoring_services.value = response.data.tailoring_services;

                    right_panel_cmp.value.setBaseCleaningPrice(response.data.detailingitem.pricecleaning);
                    right_panel_cmp.value.initCleaningServices(response.data.cust_cleaning_services,response.data.detailingitem.cleaning_price_type);
                    right_panel_cmp.value.initTailoringServices(response.data.tailoring_services);

                    if(response.data.detailingitem.tailoring_services != null && response.data.detailingitem.tailoring_services != ''){
                        right_panel_cmp.value.refreshTailoringServices(JSON.parse(response.data.detailingitem.tailoring_services),response.data.detailingitem.tailoring_price_type);
                    }
                } else {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: response.data.message ? response.data.message : 'An error has occured',
                        ttl: 5,
                        type: 'danger'
                    });

                }

            })
            .finally(() => {
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });
        }

        initDetailing();


        watch(()=>step.value,(current_value,previous_value)=>{
            if(current_value==11){
                initDetailing();
            }
        });


        function submitSearch(e) {
            store
                .dispatch(`${DETAILING_MODULE}${INIT_DETAILING}`, { detailingitem_id: detailingitem_id.value, order_id: order_id.value, item_id: item_id.value, search: search.value })
                .then((response) => {
                    if (e.target.value) {
                        showSearch.value = true;
                        showbutton.value = true;
                        itemDept.value = response.data.searched_item.department_id;
                        step.value = response.data.detailingitem.etape;
                        detailingitem.value = response.data.detailingitem;
                        detailingData.value = response.data.detailing_data;


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
                    step.value = response.data.detailingitem.etape;
                    item_description.value = response.data.item_description;
                    detailingData.value = response.data.detailing_data;
                });
        }
        function saveItemDetails(data) {

            right_panel_cmp.value.setBaseCleaningPrice(detailingitem.value.pricecleaning);

            if(data.cleaning_services){
                let details = right_panel_cmp.value.refreshCleaningServices(JSON.parse(data.cleaning_services),data.cleaning_price_type);
                data.cleaning_prices = details;
            }

            if(data.tailoring_services){
                let tailoring_price = right_panel_cmp.value.refreshTailoringServices(JSON.parse(data.tailoring_services),data.tailoring_price_type);
                data.tailoring_price = tailoring_price;
            }

            store.dispatch(`${DETAILING_MODULE}${UPDATE_DETAILING}`, data)
                .then((response) => {
                    detailingitem.value = response.data.detailingitem;
                    step.value = response.data.detailingitem.etape;
                    item_description.value = response.data.item_description;
                    detailingData.value = response.data.detailing_data;

                    //cust_cleaning_services.value = response.data.cust_cleaning_services;
                    //right_panel_cmp.value.refreshCleaningServices(response.data.cust_cleaning_services,data.cleaning_price_type);



                });
        }
        function backPreviousStep(newstep) {
            store.dispatch(`${DETAILING_MODULE}${UPDATE_DETAILING}`, { detailingitem_id: detailingitem_id.value, step: newstep })
                .then((response) => {
                    detailingitem.value = response.data.detailingitem;
                    step.value = response.data.detailingitem.etape;
                    item_description.value = response.data.item_description;
                    detailingData.value = response.data.detailing_data;

                });
        }
        function pauseDetailling() {
            // store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Please wait....']);
            const status = detailingitem.value.status == 'In Process' ? 'Pause' : 'In Process'
            store.dispatch(`${DETAILING_MODULE}${UPDATE_DETAILING}`, { detailingitem_id: detailingitem_id.value, status: status })
                .then((response) => {
                    if (response) {
                        detailingitem.value = response.data.detailingitem;
                        show_pause_popup.value=false;
                        if(detailingitem.value.status == 'Pause'){
                            router.push('/order-content/'+order_id.value);
                        }
                    }
                })
                .finally(() => {
                    // store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: status == 'Pause' ? `Detailing is paused` : `Detailing is resumed`,
                        ttl: 5,
                        type: 'success'
                    });
                });

        }
        return {
            paths,
            user,
            search,
            departements,
            showSearch,
            showbutton,
            order_id,
            item_id,
            customerName,
            step,
            itemDept,
            detailingitem_id,
            detailingitem,
            item_description,
            detailingData,
            show_pause_popup,
            submitSearch,
            clearSearch,
            chooseDepartement,
            saveItemDetails,
            backPreviousStep,
            pauseDetailling,
            main_services,
            cust_cleaning_services,
            right_panel_cmp,
            tailoring_services,
            initDetailing,
        };
    },
}
</script>
<style scoped>
.breadcrumb{
    margin-bottom: 0;
}
.container-fluid {
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 140%;
}
.main {
    padding: 0px 0px 50px 61px;
}
.container-detailing{
    height: 100%;
}
.left-panel {
    margin-top: 10px;
    padding-right: 30px;
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

.popup-pause {
    background: rgba(0, 0, 0, 0.6);
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    z-index: 10002;
    display: flex;
    align-items: center;
    justify-content: center;
}
.popup-container {
    text-align: center;
    background-color: #fff;
    width: 660px;
    height: 450px;
    display: block;
    border-radius: 6px;
    box-shadow: 0px 8px 36px rgb(0 0 0 / 16%);
}
.popup-title {
    height: 100px;
    background: #f8f8f8;
    border-radius: 6px;
    font-family: Gilroy;
    font-style: normal;
    font-weight: 800;
    font-size: 22px;
    line-height: 110%;
    display: flex;
    align-items: center;
    text-align: center;
    place-content: center;
    color: #000000;
    padding: 20px;
}
.popup-body {
    padding: 60px;
}
.popup-label {
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 16px;
    line-height: 140%;
    display: flex;
    align-items: flex-end;
    color: #000000;
    flex: none;
    order: 0;
    align-self: stretch;
    flex-grow: 0;
    margin: 6px 0px;
}
.popup-btn {
    display: flex;
    justify-content: center;
    background: #47454b;
    border-radius: 4px;
    font-family: Gotham Rounded;
    line-height: 140%;
    align-items: center;
    text-align: center;
    color: #ffffff;
    outline: none !important;
    box-shadow: none !important;
}
.popup-close {
    position: relative;
    left: 45px;
}
.button-row{
    padding: 100px 10px 10px 10px;
}

.main{
    margin-top: 55px;
}
</style>
