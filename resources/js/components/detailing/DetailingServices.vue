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
                :class="{ show: main_service==1 && group=='Dry cleaning'}"
                :id="'acdpanel_'+group.replace(' ','')"
            >
                <div class="accordion-body row mt-3">
                    <div class="col-2 d-flex text-center each-sub-service justify-content-center cleaning-subservice align-items-center" v-for="(service,id) in services" :class="{'sel_service':service.selected_default==1 || service.cust_selected==1,'is_pref_disabled':service.cleaning_group==2 && service.isPrefActive==0}" :id="'sub_service_'+service.id" @click="toggleSubService(service.id)" :data-cleaning-service-id="service.id">
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
                id="acdpanel_otherpricings"
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
    },
    setup(props,context) {
        //const main_services = ref({});
        //const cleaning_services = ref({});
        const main_service = ref(1);
        const cleaning_prices = ref(['Price now','Quote']);

        function toggleMainService(id){
            let el = document.getElementById('main_service_'+id);
            el.classList.toggle('sel_service');
            let classes = Object.values(el.classList);
            let selected_main = [];



            if(classes.includes('sel_service')){

                if(id==1){ //CLEANING
                    let core_clean_services = [1,2,3];

                    core_clean_services.forEach(function(v,i){
                        let cs = document.getElementById('sub_service_'+v);
                        cs.classList.add('sel_service');
                    });

                    updateCleaningPrice();
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
                    updateCleaningPrice();
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
                updateCleaningPrice();
            }
        }


        function openAccordionclick(id) {
            let acdbtn = document.getElementById('acdbtn_'+id);

            let acdpanel = document.getElementById('acdpanel_'+id);
            acdbtn.classList.toggle('opened');
            acdpanel.classList.toggle('show');
        }


        function updateCleaningPrice(){
            let selected_services = document.querySelectorAll('.cleaning-subservice.sel_service:not(.cleaning-prices)');
            let keys = Object.values(selected_services);
            let cleaning_services_id = [];
            let cleaning_pricing_type = "";


            if(keys.length > 0){
                 document.getElementById('main_service_1').classList.add('sel_service');

                let i;
                for(i in keys){
                    let id = selected_services[i].getAttribute('data-cleaning-service-id');
                    cleaning_services_id.push(id);
                }

                let selected_pricing = document.querySelectorAll('.cleaning-prices.sel_service');
                let pricing_el = Object.values(selected_pricing);

                if(pricing_el.length == 1){
                    cleaning_pricing_type = pricing_el[0].getAttribute('data-cleaning-price-type');
                }

            }else{
                document.getElementById('main_service_1').classList.remove('sel_service');
            }


             //Update Detailing
                context.emit("save-item-services", {
                    step:11,
                    detailingitem_id: props.detailingitem.id,
                    cleaning_services: JSON.stringify(cleaning_services_id),
                    cleaning_price_type: cleaning_pricing_type,
                });


        }

        watch(() =>main_service.value, (current_val, previous_val) => {
            //console.log('cur_main_service',current_val);
        });

        function back(){
            context.emit("go-to-step", 10);
        }

        onMounted(()=>{
            //updateCleaningPrice();
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
                console.log('update price on first load');
                updateCleaningPrice();
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


</style>
