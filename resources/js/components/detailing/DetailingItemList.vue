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
                    <h1 class="title">Basket Content</h1>
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
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(det, index) in detailing_list" class="table-row row-hover">
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
                                    >{{ det.tracking }}</td>
                                    <td
                                        class="td-status"
                                        @click="openDetailing(det.item_number)"
                                    >
                                        <tag v-if="det.status=='In Process' && (det.etape === 1||det.etape === 2)" name="To be detailed"></tag>
                                        <tag v-else-if="det.status=='Completed'" name="Detailed"></tag>
                                        <tag v-else name="Partially detailed"></tag>
                                    </td>
                                    <td
                                        class="body_medium td-suborder"
                                        @click="openDetailing(det.item_number)"
                                    >{{ det.sub_order }}</td>
                                    <td
                                        class="body_regular td-text"
                                        @click="openDetailing(det.item_number)"
                                    >£{{ det.price.toFixed(2) }}</td>
                                    <td @click="loadRemoveItemModal(det.tracking,det.item_number)"><img src="/images/garbage.svg"/></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="row add-item" v-if="count_has_invoices==0">
                            <button class="btn btn-add-item col-4" @click="showTrackingModal"> <!-- addItem() -->
                                <i class="bi bi-hash"></i> Add item<!-- without barcode-->
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
                                <span class="scan-text" @click="showTrackingModal">Scan item / new bag barcode</span>
                            </div>
                        </div>
                        <div class="row total">
                            <span>Total £ {{ item_total.toFixed(2) }}</span>
                        </div>
                        <div class="row buttons pt-4">
                            <div class="col-10 text-align-right">
                                <button class="btn btn-link btn-previous" @click="back">Previous</button>
                            </div>
                            <div class="col-2" id="create_item_btn_container">
                                <button
                                    class="btn btn-next text-white float-right"
                                    :disabled="!valid"
                                    @click="redirectToCheckOut"
                                >Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </transition>

     <modal ref="bmodal">
            <template #bheader>
                <div class="bmodal-header py-3 text-center">Scan HSL</div>
                <div class="row mx-0 justify-content-center"><div class="col-2 text-center"><img src="/images/barcode.svg"/></div></div>
            </template>
            <template #bcontent>
                <div class="row mt-5 justify-content-center" :class="{'mb-5':!show_modal_loader,'mb-3':show_modal_loader}">
                    <div class="col-5 form-group">
                        <input type="text" class="form-control text-center" id="hsl_text" ref="hsl_text" v-model="current_hsl" @keyup="checkHslAndDetail($event,'new')"/>
                    </div>
                </div>
                <div v-if="show_modal_loader">
                    <bar-loader></bar-loader>
                </div>
            </template>
            <template #mbuttons>

            </template>
        </modal>


    <modal ref="remove_item_modal">
        <template #closebtn>
            <span class="close" id="addon_modal_close" @click="closeRemoveItemModal"></span>
        </template>
        <template #bheader>
            <div class="bmodal-header py-4 text-center">Remove Item</div>
        </template>
        <template #bcontent>
            <div class="row py-5">
                <div class="col-12 text-center add_on_desc">Do you want to remove item {{cur_tracking_to_remove}}?</div>
            </div>
        </template>
        <template #mbuttons>
            <div class="row mx-0 justify-content-center mt-3 mb-5">
                <div class="col-3">
                    <button class="btn btn-outline-dark w-100" @click="removeDetailingItem(cur_item_to_remove)">YES</button>
                </div>
                <div class="col-2"></div>
                <div class="col-3">
                    <button class="btn btn-outline-dark w-100" @click="closeRemoveItemModal">NO</button>
                </div>
            </div>
        </template>
    </modal>


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
    INIT_DETAILING,
} from "../../store/types/types";
import Tag from "../miscellaneous/Tag.vue";
import Modal from '../miscellaneous/Modal.vue';
import BarLoader from '../BarLoader.vue';

export default {
    name: "DetailingItemList",
    components: { BreadCrumb, SideBar, MainHeader, Tag, Modal, BarLoader},
    setup() {
        const router = useRouter();
        const route = useRoute();
        const store = useStore();
        const order_id = ref(0);
        const customer_name = ref("");
        const detailing_list = ref([]);
        const item_total = ref(0);
        const valid = ref(false);
        const new_item_id = ref(0);
        const bmodal = ref();
        const current_hsl = ref('');
        const show_modal_loader = ref(false);
        const cur_customer = ref({});
        const remove_item_modal = ref();
        const cur_tracking_to_remove = ref("");
        const cur_item_to_remove = ref(0);
        const count_has_invoices = ref(0);

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

    function getDetailingList(){
        store
            .dispatch(`${DETAILING_MODULE}${GET_DETAILING_LIST}`, {
                order_id: order_id.value,
            })
            .then((response) => {
                if (response.data.user) {
                    customer_name.value = response.data.customer.Name;
                    cur_customer.value = response.data.customer;
                    detailing_list.value = response.data.detailing_list;
                    item_total.value = detailing_list.value.reduce((acc, ele) => {
                        return acc + ele.price;
                    }, 0);
                    count_has_invoices.value = response.data.count_has_invoices;
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
                let count_items = detailing_list.value.length;
                let completed_items = [];

                detailing_list.value.forEach(function(v,i){
                   if(v.status=='Completed'){
                       completed_items.push(v.item_number);
                   }

                });

                if(detailing_list.value.length==completed_items.length){
                    valid.value = true;
                }
            });
        }

        getDetailingList();

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

        const showTrackingModal = ()=>{

            const loadModal = async()=>{
               await showModal();
           }

            loadModal().then(()=>{
                let el = document.querySelector('#hsl_text');
                el.focus();
            });

        }

        function showModal(){
            bmodal.value.showModal();
        }


        function checkHslAndDetail(event,type){
            let err = false;
            if(event.keyCode==13){
                let value = current_hsl.value.toString().replace(' ','');

                if(value=='' || ! value.match(/\d{8}/) || value.length !=8){
                    err = true;

                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: "Invalid HSL",
                        ttl: 5,
                        type: "danger",
                    });
                }

                if(!err){
                    show_modal_loader.value = true;
                    axios.post('/check-detailing-tracking',{
                        tracking:value,
                        customer_id:cur_customer.value.CustomerID,
                        order_id:order_id.value,
                    }).then((res)=>{
                        if(res.data.err!=''){
                            let err_msg = res.data.err;

                            if(res.data.has_detailing_order){
                                err_msg = res.data.err+" Order: "+res.data.has_detailing_order.order_id;
                            }


                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    message: err_msg,
                                    ttl:(res.data.has_detailing_order?10:5),
                                    type: "danger",
                                });
                            current_hsl.value = '';
                        }else{
                            if(res.data.previous_detailed_item){
                                router.push('/detailing_item/'+order_id.value+'/'+res.data.detailingitem_id);
                            }else{
                                if(res.data.item){
                                    addItem(res.data.item.ItemTrackingKey,res.data.item.id);
                                }else{
                                    addItem(value,0);
                                }
                            }
                        }

                    }).catch((err)=>{

                    }).finally(()=>{
                        show_modal_loader.value = false;
                    });
                }
            }
        }



        function addItem(hsl,item_id){
            console.log('add item called');

            store
            .dispatch(`${DETAILING_MODULE}${INIT_DETAILING}`, { detailingitem_id: 0, order_id: order_id.value, item_id: 0, search: "",item_id:item_id,tracking:hsl })
            .then((response) => {
                new_item_id.value = response.data.detailingitem_id;
            }).catch((err)=>{

            }).finally(()=>{
                router.push('/detailing_item/'+order_id.value+'/'+new_item_id.value);
            });
        }
    /*
        function createOrderItems(){
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                true,
                "Creating items....",
            ]);

            axios.post('/create-order-items',{
                order_id:order_id.value
            }).then((res)=>{
                if(res.data.items_created.length > 0){
                    router.push('/checkout/'+order_id.value);
                }else{
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: "No items created",
                        ttl: 5,
                        type: "danger",
                    });
                }
            }).catch((err)=>{
                console.log(err);
            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });

            //

        }
    */
    function redirectToCheckOut(){
         router.push('/checkout/'+order_id.value);
    }

        function loadRemoveItemModal(tracking,id){
            cur_tracking_to_remove.value = tracking;
            cur_item_to_remove.value = id;
            remove_item_modal.value.showModal();
        }

        function closeRemoveItemModal(){
            cur_tracking_to_remove.value = "";
            cur_item_to_remove.value = 0;
            remove_item_modal.value.closeModal();
        }


        function removeDetailingItem(id){
            axios.post('/remove-detailing-item',{
                id:id
            }).then((res)=>{
                if(res.data.deleted){
                    cur_tracking_to_remove.value = "";
                    cur_item_to_remove.value = 0;
                    remove_item_modal.value.closeModal();
                }
            }).catch((err)=>{

            }).finally(()=>{
                getDetailingList();
            });

        }


        return {
            paths,
            order_id,
            customer_name,
            detailing_list,
            valid,
            item_total,
            bmodal,
            addItem,
            getIndexOfRow,
            getRowspanNumber,
            openDetailing,
            redirectToCheckOut,
            showTrackingModal,
            current_hsl,
            checkHslAndDetail,
            show_modal_loader,
            remove_item_modal,
            loadRemoveItemModal,
            closeRemoveItemModal,
            removeDetailingItem,
            cur_tracking_to_remove,
            cur_item_to_remove,
            count_has_invoices,
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

.row-hover td:hover{
    cursor: pointer;
    background: #eeeeee;
}

#create_item_btn_container{
    padding-right: 0;
}

.bmodal-header,.add_on_desc{
        font-family:'Gilroy Extra Bold';
    }

    .add_on_desc{
        font-size:18px;
    }

    .bmodal-header{
        font-size:22px;
        background:#f8f8f8;
    }

    #addon_modal_close{
        top:20px;
    }



</style>
