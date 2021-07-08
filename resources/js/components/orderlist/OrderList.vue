<template>
    <transition
            enter-active-class="animate__animated animate__fadeIn"
    >
        <div class="container-fluid h-100 bg-color" v-if="showimg">
            <main-header></main-header>
            <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax">
            <side-bar></side-bar>
                <div class="col main-view p-0"><h2>Order List</h2>

                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import {ref,onMounted,nextTick} from 'vue';
    import MainHeader from '../layout/MainHeader';
    import SideBar from '../layout/SideBar'
    export default {
        name: "OrderList",
        components: {SideBar, MainHeader},
        setup(props,context){
            const showimg=ref(false);
            onMounted(()=>{
                nextTick(()=>{
                    console.log('mounted');
                    showimg.value=true;
                });

            });
            return {
                showimg
            }
        }
    }
</script>

<style scoped>

    .hmax{
        height: calc(100% - var(--mainlogoheight));
    }

    .auth-logo{
        height: var(--authlogoheight);
    }

</style>