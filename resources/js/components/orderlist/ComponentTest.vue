<template>
    <transition
            enter-active-class="animate__animated animate__fadeIn"
    >
        <div class="container-fluid h-100 bg-color" v-if="showcontainer">
            <main-header></main-header>
            <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax" style="z-index:100">
            <side-bar></side-bar>
                <div class="col main-view p-5">
                    <h2>Component library</h2>
                    <div class="row pb-4 form-group">
                        <div class="col-12 pb-3"><label>Electron print</label></div>
                        <div class="col-2">
                            <input type="text" class="form-control w-100" v-model="cur_id" placeholder="ID invoice/order"/>
                        </div>
                        <div class="col-2">
                            <select v-model="print_type" class="form-control w-100">
                                <option value="">Type print</option>
                                <option value="invoice">Invoice</option>
                                <option value="order">Order</option>
                            </select>
                        </div>
                        <div class="col-2">
                            <button class="btn btn-success w-100 py-1 text-white" @click="loadUrl" style="height:37.6px;">Electron print</button>
                            <a href="#" id="electron_link" class="d-none">Link</a>
                        </div>
                    </div>

                    <div class="row my-4">
                        <div class="col-3">
                            <setup-intent></setup-intent>
                            <!--
                            <button class="btn btn-outline-success" @click="router.push({name:'VueStripeTest'})">Stripe test</button>
                            -->
                        </div>
                    </div>
                    <div class="row my-4">
                        <div class="col-3">
                        <mini-checkout :order_id="134932"></mini-checkout>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-3">

                           <switch-btn v-model="swtch" label-left="Label left" label-right="Label right" ></switch-btn>
                        </div>
                        <div class="col-1 p-2">
{{swtch}}
                        </div>
                        <div class="col-3">

                            <switch-btn  label-left="Disabled" label-right="xxxx" :disabled="true"></switch-btn>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-3">

                            <select-options v-model="sel" placeholder="Choose a number" :options="[{value:'',display:''},{value:'1',display: 'One'},{value:'2',display:'Two'}]" name="select1" hint="Here is your hint."></select-options>
                        </div>
                        <div class="col p-2">
                            {{sel}}
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-3">
                            <select-options v-model="sel1" placeholder="Choose a number" :options="[{value:'',display:''},{value:'1',display: 'One'},{value:'2',display:'Two'}]" name="select2" hint="nice." :valid="true" label="Positive"></select-options>
                        </div>
                        <div class="col p-2">
                            {{sel1}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <select-options v-model="sel2" placeholder="Choose a number" :options="[{value:'',display:''},{value:'1',display: 'One'},{value:'2',display:'Two'}]" name="select3" hint="Oops." :valid="false" label="Negative"></select-options>
                        </div>
                        <div class="col p-2">
                            {{sel2}}
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-3">
                            <date-picker v-model="date" name="date" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Date picker" hint="Choose any timeslot"></date-picker>

                        </div>
                        <div class="col p-2">
                            {{date}}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-3">
                            <date-picker v-model="date2" name="date2" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Date picker" hint="Choose available timeslot" :available-dates="['2021-09-14','2021-09-15','2021-09-16','2021-09-18']"></date-picker>

                        </div>
                        <div class="col p-2">
                            {{date2}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <date-picker v-model="date3" name="date3" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Date picker" hint="disabled till 2021-09-10" disabled-to-date="2021-09-10" @loadtranche="loadtranche('hd_pickup')"></date-picker>

                        </div>
                        <div class="col p-2">
                            {{date2}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <time-slot-picker placeholder="00-00 AM" v-model="slot"   name="timepick1" :available-slots="available_slots" hint="How about a very long hint" label="Time"></time-slot-picker>
                        </div>
                        <div class="col p-2">
                            {{slot}}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-3">
                            <time-slot-picker placeholder="00-00 AM" v-model="slot2"   name="timepick2" :available-slots="[1,5]" hint="Oops its disabled" label="Time" :disabled="true"></time-slot-picker>
                        </div>
                        <div class="col p-2">
                            {{slot2}}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <tab-pane :tabs="{first:'First tab',second:'second tab'}">
                                <template v-slot:first>
                                    1st
                                </template>
                                <template v-slot:second>
                                    2nd
                                </template>
                            </tab-pane>
                        </div>

                    </div>

                    <div class="row" style="padding-top:20px">
                        <div class="col">
                            <button @click="scanBarcode" >Scan Barcode</button>
                            <order-barcode  v-on:scan_Barcode="scanBarcode" class="back-layer" v-if="show_barcode" style="background: transparent;" ></order-barcode>
                            <div  v-if="show_barcode" class= "overlay">
                            </div>
                        </div>


                    </div>

                    <div class="row search">

                        <span class= "subtitle">Customer details</span>
                        <div  style="padding: 0;" >
                            <search  v-model="search" name="search" v-on:getCustomer="ClickCustomer" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Search a customer"  ></search>
                      {{search}}
                        </div>


   </div>

                    <div class="row recurring-form">
                      <div class="col-4 recurring">
                            <recurring-form  v-model="data"></recurring-form>
                      </div>

                    </div>

                    <div class="row">
                        <div class="col-4">
                              <select-options v-model="sel_picto" placeholder="Choose a picto" :options="picto_names" name="select3" hint=""></select-options>
                        </div>
                        <div class="col-12">{{sel_picto}}</div>
                    </div>
                    <div class="row mt-3 mb-5">
                        <div class="col-8">
                            <item-picto-new :pictoname="picto"
                            :selectable="true" :stainzone="stainzone" @add-stain-zone="addStainZone"
                            issue_type="stain"  @get-zone-detail="showZoneDetail" ref="svg_comp"></item-picto-new>
                        </div>
                        <div class="col-4">
                            <!--
                            <pre>{{zone_detail}}</pre>
                            -->
                            <div v-if="zone_detail.id" class="row">
                                <div class="row mx-0">
                                    <div class="col-4">Id zone:</div>
                                    <div class="col-8">{{zone_detail.id}}</div>
                                </div>
                                <div class="row mx-0 align-items-center mt-3">
                                    <div class="col-4">Face:</div>
                                    <div class="col-8 form-group">
                                        <input type="text" class="form-control" v-model="zone_face"/>
                                    </div>
                                </div>
                                <div class="row mx-0 align-items-center mt-3">
                                    <div class="col-4">Side:</div>
                                    <div class="col-8 form-group">
                                        <input type="text" class="form-control" v-model="zone_side"/>
                                    </div>
                                </div>
                                <div class="row mx-0 align-items-center mt-3">
                                    <div class="col-4">Description:</div>
                                    <div class="col-8 form-group">
                                        <input type="text" class="form-control" v-model="zone_desc"/>
                                    </div>
                                </div>
                                <div class="row mx-0 align-items-center mt-3">
                                    <div class="col-4">Position X:</div>
                                    <div class="col-8 form-group">
                                        <input type="number" class="form-control zone-coord" id="zone_label_x" v-model="zone_labelx"/>
                                    </div>
                                </div>
                                <div class="row mx-0 align-items-center mt-3">
                                    <div class="col-4">Position Y:</div>
                                    <div class="col-8 form-group">
                                        <input type="number" class="form-control zone-coord" id="zone_label_y" v-model="zone_labely"/>
                                    </div>
                                </div>
                                 <div class="row mx-0 justify-content-center mt-3">
                                     <div class="col-4">
                                        <button class="btn btn-success w-100 text-white" @click="updateZoneLabelPos">Update</button>
                                     </div>
                                 </div>

                            </div>

                        </div>
                    </div>

                    <div class="row detailing-right-panel">
                            <detailing-right-panel
                            customerName="Test"
                            :item_description="item_description"
                            :detailingitem="detailingitem"
                            step="1"
                        ></detailing-right-panel>
                    </div>

                    <div class="row my-5">
                        <div class="col-2">
                            <button class="btn btn-outline-success" @click="showModal">Show modal</button>
                            <modal ref="bmodal">
                                <template #bheader>
                                    <div class="bmodal-header py-3 text-center">HEADER</div>
                                </template>
                                <template #bcontent>
                                    <div class="row mx-0">
                                        <div class="col-12">Your content goes here</div>
                                    </div>
                                </template>
                                <template #mbuttons>
                                    <div class="row mx-0 justify-content-center mt-3">
                                        <div class="col-2">
                                            <button class="btn btn-outline-success" @click="checkMethod">Test</button>
                                        </div>
                                    </div>
                                </template>
                            </modal>

                        </div>
                    </div>

                    <div class="row my-5">
                        <div class="col-2">
                            <invoice-pdf></invoice-pdf>
                        </div>
                    </div>

            </div>
        </div>
        </div>
    </transition>
</template>

<script>
    import {TOASTER_MODULE,TOASTER_MESSAGE} from "../../store/types/types";

    import {ref,onMounted,nextTick,watch} from 'vue';
    import {useStore} from 'vuex';
    import MainHeader from '../layout/MainHeader';
    import SideBar from '../layout/SideBar'
    import SelectOptions from '../miscellaneous/SelectOptions'
    import TimeSlotPicker from '../miscellaneous/TimeSlotPicker'
    import DatePicker from '../miscellaneous/DatePicker'
    import TabPane from '../miscellaneous/TabPane'
    import {usePermission} from "../helpers/helpers";
    import {PERMISSIONS} from "../../store/types/permission_types";
    import Search from '../miscellaneous/Search';
    import SwitchBtn from '../miscellaneous/SwitchBtn'
    import OrderBarcode from '../miscellaneous/OrderBarcode'
    import RecurringForm from '../miscellaneous/RecurringForm'
    import ItemPictoNew from '../miscellaneous/ItemPictoNew.vue'
    import DetailingRightPanel from '../detailing/DetailingRightPanel.vue';
    import Modal from '../miscellaneous/Modal.vue';
    import { useRouter, useRoute } from "vue-router";
    import InvoicePdf from '../miscellaneous/InvoicePdf.vue';
    import SetupIntent from  '../miscellaneous/SetupIntent.vue';
    import MiniCheckout from "../miscellaneous/MiniCheckout.vue";


    export default {
        name: "ComponentTest",
        components: { SideBar, MainHeader,SelectOptions,TimeSlotPicker,DatePicker,TabPane,Search,SwitchBtn,OrderBarcode, RecurringForm,ItemPictoNew,DetailingRightPanel,Modal,InvoicePdf, SetupIntent,MiniCheckout},
        setup(props,context){
            const showcontainer=ref(false);
            const show_barcode= ref(false);
            const Customer= ref('');
            const Scan= ref('');

            const store = useStore();

            const swtch=ref(true);

            const sel=ref(1);
            const slot=ref(5);
            const slot2=ref(0);
            const sel1=ref(2);
            const sel2=ref('');
            const date=ref('');
            const date2=ref('2022-03-15');
            const date3=ref('2022-03-15');
            const search= ref('');
            const data= ref([]);
            const picto = ref('');
            const picto_names = ref([]);
            const sel_picto = ref('shirt');
            const svg_comp = ref();

            const item_description = ref({});
            const detailingitem= ref({});
            detailingitem.value={item_id:12345678,pricecleaning:20};
            item_description.value={typeitem_name:'shirt'};
            const stainzone=ref([]);

            const shp_postcode =ref('');
            const hd_pickup =ref('');
            const available_slots = ref([]);
            const router = useRouter();

            const cur_id = ref('');
            const print_type = ref('');

            const cur_user = ref('');

            const fetchUser = ()=>{
                axios.get('/get-current-user',{})
                    .then((res)=>{
                        cur_user.value = res.data.user;
                        console.log('user',res.data.user);
                    }).catch((err)=>{

                    }).finally(()=>{

                    });
            }

            fetchUser();

            const loadUrl = ()=>{
                if(print_type.value!='' && cur_id.value!='' && parseInt(cur_id.value)){

                    let url = "electron-param://?type="+print_type.value+"&id="+cur_id.value+'&userid='+cur_user.value.id;
                    console.log('url',url);

                    let anchor = document.getElementById('electron_link');
                    anchor.setAttribute('href',url);


                    anchor.click();
                }else{
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                        message: 'Please enter order/invoice id and print type',
                        ttl: 3,
                        type: 'danger'
                    });
                }
            }

            watch(() => Scan.value, (current_val, previous_val) => {
              if(Scan.value == false) {
                  show_barcode.value= false;
              }else {
                  show_barcode.value= true;
              }

            });

            onMounted(()=>{
                nextTick(()=>{
                    showcontainer.value=true;

                });

            });
            const perm=usePermission;
            perm(PERMISSIONS.MARK_AS_LATE).then((res)=>{
               console.log(res);
            }).catch((err)=>{
                console.log(err)
            });
            perm(PERMISSIONS.MARK_AS_LATE).then((res)=>{
                console.log(res);
            }).catch((err)=>{
                console.log(err)
            });


            function scanBarcode(value){
                Scan.value = value;
            };

            function ClickCustomer(value){
                Customer.value = value;
                console.log('customer' ,value )
             };

               watch(() => data.value, (current_val, previous_val) => {
                 console.log('recurring_order', current_val)

            });

            function getAllPictoNames(){
                 axios.post('/get-picto-names',{})
                    .then((res)=>{
                        if(res.data.details){
                            let pictos = res.data.details;

                            pictos.forEach(v => {
                                picto_names.value.push({value:v.name,display:v.name});
                            });
                        }
                    }).catch((error)=>{

                    }).finally(()=>{

                    });
            }

            getAllPictoNames();

             picto.value = sel_picto.value;

             watch(() => sel_picto.value, (current_val, previous_val) => {


                picto.value=current_val;

                if(current_val != previous_val){
                    stainzone.value = [];
                }

            });

            function loadtranche(comp){
                const var_tmp_date = eval(comp);
                const today = new Date();

                let all_tranches = [1,3,5,7,9,11];
                let available_tranches = [];

                var_tmp_date.value =date3.value;;
                shp_postcode.value='W36JQ';
                if(shp_postcode.value !=''){
                    axios.post('/get-tranche-by-postcode',{
                        postcode: shp_postcode.value
                    }).then((response)=>{
                        let tranches = response.data.available_slots;
                        let self = this;
                        all_tranches.forEach(function(v,i){
                            if(tranches[v] && tranches[v].includes(var_tmp_date.value)){
                                if(new Date(var_tmp_date.value).getTime()==today.getTime()){
                                    if(!available_tranches.includes(v)&&self.getTimeFromSlot(v)>today.getHours()){
                                        available_tranches.push(v);
                                    }
                                }else{
                                    if(!available_tranches.includes(v)){
                                        available_tranches.push(v);
                                    }
                                }
                            }
                        });
                        // set available_slots for timeSlot
                        available_slots.value=available_tranches;
                    }).catch((error)=>{
                       console.log(error);
                    }).finally(()=>{
                    });

                }
           }
           function getTimeFromSlot(slot){
                let slot_from_arr = [];
                let hr = 0;

                for (let i = 1; i <= 13; i+=2) {
                    hr = i+7;
                    if(i==13){
                        hr = 8; //To confirm for 8-8 slot
                    }

                    slot_from_arr[i] = hr.toString().padStart(2,'0');

                }

                return slot_from_arr[slot];
            }

            /*SVG zone labels*/

            const zone_detail = ref({});
            const zone_desc = ref("");
            const zone_labelx = ref(0);
            const zone_labely = ref(0);
            const zone_face = ref("");
            const zone_side = ref("");

            function showZoneDetail(zone,is_active){
                if(is_active==1){
                    zone_detail.value = zone;
                    if(zone.id){
                        zone_desc.value = zone.description;
                        zone_labelx.value = zone.label_x;
                        zone_labely.value = zone.label_y;
                        zone_face.value = zone.face;
                        zone_side.value = zone.side;
                    }
                }else{
                    zone_detail.value = {};
                }
            }

            watch(()=>zone_labelx.value,(val)=>{
                svg_comp.value.updateLabelPos(zone_detail.value.id,'x',val);
            });

            watch(()=>zone_labely.value,(val)=>{
                svg_comp.value.updateLabelPos(zone_detail.value.id,'y',val);
            });

            function updateZoneLabelPos(){
                axios.post('update-zone-label-pos',{
                    id:zone_detail.value.id,
                    description:zone_desc.value,
                    face:zone_face.value,
                    side:zone_side.value,
                    label_x:zone_labelx.value,
                    label_y:zone_labely.value,

                }).then((res)=>{
                    if(res.data.updated){
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,
                            {
                                message: 'Label position updated',
                                ttl: 3,
                                type: 'success'
                            });
                    }
                }).catch((err)=>{
                    console.log(err);
                }).finally(()=>{
                    svg_comp.value.loadSvgZones(zone_detail.value.zone_type);
                });
            }
            function addStainZone(id) {
                if (!stainzone.value.some(z => z.id_zone === id)) {
                    stainzone.value.push({ index:stainzone.value.length>0?stainzone.value[stainzone.value.length-1].index+1:1, id_zone: id, id_issue: 0, description: '' });
                } else {
                    stainzone.value.splice(stainzone.value.findIndex((z) => { return z.id_zone === id }), 1);
                }
            }

            const checkMethod = ()=>{
                console.log('method called from parent component');
            }

            const bmodal = ref();

            const showModal = ()=>{
                bmodal.value.showModal();
            }

            return {

                showcontainer,
                sel,
                sel1,
                sel2,
                slot,
                slot2,
                date,
                date2,
                date3,
                search,
                show_barcode,
                scanBarcode,
                Customer,
                Scan,
                ClickCustomer,
                data,
                swtch,
                picto,
                picto_names,
                getAllPictoNames,
                sel_picto,
                item_description,
                detailingitem,
                loadtranche,
                getTimeFromSlot,
                shp_postcode,
                hd_pickup,
                available_slots,
                showZoneDetail,
                zone_detail,
                zone_face,
                zone_side,
                zone_desc,
                zone_labelx,
                zone_labely,
                svg_comp,
                updateZoneLabelPos,
                stainzone,
                addStainZone,
                checkMethod,
                bmodal,
                showModal,
                router,
                cur_id,
                print_type,
                loadUrl,
            }
        },

    }
</script>

<style scoped>

    .hmax{
        height: calc(100% - var(--mainlogoheight));
        padding-top:var(--mainlogoheight) ;
    }

    .auth-logo{
        height: var(--authlogoheight);
    }
    .back-layer{
        background:rgba(224, 224, 224,0.6);
        position: fixed;
        top: 0;
        left:0;
        height: 100%;
        width: 100%;
        z-index: 9999;
    }
    .search{
    background: #fff;
    width: 517px;
    height: auto;
    box-shadow: 0px 4px 16px rgb(0 0 0 / 12%);
    border-radius: 5px;
    padding: 20px 18px 40px 15px;
    margin-top: 28px;
    position: relative;
    }
    .overlay{
        display: block;
        content: '';
        width: 100%;
        height: 100%;
        position: fixed;
        left: 0;
        top: 0;
        background: black;
        opacity: 0.6;
        cursor: pointer;
        z-index: 100;
    }
    .subtitle{
        height: 25px;
    }
    .recurring{
       margin: 20px;
       width: 540px;
    }
    .recurring-form{
        width: 580px;
        flex-wrap: nowrap;
    }

</style>
