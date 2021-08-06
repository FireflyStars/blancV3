<template>
    <div class="container-fluid position-relative">
        <filters></filters>
    <section class="orderlist-table" v-if="ORDER_LIST.length>0">
        <header>
        <div class="tcol noselect"  v-for="(col,index) in tabledef" :key="index" :style="{flex:col.flex,'text-align':col.header_align}" :class="{'sortable': col.sortable,'check-box': col.type=='checkbox'}"  @click="sort(index,col.sortable)">{{col.name}}
            <sort-arrows v-if="col.sortable" :sort="SORTCOL" :colname="index"></sort-arrows>
        </div>
        </header>
        <transition-group tag="div" class="position-relative" name="list" appear>
        <div class="trow" v-for="order in ORDER_LIST" :key="order.id" :class="{current_sel:order.id==CURRENT_SELECTED&&route.params.order_id>0}">
            <div class="tcol" v-for="(col,index) in tabledef"  :style="{flex:col.flex}" :class="{'check-box': col.type=='checkbox'}"  @click="selectrow(order.id,index)">
                <check-box v-if="col.type=='checkbox'" :checked_checkbox="(order.id==CURRENT_SELECTED&&route.params.order_id>0)||MULTI_CHECKED.includes(order.id)" :id="order.id" @checkbox-clicked="checkboxclicked"></check-box>
                <tag v-else-if="col.type=='tag'" :name="order[index]" ></tag>
                <express-icon v-else-if="col.type=='express'" :express_values="order[index]"></express-icon>
                <span v-else :style="col.css">{{preprocess(col,order[index])}}</span></div>
            </div>
        </transition-group>

        <footer>
        <div class="tcol" width="100%" style="text-align: center" :colspan="Object.keys(tabledef).length">  <button class="btn btn-link" @click="loadMore">Show more</button></div>
        </footer>
    </section>

      <section class="nodata p-2" v-if="ORDER_LIST.length==0">
            <p v-if="!loader_running">No orders available.</p>
      </section>

    </div>


</template>

<script>
    import {useRouter,useRoute} from 'vue-router'
    import {ref,computed} from 'vue';
    import {useStore} from 'vuex';
    import {ORDERLIST_LOAD_LIST,ORDERLIST_MODULE,ORDERLIST_GET_CURRENT_SELECTED,ORDERLIST_SELECT_CURRENT,ORDERLIST_GET_ALL_ORDER_MULITCHECKED,ORDERLIST_MULITCHECKED,ORDERLIST_MULITUNCHECKED,LOADER_MODULE,DISPLAY_LOADER,ORDERLIST_GET_LIST,GET_SHOW_LOADER,ORDERLIST_SORT,ORDERLIST_GET_SORT,ORDERLIST_LOADERMSG} from '../../store/types/types';
    import Tag from  '../miscellaneous/Tag'
    import CheckBox from '../miscellaneous/CheckBox'
    import ExpressIcon  from '../miscellaneous/ExpressIcon'
    import SortArrows from '../miscellaneous/SortArrows'
    import Filters from '../miscellaneous/Filters'
    export default {
        name: "OrderListTable",
        props:['tabledef',"tab"],
        components:{Filters, Tag,CheckBox,ExpressIcon,SortArrows},
        setup(props){
            const router = useRouter();
            const store=useStore();
            const route = useRoute();
            const ORDER_LIST=computed(()=>{
                //if(props.tab=="all_orders")
                return store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_LIST}`];
            });
            const CURRENT_SELECTED=computed(()=>{
                return store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_CURRENT_SELECTED}`];
            });
            const MULTI_CHECKED=computed(()=>{
                return store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_ALL_ORDER_MULITCHECKED}`];
            });
            const SORTCOL=computed(()=>{
               return store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_SORT}`];
            });
            function loadMore(){
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOADERMSG}`,'Loading more, please wait...');
                 store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOAD_LIST}`,{showmore:1}).finally(()=>{
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
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_SELECT_CURRENT}`,id);
                  router.push({
                    name:'OrderDetails',
                    params: {
                        order_id:id,
                    },
                })
            }
            function checkboxclicked(check,id) {
                if(CURRENT_SELECTED.value==id&&check==false){
                    store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_SELECT_CURRENT}`,'');
                }
                if(check==true){
                    store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_MULITCHECKED}`,id);
                }
                if(check==false){
                    store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_MULITUNCHECKED}`,id);
                }
            }

            function sort(colname,sortable){

                if(sortable)
                    store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_SORT}`,colname);
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
                loader_running:computed(()=>{return (store.getters[`${LOADER_MODULE}${GET_SHOW_LOADER}`]);}),
                sort,
                SORTCOL

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
    .nodata{
        background: #FFFFFF;
        min-height: 550px;
        display: flex;
        align-items: center;justify-content: center;
    }

</style>