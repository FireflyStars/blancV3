<template>
    <transition
            enter-active-class="animate__animated animate__fadeIn"
    >
        <div class="container-fluid h-100 bg-color" v-if="showcontainer">
            <main-header></main-header>
            <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax">
            <side-bar></side-bar>
                <div class="col main-view p-0"><h2>Order List</h2>
                        {{ALL_ORDER_LIST}}
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import {ref,onMounted,computed,nextTick} from 'vue';
    import MainHeader from '../layout/MainHeader';
    import SideBar from '../layout/SideBar'

    import {useStore} from 'vuex';
    import {LOAD_ALL_ORDER_LIST,GET_ALL_ORDER_LIST,ORDERLIST_MODULE} from '../../store/types/types'
    export default {
        name: "OrderList",
        components: {SideBar, MainHeader},
        setup(props,context){
            const showcontainer=ref(false);
            const store=useStore();
            onMounted(()=>{
                nextTick(()=>{
                    console.log('mounted');
                    showcontainer.value=true;
                });

            });
            store.dispatch(`${ORDERLIST_MODULE}${LOAD_ALL_ORDER_LIST}`);
            return {
                showcontainer,
                ALL_ORDER_LIST:computed(()=>store.getters[`${ORDERLIST_MODULE}${GET_ALL_ORDER_LIST}`])
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