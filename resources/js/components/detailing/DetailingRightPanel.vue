<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <div class="col-4 right-panel" v-if="show">
            <div class="panel-header row" @click="show = !show">
                <div class="col-5">Item n°{{ detailingitem.tracking }}</div>
                <div class="col-4">Detailed by @{{ customerName }}</div>
                <div class="col-3 price">£{{final_price}}</div>
            </div>
            <div class="progress-bar" :style="{ width: progress_percent + '%' }"></div>
            <div class="accordion-container">
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                @click="openAccordionclick(1)"
                                :class="{ opened: instAcc === true, done: step > 1 }"
                            >Customer instructions</button>
                        </h2>
                        <div
                            id="flush-collapseOne"
                            class="accordion-collapse collapse"
                            :class="{ show: instAcc === true }"
                        >
                            <div class="accordion-body">
                                <!--
                                <div class="accordion-body-title">Customer instructions</div>
                                -->

                                <div class="row justify-content-center mb-2 mx-0" v-if="showCustomerInstructions">
                                    <div class="col-12 mb-3 px-0">Voucher: <span id="voucher_code" class="text-white">{{detailingitem.voucher}}</span></div>

                                    <div class="col-12 mb-3" id="garment_instructions_table">
                                        <div class="row py-2 text-white" id="item-table-heading">
                                            <div class="col-2 px-0">Category</div>
                                            <div class="col-2">Item</div>
                                            <div class="col-2">Brand</div>
                                            <div class="col-3">Action</div>
                                            <div class="col-3">Comment</div>
                                        </div>

                                        <div class="row each-category-row" v-for="(a,i) in detailingitem.customer_instructions">
                                            <div class="col-2 each-category-name">
                                                <span v-if="a.Category !=''">{{detailingitem.instruction_categories[a.Category]}}</span>
                                            </div>
                                            <div class="col-2"><span v-if="a.Item !=''">{{a.Item}}</span><span v-else>-</span></div>
                                            <div class="col-2"><span v-if="a.Brand !=''">{{a.Brand}}</span><span v-else>-</span></div>
                                            <div class="col-3"><span v-if="a.Actions !=''">{{a.Actions}}</span><span v-else>-</span></div>
                                            <div class="col-3"><span v-if="a.Comment !=''">{{a.Comment}}</span><span v-else>-</span></div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingTwo">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                @click="openAccordionclick(2)"
                                :class="{ opened: descAcc === true, done: step > 9 }"
                            >Description</button>
                        </h2>
                        <div
                            id="flush-collapseTwo"
                            class="accordion-collapse collapse"
                            :class="{ show: descAcc === true }"
                        >
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="description-title">Item type</div>
                                        <div
                                            class="description-text"
                                        >{{ item_description.typeitem_name }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="description-title">Brand</div>
                                        <div
                                            v-if="item_description.brand_name"
                                            class="row description-box"
                                        >
                                            <div
                                                class="col description-text"
                                            >{{ item_description.brand_name }}</div>
                                            <div
                                                class="col brand-coefcleaning"
                                            >+{{ item_description.brand_coef_cleaning.toFixed(2) }}%</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="description-title">Size</div>
                                        <div class="description-text">{{ item_description.size }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="description-title">Item fabric</div>
                                        <div
                                            v-if="item_description.fabrics_name"
                                            v-for="fab in item_description.fabrics_name"
                                            class="row description-box"
                                        >
                                            <div class="col description-text">{{ fab.name }}</div>
                                            <div
                                                class="col fabric-coefcleaning"
                                                v-if="fab.coefcleaning != 0"
                                            >£{{ (fab.coefcleaning * item_description.base_price).toFixed(2) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="description-title">Colour</div>
                                        <div
                                            class="d-flex description-text"
                                            v-if="item_description.colors_name != undefined && item_description.colors_name.length > 0"
                                        >
                                            <div>
                                                <span
                                                    v-for="(colour) in item_description.colors_name"
                                                    class="tool-tip colour-span"
                                                    :data-tooltip="colour.name"
                                                    :style="{ background: colour.code }"
                                                ></span>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-6">
                                        <div class="description-title">Condition</div>
                                        <div
                                            class="description-text"
                                        >{{ item_description.condition_name }}</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="description-title">pattern</div>
                                        <div
                                            class="description-text"
                                        >{{ item_description.pattern_name }}</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="description-title">Complexities</div>
                                        <div
                                            v-for="comp in item_description.complexities_name"
                                            class="row description-box"
                                        >
                                            <div class="col description-text">{{ comp.name }}</div>
                                            <div
                                                class="col comp-coefcleaning"
                                            >£{{ (comp.coefcleaning * item_description.base_price).toFixed(2) }}</div>
                                        </div>
                                        <p class="description-text" v-if ="detailingitem.complexities_id == '[0]' ">None</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingThree">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                @click="openAccordionclick(3)"
                                :class="{ opened: issusesAcc === true, done: step > 10 }"
                            >Issues</button>
                        </h2>
                        <div
                            id="flush-collapseThree"
                            class="accordion-collapse collapse"
                            :class="{ show: issusesAcc === true }"
                        >
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col accordion-body-title">Stains</div>
                                    <div class="col accordion-body-title">Damages</div>
                                </div>
                                <div class="row">
                                    <div class="col-6 stain-bloc">
                                        <div
                                            class="stain-row"
                                            v-for="(stain, index) in item_description.stains"
                                        >
                                            <span class="stain eclipse-number">{{ index + 1 }}</span>
                                            <span
                                                v-if="stain.id_issue != 0"
                                            >{{ getStainName(stain.id_issue) }} &nbsp;-&nbsp; {{ getStainZone(stain.id_zone) }}</span>
                                            <span
                                                v-else-if="stain.description != ''"
                                            >{{ stain.description }} &nbsp;-&nbsp; {{ getStainZone(stain.id_zone) }}</span>
                                            <span
                                                v-else
                                            >Stain &nbsp;-&nbsp; {{ getStainZone(stain.id_zone) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div
                                            class="stain-row"
                                            v-for="(dam, index) in item_description.damages"
                                        >
                                            <span class="damage eclipse-number">{{ index + 1 }}</span>
                                            <span
                                                v-if="dam.id_issue != 0"
                                            >{{ getStainName(dam.id_issue) }}&nbsp;-&nbsp; {{ getStainZone(dam.id_zone) }}</span>
                                            <span
                                                v-else-if="dam.description != ''"
                                            >{{ dam.description }}&nbsp;-&nbsp; {{ getStainZone(dam.id_zone) }}</span>
                                            <span
                                                v-else
                                            >Damage&nbsp;-&nbsp; {{ getStainZone(dam.id_zone) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <p>{{getIssueStep()}}</p>
                                <div class="row free-text pt-3" v-if="issuesStep == 0 || issuesStep == 1 || issuesStep == 2">
                                        <span class="free-text-label">Stains - Additional Comments </span>
                                        <textarea
                                            placeholder="Specification about stain"
                                            class="free-text-input"
                                            maxlength="140"
                                            @keyup.prevent="submit"
                                            v-model="stain_free_text"
                                            @blur="addFreeText"
                                        ></textarea>
                                </div>
                                <div class="row free-text pt-3" v-if="issuesStep == 3 || issuesStep == 4 || issuesStep == 5">
                                        <span class="free-text-label">Damages - Additional Comments</span>
                                        <textarea
                                            placeholder="Specification about damage"
                                            class="free-text-input"
                                            maxlength="140"
                                            @keyup.prevent="submit"
                                            v-model="damage_free_text"
                                            @blur="addFreeText"
                                        ></textarea>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingThree">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                @click="openAccordionclick(4)"
                                :class="{ opened: servicesAcc === true }"
                            >Services</button>
                        </h2>
                        <div
                            id="flush-collapseFour"
                            class="accordion-collapse collapse"
                            :class="{ show: servicesAcc === true }"
                        >
                            <div class="accordion-body">
                                <div class="row mb-2" v-if="Object.values(grouped_cleaning_services).length > 0">
                                    <div class="col-9"><h5 class="gp_service mb-0">Cleaning</h5></div>
                                </div>
                                <div class="row mb-3" v-for="(services,gpService) in grouped_cleaning_services">
                                    <div class="col-9 pr-0">
                                        <span>{{gpService}}</span>
                                        (<span v-for="(service,index) in services">{{service.name}}<span v-if="index+1 < services.length">, </span></span>)
                                    </div>
                                    <div class="col-3 pl-0 text-right">
                                        <span v-if="grouped_cleaning_price[gpService]" class="float-right">
                                            <span v-if="typeof(cleaning_price_type)!='undefined' && cleaning_price_type=='Quote'">
                                                <span class="question_mark">?</span>
                                                &#163;0.00
                                            </span>
                                            <span v-else-if="cleaning_price_type=='PriceNow'">&#163;{{detailingitem.dry_cleaning_price.toFixed(2)}}</span>
                                            <span v-else>&#163;{{grouped_cleaning_price[gpService]}}</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="row  mb-2" :class="{'mt-4':Object.values(grouped_cleaning_services).length > 0}" v-if="sel_tailoring_services.length > 0">
                                    <div class="col-9"><h5 class="gp_service mb-0">Tailoring</h5></div>
                                    <div class="col-3 d-flex justify-content-end">
                                        <span v-if="detailingitem.tailoring_price_type=='Quote'">
                                            <span class="question_mark">?</span>&#163;0.00
                                        </span>
                                        <span v-else-if="detailingitem.tailoring_price_type=='PriceNow'">&#163;{{detailingitem.tailoring_price.toFixed(2)}}</span>
                                        <span v-else>&#163;{{tailoring_price}}</span>
                                    </div>
                                </div>
                                <div class="row" v-if="sel_tailoring_services.length > 0">
                                    <div class="col-9 pr-0">
                                        <p v-for="service in sel_tailoring_services" class="mb-2">{{service}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else @click.prevent="show = !show" class="show-btn">
            <img class="img-arrow" src="/images/accordion_arrow.png" />
        </div>
    </transition>
</template>
<script>
import { ref, watch, onMounted, onUpdated, nextTick } from 'vue';
import { useRouter, useRoute } from 'vue-router';

export default {
    name: "DetailingRightPanel",
    components: {},
    props: {
        customerName: String,
        item_description: {},
        detailingitem: {},
        step: Number,
        cleaning_services:{},
        IssueStep:Number
    },
    emits:['update-cleaning-price'],
    setup(props, context) {
        const router = useRouter();
        const route = useRoute();
        const progress_percent = ref(0);
        const instAcc = ref(false);
        const descAcc = ref(false);
        const issusesAcc = ref(false);
        const servicesAcc = ref(false);
        const show = ref(true);
        const grouped_cleaning_services = ref({});
        const grouped_cleaning_price = ref({});
        const base_cleaning_price = ref(0);
        const final_price = ref(0);
        const cleaning_price_type = ref('');
        const all_tailoring_services = ref({});
        const sel_tailoring_services = ref({});
        const tailoring_price = ref(0);
        const showCustomerInstructions = ref(false);
        const stain_free_text = ref('');
        const damage_free_text = ref('');
        const issuesStep = ref([]);
        const timeout =ref('');
        
        stain_free_text.value = props.detailingitem.stainstext;
        damage_free_text.value = props.detailingitem.damagestext;

        watch(() => props, (current_val,previous_val ) => {
            stain_free_text.value = props.detailingitem.stainstext;
            damage_free_text.value = props.detailingitem.damagestext;
        });
       
        watch(() => props.IssueStep, (current_val,previous_val ) => {
           issuesStep.value = current_val
        });

        watch(() => props.detailingitem.stainstext, (current_val,previous_val ) => {
            stain_free_text.value = current_val
        });

        watch(() => props.detailingitem.damagestext, (current_val,previous_val ) => {
            damage_free_text.value = current_val
        });


        instAcc.value = props.step == 1 ? true : false;
        descAcc.value = props.step > 1 && props.step <= 9 ? true : false;
        issusesAcc.value = props.step == 10 ? true : false;
        progress_percent.value = props.step / 12 * 100;
        watch(() => props.step, (current_val, previous_val) => {
            instAcc.value = current_val == 1 ? true : false;
            descAcc.value = current_val > 1 && current_val <= 9 ? true : false;
            issusesAcc.value = props.step == 10 ? true : false;
            progress_percent.value = current_val / 12 * 100;
            servicesAcc.value = props.step == 11 ? true:false;
        });
        function openAccordionclick(id) {
            if (id === 1) {
                instAcc.value = !instAcc.value;
                descAcc.value = false;
                issusesAcc.value = false;
                servicesAcc.value = false;
            }
            if (id === 2) {
                descAcc.value = !descAcc.value;
                instAcc.value = false;
                issusesAcc.value = false;
                servicesAcc.value = false;
            }
            if (id === 3) {
                issusesAcc.value = !issusesAcc.value;
                descAcc.value = false;
                instAcc.value = false;
                servicesAcc.value = false;
            }
            if (id === 4) {
                servicesAcc.value = !servicesAcc.value;
                descAcc.value = false;
                issusesAcc.value = false;
                instAcc.value = false;
            }
        }
        function getStainZone(id) {
            let zone = props.item_description.issues_zones.filter((zone) => zone.id === id)[0];
            if(typeof(zone !='undefined')){
                return zone.description+" - "+ zone.face + (zone.position!=""&&zone.position!='na'?" - "+zone.position:"")+ (zone.side!=""&&zone.side!='na'?" - "+zone.side:"");
            }
        }
        function getStainName(id) {
            return props.item_description.issues_tags.filter((tag) => tag.id === id)[0].name;
        }

        function getIssueStep(){
            watch(() => props.IssueStep, (current_val,previous_val ) => {       
                issuesStep.value = current_val.issuesStep
            });
        }

        function initCleaningServices(data,price_type){
            let sel_services = [];

            let services = data;

            let keys = Object.keys(services);

            keys.forEach(function(v,i){
                let gp = services[v];

                gp.forEach(function(sv,index){
                    if(sv.cust_selected==1){
                        sel_services.push(sv);
                    }
                });
            });

            groupCleaningServices(sel_services,price_type);

        }

        function refreshCleaningServices(data,price_type){
            if(props.step==11){

                let cleaning_services = data;
                let sel_services = [];

                if(cleaning_services.length > 0){
                    let services = props.cleaning_services;

                    let keys = Object.keys(services);

                    keys.forEach(function(v,i){
                        let gp = services[v];

                        gp.forEach(function(sv,index){
                            if(cleaning_services.includes(sv.id.toString())){
                                sel_services.push(sv);
                            }
                        });
                    });
                }

                groupCleaningServices(sel_services,price_type);

                return grouped_cleaning_price.value;
            }
        }

        function groupCleaningServices(services,price_type){
            cleaning_price_type.value = price_type;


            let base_price = base_cleaning_price.value;

            const groupBy = (x, f) => x.reduce((a, b) => ((a[f(b)] ||= []).push(b), a), {});

            let gp = groupBy(services, v => v.group_name);
/*
            console.log('gp',gp);
            console.log('base_price',base_price);
*/
            let keys = Object.keys(gp);

            grouped_cleaning_services.value = {};
            grouped_cleaning_price.value = {};


            if(keys.length > 0){
                keys.forEach(function(v,i){
                    let sv = gp[v];
                    let price = 0;
                    let price_add_on = 0
                    let total_price = 0;

                    let service_with_perc = [];
                    let service_perc = [];

                    sv.forEach(function(service,index){

                        if(service.perc > 0){
                            service_with_perc[service.id] = service.perc;
                            service_perc.push(service.id);

                        }else if(service.fixed_price > 0){
                            price_add_on += service.fixed_price;
                        }
                    });

                    let perc = 0;

                    if(service_perc.includes(1) && service_perc.includes(3)){
                        perc = 100;
                    }else{
                        service_with_perc.forEach(function(v,i){
                            perc += v;
                        });
                    }

                    price = (perc/100)*base_price;

                    total_price = price_add_on+price;

                    if(sv.length > 0){
                        grouped_cleaning_price.value[v] = (typeof(price_type!='undefined') && ['Quote','PriceNow'].includes(price_type)?parseFloat(0).toFixed(2):total_price.toFixed(2));
                        grouped_cleaning_services.value[v] = sv;
                    }else{
                        grouped_cleaning_price.value[v] = {};
                        grouped_cleaning_services.value[v] = {};

                    }
                });
            }else{
                grouped_cleaning_services.value = {};
                grouped_cleaning_price.value = {};
            }

            /*
            context.emit('update-cleaning-price',{
                step:11,
                detailingitem_id:props.detailingitem.id,
                cleaning_prices:JSON.stringify(grouped_cleaning_price.value),
            });
            */
        }

        function setBaseCleaningPrice(val){
            base_cleaning_price.value = val;
        }

        function initTailoringServices(services){
            all_tailoring_services.value = services;
        }

        function refreshTailoringServices(services_id,tailoring_price_type,fixed_price){
            let services_int_id = [];
            let all_services = all_tailoring_services.value;
            let sel_services = [];
            let price = 0;

            services_id.forEach(function(v,i){
                services_int_id.push(parseInt(v));
            });

            let keys = Object.keys(all_services);

            keys.forEach(function(v,i){
                let gp = all_services[v];

                gp.forEach(function(service,index){
                    if(services_int_id.includes(service.id)){
                        sel_services.push(service.name);
                        price += parseFloat(service.price);
                    }
                });
            });

            if(tailoring_price_type=='Quote'){
                price = 0;
            }

            sel_tailoring_services.value = sel_services;
            tailoring_price.value = price.toFixed(2);

            if(tailoring_price_type=='PriceNow'){
                return fixed_price;
            }else{
                return price;
            }
        }
        function submit(e) { 

               clearTimeout(timeout.value);
               timeout.value = setTimeout(function(){
                   nextTick(() => {
                     if ([0,1,2].includes(issuesStep.value)) {

                        context.emit("get-free-text", {
                            text_stain : e.target.value,
                            issuesStep :issuesStep.value
                        });
                    }
                    if ([3,4,5].includes(issuesStep.value)) {

                        context.emit("get-free-text", {
                            text_damage: e.target.value,
                            issuesStep :issuesStep.value
                        });
                    }
                });
               }  
              , 500)
        };

        function addFreeText(e) {
            let data = {};
            data['detailingitem_id'] = props.detailingitem.id;
            if ([0,1,2].includes(issuesStep.value)) {
                /*
                //stainZone.value[0].description = e.target.value;
                context.emit("save-item-issues", {
                    detailingitem_id: props.detailingitem.id,
                    stains_text: e.target.value,
                    //JSON.stringify(stainZone.value)
                });
                */
                data['stains_text']  = e.target.value;
            }
            if ([3,4,5].includes(issuesStep.value)) {
               /*
               context.emit("save-item-issues", {
                    detailingitem_id: props.detailingitem.id,
                    damages_text: e.target.value,
                });
                */
               data['damages_text'] = e.target.value;
            }

            axios.post('/update-detailing-issues-text',data)
                .then((res)=>{
                    if(res.data.updated){
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                            message: 'Free text updated',
                            ttl: 5,
                            type: 'success'
                        });
                    }
                }).catch((err)=>{

                }).finally(()=>{

                });

        }

        onMounted(()=>{

        });

        onUpdated(()=>{
            let price = 0;
            if(props.detailingitem){
                price = props.detailingitem.pricecleaning;
                if(props.detailingitem.etape==11){
                    // parseFloat(props.detailingitem.pricecleaning)+
                    price = parseFloat(props.detailingitem.dry_cleaning_price)+parseFloat(props.detailingitem.cleaning_addon_price)+parseFloat(props.detailingitem.tailoring_price);
                    final_price.value = price.toFixed(2);
                }else{
                    final_price.value = props.detailingitem.pricecleaning.toFixed(2);
                }
            }

            let instructions = props.detailingitem.customer_instructions;

            if(typeof(instructions) !='undefined' && instructions.length > 0){
                showCustomerInstructions.value = true;
            }
        });

        return {
            progress_percent,
            instAcc,
            descAcc,
            issusesAcc,
            servicesAcc,
            show,
            openAccordionclick,
            getStainName,
            getStainZone,
            initCleaningServices,
            refreshCleaningServices,
            grouped_cleaning_services,
            grouped_cleaning_price,
            setBaseCleaningPrice,
            base_cleaning_price,
            final_price,
            cleaning_price_type,
            initTailoringServices,
            refreshTailoringServices,
            sel_tailoring_services,
            tailoring_price,
            showCustomerInstructions,
            stain_free_text,
            damage_free_text,
            getIssueStep,
            issuesStep,
            submit,
            addFreeText
        };
    },
}
</script>
<style scoped>
.panel-header {
    background-color: #47454b;
    width: 100%;
    height: 64px;
    display: flex;
    align-items: center;
    color: #ffffff;
    margin: 0;
    font-family: Gotham Rounded;
}
.price {
    font-family: Gilroy;
    font-style: normal;
    font-weight: 800;
    font-size: 20px;
    line-height: 110%;
    display: flex;
    align-items: center;
    text-align: right;
    color: #ffffff;
    justify-content: right;
}
.progress-bar {
    background: #42a71e;
    height: 6px;
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
/* .accordion-button::after {
    background-image: none;
} */
.accordion-item {
    margin: 10px;
    border-radius: 6px;
    border: 0.5px solid #c3c3c3;
}
.accordion-flush .accordion-item .accordion-button {
    border-radius: 6px !important;
}
.accordion-flush .accordion-item:first-child {
    border-top: 0.5px solid #c3c3c3 !important;
}
.accordion-flush .accordion-item:last-child {
    border-bottom: 0.5px solid #c3c3c3 !important;
}
.accordion-body {
    background: #ffffff;
}
.accordion-button.opened {
    background: #ffffff;
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
.right-panel {
    background: white;
    padding-left: 0px;
}
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
.accordion-body-title {
    display: flex;
    align-items: flex-end;
    line-height: 140%;
    color: #868686;
    font-family: Gotham Rounded;
}
.description-title {
    color: #868686;
    padding: 5px;
    font-family: Gotham Rounded;
}
.description-text {
    color: #47454b;
    padding: 5px;
    font-family: Gotham Rounded;
}
.description-box {
    background: #f8f8f8;
    border-radius: 10px;
    margin: 5px;
}
.brand-coefcleaning {
    padding: 5px;
    text-align: right;
    color: #b69968;
}
.fabric-coefcleaning,
.comp-coefcleaning {
    padding: 5px;
    text-align: right;
}
.colour-span {
    display: inline-block;
    width: 15px !important;
    height: 15px;
    border-radius: 50%;
    margin: 0 2px;
    border: 1px solid #f8f8f8;
    box-sizing: border-box;
    filter: drop-shadow(0px 0px 4px rgba(80, 80, 80, 0.2));
}
.accordion-header {
    margin: 0px !important;
}
.show-btn {
    position: absolute;
    top: 150px;
    left: 97%;
}
.img-arrow {
    transform: rotate(270deg);
}
.done {
    background: #f8f8f8;
}
.stain-bloc {
    display: block;
    padding: 0px;
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 140%;
    align-items: center;
    color: #47454b;
}
.eclipse-number {
    margin: 0 5px;
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 140%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #ffffff;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    min-width: 18px !important;
    min-height: 18px !important;
}
.stain {
    background-color: #ef8f00;
}
.damage {
    background-color: #eb5757;
}
.stain-row {
    word-break: break-all;
    display: flex;
}

.question_mark{
    width: 20px;
    height: 20px;
    border: 1px solid red;
    display: flex;
    float: left;
    justify-content: center;
    align-items: center;
    text-align: center;
    border-radius: 50%;
    color: red;
    margin-right:10px;
}

.question_mark:hover{
    cursor: pointer;
    color:#000;
    border-color: #000;
}

.gp_service{
    font-size:14px;
    font-family: 'Gilroy';
    font-weight: bold;
}

#item-table-heading{
    background:#333;
}

.each-category-row > div,
#item-table-heading > div{
    font-size:12px;
    padding-left:0.25rem;
}

.each-category-row > div{
    border-bottom:thin solid #dadada;
    border-right:thin solid #dadada;
}

.each-category-row > div:first-child{
    border-left:thin solid #dadada;
}

#item-table-heading > div{
    text-indent: 0.25rem;
}

#voucher_code{
    background:#333;
    padding:0.25rem;
}
.free-text-input {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding: 16px;
    border: 1px solid #868686;
    box-sizing: border-box;
    border-radius: 5px;
    flex: none;
    order: 2;
    align-self: stretch;
    flex-grow: 1;
    margin: 8px;
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 140%;
    color: #868686;
}

</style>
