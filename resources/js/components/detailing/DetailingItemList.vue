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
                    <span class="order-no">Order n°{{ order_id }} - {{ customer_name }}</span>
                    <h1 class="title">Order Content</h1>
                    <div class="content">
                        <button class="btn-shopify">
                            <i class="bi bi-bag p-2"></i>Add from Shopify
                        </button>
                        <table class="detailing-table">
                            <thead class="detailing-header">
                                <tr>
                                    <th>Bag n°</th>
                                    <th>Item type</th>
                                    <th>Item n°</th>
                                    <th>Status</th>
                                    <th>Sub-order</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(det, index) in detailing_list" class="table-row">
                                    <td
                                        class="body_bold td-text td-bag-no"
                                        :rowspan="getRowspanNumber(det.NoBag)"
                                        v-if="getIndexOfRow(det.NoBag, det.item_number)"
                                    >{{ det.NoBag }}</td>
                                    <td
                                        class="body_medium td-text"
                                        @click="openDetailing(det.item_number)"
                                    >{{ det.typeitem_name }}</td>
                                    <td
                                        class="body_regular td-text"
                                        @click="openDetailing(det.item_number)"
                                    >{{ det.item_number }}</td>
                                    <td
                                        class="td-status"
                                        @click="openDetailing(det.item_number)"
                                    >
                                        <tag v-if="det.etape === 1||det.etape === 2" name="To be detailed"></tag>
                                        <tag v-else-if="det.etape === 12" name="Detailed"></tag>
                                        <tag v-else name="Partially detailed"></tag>
                                    </td>
                                    <td
                                        class="body_medium td-suborder"
                                        @click="openDetailing(det.item_number)"
                                    >{{ det.sub_order }}</td>
                                    <td
                                        class="body_regular td-text"
                                        @click="openDetailing(det.item_number)"
                                    >£{{ det.price }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="row add-item">
                            <button class="btn btn-add-item col-4" @click="addItem">
                                <i class="bi bi-hash"></i> Add item without barcode
                            </button>
                            <span class="col-1 text-center">OR</span>
                            <div class="col-5 btn-barcode">
                                <svg
                                    width="20"
                                    height="20"
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M15.4544 6.98256e-05C15.2676 0.00363376 15.1185 0.162229 15.1185 0.356463C15.1185 0.549808 15.2676 0.708409 15.4544 0.712857H18.5412V3.96225V3.96136C18.5412 4.15916 18.6946 4.31864 18.884 4.31864C19.0734 4.31864 19.2277 4.15916 19.2277 3.96136V0.357283C19.2277 0.160376 19.0751 0 18.8857 0L15.4544 6.98256e-05ZM0.352471 0.0178895C0.260772 0.0169986 0.173358 0.0553102 0.108224 0.122134C0.0439488 0.188958 0.00795561 0.280729 0.00795561 0.375179V3.94276C0.00624173 4.03899 0.0413775 4.13165 0.105652 4.20026C0.169927 4.26886 0.258197 4.30717 0.350755 4.30717C0.443314 4.30717 0.531581 4.26886 0.595858 4.20026C0.660132 4.13165 0.695268 4.03899 0.693555 3.94276V0.730774H3.81899C3.91154 0.732556 4.00067 0.696027 4.06666 0.629203C4.13265 0.562379 4.1695 0.470608 4.1695 0.374379C4.1695 0.278149 4.13265 0.186381 4.06666 0.119555C4.00067 0.0518386 3.91154 0.0153098 3.81899 0.017983L0.352471 0.0178895ZM2.0656 2.86995V8.21597H2.7512V2.86995H2.0656ZM3.43679 2.86995V8.21597H4.80883V2.86995H3.43679ZM5.8356 2.86995V8.21597H6.52205V2.86995H5.8356ZM7.20765 2.86995V8.21597H7.89324V2.86995H7.20765ZM8.92334 2.86995V8.21597H10.292V2.86995H8.92334ZM11.3222 2.86995V8.21597H12.0078V2.86995H11.3222ZM12.6933 2.86995V8.21597H13.3789V2.86995H12.6933ZM14.4065 2.86995V8.21597H15.7785V2.86995H14.4065ZM16.4641 2.86995V8.21597H17.1497V2.86995H16.4641ZM0.316556 9.64222V9.64133C0.130588 9.65648 -0.00996201 9.8222 -0.000528227 10.0164C0.00889875 10.2098 0.165728 10.3603 0.352558 10.3541H18.8627C19.0487 10.3497 19.1978 10.192 19.1978 9.99773C19.1978 9.80438 19.0487 9.64578 18.8627 9.64133H0.352558H0.316565L0.316556 9.64222ZM2.06569 11.7815V17.1275L2.75128 17.1266V11.7806L2.06569 11.7815ZM3.43688 11.7815V17.1275H4.80892V11.7815H3.43688ZM5.83569 11.7815V17.1275H6.52214V11.7815H5.83569ZM7.20774 11.7815V17.1275L7.89333 17.1266V11.7806L7.20774 11.7815ZM8.92343 11.7815V17.1275H10.2921V11.7815H8.92343ZM11.3222 11.7815V17.1275H12.0078V11.7815H11.3222ZM12.6934 11.7815V17.1275H13.379V11.7815H12.6934ZM14.4066 11.7815V17.1275L15.7786 17.1266V11.7806L14.4066 11.7815ZM16.4642 11.7815V17.1275H17.1498V11.7815H16.4642ZM0.347578 15.6768C0.256738 15.6777 0.170182 15.716 0.105898 15.7837C0.0424811 15.8515 0.00734353 15.9423 0.00820134 16.0377V19.6418C0.00820134 19.7371 0.0441945 19.828 0.108469 19.8948C0.173602 19.9625 0.261014 19.9999 0.352716 19.9999H3.7842C3.8759 20.0017 3.96502 19.9652 4.03101 19.8975C4.097 19.8307 4.13385 19.7389 4.13385 19.6435C4.13385 19.5473 4.097 19.4555 4.03101 19.3887C3.96502 19.321 3.8759 19.2845 3.7842 19.2871H0.693868V16.0377C0.694725 15.9415 0.658731 15.8498 0.5936 15.7811C0.528468 15.7134 0.440198 15.676 0.347642 15.6769L0.347578 15.6768ZM18.881 15.6946C18.7902 15.6955 18.7028 15.7347 18.6393 15.8016C18.5759 15.8693 18.5408 15.9611 18.5416 16.0555V19.2684H15.4171C15.2302 19.2729 15.082 19.4306 15.082 19.6248C15.082 19.8181 15.2303 19.9767 15.4171 19.9812H18.8861C19.0755 19.9803 19.228 19.8199 19.228 19.623V16.0554C19.2289 15.9592 19.1929 15.8674 19.1269 15.7997C19.0618 15.7311 18.9735 15.6937 18.8809 15.6946L18.881 15.6946Z"
                                        fill="black"
                                    />
                                </svg>
                                <span class="scan-text">Scan item / new bag barcode</span>
                            </div>
                        </div>
                        <div class="row total">
                            <span>Total £ {{ item_total }}</span>
                        </div>
                        <div class="row buttons">
                            <div class="col-10 text-align-right">
                                <button class="btn btn-link btn-previous" @click="back">Previous</button>
                            </div>
                            <div class="col-2 text-align-right">
                                <button
                                    class="btn btn-next text-white"
                                    :disabled="!valid"
                                    @click="next"
                                >Next</button>
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
    DETAILING_MODULE,
    GET_DETAILING_LIST,
    TOASTER_MESSAGE,
    TOASTER_MODULE,
    LOADER_MODULE,
    SET_LOADER_MSG,
    DISPLAY_LOADER,
    HIDE_LOADER,
} from "../../store/types/types";
import Tag from "../miscellaneous/Tag.vue";

export default {
    name: "DetailingItemList",
    components: { BreadCrumb, SideBar, MainHeader, Tag },
    setup() {
        const router = useRouter();
        const route = useRoute();
        const store = useStore();
        const order_id = ref(0);
        const customer_name = ref("");
        const detailing_list = ref([]);
        const item_total = ref(0);
        const valid = ref(false);
        store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
            true,
            "Please wait....",
        ]);

        order_id.value = route.params.order_id;
        const paths = ref([
            { name: "Order", route: "LandingPage" },
            { name: "New order", route: "NewOrder" },
            { name: "Item List", route: "DetailingItemList" },
        ]);

        store
            .dispatch(`${DETAILING_MODULE}${GET_DETAILING_LIST}`, {
                order_id: order_id.value,
            })
            .then((response) => {
                if (response.data.user) {
                    customer_name.value = response.data.customer.Name;
                    detailing_list.value = response.data.detailing_list;
                    item_total.value = detailing_list.value.reduce((acc, ele) => {
                        return acc + ele.price;
                    }, 0);
                } else {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: response.data.message
                            ? response.data.message
                            : "An error has occured",
                        ttl: 5,
                        type: "danger",
                    });
                }
            })
            .finally(() => {
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });
        function getRowspanNumber(bagno) {
            return detailing_list.value.filter(x => x.NoBag == bagno).length;
        }
        function getIndexOfRow(bagno, item_id) {
            const flitered_list = detailing_list.value.filter(x => x.NoBag == bagno);
            return flitered_list.findIndex(x => x.item_number === item_id) === 0;

        }
        function openDetailing(item_id) {
            router.push('/detailing_item/' + order_id.value + '/' + item_id);
        }
        function addItem() {
            router.push('/detailing_item/'+order_id.value+'/10021619');
        }
        return {
            paths,
            order_id,
            customer_name,
            detailing_list,
            valid,
            item_total,
            addItem,
            getIndexOfRow,
            getRowspanNumber,
            openDetailing
        };
    },
};
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
    padding: 0px 0px 50px 61px;
}
.breadcrumb {
    margin-bottom: 0;
}
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
.btn-shopify {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    padding: 4px 16px 4px 8px;
    width: 205px;
    height: 32px;
    background: #ffffff;
    border: 1px solid #c3c3c3;
    box-sizing: border-box;
    border-radius: 4px;
    text-align: center;
    line-height: 1.5;
    color: #868686;
    float: right;
    margin-bottom: 10px;
    outline: none !important;
    box-shadow: none !important;
    font-size: 14px;
}
.content {
    padding: 15px;
    margin: 15px;
}
.detailing-table {
    width: 100%;
    text-align: center;
    border-collapse: separate;
    border-spacing: 0 10px;
}
.detailing-header {
    background: #f8f8f8;
    filter: drop-shadow(0px 0px 4px rgba(80, 80, 80, 0.2));
    height: 48px;
    color: #47454b;
    font-size: 16px;
}

.table-row {
    background: #ffffff;
    height: 50px;
}
.td-bag-no{
    border-right: 10px solid #f8f8f8;

}
.td-text {
    color: #47454b;
}
.td-item-status {
    color: #000000;
    font-size: 12px;
}
.td-suborder {
    font-size: 12px;
    color: #c3c3c3;
}
.add-item {
    background: #eeeeee;
    border-radius: 4px;
    padding: 10px 16%;
    margin: 20px 0px;
    justify-content: center;
    align-items: center;
}
.btn-add-item {
    outline: none !important;
    box-shadow: none !important;
    height: 40px;
    width: 250px;
    background: #47454b;
    border-radius: 4px;
    font-size: 14px;
    color: #ffffff;
    align-items: center;
    text-align: center;
}
.btn-barcode{
    width: 272px;
}
.scan-text {
    color: #868686;
    margin: 10px;
}
.total {
    float: right;
    text-align: right;
    width: 100%;
    color: #000000;
    font-family: Gilroy;
    font-style: normal;
    font-weight: 800;
    font-size: 22px;
    line-height: 110%;
    margin: 10px 40px;
}
.buttons {
    padding: 10px;
}
.btn-next {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    padding: 18px 14px;
    width: 148px;
    height: 41px;
    border-radius: 4px;
    background: #42a71e;
    outline: none !important;
    box-shadow: none !important;
}
.btn-next:disabled {
    background: #e0e0e0;
}
.btn-previous {
    font-size: 16px;
    line-height: 19px;
    align-items: center;
    text-align: center;
    text-decoration-line: underline;
}
</style>
