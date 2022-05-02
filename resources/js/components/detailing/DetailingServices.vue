<template>
    <div class="row mb-3">
        <div class="col-12" id="services-desc">
            Select one or multiple services. Note that available services depend on customer preferences.<br/>
            However, you have the possibility to unlock a disabled service with the consent of the customer.
        </div>
    </div>
    <div class="row block-service-main justify-content-between">
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
                    <div class="col-2 d-flex text-center each-sub-service justify-content-center cleaning-subservice align-items-center" v-for="(service,id) in services" :class="{'sel_service':(detailingitem.cleaning_services==null && service.selected_default==1) || service.cust_selected==1,'is_pref_disabled':service.cleaning_group==2 && service.isPrefActive==0}" :id="'sub_service_'+service.id" @click="toggleSubService(service.id)" :data-cleaning-service-id="service.id">
                        <div class="d-block w-100 text-center">{{service.name}}<span v-if="service.cleaning_group==2" class="text-center d-block w-100">(&#163;{{service.fixed_price}})</span></div>
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

                    <div class="col-2 d-flex text-center each-sub-service py-4 justify-content-center cleaning-subservice cleaning-prices" v-for="name in cleaning_prices" :id="'sub_service_'+name.replace(' ','')" @click="toggleSubService(name.replace(' ',''))" :data-cleaning-price-type="name.replace(' ','')">
                        {{name}}
                    </div>

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
                    <div class="col-3 mb-3 d-flex text-center each-sub-service justify-content-center tailoring-subservice align-items-center" v-for="(service,id) in services" :id="'sub_service_'+service.id" @click="toggleSubService(service.id)" :data-tailoring-service-id="service.id" :class="{'sel_service':service.cust_selected==1}">
                        <div class="d-block w-100 text-center">
                            {{service.name}}
                            <span class="text-center d-block w-100">(&#163;{{service.price.toFixed(2)}})</span>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <!-- END TAILORING SERVICES -->

    <div class="row buttons">
        <div class="col-10 text-align-right">
            <button class="btn btn-link btn-previous" @click="back">Previous</button>
        </div>
        <div class="col-2 text-align-right">
            <button class="btn btn-next">Next</button>
        </div>
    </div>

</template>
<script>
 import {onMounted,onUpdated, ref,watch} from 'vue';

export default {

    name: "DetailingServices",
    emits:['save-item-services','go-to-step'],
    components: {},
    props: {
        detailingitem: {},
        main_services:{},
        cleaning_services:{},
        tailoring_services:{},
    },
    setup(props,context) {
        //const main_services = ref({});
        //const cleaning_services = ref({});
        const main_service = ref(1);
        const cleaning_prices = ref(['Price now','Quote']);
        const sel_cleaning_group = ref([]);
        const sel_tailoring_group = ref([]);
        const sel_tailoring_service_id = ref([]);
        const sel_cleaning_service_id = ref([]);
        const sel_cleaning_price_type = ref("");

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

        function toggleSubService(id){
            let el = document.getElementById('sub_service_'+id);
             el.classList.toggle('sel_service');
             let classes = Object.values(el.classList);


            if(classes.includes('cleaning-prices')){
                let cleaning_prices = document.querySelectorAll('.cleaning-prices:not(#sub_service_'+id+')');
                //console.log(cleaning_prices)

                let keys = Object.keys(cleaning_prices);

                let i;
                for(i in keys){
                    let elp = cleaning_prices[i];
                    elp.classList.remove('sel_service');
                }
            }

            if(main_service.value==1){
                checkSelectedCleaning(true);
            }

            if(main_service.value==2){
                checkSelectedTailoring(true);
            }
        }


        function openAccordionclick(id) {
            let acdbtn = document.getElementById('acdbtn_'+id);

            let acdpanel = document.getElementById('acdpanel_'+id);
            acdbtn.classList.toggle('opened');
            acdpanel.classList.toggle('show');
        }


        function checkSelectedTailoring(on_click){
            let selected_services = document.querySelectorAll('.tailoring-subservice.sel_service');
            let els = Object.values(selected_services);
            let tailoring_services_id = [];

            els.forEach(function(v,i){
                let id = v.getAttribute('data-tailoring-service-id');
                tailoring_services_id.push(id);
            });

            sel_tailoring_service_id.value = tailoring_services_id;

            if(tailoring_services_id.length > 0){
                document.getElementById('main_service_2').classList.add('main_selected');
            }else{
                document.getElementById('main_service_2').classList.remove('main_selected');
            }

            if(on_click){
                //Update Detailing
                context.emit("save-item-services", {
                    step:11,
                    detailingitem_id: props.detailingitem.id,
                    tailoring_services: JSON.stringify(tailoring_services_id),
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
                });
            }
        }


        function checkCleaningGroup(){
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

        watch(() =>main_service.value, (current_val, previous_val) => {
            //console.log('cur_main_service',current_val);
        });

        function back(){
            context.emit("go-to-step", 10);
        }

        onMounted(()=>{
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


        return {
            back,
            //main_services,
            //cleaning_services,
            toggleMainService,
            toggleSubService,
            main_service,
            openAccordionclick,
            cleaning_prices,
            sel_cleaning_group,
            sel_tailoring_group,
            sel_cleaning_price_type,
        };
    },
}
</script>
<style scoped>
    #services-desc{
        font:normal 16px/1.5em "Gotham Rounded Light";
    }

    .each-main-service, .each-sub-service{
        box-shadow: 0px 0px 4px rgba(80,80,80,0.2);
        border-radius:4px;
        font:normal 16px "Gotham Rounded Light";
    }

    .each-sub-service{
        margin-right: 25px;
        height:70px;
    }


    .each-main-service{
        height:150px;
        width:150px;
    }


    .each-main-service:hover,
    .each-main-service.sel_service,
    .each-sub-service:hover,
    .each-sub-service.sel_service{
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


</style>
