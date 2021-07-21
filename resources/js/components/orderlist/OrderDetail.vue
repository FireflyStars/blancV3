<template>
    <div v-if="showorderdetail" class="odv container">
       <p>order detail view</p>
    </div>
</template>

<script>
    import {ref,computed} from 'vue';
    import {useRoute} from 'vue-router';
    import {useStore} from 'vuex';
    import {ORDERLIST_MODULE,ALL_ORDER_GET_CURRENT_SELECTED,ALL_ORDER_SELECT_CURRENT} from "../../store/types/types";

    export default {
        name: "OrderDetail",
        setup(){
            const route =useRoute();
            const store =useStore();
            const CURRENT_SELECTED=computed(()=>{
                return store.getters[`${ORDERLIST_MODULE}${ALL_ORDER_GET_CURRENT_SELECTED}`];
            });
            if(CURRENT_SELECTED.value==''&&route.params.order_id>0){
                store.dispatch(`${ORDERLIST_MODULE}${ALL_ORDER_SELECT_CURRENT}`,route.params.order_id)
            }
            return {
                showorderdetail:computed(()=>{
                    console.log(store.getters[`${ORDERLIST_MODULE}${ALL_ORDER_GET_CURRENT_SELECTED}`]);
                    return (store.getters[`${ORDERLIST_MODULE}${ALL_ORDER_GET_CURRENT_SELECTED}`])&&route.params.order_id>0;})
            }
        }
    }
</script>

<style scoped>
    .odv{
        max-width: 680px;
        background: #FFF;
        height: calc(100% - var(--mainlogoheight));
        position: fixed;
        top: var(--mainlogoheight);
        overflow-y: auto;
        right: 0;
        z-index: 10001;
    }
</style>