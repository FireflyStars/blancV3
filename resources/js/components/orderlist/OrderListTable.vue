<template>
    <div class="container-fluid">

    <section class="orderlist-table table">
        <header>
        <div class="tcol"  v-for="(col,index) in tabledef" :key="index" :style="{flex:col.flex}" :class="{'sortable': col.sortable,'check-box': col.type=='checkbox'}" >{{col.name}}</div>
        </header>
        <transition-group tag="div" class="position-relative" name="list" appear>
        <div class="trow" v-for="order in ORDER_LIST" :key="order.id" :class="{current_sel:order.id==CURRENT_SELECTED&&route.params.order_id>0}">
            <div class="tcol" v-for="(col,index) in tabledef"  :style="{flex:col.flex}" :class="{'check-box': col.type=='checkbox'}"  @click="selectrow(order.id,index)">
                <check-box v-if="col.type=='checkbox'" :checked_checkbox="(order.id==CURRENT_SELECTED&&route.params.order_id>0)||MULTI_CHECKED.includes(order.id)" :id="order.id" @checkbox-clicked="checkboxclicked"></check-box>
                <tag v-else-if="col.type=='tag'" :name="order[index]"></tag>
                <span v-else :style="col.css">{{preprocess(col,order[index])}}</span></div>
            </div>
        </transition-group>

        <footer>
        <div class="tcol" width="100%" style="text-align: center" :colspan="Object.keys(tabledef).length">  <button class="btn btn-link" @click="loadMore">Show more</button></div>
        </footer>
    </section>

    </div>


</template>

<script>
    import {useRouter,useRoute} from 'vue-router'
    import {ref,computed} from 'vue';
    import {useStore} from 'vuex';
    import {ALL_ORDER_LOAD_LIST,ALL_ORDER_GET_LIST,ORDERLIST_MODULE,ALL_ORDER_GET_CURRENT_SELECTED,ALL_ORDER_SELECT_CURRENT,ALL_ORDER_GET_ALL_ORDER_MULITCHECKED,ALL_ORDER_MULITCHECKED,ALL_ORDER_MULITUNCHECKED,LOADER_MODULE,DISPLAY_LOADER} from '../../store/types/types';
    import Tag from  '../miscellaneous/Tag'
    import CheckBox from '../miscellaneous/CheckBox'
    export default {
        name: "OrderListTable",
        props:['tabledef',"tab"],
        components:{Tag,CheckBox},
        setup(props){
            const router = useRouter();
            const store=useStore();
            const route = useRoute();
            const ORDER_LIST=computed(()=>{
                if(props.tab=="allorders")
                return store.getters[`${ORDERLIST_MODULE}${ALL_ORDER_GET_LIST}`];
            });
            const CURRENT_SELECTED=computed(()=>{
                return store.getters[`${ORDERLIST_MODULE}${ALL_ORDER_GET_CURRENT_SELECTED}`];
            });
            const MULTI_CHECKED=computed(()=>{
                return store.getters[`${ORDERLIST_MODULE}${ALL_ORDER_GET_ALL_ORDER_MULITCHECKED}`];
            });
            function loadMore(){
                 store.dispatch(`${ORDERLIST_MODULE}${ALL_ORDER_LOAD_LIST}`,{showmore:1}).finally(()=>{
                     window.scrollTo({ left: 0, top: document.body.scrollHeight, behavior: "smooth" })
                });

            }

            function preprocess(def,val) {
                if(typeof def.type!="undefined"&&def.type=="price"){

                    return "£"+(val!=0?val.toFixed(2):0);
                }
                return val;
            }

            function selectrow(id,colname){
                if(colname=='line_select') return;
                store.dispatch(`${ORDERLIST_MODULE}${ALL_ORDER_SELECT_CURRENT}`,id);
                  router.push({
                    name:'OrderDetails',
                    params: {
                        order_id:id,
                    },
                })
            }
            function checkboxclicked(check,id) {
                if(CURRENT_SELECTED.value==id&&check==false){
                    store.dispatch(`${ORDERLIST_MODULE}${ALL_ORDER_SELECT_CURRENT}`,'');
                }
                if(check==true){
                    store.dispatch(`${ORDERLIST_MODULE}${ALL_ORDER_MULITCHECKED}`,id);
                }
                if(check==false){
                    store.dispatch(`${ORDERLIST_MODULE}${ALL_ORDER_MULITUNCHECKED}`,id);
                }
            }

            return {
                route,
                CURRENT_SELECTED,
                MULTI_CHECKED,
                ORDER_LIST,
                loadMore,
                preprocess,
                selectrow,
                checkboxclicked,

            }
        }
    }
</script>

<style scoped>
    .container-fluid{
        padding-left: 70px;
    }
    .current_sel{
        position: relative;
        z-index:10000;
        background: rgb(247, 251, 246);
        box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.25);
    }
    /*list transitions*/
    .list-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .list-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .list-enter-active{
        transition: all 1s ease;
    }

    .list-leave-from{
        opacity: 1;
        transform: scale(1);
    }
    .list-leave-to{
        opacity: 0;
        transform: scale(0.6);
    }
    .list-leave-active{
        transition: all 1s ease;
        position: absolute;
        width: 100%;
    }
    .list-move{
        transition:all 0.9s ease;
    }
    .check-box{
        padding:20px 22px
    }
</style>