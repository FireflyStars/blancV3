<template>
    <transition name="popinout">
        <div>
            <div class="accordion-container">
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item" v-if="detailingData.brands.length > 0">
                        <h2 class="accordion-header" id="accordion-brand">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                @click="descTypeClick('brand')"
                                :class="{ opened: desc_type === 'brand' }"
                            >Brand</button>
                        </h2>
                        <div
                            id="flush-collapseOne"
                            class="accordion-collapse collapse"
                            :class="{ show: desc_type === 'brand' }"
                        >
                            <div class="accordion-body">
                                <div class="row position-relative">
                                    <input
                                        type="text"
                                        class="input-search-brand"
                                        placeholder="Type the a brand name"
                                        v-model="search"
                                        @keyup.enter="brandFilterClick(search)"
                                    />
                                    <span
                                        v-if="showbutton"
                                        @click="clearSearch"
                                        class="icon-close-position"
                                    >
                                        <i class="icon-close"></i>
                                    </span>
                                    <div
                                        class="btn btn-link btn-new-brand"
                                        @click="show_popup = true"
                                    >Couldn’t find the brand in the list?</div>
                                </div>
                                <div class="row">
                                    <div
                                        class="letter"
                                        v-for="(letter) in letters"
                                        :class="{ selected: brand_filter === letter }"
                                        @click="brandFilterClick(letter)"
                                    >{{ letter }}</div>
                                </div>
                                <div class="row">
                                    <div
                                        class="box brand"
                                        :class="{ selected: brand_id === brand.id }"
                                        v-for="(brand, j) in filteredBrand()"
                                        @click="select(brand.id)"
                                    >{{ brand.name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item" v-if="detailingData.colours.length > 0">
                        <h2 class="accordion-header" id="accordion-colour">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                @click="descTypeClick('colour')"
                                :class="{ opened: desc_type === 'colour' }"
                            >Colours</button>
                        </h2>
                        <div
                            id="flush-colour"
                            class="accordion-collapse collapse"
                            :class="{ show: desc_type === 'colour' }"
                        >
                            <div class="row accordion-body">
                                <div
                                    class="box colour"
                                    v-for="(colour, j) in detailingData.colours"
                                    :class="{ selected: color_id.includes(colour.id) }"
                                    @click="select(colour.id)"
                                >
                                    <span
                                        class="tool-tip colour-span"
                                        :data-tooltip="colour.name"
                                        :style="{ background: colour.code }"
                                    ></span>
                                    {{ colour.name }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item" v-if="detailingData.fabrics.length > 0">
                        <h2 class="accordion-header" id="accordion-fabric">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                @click="descTypeClick('fabric')"
                                :class="{ opened: desc_type === 'fabric' }"
                            >Fabrics</button>
                        </h2>
                        <div
                            id="flush-collapseOne"
                            class="accordion-collapse collapse"
                            :class="{ show: desc_type === 'fabric' }"
                        >
                            <div class="row accordion-body">
                                <div
                                    class="box fabric"
                                    v-for="(fabric, j) in detailingData.fabrics"
                                    :class="{ selected: fabric_id.includes(fabric.id) }"
                                    @click="select(fabric.id)"
                                >
                                    {{ fabric.Name }}
                                    <br />
                                    <span
                                        v-if="fabric.coefcleaning != 0"
                                    >(£{{ (fabric.coefcleaning * item_description.base_price).toFixed(2) }})</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item" v-if="detailingData.sizes.length > 0">
                        <h2 class="accordion-header" id="accordion-size">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                @click="descTypeClick('size')"
                                :class="{ opened: desc_type === 'size' }"
                            >Size</button>
                        </h2>
                        <div
                            id="flush-collapseOne"
                            class="accordion-collapse collapse"
                            :class="{ show: desc_type === 'size' }"
                        >
                            <div class="row accordion-body">
                                <div
                                    class="box size"
                                    v-for="(size, j) in detailingData.sizes"
                                    :class="{ selected: size_id === size.id }"
                                    @click="select(size.id)"
                                >
                                    {{ size.name }}
                                    <span class="size-comment">{{ size.comment }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item" v-if="detailingData.patterns.length > 0">
                        <h2 class="accordion-header" id="accordion-pattern">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                @click="descTypeClick('pattern')"
                                :class="{ opened: desc_type === 'pattern' }"
                            >Patterns</button>
                        </h2>
                        <div
                            id="flush-collapseOne"
                            class="accordion-collapse collapse"
                            :class="{ show: desc_type === 'pattern' }"
                        >
                            <div class="row accordion-body">
                                <div
                                    class="box pattern"
                                    v-for="(pattern, j) in detailingData.patterns"
                                    :class="{ selected: pattern_id === pattern.id }"
                                    @click="select(pattern.id)"
                                >{{ pattern.name }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" v-if="detailingData.conditions.length > 0">
                        <h2 class="accordion-header" id="accordion-condition">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                @click="descTypeClick('condition')"
                                :class="{ opened: desc_type === 'condition' }"
                            >Condition</button>
                        </h2>
                        <div
                            id="flush-condition"
                            class="accordion-collapse collapse"
                            :class="{ show: desc_type === 'condition' }"
                        >
                            <div class="row accordion-body">
                                <div
                                    class="box condition"
                                    v-for="(condition, j) in detailingData.conditions"
                                    :class="{ selected: condition_id === condition.id }"
                                    @click="select(condition.id)"
                                >{{ condition.name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row buttons">
                <div class="col-10 text-align-right">
                    <button class="btn btn-link btn-previous" @click="back">Previous</button>
                </div>
                <div class="col-2 text-align-right">
                    <button
                        class="btn btn-next text-white"
                        id="btn-next"
                        :disabled="!valid"
                        @click="next"
                    >Next</button>
                </div>
            </div>
        </div>
    </transition>
    <transition
        enter-active-class="animate__animated animate__fadeIn"
        leave-active-class="animate__animated animate__fadeOut"
    >
        <div v-if="show_popup" class="popup-brand">
            <div class="popup-container">
                <div class="popup-title">
                    Suggest new brand
                    <i
                        class="icon-close popup-close"
                        @click="show_popup = !show_popup"
                    ></i>
                </div>
                <div class="popup-body">
                    <div class="popup-label">New brand</div>
                    <input
                        type="text"
                        class="popup-input"
                        placeholder="Type here"
                        v-model="brand_suggested_name"
                    />
                    <button class="btn popup-btn" @click="saveBrand">Suggest new brand</button>
                </div>
            </div>
        </div>
    </transition>
</template>
<script>
import { propsToAttrMap } from '@vue/shared';
import { ref, onMounted, watch } from 'vue';
export default {
    name: "DetailingItemDescription",
    components: {},
    props: {
        detailingData: {},
        detailingitem: {},
        item_description: {}
    },
    emits: ['save-item-description', 'go-to-step'],
    setup(props, context) {
        const desc_type = ref('size');
        const brand_filter = ref('');
        const size_id = ref(0);
        const brand_id = ref(0);
        const fabric_id = ref([]);
        const color_id = ref([]);
        const pattern_id = ref([]);
        const condition_id = ref([]);
        const valid = ref(false);
        const letters = ref([]);
        const showbutton = ref(false);
        const showSearch = ref(false);
        const search = ref("");
        const show_popup = ref(false);
        const brand_suggested_name = ref("");
        const steps = ref({});
        steps.value = props.detailingData.steps;
        for (let i = "A".charCodeAt(0); i <= "Z".charCodeAt(0); i++) {
            letters.value.push(String.fromCharCode([i]));
        }
        size_id.value = props.detailingitem.size_id != null ? props.detailingitem.size_id : (props.detailingData.sizes.length > 0 ? 0 : -1);
        brand_id.value = props.detailingitem.brand_id != null ? props.detailingitem.brand_id : (props.detailingData.brands.length > 0 ? 0 : -1);
        fabric_id.value = props.detailingitem.fabric_id != null && props.detailingitem.fabric_id != ""? JSON.parse(props.detailingitem.fabric_id) : [];
        color_id.value = props.detailingitem.color_id != null ? JSON.parse(props.detailingitem.color_id) : [];
        pattern_id.value = props.detailingitem.pattern_id != null ? props.detailingitem.pattern_id : (props.detailingData.patterns.length > 0 ? 0 : -1);
        condition_id.value = props.detailingitem.condition_id != null ? props.detailingitem.condition_id : (props.detailingData.conditions.length > 0 ? 0 : -1);
        valid.value = brand_id.value != 0
            && ((color_id.value.length > 0 && props.detailingData.colours.length > 0) || (color_id.value.length == 0 && props.detailingData.colours.length == 0))
            && pattern_id.value != 0;
        switch (props.detailingitem.etape) {
            case 3:
                desc_type.value = 'brand';
                break;
            case 4:
                desc_type.value = 'colour';
                break;
            case 5:
                desc_type.value = 'fabric';
                break;
            case 6:
                desc_type.value = 'size';
                break;
            case 7:
                desc_type.value = 'pattern';
                break;
            case 8:
                desc_type.value = 'condition';
                break;
            default:
                desc_type.value = 'size';
        }
        watch(() => [size_id.value, brand_id.value, fabric_id.value, color_id.value, pattern_id.value, condition_id.value], ([current_size, current_brand, current_fabric, current_color, current_pattern, current_condition], [previous_size, previous_brand, previous_fabric, previous_color]) => {
            valid.value = current_brand != 0
                && ((current_color.length > 0 && props.detailingData.colours.length > 0) || (current_color.length == 0 && props.detailingData.colours.length == 0))
                && current_pattern != 0 ;
        });
        watch(() => props.detailingitem, (current_val, previous_val) => {
            size_id.value = current_val.size_id != null ? current_val.size_id : (props.detailingData.sizes.length > 0 ? 0 : -1);
            brand_id.value = current_val.brand_id != null ? current_val.brand_id : (props.detailingData.brands.length > 0 ? 0 : -1);
            fabric_id.value = current_val.fabric_id != null ? JSON.parse(current_val.fabric_id) : [];
            color_id.value = current_val.color_id != null ? JSON.parse(current_val.color_id) : [];
            pattern_id.value = current_val.pattern_id != null ? current_val.pattern_id : (props.detailingData.patterns.length > 0 ? 0 : -1);
            condition_id.value = current_val.condition_id != null ? current_val.condition_id : (props.detailingData.conditions.length > 0 ? 0 : -1);
            switch (current_val.etape) {
                case 3:
                    desc_type.value = 'brand';
                    break;
                case 4:
                    desc_type.value = 'colour';
                    break;
                case 5:
                    desc_type.value = 'fabric';
                    break;
                case 6:
                    desc_type.value = 'size';
                    break;
                case 7:
                    desc_type.value = 'pattern';
                    break;
                case 8:
                    desc_type.value = 'condition';
                    break;
                default:
                    desc_type.value = 'size';
            }
        });

        function descTypeClick(type) {
            desc_type.value = type;
            let step = props.detailingitem.etape;
            switch (desc_type.value) {
                case 'brand':
                    step = 3;
                    break;
                case 'colour':
                    step = 4;
                    break;
                case 'fabric':
                    step = 5;
                    break;
                case 'size':
                    step = 6;
                    break;
                case 'pattern':
                    step = 7;
                    break;
                case 'condition':
                    step = 8;
                    break;
                case 'complexities':
                    step = 9;
                    break;
                default:
                    step = props.detailingitem.etape;
            }
            context.emit("go-to-step", step);
        }
        function brandFilterClick(letter) {
            brand_filter.value = letter;
            showbutton.value = brand_filter.value.length > 1 ? true : false;
        }
        function filteredBrand() {
            showbutton.value = search.value.length > 0 ? true : false;
            if (brand_filter.value.length == 1) {
                return props.detailingData.brands.filter(brand =>
                    brand.name.toString().toLowerCase().startsWith(brand_filter.value.toLowerCase()));
            } else if (brand_filter.value.length > 1) {
                return props.detailingData.brands.filter(brand =>
                    brand.name.toString().toLowerCase().includes(brand_filter.value.toLowerCase()));
            } else {
                return props.detailingData.brands;
            }
        }
        function clearSearch() {
            search.value = '';
            brand_filter.value = '';
            showSearch.value = false;
            showbutton.value = false;
        }
        function select(id) {
            let index = steps.value.findIndex((step) => step.name === desc_type.value);
             index =(steps.value[index + 1].id == 6 && props.detailingData.sizes.length==0)?index+1:index;
            if (desc_type.value == 'size') {
                size_id.value = id;
                context.emit("save-item-description", {
                    detailingitem_id: props.detailingitem.id,
                    size_id: size_id.value,
                    step: steps.value[index + 1].id
                });
            }
            if (desc_type.value == 'brand') {
                brand_id.value = id;
                context.emit("save-item-description", {
                    detailingitem_id: props.detailingitem.id,
                    brand_id: brand_id.value,
                    step: steps.value[index + 1].id
                });
            }
            if (desc_type.value == 'fabric') {
                if (!fabric_id.value.includes(id)) {
                    fabric_id.value.push(id);
                } else {
                    fabric_id.value.splice(fabric_id.value.indexOf(id), 1);
                }
                context.emit("save-item-description", {
                    detailingitem_id: props.detailingitem.id,
                    fabrics_id: JSON.stringify(fabric_id.value),
                    step: steps.value[index + 1].id
                });


            }
            if (desc_type.value == 'colour') {
                if (!color_id.value.includes(id)) {
                    color_id.value.push(id);
                } else {
                    color_id.value.splice(color_id.value.indexOf(id), 1);
                }
                context.emit("save-item-description", {
                    detailingitem_id: props.detailingitem.id,
                    color_id: JSON.stringify(color_id.value),
                    step: steps.value[index + 1].id
                });
            }
            if (desc_type.value == 'pattern') {
                pattern_id.value = id;
                context.emit("save-item-description", {
                    detailingitem_id: props.detailingitem.id,
                    pattern_id: pattern_id.value,
                    step: steps.value[index + 1].id
                });
            }
            if (desc_type.value == 'condition') {
                condition_id.value = id;
                context.emit("save-item-description", {
                    detailingitem_id: props.detailingitem.id,
                    condition_id: condition_id.value
                });
            }
        }
        function next() {
            context.emit("save-item-description", {
                detailingitem_id: props.detailingitem.id,
                step: 9
            });
            window.scrollTo(0,0);
        }
        function back() {
            context.emit("go-to-step", 2);
             window.scrollTo(0,0);
        }
        function saveBrand() {
            context.emit("save-item-description", {
                detailingitem_id: props.detailingitem.id,
                brand_name: brand_suggested_name.value
            });
            show_popup.value = false;
            brand_suggested_name.value = "";
        }
        return {
            desc_type,
            brand_filter,
            size_id,
            brand_id,
            fabric_id,
            color_id,
            pattern_id,
            condition_id,
            valid,
            letters,
            search,
            showSearch,
            showbutton,
            show_popup,
            brand_suggested_name,
            descTypeClick,
            brandFilterClick,
            clearSearch,
            filteredBrand,
            select,
            next,
            back,
            saveBrand
        };
    },
}
</script>
<style scoped>
.accordion-button {
    background: #ffffff;
    box-sizing: border-box;
    border-radius: 6px;
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 19.6px;
    color: #000000;
    height: 40px;
}
.accordion-item {
    margin: 10px;
    border-radius: 6px;
    border: none;
}
.accordion-flush .accordion-item .accordion-button {
    border-radius: 6px !important;
}
.accordion-body {
    background: #f8f8f8;
}
.box {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 8px;
    font-family: Gotham Rounded;
    font-size: 18px;
    margin: 15px;
    background: #ffffff;
    box-shadow: 0px 0px 4px rgba(80, 80, 80, 0.2);
    border-radius: 4px;
}
.size {
    width: 130px;
    height: 121px;
}
.brand {
    height: 35px;
    width: 210px;
    min-width: fit-content;
    margin: 10px;
    padding: 10px;
    font-size: 14px;
    align-items: flex-start;
}
.fabric {
    height: 112px;
    width: 112px;
}
.colour {
    height: 93px;
    width: 125px;
    font-size: 12px;
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
.size-comment {
    font-size: 12px;
    width: 65%;
}
.pattern {
    height: 93px;
    width: 125px;
    font-size: 12px;
}
.condition {
    height: 200px;
    width: 200px;
}
.box:hover,
.letter:hover {
    background-color: #d3e7cc;
    /* background-color: #47454b;
    color: #ffffff; */
}
.box.selected,
.letter.selected {
    background-color: #47454b;
    color: #ffffff;
}

.letter {
    color: #000000;
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 18px;
    line-height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    box-shadow: 0px 0px 4px rgba(80, 80, 80, 0.2);
    border-radius: 4px;
    width: 32px;
    height: 32px;
    padding: 5px;
    margin: 5px;
}
.accordion-button.opened {
    background: rgba(217, 237, 210, 0.2);
}
.accordion-flush .accordion-collapse {
    background: #f8f8f8;
    border-radius: 6px;
}
.accordion-button:focus,
.accordion-button:active {
    outline: none !important;
    box-shadow: none !important;
}
.opened:after {
    transform: rotate(180deg);
}

.accordion-header {
    margin: 0px !important;
}
.buttons {
    padding: 10px 60px;
}
.btn-next {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    padding: 18px 14px;
    width: 148px;
    height: 41px;
    border-radius: 4px;
    background: #42a71e;
    outline: none !important;
    box-shadow: none !important;
}
.btn-next:disabled {
    background: #e0e0e0;
}
.btn-previous {
    font-size: 16px;
    line-height: 19px;
    align-items: center;
    text-align: center;
    text-decoration-line: underline;
}

/* Search */
input {
    background: #f8f8f8 url(/images/search_gray.svg?9dab47b…) no-repeat center
        left 11px;
    height: 40px;
    padding-left: 45px;
    padding-right: 30px;
    width: 40%;
    height: 40px;
    margin-bottom: 12px;
    border: 0.5px solid #868686;
    border-radius: 4px;
}
input:focus-visible {
    outline: 0.5px #000000 solid;
    background-color: #ffffff;
}
.icon-close-position {
    position: absolute !important;
    top: 10px;
    left: 35%;
}
.icon-close:before {
    width: 16px;
    left: 4px;
    top: 2px;
}
.icon-close:after {
    width: 16px;
    top: 2px;
    left: -2px;
}

.btn-new-brand {
    width: fit-content;
    color: #868686;
    font-size: 14px;
    margin-left: auto;
}
.popup-brand {
    background: rgba(224, 224, 224, 0.6);
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    z-index: 10002;
    display: flex;
    align-items: center;
    justify-content: center;
}
.popup-container {
    text-align: center;
    background-color: #fff;
    width: 500px;
    height: 400px;
    display: block;
    border-radius: 6px;
    box-shadow: 0px 8px 36px rgb(0 0 0 / 16%);
}
.popup-title {
    height: 100px;
    background: #f8f8f8;
    border-radius: 6px;
    font-family: Gilroy;
    font-style: normal;
    font-weight: 800;
    font-size: 22px;
    line-height: 110%;
    display: flex;
    align-items: center;
    text-align: center;
    place-content: center;
    color: #000000;
    padding: 20px;
}
.popup-body {
    padding: 60px;
}
.popup-label {
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 16px;
    line-height: 140%;
    display: flex;
    align-items: flex-end;
    color: #000000;
    flex: none;
    order: 0;
    align-self: stretch;
    flex-grow: 0;
    margin: 6px 0px;
}
.popup-input {
    background: #ffffff;
    border: 0.5px solid #c3c3c3;
    box-sizing: border-box;
    border-radius: 5px;
    flex: none;
    order: 2;
    align-self: stretch;
    flex-grow: 0;
    margin: 6px 0px;
    width: 100%;
    padding: 10px;
}
.popup-btn {
    display: flex;
    justify-content: center;
    background: #47454b;
    border-radius: 4px;
    font-family: Gotham Rounded;
    line-height: 140%;
    align-items: center;
    text-align: center;
    color: #ffffff;
    margin: 85px auto 20px auto;
    outline: none !important;
    box-shadow: none !important;
}
.popup-close {
    position: relative;
    left: 115px;
}
</style>
