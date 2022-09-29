<template>
    <div class="row mb-3">
        <div class="col-12" id="services-desc">
            Select one or multiple services. Note that available services depend on customer preferences.<br/>
            However, you have the possibility to unlock a disabled service with the consent of the customer.
        </div>
    </div>
    <div class="row block-service-main">
        <div class="col-2 each-main-service d-flex text-center align-items-center justify-content-center" v-for="(service,index) in main_services" :id="'main_service_'+index" :class="{'sel_service':index==1}" @click="toggleMainService(index)">
            {{service}}
        </div>
    </div>
    <!--CLEANING SERVICES-->
    <div class="row" :class="{'d-none':main_service!=1}">
        <div class="accordion-item col-12 px-0 mt-4" v-for="(services,group) in cleaning_services">
            <h2 class="accordion-header">
                <button
                    class="accordion-button collapsed"
                    :id="'acdbtn_'+group.replace(' ','')"
                    type="button"
                    @click="openAccordionclick(group.replace(' ',''))"
                    :class="{ opened: main_service==1 && group=='Dry cleaning'}"
                >{{group}}</button>
            </h2>
            <div
                class="accordion-collapse collapse"
                :class="{ show: (main_service==1 && group=='Dry cleaning') || sel_cleaning_group.includes(group)}"
                :id="'acdpanel_'+group.replace(' ','')"
            >

                <div class="accordion-body row mt-3">
                    <!--
(!detailingitem.cleaning_services && service.selected_default==1) ||
                        -->
                    <div class="col-2 d-flex text-center each-sub-service justify-content-center cleaning-subservice align-items-center position-relative" v-for="(service,id) in services" :class="{'sel_service': service.cust_selected==1,'is_pref_disabled':service.cleaning_group==2 && service.isPrefActive==0,'mb-4':service.cleaning_group==2, 'active_service': preference_customer && preference_customer.includes(service.id_preference)  && detailingitem.status != 'Completed' , 'cleaning': group=='Cleaning Add-on'}" :id="'sub_service_'+service.id" @click="checkSubService(service.id)" :data-cleaning-service-id="service.id">
                        <div class="d-block w-100 text-center">
                            {{service.name}}<span v-if="service.cleaning_group==2" class="text-center d-block w-100">(&#163;{{service.fixed_price}})</span>

                        </div>
                        <img src="/images/padlock.svg" class="position-absolute pref-disable-icon" v-if="service.cleaning_group==2 && service.isPrefActive==0"/>
                    </div>

                </div>
            </div>
        </div>
        <div class="accordion-item col-12 px-0 mt-4">
            <h2 class="accordion-header">
                <button
                    class="accordion-button collapsed"
                    id="acdbtn_otherpricings"
                    type="button"
                    @click="openAccordionclick('otherpricings')"
                >Other pricings</button>
            </h2>
            <div
                class="accordion-collapse collapse"
                id="acdpanel_otherpricings" :class="{'show':detailingitem.cleaning_price_type!=null}"
            >
                <div class="accordion-body row mt-3">
                    <div class="col-2 d-flex text-center each-sub-service py-4 justify-content-center cleaning-subservice cleaning-prices" v-for="name in type_prices" :id="'sub_service_'+name.replace(' ','')" @click="toggleSubService(name.replace(' ',''))" :data-cleaning-price-type="name" :class="{'sel_service':detailingitem.cleaning_price_type==name}">
                       {{name}}
                    </div>
                     <div class="col-2 d-flex text-center each-price-now-btn py-4 justify-content-center" id="pricenow_cleaning" @click="loadPriceNowModal('cleaning')" :class="{'sel_service':detailingitem.cleaning_price_type=='PriceNow'}">Price now</div>

                </div>
            </div>
        </div>
    </div>
    <!--END CLEANING SERVICES-->

    <!-- TAILORING SERVICES -->
    <div class="row" :class="{'d-none':main_service!=2}">
        <div class="accordion-item col-12 px-0 mt-4" v-for="(services,group) in tailoring_services">
            <h2 class="accordion-header">
                <button
                    class="accordion-button collapsed"
                    :id="'acdbtn_'+group.replace(' ','')"
                    type="button"
                    @click="openAccordionclick(group.replace(' ',''))"
                >{{group}}</button>
            </h2>
            <div
                class="accordion-collapse collapse" :class="{'show':sel_tailoring_group.includes(group)}"
                :id="'acdpanel_'+group.replace(' ','')"
            >
                <div class="accordion-body row mt-3">
                    <div class="col-3 mb-3 d-flex text-center each-sub-service justify-content-center tailoring-subservice align-items-center" v-for="(service,id) in services" :id="'sub_service_tailoring_'+service.id" @click="toggleSubService('tailoring_'+service.id)" :data-tailoring-service-id="service.id" :class="{'sel_service':service.cust_selected==1}">
                        <div class="d-block w-100 text-center">
                            {{service.name}}
                            <span class="text-center d-block w-100">(&#163;{{service.price.toFixed(2)}})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <div class="accordion-item col-12 px-0 mt-4">
            <h2 class="accordion-header">
                <button
                    class="accordion-button collapsed"
                    id="acdbtn_tailoringpricetype"
                    type="button"
                    @click="openAccordionclick('tailoringpricetype')"
                >Other pricings</button>
            </h2>
            <div
                class="accordion-collapse collapse"
                id="acdpanel_tailoringpricetype" :class="{'show':detailingitem.tailoring_price_type!=null && detailingitem.tailoring_price_type!=''}"
            >
                <div class="accordion-body row mt-3">
                    <div   class="col-2 d-flex text-center each-sub-service py-4 justify-content-center tailoring-subservice tailoring-price-type" :id="'sub_service_tailoring_'+type.replace(' ','')" :class="{'sel_service':detailingitem.tailoring_price_type==type}" :data-tailoring-price-type="type"  v-for="(type) in type_prices" @click="toggleSubService('tailoring_'+type.replace(' ',''))">
                        {{type}}
                    </div>
                    <div class="col-2 d-flex text-center each-price-now-btn py-4 justify-content-center" id="pricenow_tailoring" @click="loadPriceNowModal('tailoring')" :class="{'sel_service':detailingitem.tailoring_price_type=='PriceNow'}">Price now</div>

                </div>
            </div>
        </div>


    </div>
    <!-- END TAILORING SERVICES -->

    <div class="row buttons mt-4">
        <div class="col-9 text-align-right pr-0">
            <button class="btn btn-link btn-previous" @click="back">Previous</button>
        </div>
        <div class="col-3 text-align-right px-0">
            <button class="btn btn-success text-white" id="completeDetailingBtn" @click="validateServices">Finish detailing</button>
        </div>
    </div>


    <modal ref="addon_modal">
        <template #closebtn>
            <span class="close" id="addon_modal_close" @click="closeAddOnModal"></span>
        </template>
        <template #bheader>
            <div class="bmodal-header py-4 text-center">Add-ons</div>
        </template>
        <template #bcontent>
            <div class="row py-5">
                <div class="col-12 text-center add_on_desc">Select service add-ons</div>
            </div>
        </template>
        <template #mbuttons>
            <div class="row mx-0 justify-content-center mt-3 mb-5">
                <div class="col-5">
                    <button class="btn btn-outline-dark w-100" @click="changeCustomerPreference(sel_addon_id)">Change preferences</button>
                </div>
                <div class="col-1"></div>
                <div class="col-5">
                    <button class="btn btn-outline-dark w-100" @click="setServiceToggle(sel_addon_id)">Only for this order</button>
                </div>
            </div>
        </template>
    </modal>

    <modal ref="pricenow_modal">
        <template #closebtn>
            <span class="close" id="pricenow_modal_close" @click="closePriceNowModal"></span>
        </template>
        <template #bheader>
            <div class="bmodal-header py-4 text-center">Describe & Price Now</div>
        </template>
        <template #bcontent>
            <div class="row justify-content-center pt-4">
                <div class="Describe row justify-content-center d-block">
                    <div class="form-group">
                        <label for="exampleInputEmail1" class="form-label body_medium">Describe Job (50 characters max.)</label>
                        <input type="text" class="form-control py-2" v-model="describe_job_value" placeholder="Type job description">
                    </div>
                    <div class="col-4 form-group pt-3">
                        <label  class="form-label body_medium">Enter Price</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold" style="background: none;">£</span>
                            <input style="border-left: none;" type="text" v-model="price_now_value" class="form-control"  placeholder="0.00">
                        </div>
                    </div>
                </div>
                <div class="row mx-0 justify-content-center mt-3 mb-5">
                    <div class="col-4">
                        <button class="btn btn-outline-danger body_medium w-100" @click="closePriceNowModal">Cancel</button>
                    </div>
                    <div class="col-1"></div>
                    <div class="col-4">
                        <button class="btn btn-dark body_medium w-100" @click="priceNow">Save</button>
                    </div>
                </div>
            </div>
           
        </template>

    </modal>



</template>
<script>
 import {computed, onMounted,onUpdated, ref,watch} from 'vue';
 import { useStore } from "vuex";
 import {useRouter} from 'vue-router';
 import Modal from '../miscellaneous/Modal.vue';

import {
    TOASTER_MESSAGE, TOASTER_MODULE,
    LOADER_MODULE, SET_LOADER_MSG, DISPLAY_LOADER, HIDE_LOADER
} from "../../store/types/types";


export default {
    name: "DetailingServices",
    emits:['save-item-services','go-to-step','init-detailing'],
    components: {Modal},
    props: {
        detailingitem: {},
        main_services:{},
        cleaning_services:{},
        tailoring_services:{},
    },
    setup(props,context) {
         const store = useStore();
         const router = useRouter();
        //const main_services = ref({});
        //const cleaning_services = ref({});
        const main_service = ref(1);
        const type_prices = ref(['Standard','Quote']);
        const sel_cleaning_group = ref([]);
        const sel_tailoring_group = ref([]);
        const sel_tailoring_service_id = ref([]);
        const sel_cleaning_service_id = ref([]);
        const sel_cleaning_price_type = ref("");
        const sel_tailoring_price_type = ref("");
        const addon_modal = ref();
        const sel_addon_id = ref(0);
        const price_now_type = ref('');
        const pricenow_modal = ref();
        const price_now_value = ref('');
        const describe_job_value = ref('');
        const preference_customer = ref([]);

        function toggleMainService(id){
            let el = document.getElementById('main_service_'+id);
            el.classList.toggle('sel_service');
            let classes = Object.values(el.classList);
            let selected_main = [];

/*
            if(classes.includes('sel_service')){

                if(id==1){ //CLEANING
                    let core_clean_services = [1,2,3];

                    core_clean_services.forEach(function(v,i){
                        let cs = document.getElementById('sub_service_'+v);
                        cs.classList.add('sel_service');
                    });

                    checkSelectedCleaning(true);
                }


            }else{

                //Clears all subselected;
                if(id==1){ //CLEANING

                    let sub_services = document.querySelectorAll('.cleaning-subservice');
                    let keys = Object.keys(sub_services);

                    let i;
                    for(i in keys){
                        let el = sub_services[i];
                        el.classList.remove('sel_service');
                    }
                     checkSelectedCleaning(true);
                }
            }
*/
            if(classes.includes('sel_service')){
                let other_mains = document.querySelectorAll('.each-main-service:not(#main_service_'+id+')');

                let els = Object.values(other_mains);

                els.forEach(function(v,i){
                    v.classList.remove('sel_service');
                });

                if(id==1){ //Cleaning
                    checkCleaningGroup();
                }

                if(id==2){ //Tailoring
                    checkSelTailoringGroups();
                }
            }

            main_service.value = id;

            let mains = document.querySelectorAll('.each-main-service.sel_service');

            if(mains.length==1){
                let id = mains[0].getAttribute('id').replace('main_service_','');
                main_service.value = id;
            }
        }

        function checkSubService(id){
            let el = document.getElementById('sub_service_'+id);

            let classes = Object.values(el.classList);

            if(classes.includes('is_pref_disabled') && !classes.includes('sel_service')){
                sel_addon_id.value = id;
                addon_modal.value.showModal();
            }else{
                toggleSubService(id);
            }
        }

        function changeCustomerPreference(id){
            axios.post('/update-cust-preference-from-service',{
                service_id:id,
                customer_id:props.detailingitem.customer_id,
            }).then((res)=>{
                if(res.data.updated){
                    toggleSubService(id);
                    closeAddOnModal();
                }
            }).catch((err)=>{

            }).finally(()=>{
               let el = document.getElementById('sub_service_'+id);
               el.classList.remove('is_pref_disabled');

            });

        }

        function setServiceToggle(id){
            toggleSubService(id);
            closeAddOnModal();
        }

        function toggleSubService(id){
            let el = document.getElementById('sub_service_'+id);
            el.classList.toggle('sel_service');

             let classes = Object.values(el.classList);

                //TO optimize
                if(classes.includes('cleaning-prices')){
                    let cleaning_prices = document.querySelectorAll('.cleaning-prices:not(#sub_service_'+id+')');
                    //console.log(cleaning_prices)

                    let keys = Object.keys(cleaning_prices);

                    let i;
                    for(i in keys){
                        let elp = cleaning_prices[i];
                        elp.classList.remove('sel_service');
                    }

                    if(classes.includes('sel_service')){
                        document.getElementById('pricenow_cleaning').classList.remove('sel_service');
                    }
                }

                if(classes.includes('tailoring-price-type')){
                    let cleaning_prices = document.querySelectorAll('.tailoring-price-type:not(#sub_service_'+id+')');
                    //console.log(cleaning_prices)

                    let keys = Object.keys(cleaning_prices);

                    let i;
                    for(i in keys){
                        let elp = cleaning_prices[i];
                        elp.classList.remove('sel_service');
                    }

                    if(classes.includes('sel_service')){
                        document.getElementById('pricenow_tailoring').classList.remove('sel_service');
                    }
                }

                //END to optimize

                if(main_service.value==1){
                    checkSelectedCleaning(true);
                }

                if(main_service.value==2){
                    checkSelectedTailoring(true);
                }

                sel_addon_id.value = 0;
        }

        function openAccordionclick(id) {
            let acdbtn = document.getElementById('acdbtn_'+id);

            let acdpanel = document.getElementById('acdpanel_'+id);
            acdbtn.classList.toggle('opened');
            acdpanel.classList.toggle('show');
        }


        function checkSelectedTailoring(on_click){
            let selected_services = document.querySelectorAll('.tailoring-subservice.sel_service:not(.tailoring-price-type)');
            let els = Object.values(selected_services);
            let tailoring_services_id = [];
            let price_type = "";

            els.forEach(function(v,i){
                let id = v.getAttribute('data-tailoring-service-id');
                tailoring_services_id.push(id);
            });

            sel_tailoring_service_id.value = tailoring_services_id;

            let price_type_els = document.querySelectorAll('.tailoring-price-type.sel_service');

            if(price_type_els.length > 0){
                let price_type_el = price_type_els[0];
                price_type = price_type_el.getAttribute('data-tailoring-price-type');
            }

            if(tailoring_services_id.length > 0){
                document.getElementById('main_service_2').classList.add('main_selected');
            }else{
                document.getElementById('main_service_2').classList.remove('main_selected');
            }

            let price_now_el = document.querySelectorAll('#pricenow_tailoring.sel_service');
            if(price_now_el.length > 0){
                price_type = 'PriceNow';
            }


            sel_tailoring_price_type.value = price_type;

            checkSelectedCleaning(false);

            if(on_click){
                //Update Detailing
                context.emit("save-item-services", {
                    step:11,
                    detailingitem_id: props.detailingitem.id,
                    tailoring_services: JSON.stringify(tailoring_services_id),
                    tailoring_price_type: price_type,
                    cleaning_services:JSON.stringify(sel_cleaning_service_id.value),
                    cleaning_pricing_type:sel_cleaning_price_type.value,
                });
            }
        }

        function checkSelTailoringGroups(){

            //Open accordion if has selected services
           let ts = props.tailoring_services;
            let gp = Object.keys(ts);
            let sel_tailoring_gp = [];

            gp.forEach(function(v,i){
                let services = ts[v];

                services.forEach(function(service,index){
                    if(sel_tailoring_service_id.value.includes(service.id.toString())){
                        if(!sel_tailoring_gp.includes(v)){
                            sel_tailoring_gp.push(v);
                        }
                    }
                });
            })

            sel_tailoring_group.value = sel_tailoring_gp;
            //End open accordion


        }


        function checkSelectedCleaning(on_click){
            let selected_services = document.querySelectorAll('.cleaning-subservice.sel_service:not(.cleaning-prices)');
            let keys = Object.values(selected_services);
            let cleaning_services_id = [];
            let cleaning_pricing_type = "";

            if(keys.length > 0){
                 document.getElementById('main_service_1').classList.add('main_selected');

                let i;
                for(i in keys){
                    let id = selected_services[i].getAttribute('data-cleaning-service-id');
                    cleaning_services_id.push(id);
                }

                sel_cleaning_service_id.value = cleaning_services_id;

                let selected_pricing = document.querySelectorAll('.cleaning-prices.sel_service');
                let pricing_el = Object.values(selected_pricing);

                if(pricing_el.length == 1){
                    cleaning_pricing_type = pricing_el[0].getAttribute('data-cleaning-price-type');
                    sel_cleaning_price_type.value = cleaning_pricing_type;
                }

                let price_now_el = document.querySelectorAll('#pricenow_cleaning.sel_service');
                if(price_now_el.length > 0){
                    sel_cleaning_price_type.value = 'PriceNow';
                }

            }else{
                document.getElementById('main_service_1').classList.remove('main_selected');
            }

            if(on_click){
                 //Update Detailing
                context.emit("save-item-services", {
                    step:11,
                    detailingitem_id: props.detailingitem.id,
                    cleaning_services: JSON.stringify(cleaning_services_id),
                    cleaning_price_type: cleaning_pricing_type,
                    tailoring_services: JSON.stringify(sel_tailoring_service_id.value),
                    tailoring_price_type:sel_tailoring_price_type.value,
                });
            }
        }


        function checkCleaningGroup(){

            if(props.detailingitem.status != 'Completed'){

                axios.post('/getPreferenceCustomer',{
                Customer_id: props.detailingitem.customer_id,
                typeitem_id: props.detailingitem.typeitem_id
               }).then((res)=>{
                   preference_customer.value = res.data.prefrenceActive
                //    preference_customer.value.forEach(function(id){
                //     changeCustomerPreference(id)
                //    });
                toggleSubService
               }).catch((err)=>{

               })
            }


            let cs = props.cleaning_services;
            let gp = Object.keys(cs);
            let sel_cleaning_gp = [];


            gp.forEach(function(v,i){
                let services = cs[v];

                services.forEach(function(service,index){
                    if(sel_cleaning_service_id.value.includes(service.id.toString())){
                        if(!sel_cleaning_gp.includes(v)){
                            sel_cleaning_gp.push(v);
                        }
                    }
                });
            })

            sel_cleaning_group.value = sel_cleaning_gp;

        }

        function setTailoringPrice(type){
            console.log('cleaning',sel_tailoring_service_id.value.length);
        }

        watch(() =>main_service.value, (current_val, previous_val) => {
            //console.log('cur_main_service',current_val);
        });

        function back(){
            context.emit("go-to-step", 10);
        }

        onMounted(()=>{
            sel_cleaning_price_type.value = 'Standard';
            sel_tailoring_price_type.value = 'Standard';

            checkSelectedCleaning(false);
            checkSelectedTailoring(false);
            checkSelTailoringGroups();
            checkCleaningGroup();

            let price_type = props.detailingitem.cleaning_price_type;
            if(price_type && price_type!=''){
                let cp = document.querySelectorAll('.cleaning-prices');
                let elcp = Object.values(cp);

                elcp.forEach(function(el,index){
                    let ptype = el.getAttribute('data-cleaning-price-type').toString().replace(' ','');
                    if(ptype==price_type){
                        el.classList.add('sel_service');
                    }
                });
            }


            if(props.detailingitem.cleaning_services==null){
               checkSelectedCleaning(true);
            }
        });


        function validateServices(){
            let is_err = false;



            console.log('cleaning price_type',sel_cleaning_price_type.value);

            if(sel_cleaning_service_id.value.length==0 && sel_tailoring_service_id.value.length==0){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: 'No services selected',
                        ttl: 5,
                        type: 'danger'
                    });
                is_err = true;
            }

            if(sel_cleaning_service_id.value.length > 0 && sel_cleaning_price_type.value==''){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: 'Cleaning price type not set',
                        ttl: 5,
                        type: 'danger'
                    });
                is_err = true;
            }

            if(sel_tailoring_service_id.value.length > 0 && sel_tailoring_price_type.value==''){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        message: 'Tailoring price type not set',
                        ttl: 5,
                        type: 'danger'
                    });

                is_err = true;
            }
            if(!is_err){

               axios.post('/complete-detailing-item',{
                    detailingitem_id: props.detailingitem.id,
               }).then((res)=>{
                   console.log(res);
               }).catch((err)=>{

               }).finally(()=>{
                    router.push('/order-content/'+props.detailingitem.order_id);
               });
            }
        }

        function closeAddOnModal(){
            addon_modal.value.closeModal();
        }

        function loadPriceNowModal(type){
            let el = document.getElementById('pricenow_'+type);
            el.classList.toggle('sel_service');

            let classes = Object.values(el.classList);

            if(classes.includes('sel_service')){
                if(type=='tailoring'){
                    sel_tailoring_price_type.value = 'PriceNow';
                    let elt = Object.values(document.querySelectorAll('.tailoring-price-type'));
                    elt.forEach(function(v,i){
                        v.classList.remove('sel_service');
                    });

                }
                else if(type=='cleaning'){
                    sel_cleaning_price_type.value = 'PriceNow';
                    let elc = Object.values(document.querySelectorAll('.cleaning-prices'));
                    elc.forEach(function(v,i){
                        v.classList.remove('sel_service');
                    });
                }

                price_now_type.value = type;
                pricenow_modal.value.showModal();
            }
        }

        function priceNow(){
           let montant = price_now_value.value;
           let describeprixnow = describe_job_value.value

           console.log("describeprixnow == '' && montant==''" ,describeprixnow == '' || (montant=='' || parseFloat(montant)=='NaN'))

           if(describeprixnow == '' || montant=='' || parseFloat(montant)=='NaN'){
                if(describeprixnow == ''){
                    describe_job_value.value = '';
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            message: 'Invalid describe',
                            ttl: 5,
                            type: 'danger'
                        });
                }
                if(montant=='' || parseFloat(montant)=='NaN'){
                    price_now_value.value = '';
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            message: 'Invalid price now value',
                            ttl: 5,
                            type: 'danger'
                        });
                }
           }else{
                axios.post('/set-price-now',{
                    id:props.detailingitem.id,
                    type:price_now_type.value,
                    montant:montant,
                    describeprixnow : describe_job_value.value
                }).then((res)=>{
                    //console.log(res);
                    if(res.data.updated){
                        price_now_value.value='';
                        price_now_type.value = '';
                        closePriceNowModal();

                        context.emit('init-detailing');

/*
                        context.emit("save-item-services", {
                            step:11,
                            detailingitem_id: props.detailingitem.id,
                            cleaning_services: JSON.stringify(sel_cleaning_service_id.value),
                            cleaning_price_type: sel_cleaning_price_type.value,
                            tailoring_services: JSON.stringify(sel_tailoring_service_id.value),
                            tailoring_price_type:sel_tailoring_price_type.value,
                            montant:montant,
                        });
*/

                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            message: 'Item describe and price updated',
                            ttl: 5,
                            type: 'success'
                        });
                    }
                }).catch((err)=>{
                    console.log(err);
                }).finally(()=>{

                });
            }
        }

        function closePriceNowModal(){
            pricenow_modal.value.closeModal();
        }

        return {
            back,
            //main_services,
            //cleaning_services,
            toggleMainService,
            toggleSubService,
            main_service,
            openAccordionclick,
            type_prices,
            sel_cleaning_group,
            sel_tailoring_group,
            sel_cleaning_price_type,
            setTailoringPrice,
            validateServices,
            addon_modal,
            checkSubService,
            changeCustomerPreference,
            setServiceToggle,
            closeAddOnModal,
            sel_addon_id,
            priceNow,
            price_now_type,
            pricenow_modal,
            loadPriceNowModal,
            closePriceNowModal,
            price_now_value,
            preference_customer,
            describe_job_value
        };
    },
}
</script>
<style scoped>
    #services-desc{
        font:normal 16px/1.5em "Gotham Rounded Light";
    }

    .each-main-service, .each-sub-service, .each-price-now-btn{
        box-shadow: 0px 0px 4px rgba(80,80,80,0.2);
        border-radius:4px;
        font:normal 16px "Gotham Rounded Light";
    }

    .each-sub-service, .each-price-now-btn{
        margin-right: 25px;
        height:70px;
    }


    .each-main-service{
        height:150px;
        width:150px;
        margin-right:1.5rem;
    }


    .each-main-service:hover,
    .each-main-service.sel_service,
    .each-sub-service:hover,
    .each-sub-service.sel_service,
    .each-price-now-btn:hover,
    .each-price-now-btn.sel_service{
        cursor:pointer;
        color:#fff;
        background:#47454B;
    }

    /*Accordion*/
    .accordion-container {
        font-family: Gotham Rounded;
        background-color: white;
        height: 100%;
    }
    .opened:after {
        /* background-image: url("../../../../resources/img/accordion_arrow.png");
        background-repeat: no-repeat !important; */
        transform: rotate(180deg);
    }

    .accordion-button {
        background: #ffffff;
        box-sizing: border-box;
        border-radius: 6px;
        font-family: Gilroy;
        font-style: normal;
        font-weight: 800;
        font-size: 16px;
        line-height: 110%;
        color: #47454b;
    }

    .accordion-item {
        background:none !important;
        border:none !important;
    }
    .accordion-flush .accordion-item .accordion-button {
        border-radius: 6px !important;
    }
    .accordion-body {
     background:none;
    }
    .accordion-button.opened {
        background: rgba(217, 237, 210, 0.2);
    }
    .accordion-flush .accordion-collapse {
        background: #ffffff;
        border-radius: 6px;
        border: 0.5px solid transparent;
    }
    .accordion-button:focus,
    .accordion-button:active {
        outline: none !important;
        box-shadow: none !important;
    }

    .main_selected{
        background:rgba(66,167,30,.3);
    }

    #completeDetailingBtn:hover{
        background:#333;
        border:thin solid #333;
    }

    .pref-disable-icon{
        left:10px;
        bottom:10px;
    }

    .bmodal-header,.add_on_desc{
        font-family:'Gilroy Extra Bold';
    }

    .add_on_desc{
        font-size:18px;
    }

    .bmodal-header{
        font-size:22px;
        background:#f8f8f8;
    }

    #addon_modal_close{
        top:20px;
    }


    .each-sub-service:not(.is_pref_disabled) .pref-disable-icon{
        display:none;
    }
    .each-sub-service.active_service {
        cursor: pointer;
        color: #fff;
        background: #47454B;
    }
    .disabled_service{
        pointer-events: none;
        background: transparent;
        color:rgba(0, 23, 84, 0.15);
        box-shadow: none;
        cursor: default;
    }
    .each-sub-service.sel_service.disabled_service{
        background-color: #47454b;
    }
    .Describe{
        padding: 3% 14%;
    }
    .form-control::placeholder {
    color: #C3C3C3;
    opacity: 1;
    font-style: normal;
    font-weight: 325;
    font-size: 16px;
    line-height: 140%;
}


</style>
