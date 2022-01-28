<template>
    <transition
            enter-active-class="animate__animated animate__fadeIn"
    >
        <div class="container-fluid h-100 bg-color" v-if="showcontainer">
           <main-header></main-header>
            <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax">
                <side-bar></side-bar>
                <div class="col  p-0 m-0">
                    <bread-crumb :paths="paths"></bread-crumb>
                    <div class="main-view-2 row">
                        <div class="col">
                        <h2 class="subtitle">Choose item type</h2>

                        content
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>

</template>
<script>
    //import MainHeader from '../layout/MainHeader';
    import BreadCrumb from '../layout/BreadCrumb';
    import SideBar from '../layout/SideBar';
    import {ref} from 'vue';
export default {
    name:"DetailingItemType",
    components:{BreadCrumb,SideBar},
    setup() {
        const showcontainer=ref(true);

        const paths=ref([{name:'All orders',route:'LandingPage'},{name:'Order no',route:'NewOrder'},{name:'Detailing',route:'DetailingItemType'}]);

        return {
            showcontainer,
            paths
        };
    },
}
</script>
<style scoped>

</style>
