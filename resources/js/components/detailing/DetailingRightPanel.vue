<template>
    <div class="col-4 right-panel">
        <div class="panel-header row">
            <div class="col-5">Item n°{{ item_id }}</div>
            <div class="col-4">Detailed by @{{ customerName }}</div>
            <div class="col-3 price">£{{ item_price }}</div>
        </div>
        <div class="progress-bar" :style="{ width: progress_percent + '%' }"></div>
        <div class="accordion-container">
            <div class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-headingOne">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#flush-collapseOne"
                            aria-expanded="false"
                            aria-controls="flush-collapseOne"
                            @click="openAccordionclick(1)"
                            :class="{ opened: instAcc === true }"
                        >Customer instructions</button>
                    </h2>
                    <div
                        id="flush-collapseOne"
                        class="accordion-collapse collapse"
                        aria-labelledby="flush-headingOne"
                        data-bs-parent="#accordionFlushExample"
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
                            data-bs-toggle="collapse"
                            data-bs-target="#flush-collapseTwo"
                            aria-expanded="false"
                            aria-controls="flush-collapseTwo"
                            @click="openAccordionclick(2)"
                            :class="{ opened: descAcc === true }"
                        >Description</button>
                    </h2>
                    <div
                        id="flush-collapseTwo"
                        class="accordion-collapse collapse"
                        aria-labelledby="flush-headingTwo"
                        data-bs-parent="#accordionFlushExample"
                        :class="{ show: descAcc === true }"
                    >
                        <div class="accordion-body"></div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-headingThree">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#flush-collapseThree"
                            aria-expanded="false"
                            aria-controls="flush-collapseThree"
                            @click="openAccordionclick(3)"
                            :class="{ opened: issusesAcc === true }"
                        >Issues</button>
                    </h2>
                    <div
                        id="flush-collapseThree"
                        class="accordion-collapse collapse"
                        aria-labelledby="flush-headingThree"
                        data-bs-parent="#accordionFlushExample"
                        :class="{ show: issusesAcc === true }"
                    >
                        <div class="accordion-body"></div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-headingThree">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#flush-collapseThree"
                            aria-expanded="false"
                            aria-controls="flush-collapseThree"
                            @click="openAccordionclick(4)"
                            :class="{ opened: servicesAcc === true }"
                        >Services</button>
                    </h2>
                    <div
                        id="flush-collapseFour"
                        class="accordion-collapse collapse"
                        aria-labelledby="flush-headingFour"
                        data-bs-parent="#accordionFlushExample"
                        :class="{ show: servicesAcc === true }"
                    >
                        <div class="accordion-body"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
export default {
    name: "DetailingRightPanel",
    components: {  },
    props: {
            customerName: String,
            item_price: Number,
            item_id: String,
            step: Number,
        },
    setup(props,context) {
        const router = useRouter();
        const route = useRoute();
        const progress_percent = ref(0);
        const instAcc = ref(false);
        const descAcc = ref(false);
        const issusesAcc = ref(false);
        const servicesAcc = ref(false);
        instAcc.value=props.step==1;
        progress_percent.value = props.step / 8 * 100;

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
    background: #f8f8f8;
}
.accordion-button.opened {
    background: #f8f8f8;
}
.accordion-flush .accordion-collapse {
    background: #f8f8f8;
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
    color: #c3c3c3;
}
.accordion-header {
        margin: 0px !important;
}
</style>
