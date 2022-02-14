<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <div class="col-4 right-panel" v-if="show">
            <div class="panel-header row" @click="show = !show">
                <div class="col-5">Item n°{{ detailingitem.item_id }}</div>
                <div class="col-4">Detailed by @{{ customerName }}</div>
                <div class="col-3 price">£{{ detailingitem.pricecleaning }}</div>
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
                                :class="{ opened: instAcc === true }"
                            >Customer instructions</button>
                        </h2>
                        <div
                            id="flush-collapseOne"
                            class="accordion-collapse collapse"
                            :class="{ show: instAcc === true }"
                        >
                            <div class="accordion-body">
                                <div class="accordion-body-title">Customer instructions</div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingTwo">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                @click="openAccordionclick(2)"
                                :class="{ opened: descAcc === true }"
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
                                            >+{{ item_description.brand_coef_cleaning }}%</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="description-title">Size</div>
                                        <div class="description-text">{{ item_description.size }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="description-title">Item fabric</div>
                                        <div
                                            v-if="item_description.fabric_name"
                                            class="row description-box"
                                        >
                                            <div
                                                class="col description-text"
                                            >{{ item_description.fabric_name }}</div>
                                            <div
                                                class="col fabric-coefcleaning"
                                            >£{{ item_description.fabric_coef_cleaning }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="description-title">Colour & pattern</div>
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
                                        {{ item_description.pattern_name }}
                                    </div>
                                    <div class="col-6">
                                        <div class="description-title">Condition</div>
                                        <div
                                            class="description-text"
                                        >{{ item_description.condition_name }}</div>
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
                                            >£{{ comp.coefcleaning * item_description.base_price }}</div>
                                        </div>
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
                                :class="{ opened: issusesAcc === true }"
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
                            <div class="accordion-body"></div>
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
import { ref, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';

export default {
    name: "DetailingRightPanel",
    components: { },
    props: {
        customerName: String,
        item_description: {},
        detailingitem: {},
        step: Number,
    },
    setup(props, context) {
        const router = useRouter();
        const route = useRoute();
        const progress_percent = ref(0);
        const instAcc = ref(false);
        const descAcc = ref(false);
        const issusesAcc = ref(false);
        const servicesAcc = ref(false);
        const show = ref(true);
        instAcc.value = props.step == 1 ? true : false;
        descAcc.value = props.step > 1 && props.step <= 9 ? true : false;
        issusesAcc.value = props.step == 10 ? true : false;
        progress_percent.value = props.step / 12 * 100;
        watch(() => props.step, (current_val, previous_val) => {
            instAcc.value = current_val == 1 ? true : false;
            descAcc.value = current_val > 1 && current_val <= 9 ? true : false;
            issusesAcc.value = props.step == 10 ? true : false;
            progress_percent.value = current_val / 12 * 100;
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
        return {
            progress_percent,
            instAcc,
            descAcc,
            issusesAcc,
            servicesAcc,
            show,
            openAccordionclick
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
.accordion-button::after {
    background-image: none;
}
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
    background-color: white;
    height: 100%;
}
.opened:after {
    background-image: url("../../../../resources/img/accordion_arrow.png");
    background-repeat: no-repeat !important;
}
.accordion-body-title {
    display: flex;
    align-items: flex-end;
    line-height: 140%;
    color: #868686;
}
.description-title {
    color: #868686;
    padding: 5px;
}
.description-text {
    color: #47454b;
    padding: 5px;
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
</style>
