<template>
    <div class="odv container item-detail-panel"  :style="{'right': (itemIdFromRoute > 0 ?
          0:684)+'px'}" v-if="showItemDetail">
        <div class="item-detail-progressbar" :class="loaderclass"></div>
        <header class="item-detail-header d-flex justify-content-between" v-if="ITEM.breif_info.id !=''">
            <h3 class="m-0 item-detail-header-title">Item {{ ITEM.breif_info.item_key }}</h3>
            <svg class="item-detail-close" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" @click="close">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.78812 0.297179C1.3976 -0.0953162 0.764438 -0.0953162 0.373917 0.297179C-0.0166053 0.689674 -0.0166053 1.32604 0.373916 1.71853L5.58834 6.9593L0.292891 12.2815C-0.0976305 12.674 -0.0976304 13.3104 0.292891 13.7029C0.683413 14.0954 1.31657 14.0954 1.7071 13.7029L7.00254 8.38065L12.293 13.6978C12.6835 14.0903 13.3166 14.0903 13.7072 13.6978C14.0977 13.3053 14.0977 12.6689 13.7072 12.2765L8.41675 6.9593L13.6261 1.72358C14.0167 1.33109 14.0167 0.694726 13.6261 0.302231C13.2356 -0.0902646 12.6025 -0.0902643 12.2119 0.302231L7.00254 5.53795L1.78812 0.297179Z" fill="white"/>
            </svg>  
        </header>
        <div class="item-detail-body" v-if="ITEM.breif_info.id !=''">
            <div class="item-detail-info-section d-flex flex-wrap">
                <div class="item-detail-name-panel">
                    <p class="m-0 item-detail-name">{{ ITEM.breif_info.item_name }}</p>
                    <p class="m-0 item-detail-suborder" v-if="ITEM.suborder !=''" >Sub order {{ ITEM.suborder.NumInvoice}}</p>
                </div>
                <div class="item-detail-location-panel">
                    <div class="item-detail-location-type d-flex justify-content-between">
                        <div class="item-detail-location-type-name">{{ ITEM.breif_info.customer_name }}</div>
                        <div class="item-detail-location-type-icon d-flex justify-content-center align-items-center"
                            v-if="( ITEM.breif_info.CustomerIDMaster !='' || ITEM.breif_info.CustomerIDMasterAccount != '')
                                ||  ITEM.breif_info.IsMaster == 1 || ITEM.breif_info.IsMasterAccount ==  1
                            "
                        >B2B</div>
                        <div class="item-detail-location-type-icon d-flex justify-content-center align-items-center" v-else>B2C</div>
                    </div>
                    <div class="item-detail-location-name d-flex mt-1">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.2981 16H1.02148V8.17017H1.70234V15.3191H13.6172V8.17017H14.2981V16Z" fill="black"/>
                            <path d="M13.5149 8.61277C12.5202 8.61277 11.7106 7.80358 11.7106 6.80851H12.3915C12.3915 7.42774 12.8953 7.93192 13.5149 7.93192C14.1341 7.93192 14.6383 7.42809 14.6383 6.80851V5.53192L12.0511 0.680851H3.26809L0.680851 5.53192V6.80851C0.680851 7.42774 1.18468 7.93192 1.80426 7.93192C2.42383 7.93192 2.92766 7.42809 2.92766 6.80851H3.60851C3.60851 7.80323 2.79898 8.61277 1.80426 8.61277C0.809532 8.61277 0 7.80323 0 6.80851V5.3617L2.85957 0H12.4596L15.3191 5.3617V6.80851C15.3191 7.80323 14.51 8.61277 13.5149 8.61277Z" fill="black"/>
                            <path d="M4.73199 8.61273C3.73727 8.61273 2.92773 7.80354 2.92773 6.80847H3.60859C3.60859 7.42771 4.11241 7.93188 4.73199 7.93188C5.35156 7.93188 5.85539 7.42805 5.85539 6.80847H6.53625C6.53625 7.8032 5.72671 8.61273 4.73199 8.61273Z" fill="black"/>
                            <path d="M7.65972 8.61273C6.665 8.61273 5.85547 7.80354 5.85547 6.80847H6.53632C6.53632 7.42771 7.04015 7.93188 7.65972 7.93188C8.2793 7.93188 8.78313 7.42805 8.78313 6.80847H9.46398C9.46398 7.8032 8.65445 8.61273 7.65972 8.61273Z" fill="black"/>
                            <path d="M10.5875 8.61273C9.59273 8.61273 8.7832 7.80354 8.7832 6.80847H9.46405C9.46405 7.42771 9.96788 7.93188 10.5875 7.93188C11.2067 7.93188 11.7109 7.42805 11.7109 6.80847H12.3917C12.3917 7.8032 11.5822 8.61273 10.5875 8.61273Z" fill="black"/>
                            <path d="M12.255 13.9575H3.06348V7.82983H3.74433V13.2766H11.5741V7.82983H12.255V13.9575Z" fill="black"/>
                            <path d="M10.964 11.5038L9.77246 10.3123L9.29103 10.7937L10.4826 11.9852L10.964 11.5038Z" fill="black"/>
                            <path d="M9.94312 12.1846L7.73047 9.97192L7.24904 10.4534L9.46169 12.666L9.94312 12.1846Z" fill="black"/>
                        </svg>
                        <span class="ms-2">{{ ITEM.breif_info.store }}</span>
                    </div>
                </div>
            </div>
            <div class="item-detail-variant-section d-flex">
                <div class="item-detail-brand-color">
                    <div class="item-detail-brand d-flex">
                        <p class="w-50 m-0">Brand</p>
                        <p class="w-50 m-0">{{ ITEM.breif_info.brand }}</p>
                    </div>
                    <div class="item-detail-color mt-2 d-flex">
                        <p class="w-50 m-0 d-flex align-items-center">Colours & patterns</p>
                        <p class="w-50 m-0 d-flex align-items-center flex-wrap">
                            <color-tag :colors="ITEM.breif_info.colors.toLowerCase().trim()"></color-tag>
                        </p>
                    </div>
                </div>
                <div class="item-detail-fabric-size">
                    <div class="item-detail-variant-fabric d-flex">
                        <p class="w-50 m-0">Fabric</p>
                        <p class="w-50 m-0">{{ ITEM.breif_info.fabrics}}</p>
                    </div>
                    <div class="item-detail-variant-size mt-2 d-flex">
                        <p class="w-50 m-0">Size</p>
                        <p class="w-50 m-0">{{ ITEM.breif_info.size }}</p>
                    </div>
                </div>
            </div>
            <div class="toggle-panel" :class="{ active: production_panel }" >
                <div class="toggle-panel-header" @click="production_panel = !production_panel">
                    <h4 class="m-0">Production Track</h4>
                </div>
                <div class="toggle-panel-content" :class="{ 'd-none': !production_panel }">
                    <div class="d-flex">
                        <div class="col-9 location-title">
                            Current location
                        </div>
                        <div class="col-3">
                            <div class="invoice-location rounded-pill" :style="{'background': ITEM.breif_info.location_color }">
                                <svg v-if="ITEM.breif_info.process != 0" width="12" height="12" viewBox="0 0 12 12" :fill="'#'+ITEM.breif_info.circle_color" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" :fill="'#'+ITEM.breif_info.circle_color"/>
                                    <circle cx="6" cy="6" r="5" :stroke="'#'+ITEM.breif_info.circle_color" stroke-width="2"/>
                                </svg>
                                <svg v-else width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" :fill="'#'+ITEM.breif_info.circle_color"/>
                                    <circle cx="6" cy="6" r="5" :stroke="'#'+ITEM.breif_info.circle_color" stroke-width="2"/>
                                </svg>                                
                            &nbsp;&nbsp;<span class="d-block text-center" :style="{ width: 'calc( 100% - 12px )'}"> {{ ITEM.breif_info.location }}</span>
                        </div>
                        </div>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="col-9 location-title">
                            Previous Locations
                        </div>
                        <div class="col-3"></div>
                    </div>
                    <div class="d-flex mt-2" v-for="(history, index) in ITEM.location_history" :key="index">
                        <div class="col-3 previous-location-item-title d-flex align-items-center">
                            {{ history.name }}
                        </div>
                        <div class="col-6 ps-3 d-flex align-items-center previous-location-item-date">
                                {{ history.date }}
                        </div>
                        <div class="col-3 previous-location-item">
                            <div class="invoice-location rounded-pill">
                                <span>{{ history.location }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="toggle-panel" :class="{ active: customer_instruction_panel }" >
                <div class="toggle-panel-header" @click="customer_instruction_panel = !customer_instruction_panel">
                    <h4 class="m-0">Customer instructions</h4>
                </div>
                <div class="toggle-panel-content" :class="{ 'd-none': !customer_instruction_panel }">

                </div>
            </div>
            <div class="toggle-panel" :class="{ active: issues_panel }" >
                <div class="toggle-panel-header" @click="issues_panel = !issues_panel">
                    <h4 class="m-0">Issues</h4>
                </div>
                <div class="toggle-panel-content" :class="{ 'd-none': !issues_panel }">
                    <div class="d-flex" v-if="ITEM.Issues != null">
                        <div class="w-50">
                            <p class="w-100 m-0 sub-title mb-2">Stains</p>
                            <div class="issue-item stain d-flex align-items-center" v-for="(stain, index) in ITEM.Issues.stains" :key="index">
                                <span class="issue-item-num d-flex align-items-center justify-content-center">1</span>
                                <span class="issue-item-name ms-1">Stain - {{stain.name}} </span>
                            </div>
                            <div class="issue-item stain d-flex align-items-center" >
                                <span class="issue-item-name ms-1 pt-2">{{ITEM.Issues.stainstext}} </span>
                            </div>
                            
                        </div>
                        <div class="w-50">
                            <p class="m-0 sub-title mb-2">Damages</p>
                            <div class="issue-item damage d-flex align-items-center" v-for="(damage, index) in ITEM.Issues.damages" :key="index">
                                <span class="issue-item-num d-flex align-items-center justify-content-center">1</span>
                                <span class="issue-item-name ms-1">Damage - {{damage.name}}</span>                                
                            </div> 
                            <div class="issue-item stain d-flex align-items-center" >
                                <span class="issue-item-name ms-1 pt-2">{{ITEM.Issues.damagestext}} </span>
                            </div>                                                       
                        </div>
                    </div>
                </div>
            </div>
            <div class="toggle-panel" :class="{ active: services_panel }" >
                <div class="toggle-panel-header" @click="services_panel = !services_panel">
                    <h4 class="m-0">Services</h4>
                </div>
                <div class="toggle-panel-content" :class="{ 'd-none': !services_panel }">
                    <div class="d-flex" v-if="ITEM.Services != null">
                        <div class="w-50">
                            <p class="w-100 m-0 sub-title mb-2">Cleaning</p>
                            <div class="issue-item stain d-flex align-items-center" v-for="(cleaning, index) in ITEM.Services.cleaning_services" :key="index">
                                <span class="service-item-num service d-flex align-items-center justify-content-center"></span>
                                <span class="issue-item-name ms-1">{{cleaning.name}} </span>
                            </div>
                            <div class="issue-item stain d-flex align-items-center">
                                <span class="issue-item-name ms-1 mt-3">{{ITEM.Services.describeprixnow}} </span>
                            </div>
                        </div>
                        <div class="w-50">
                            <p class="m-0 sub-title mb-2">Tailoring</p>
                            <div class="issue-item stain d-flex align-items-center" v-for="(tailoring, index) in ITEM.Services.tailoring_services" :key="index">
                                <span class="service-item-num d-flex align-items-center justify-content-center" ></span>
                                <span class="issue-item-name ms-1">{{tailoring.name}}</span>                                
                            </div> 
                            <div class="issue-item stain d-flex align-items-center">
                                <span class="issue-item-name ms-1 mt-3">{{ITEM.Services.describeprixnowtailoring}} </span>
                            </div>                                                       
                        </div>
                    </div>
                </div>
            </div>
            <div class="toggle-panel" :class="{ active: item_history_panel }" >
                <div class="toggle-panel-header" @click="item_history_panel = !item_history_panel">
                    <h4 class="m-0">Item history</h4>
                </div>
                <div class="toggle-panel-content" :class="{ 'd-none': !item_history_panel }">

                </div>
            </div>
        </div>
        <div class="item-detail-footer" v-if="ITEM.breif_info.id !=''">
            <div class="d-flex col-12 p-0">
                <div class="col-6 p-0 d-flex justify-content-between">
                    <!-- <button class="item-detail-btn item-detail-btn-void text-center">
                        Void
                    </button>
                    <button class="item-detail-btn item-detail-btn-mark-as-late text-center" @click="markaslate">
                        Mark as late
                    </button> -->
                </div>
                <div class="col-6 p-0 text-end">
                    <button class="item-detail-btn item-detail-btn-print text-center" @click="openModal">
                        Print ticket(s)
                    </button>
                </div>
            </div>
        </div>
    </div>
    <qz-print ref="qz_printer"></qz-print>
</template>
<script>
import { 
    INVOICELIST_GET_CURRENT_SELECTED,
    INVOICELIST_SET_CURRENT_SELECTED,
    INVOICE_MODULE, 
    ITEM_DETAIL_MODULE, 
    ITEM_DETAIL_GET_LOADER,
    ASSEMBLY_HOME_MODULE,
    GET_SELECTED_NAV,
    ITEM_DETAIL_GET_DETAIL,
    ITEM_DETAIL_SET_DETAIL,
    ITEM_DETAIL_LOAD_DETAIL,
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    ORDERLIST_MODULE,
    ORDERLIST_LOADERMSG,
    ORDERLIST_MARK_AS_LATE,
    ORDERDETAIL_UPDATE,
    ORDERDETAIL_MODULE,
    ORDERDETAIL_SET_CURRENT_SELECTED,
    ORDERDETAIL_GET_CURRENT_SELECTED
    } from '../../store/types/types';
import { ref, computed, onMounted, nextTick, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useStore } from 'vuex';
import QzPrint from "../QzPrint";
import ColorTag from "../miscellaneous/ColorTag";

export default {
    name: "ItemDetail",
    props:['item_id' , 'invoiceId'],
    components:{
        QzPrint,
        ColorTag
    },
    setup(props, context){
        const route =useRoute();
        const qz_printer = ref(null);
        const router=useRouter();
        const store =useStore();
        const production_panel = ref(true);
        const customer_instruction_panel = ref(false);
        const issues_panel = ref(false);
        const services_panel = ref(false);
        const item_history_panel = ref(false);
        const itemId = ref('');
        const invoiceId = ref('');
        const itemIdFromRoute = route.params.item_id;

        if(itemIdFromRoute> 0){ 
           itemId.value = itemIdFromRoute;
           invoiceId.value = props.invoiceId;
        }else {
            itemId.value = props.item_id;
            invoiceId.value = props.invoiceId;
        }
        onMounted(()=>{
        })
        const showItemDetail = computed(()=>{
            
            if(store.getters[`${ASSEMBLY_HOME_MODULE}${GET_SELECTED_NAV}`] == 'AssemblyHome')
                return (store.getters[`${ASSEMBLY_HOME_MODULE}${INVOICELIST_GET_CURRENT_SELECTED}`])&&itemId.value>0;
            else if (store.getters[`${ASSEMBLY_HOME_MODULE}${GET_SELECTED_NAV}`] == 'OrderDetails')
                return (store.getters[`${ORDERDETAIL_MODULE}${ORDERDETAIL_GET_CURRENT_SELECTED}`])&&itemId.value>0;
            else 
                return (store.getters[`${INVOICE_MODULE}${INVOICELIST_GET_CURRENT_SELECTED}`])&&itemId.value>0;
        })
        const CURRENT_SELECTED = computed(()=>{
            if(store.getters[`${ASSEMBLY_HOME_MODULE}${GET_SELECTED_NAV}`] == 'AssemblyHome')
                return store.getters[`${ASSEMBLY_HOME_MODULE}${INVOICELIST_GET_CURRENT_SELECTED}`];
            else if (store.getters[`${ASSEMBLY_HOME_MODULE}${GET_SELECTED_NAV}`] == 'OrderDetails')
                return itemId.value;
            else 
                return store.getters[`${INVOICE_MODULE}${INVOICELIST_GET_CURRENT_SELECTED}`];
        });       
        if(CURRENT_SELECTED.value=='' && itemId.value >0){
            if(store.getters[`${ASSEMBLY_HOME_MODULE}${GET_SELECTED_NAV}`] == 'AssemblyHome')
                store.dispatch(`${ASSEMBLY_HOME_MODULE}${INVOICELIST_SET_CURRENT_SELECTED}`, itemId.value)
            else if (store.getters[`${ASSEMBLY_HOME_MODULE}${GET_SELECTED_NAV}`] == 'OrderDetails')
                store.dispatch(`${ORDERDETAIL_MODULE}${ORDERDETAIL_SET_CURRENT_SELECTED}`, itemId.value  )
            else 
                store.dispatch(`${INVOICE_MODULE}${INVOICELIST_SET_CURRENT_SELECTED}`, itemId.value)
        }         
        if(showItemDetail) {
            // store.commit(`${ORDERDETAIL_MODULE}${ORDERDETAIL_SET_LOADER}`,'');
            nextTick(() => {
                store.dispatch(`${ITEM_DETAIL_MODULE}${ITEM_DETAIL_LOAD_DETAIL}`, {itemId :CURRENT_SELECTED.value , invoiceId :invoiceId.value} ).catch((error)=>{
                    if(typeof error.response!="undefined")
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'});
                });;
            });
        }    
        const ITEM = computed( ()=>{
            return store.getters[`${ITEM_DETAIL_MODULE}${ITEM_DETAIL_GET_DETAIL}`];
        } )
        const markaslate = ()=>{
                const order= [ this.ITEM.breif_info.order_id ];
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_LOADERMSG}`,`Marking order as late, please wait...`);
                store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_MARK_AS_LATE}`,order).then(()=>{
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'Order marked as late successfully.',ttl:5,type:'success'});
                    store.commit(`${ORDERDETAIL_MODULE}${ORDERDETAIL_UPDATE}`,{Status:'LATE'});
                }).catch((error)=>{
                    if(typeof error.response!="undefined")
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{ message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'});
                });
        }
        return{
            ITEM,
            qz_printer,
            showItemDetail,
            loaderclass: computed(()=>{
                return store.getters[`${ITEM_DETAIL_MODULE}${ITEM_DETAIL_GET_LOADER}`];
            }),
            production_panel,
            customer_instruction_panel,
            issues_panel,
            services_panel,
            item_history_panel,
            itemIdFromRoute,
            markaslate,
            close: ()=>{
                if(itemIdFromRoute > 0){
                  if(store.getters[`${ASSEMBLY_HOME_MODULE}${GET_SELECTED_NAV}`] == 'AssemblyHome')
                    store.dispatch(`${ASSEMBLY_HOME_MODULE}${INVOICELIST_SET_CURRENT_SELECTED}`,'');
                  else 
                    store.dispatch(`${INVOICE_MODULE}${INVOICELIST_SET_CURRENT_SELECTED}`, '');
                    store.commit(`${ITEM_DETAIL_MODULE}${ITEM_DETAIL_SET_DETAIL}`, { 
                        breif_info: {
                            id: ''
                        },
                        location_history: []
                    });
                    router.back();   
                } else if(props.item_id > 0){
                    context.emit('close');
                }
                             
            },
            // openModal
        }
    },
    methods:{
        openModal(){
            this.$refs.qz_printer.loadPrinterModal(this.ITEM.breif_info.InvoiceID,  ".item-detail-panel")
        }
    }    
}
</script>
<style scoped>
    .odv{
        max-width: 684px;
        background: #FFF;
        height: calc(100% - var(--mainlogoheight));
        position: fixed;
        top: var(--mainlogoheight);
        overflow-y: auto;
        z-index: 10001;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.18);
        padding: 0;
    }
    .item-detail-header{
        padding: 18px 34px 23px 34px;
        height: 65px;
        background: #47454B;
    }
    .item-detail-header-title{
        font-family: 'Gotham Rounded';
        font-size: 22px;
        line-height: 110%;
        color: white;
    }
    .item-detail-close{
        cursor: pointer;
    }
    .item-detail-body{
        padding: 25px 20px 30px;
    }
    .item-detail-info-section,
    .item-detail-variant-section{
        padding-left: 10px;
    }
    .item-detail-name-panel{
        /* width: 380px; */
        width: 55%;

    }
    .item-detail-name{
        font-family: 'Gotham Rounded';
        font-size: 24px;
        line-height: 140%;
        color: #000000;
    }
    .item-detail-suborder{
        font-family: 'Gotham Rounded';
        font-size: 16px;
        line-height: 140%;
        color: #868686;
    }
    .item-detail-location-panel{
        /* width: 250px; */
        width: 45%;
        border-left: solid 3px#42A71E;
        background-color: #ECECEC;
        padding: 5px 12px;
    }
    .item-detail-location-type-name{
        font-family: 'Gotham Rounded';
        font-size: 20px;
        line-height: 140%;
        color: #767676;
    }
    .item-detail-location-type-icon{
        width: 55px;
        height: 25px;
        background: #47454B;
        border-radius: 70px;
        font-family: 'Gotham Rounded';
        font-size: 12px;
        line-height: 140%;    
        color: white;    
    }
    .item-detail-location-name span{
        font-family: 'Gotham Rounded';
        font-style: normal;
        font-weight: normal;
        font-size: 14px;
        line-height: 140%;
        color: #000000;
    }
    .item-detail-variant-section{
        margin-top: 20px;
    }
    .item-detail-brand-color{
        /* width: 380px; */
        width: 55%;        
    }
    .item-detail-fabric-size{
        /* width: 380px; */
        width: 45%;        
    }
    .item-detail-brand p:first-child,
    .item-detail-color p:first-child,
    .item-detail-variant-fabric p:first-child,
    .item-detail-variant-size p:first-child{
        font-family: 'Gotham Rounded Book';
        font-style: normal;
        font-weight: normal;
        font-size: 16px;
        line-height: 140%;
        color: #868686;
    }
    .item-detail-brand p:nth-child(2),
    .item-detail-variant-fabric p:nth-child(2),
    .item-detail-variant-size p:nth-child(2){
        font-family: 'Gotham Rounded';
        font-style: normal;
        font-weight: normal;
        font-size: 16px;
        line-height: 140%;
        color: #47454B;
    }
    .item-detail-fabric-size{
        /* width: 250px; */
        width: 40%;        
    }

    .toggle-panel{
        background: #F8F8F8;
        border: 0.5px solid #C3C3C3;
        box-sizing: border-box;
        border-radius: 6px;
        margin-top: 20px;        
    }
    .toggle-panel-header{
        padding: 20px;
        cursor: pointer;
    }
    .toggle-panel-header:hover{
        background: #F8F8F8;
        border-radius: 6px;
    }
    .toggle-panel.active{
        background: #FFFFFF;
    }
    .toggle-panel-header h4{
        font-family: 'Gilroy';
        font-style: normal;
        font-weight: 800;
        font-size: 22px;
        line-height: 110%;
        color: #47454B;
    }
    .toggle-panel-content{
        padding: 0 30px 40px 30px; 
    }
    .item-detail-body .sub-title{
        font-family: 'Gotham Rounded';
        font-style: normal;
        font-weight: normal;
        font-size: 16px;
        line-height: 140%;
        color: #868686;
    }
    .previous-location-item-title{
        font-family: 'Gotham Rounded';
        font-style: normal;
        font-weight: normal;
        font-size: 14px;
        line-height: 140%;
        color: #000000;
    }
    .previous-location-item-day,
    .previous-location-item-date,
    .previous-location-item-time{
        font-family: 'Gotham Rounded Book';
        font-size: 14px;
        line-height: 140%;        
    }
    .previous-location-item .invoice-location{
        border: solid 1px black;
        padding: 5px;
    }
        
    .issue-item.stain .issue-item-num{
        background: #EF8F00;
    }
    .issue-item.damage .issue-item-num{
        background: #EB5757;
    }
    .issue-item.stain .service-item-num {
        background: #6c757d;
    }
    .issue-item .issue-item-num{
        width: 16.53px;
        height: 18px;        
        border-radius: 50%;
        color: white;
        font-family: 'Gotham Rounded';
        font-style: normal;
        font-weight: normal;
        font-size: 14px;
        line-height: 140%;
    }
    .issue-item .service-item-num {
        width: 16.53px;
        height: 18px;        
        border-radius: 50%;
        color: white;
        font-family: 'Gotham Rounded';
        font-style: normal;
        font-weight: normal;
        font-size: 14px;
        line-height: 140%;
    }
    .issue-item .issue-item-name{
        font-family: 'Gotham Rounded';
        font-style: normal;
        font-weight: normal;
        font-size: 16px;
        line-height: 140%;
        color: #47454B;
    }
    .item-detail-color-item{
        min-width: 15px;
        height: 16px;
        box-sizing: border-box;
        border: 1px solid #F8F8F8;
        box-shadow: 0px 0px 4px rgba(80, 80, 80, 0.2);
        border-radius: 50%;
    }
    .item-detail-footer{
        padding: 30px 20px;
    }
    .item-detail-btn{
        font-family: 'Gotham Rounded';
        font-style: normal;
        font-weight: normal;
        font-size: 16px;
        line-height: 140%;        
        padding: 15px 0;
        border-radius: 4px;
        cursor: pointer;
        background: transparent;
        border: none;
        outline: none;
    }
    .item-detail-btn-void{
        color: #000000;
        border: solid 1px black;
        width: 150px;
    }
    .item-detail-btn-mark-as-late{
        color: #EB5757;
        border: solid 1px #EB5757;
        width: 130px;
    }
    .item-detail-btn-print{
        color: #FFF;
        background: #42A71E;
        width: 225px;
    }
    .item-detail-progressbar{
        background: #FFF;
        height: 6px;
        width: 100%;
        display: block;
        position: absolute;
    }
    .item-detail-progressbar:after{
        background: #42A71E;
        position: absolute;
        width: 0%;
        left: 0;
        content: "";
        height:6px;
    }

    .animate40:after{
        transition: width 2s ease;
        width: 40%;
        animation: pulse 500ms infinite;
    }
    .animate100:after{
        animation: ani100 0.5s ease forwards;
    }
    .item-detail-progressbar.animate40.animate100{
        display: none;
    }
    @keyframes ani100 {
        50%{
            width: 50%;
            opacity: 1;
        }
        60%{
            width: 50%;
            opacity: 1;
        }
       99%{
           width: 99%;
            opacity: 1;
        }
        100%{
            width: 100%;
            opacity: 0;
        }
    }

    @keyframes pulse {
        0%{
            opacity: 0.7;
        }
        100%{
            opacity: 1;
        }
    }
    .invoice-location.rounded-pill {
    display: flex;
    flex-direction: row;
    justify-content: space-evenly;
    align-items: center;
    padding: 0 16px 0 8px;
    width: -webkit-max-content;
    width: -moz-max-content;
    width: max-content;
    min-width: 135px;
}
.invoice-location span {
    font-size: 12px;
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: normal;
    font-size: 12px;
    line-height: 14px;
    color: #000000;
}

</style>