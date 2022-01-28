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
                        <h1 class="tile_h1">New order</h1>
                         <div class="row">
                            <div class="col-2">
                                <button class="btn btn-outline-dark body_small_bold" @click="proceedToDetailling">
                                Proceed to detailing
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                        <div class="barcode-scan col p-0" @click="featureUnavailable('Scan barcode')"><svg width="41" height="40" viewBox="0 0 41 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M32.542 0.000139651C32.1562 0.00726752 31.8482 0.324459 31.8482 0.712927C31.8482 1.09962 32.1562 1.41682 32.542 1.42571H38.9173V7.9245V7.92272C38.9173 8.31832 39.2342 8.63729 39.6253 8.63729C40.0165 8.63729 40.3351 8.31832 40.3351 7.92272V0.714566C40.3351 0.320752 40.02 0 39.6289 0L32.542 0.000139651ZM1.35145 0.035779C1.16206 0.0339971 0.98152 0.11062 0.846995 0.244269C0.714246 0.377917 0.639908 0.561458 0.639908 0.750359V7.88553C0.636368 8.07798 0.708935 8.2633 0.841684 8.40051C0.974433 8.53772 1.15674 8.61435 1.3479 8.61435C1.53907 8.61435 1.72137 8.53772 1.85412 8.40051C1.98687 8.2633 2.05944 8.07798 2.0559 7.88553V1.46155H8.51096C8.70212 1.46511 8.8862 1.39205 9.02248 1.25841C9.15877 1.12476 9.23488 0.941217 9.23488 0.748758C9.23488 0.556298 9.15877 0.372762 9.02248 0.239109C8.8862 0.103677 8.70212 0.0306196 8.51096 0.0359661L1.35145 0.035779ZM4.88963 5.7399V16.4319H6.30562V5.7399H4.88963ZM7.7216 5.7399V16.4319H10.5553V5.7399H7.7216ZM12.6759 5.7399V16.4319H14.0937V5.7399H12.6759ZM15.5097 5.7399V16.4319H16.9257V5.7399H15.5097ZM19.0532 5.7399V16.4319H21.8798V5.7399H19.0532ZM24.0075 5.7399V16.4319H25.4235V5.7399H24.0075ZM26.8395 5.7399V16.4319H28.2555V5.7399H26.8395ZM30.3777 5.7399V16.4319H33.2114V5.7399H30.3777ZM34.6274 5.7399V16.4319H36.0434V5.7399H34.6274ZM1.27727 19.2844V19.2827C0.893185 19.313 0.602902 19.6444 0.622386 20.0329C0.641856 20.4195 0.965761 20.7207 1.35163 20.7082H39.5813C39.9654 20.6993 40.2734 20.3839 40.2734 19.9955C40.2734 19.6088 39.9654 19.2916 39.5813 19.2827H1.35163H1.27729L1.27727 19.2844ZM4.88982 23.5629V34.255L6.3058 34.2532V23.5612L4.88982 23.5629ZM7.72178 23.5629V34.255H10.5555V23.5629H7.72178ZM12.6761 23.5629V34.255H14.0939V23.5629H12.6761ZM15.5099 23.5629V34.255L16.9258 34.2532V23.5612L15.5099 23.5629ZM19.0534 23.5629V34.255H21.88V23.5629H19.0534ZM24.0077 23.5629V34.255H25.4237V23.5629H24.0077ZM26.8397 23.5629V34.255H28.2556V23.5629H26.8397ZM30.3778 23.5629V34.255L33.2116 34.2532V23.5612L30.3778 23.5629ZM34.6276 23.5629V34.255H36.0435V23.5629H34.6276ZM1.34134 31.3537C1.15373 31.3554 0.974959 31.4321 0.842192 31.5675C0.711215 31.7029 0.638644 31.8847 0.640415 32.0754V39.2835C0.640415 39.4742 0.714753 39.6559 0.847502 39.7896C0.982023 39.925 1.16256 39.9999 1.35195 39.9999H8.43911C8.6285 40.0034 8.81258 39.9304 8.94887 39.795C9.08515 39.6613 9.16126 39.4778 9.16126 39.2871C9.16126 39.0946 9.08515 38.9111 8.94887 38.7774C8.81258 38.642 8.6285 38.5689 8.43911 38.5743H2.05655V32.0755C2.05832 31.883 1.98398 31.6995 1.84946 31.5623C1.71494 31.4269 1.53263 31.352 1.34147 31.3538L1.34134 31.3537ZM39.6191 31.3893C39.4315 31.3911 39.2509 31.4695 39.1199 31.6031C38.9889 31.7386 38.9164 31.9221 38.9181 32.111V38.5368H32.4649C32.079 38.5457 31.7728 38.8611 31.7728 39.2496C31.7728 39.6363 32.0791 39.9535 32.4649 39.9624H39.6295C40.0207 39.9606 40.3358 39.6398 40.3358 39.246V32.1109C40.3375 31.9184 40.2632 31.7349 40.1269 31.5994C39.9924 31.4622 39.8101 31.3874 39.6189 31.3892L39.6191 31.3893Z" fill="black"/>
                        </svg>
                            Scan bag barcode to pre-fill order details
                        </div>
                            </div>
                        </div>

                        <div class="panel-wrapper row">
                            <div class="panel-col-1 col  ">
                                <customer-details-panel></customer-details-panel>
                                <div class="panel">
                                    <h2 class="subtitle">Payment method</h2>
                                </div>
                            </div>
                            <div class="panel-col-2 col  ">
                                <div class="panel">
                                    <h2 class="subtitle">Order Details</h2>
                                    <div class="row border-bottom m-0">
                                        <div class="col p-0"><h2 class="subtitle">Slot</h2></div>
                                        <div class="col p-0 justify-content-end d-flex rec_switch" v-if="showRecurring"><switch-btn v-model="isRecurring" label-left="Recurring"></switch-btn></div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="" :class="{'col-4':isRecurring,'col-12':!isRecurring}">
                                            <div class="row">
                                                <div class="col">
                                            <select-options classnames="deliverymethod" v-model="deliverymethod" placeholder="Choose a method" :options="deliverymethods" name="delivery_method" hint=""  label="Delivery method" :disabled="deliverymethod_disabled"></select-options>
                                                </div>
                                            </div>
                                            <transition name="popinout">
                                            <div class="row mt-3" v-if="deliverymethod=='in_store_collection'">
                                                <div class="col-3"><date-picker v-model="isc_dropoff" name="isc_dropoff" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Drop off" ></date-picker>
                                                </div>
                                                <div class="col-3">
                                                    <time-slot-picker v-model="isc_dropoff_timeslot"   name="isc_dropoff_timeslot" :available-slots="[1]"  label=" "></time-slot-picker>
                                                </div>

                                                <div class="col-3"><date-picker v-model="isc_pickup" name="isc_pickup" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Pickup" ></date-picker>
                                                </div>
                                                <div class="col-3">
                                                    <time-slot-picker v-model="isc_pickup_timeslot"   name="isc_pickup_timeslot" :available-slots="[1]"  label="Pick up Time"></time-slot-picker>
                                                </div>
                                            </div>
                                            </transition>


                                            <transition name="popinout">
                                                <div class="row mt-3" v-if="deliverymethod=='delivery_only'">
                                                    <div class="col-3"><date-picker v-model="do_dropoff" name="do_dropoff" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Drop off" ></date-picker>
                                                    </div>
                                                    <div class="col-3">
                                                        <time-slot-picker v-model="do_dropoff_timeslot"   name="do_dropoff_timeslot" :available-slots="[1]"  label=" "></time-slot-picker>
                                                    </div>

                                                    <div class="col-3"><date-picker v-model="do_delivery" name="do_delivery" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Delivery" ></date-picker>
                                                    </div>
                                                    <div class="col-3">
                                                        <time-slot-picker v-model="do_delivery_timeslot"   name="do_delivery_timeslot" :available-slots="[1]"  label=" "></time-slot-picker>
                                                    </div>
                                                </div>
                                            </transition>


                                            <transition name="popinout">
                                                <div class="row mt-3" v-if="deliverymethod=='home_delivery'&&!isRecurring">
                                                    <div class="col-3"><date-picker v-model="hd_pickup" name="hd_pickup" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Pick up" ></date-picker>
                                                    </div>
                                                    <div class="col-3">
                                                        <time-slot-picker v-model="hd_pickup_timeslot"   name="hd_pickup_timeslot" :available-slots="[1]"  label=" "></time-slot-picker>
                                                    </div>

                                                    <div class="col-3"><date-picker v-model="hd_delivery" name="hd_delivery" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Delivery" ></date-picker>
                                                    </div>
                                                    <div class="col-3">
                                                        <time-slot-picker v-model="hd_delivery_timeslot"   name="hd_delivery_timeslot" :available-slots="[1]"  label=" "></time-slot-picker>
                                                    </div>
                                                </div>
                                            </transition>

                                            <transition name="popinout">
                                                <div class="row mt-3" v-if="deliverymethod=='shipping'">
                                                    <div class="col-3"><date-picker v-model="shp_received" name="shp_received" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Received" ></date-picker>
                                                    </div>
                                                    <div class="col-3">
                                                        <time-slot-picker v-model="shp_received_timeslot"   name="shp_received_timeslot" :available-slots="[1]"  label=" "></time-slot-picker>
                                                    </div>

                                                    <div class="col-3"><date-picker v-model="shp_delivery" name="shp_delivery" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top left'}" label="Delivery" ></date-picker>
                                                    </div>

                                                </div>
                                            </transition>
                                        </div>
                                        <div :class="{'col-8':isRecurring}" v-if="isRecurring">
                                            <transition name="popinout">
                                                <div v-if="isRecurring">
                                                    <recurring-form></recurring-form>
                                                </div>
                                            </transition>
                                        </div>
                                    </div>

                                    <div class="row border-bottom">
                                        <div class="col detailsection"><h2 class="subtitle">Details</h2></div>
                                    </div>
                                    <div class="row mt-3" v-if="deliverymethod=='shipping'">
                                        <h3 class="body_medium">Delivery Address</h3>
                                        <div class="col-6 body_medium mt-3">
                                            <div class="row form-group mx-0">
                                                <input type="text" v-model="shp_address1" class="form-control" placeholder="Address1"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3" v-else>
                                        <h3 class="body_medium">Delivery Address</h3>
                                        <div class="body_medium mt-3" v-if="cur_cust">
                                            <span v-if="cur_cust.address1 && cur_cust.address1!=''">{{cur_cust.address1}}</span>
                                            <br/>
                                            <span v-if="cur_cust.postcode && cur_cust.postcode!=''">{{cur_cust.postcode}}</span><span v-if="cur_cust.Town && cur_cust.Town!=''">, {{cur_cust.Town}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mx-0 mt-5 mb-4 justify-content-end align-items-center">
                                <div class="col-2">
                                    <a href="javascript:void(0)" id="cancel_new_order">Cancel</a>
                                </div>
                                <div class="col-2 px-0">
                                    <button class="btn btn-grey w-100">Proceed to detailing</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    </div>
                   
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import BreadCrumb from '../layout/BreadCrumb'
    import SideBar from '../layout/SideBar'
    import {featureUnavailable} from "../helpers/helpers";
    import SelectOptions from '../miscellaneous/SelectOptions'
    import SwitchBtn from '../miscellaneous/SwitchBtn'
    import {useStore} from 'vuex';
    import DatePicker from '../miscellaneous/DatePicker';
    import TimeSlotPicker from '../miscellaneous/TimeSlotPicker';
    import CustomerDetailsPanel from './CustomerDetailsPanel';

    import { useRouter, useRoute } from 'vue-router';

    import {ref,onMounted,nextTick,computed,watch} from 'vue';
    import {
      NEWORDER_CUR_CUSTOMER,
        NEWORDER_MODULE,
        NEWORDER_PRELOAD_FORM,
        NEWORDER_PRELOAD_FORM_GET,
        NEWORDER_PRELOAD_ORDER_GET
    } from "../../store/types/types";
import RecurringForm from '../miscellaneous/RecurringForm.vue';
    export default {
        name: "NewOrder",
        components:{BreadCrumb,SideBar,SelectOptions,SwitchBtn,DatePicker,TimeSlotPicker,CustomerDetailsPanel,RecurringForm},
        setup(){
            const router = useRouter();

            const showcontainer=ref(false);
            const deliverymethod =ref('');
            //isc : in store collection
            const isc_dropoff =ref('');
            const isc_dropoff_timeslot=ref('');
            const isc_pickup=ref('');
            const isc_pickup_timeslot=ref('');

            //do : delivery only
            const do_dropoff =ref('');
            const do_dropoff_timeslot=ref('');
            const do_delivery=ref('');
            const do_delivery_timeslot=ref('');

            //hd : home delivery
            const hd_pickup =ref('');
            const hd_pickup_timeslot=ref(0);
            const hd_delivery=ref('');
            const hd_delivery_timeslot=ref(0);

            //shp : shipping
            const shp_received =ref('');
            const shp_received_timeslot=ref('');
            const shp_delivery=ref('');
            const shp_address1=ref('');
            const shp_postcode = ref('');
            const shp_town = ref('');


            const deliverymethod_disabled=ref(false);
            const isRecurring=ref(false);
            const recurring_date = ref([]);

            const paths=ref([{name:'Order',route:'LandingPage'},{name:'New Order',route:'NewOrder'}]);
            const store=useStore();
            store.dispatch(`${NEWORDER_MODULE}${NEWORDER_PRELOAD_FORM}`);
            onMounted(()=>{
                nextTick(()=>{
                    showcontainer.value=true;
                });

            });
            const deliverymethods=ref([
                {
                    value:'in_store_collection',
                    display:'In Store collection'
                },
                {
                    value:'home_delivery',
                    display:'Home Delivery'
                },
                {
                    value:'delivery_only',
                    display:'Delivery Only'
                },
                {
                    value:'shipping',
                    display:'Shipping'
                },
            ]);

            const showRecurring = ref(true);


            watch(()=>deliverymethod.value,(val)=>{
                if(val=='home_delivery' || val==''){
                    showRecurring.value=true;
                }else{
                    showRecurring.value=false;
                }
            });


            watch(() =>isRecurring.value, (current_val, previous_val) => {
                    if(current_val===true) {
                        deliverymethod.value = 'home_delivery';
                        deliverymethod_disabled.value=true;
                    }else {
                        deliverymethod_disabled.value=false;
                        deliverymethod.value = '';
                    }
                });
            /*const FORM=computed(()=>store.getters[`${NEWORDER_MODULE}${NEWORDER_PRELOAD_FORM_GET}`]);
            watch(() =>_.cloneDeep(FORM.value), (current_val, previous_val) => {
                current_val.TypeDeliveries.forEach(item=>{
                    deliverymethods.value.push({value:item,display:item});
                })
            })*/
            const order=computed(()=>store.getters[`${NEWORDER_MODULE}${NEWORDER_PRELOAD_ORDER_GET}`]);
            function proceedToDetailling() {
                router.push('/detailing_item/57908/12345678');
            }
            const cur_cust = computed(()=>{
                const cust_cur = store.getters[`${NEWORDER_MODULE}${NEWORDER_CUR_CUSTOMER}`];
                console.log(cust_cur);

                if(cust_cur){
                    shp_address1.value = cust_cur.address1;
                }

                return cust_cur;
            });


            return {
                showcontainer,
                paths,
                featureUnavailable,
                deliverymethod,
                deliverymethod_disabled,
                deliverymethods,
                isRecurring,
                isc_dropoff,
                isc_dropoff_timeslot,
                isc_pickup,
                isc_pickup_timeslot,
                do_dropoff,
                do_dropoff_timeslot,
                do_delivery,
                do_delivery_timeslot,
                hd_pickup,
                hd_pickup_timeslot,
                hd_delivery,
                hd_delivery_timeslot,
                shp_received,
                shp_received_timeslot,
                shp_delivery,
                order,
                proceedToDetailling,
                showRecurring,
                cur_cust,
                shp_address1,
                shp_postcode,
                shp_town
            }
        }
    }
</script>

<style scoped>

    .panel>h2:first-child{
        line-height: 22px;
        margin-bottom: 30px;
    }
    .rec_switch .switch-wrapper{
        margin-bottom: 0.5rem;
    }
    .detailsection{
        margin-top: 30px;
    }

    .btn-grey{
        background: #E0E0E0;
    }

    .btn-grey:hover{
        background: #42A71E;
    }

    h3.body_medium{
        color:#868686;
    }


</style>
