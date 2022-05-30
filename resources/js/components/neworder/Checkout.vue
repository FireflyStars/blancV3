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
                    <span class="order-no my-3 d-block">New order n°{{ order_id }}</span>
                    <h1 class="title">Order Summary</h1>
                    <div class="content">

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
            axios.post('/get-checkout-items',{
                order_id:order_id.value,
            }).then((res)=>{
                console.log(res);
            }).catch((err)=>{

            }).finally(()=>{

            });
        }
        getCheckoutItems();

        return {
            order_id,
            paths,
        }

    },
}
</script>
<style scoped>
    .order-no{
        font:normal 16px "Gotham Rounded Bold";
        color:#868686;
    }
</style>
