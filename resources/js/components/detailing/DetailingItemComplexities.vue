<template>
    <transition name="popinout">
        <div class="row">
            <div>Select one or multiple item complexities.</div>
            <div
                class="box complexity"
                v-for="(comp, index) in detailingData.complexities"
                @click="select(comp.id)"
                :class="{ selected: complexities_id.includes(comp.id) }"
            >
                <div class="complexity-name">{{ comp.name }}</div>
            </div>
            <div class="box complexity complexity-name"
                @click="select(0)"
                :class="{ selected: complexities_id.includes(0) }"
                >None</div>
            <div class="row buttons">
                <div class="col-10 text-align-right">
                    <button class="btn btn-link btn-previous" @click="back">Previous</button>
                </div>
                <div class="col-2 text-align-right">
                    <button class="btn btn-next text-white" :disabled="!valid" @click="save">Next</button>
                </div>
            </div>
        </div>
    </transition>
</template>
<script>
import { ref, watch } from 'vue';
export default {
    name: "DetailingItemComplexities",
    components: {},
    props: {
        detailingData: {},
        detailingitem: {}
    },
    emits: ['save-item-complexities', 'go-to-step'],
    setup(props, context) {
        const complexities_id = ref([]);
        const valid = ref(false);
        complexities_id.value = props.detailingitem.complexities_id != null ? JSON.parse(props.detailingitem.complexities_id) : [];
        valid.value = complexities_id.value.length > 0;
        watch(() => props.detailingitem, (current_val, previous_val) => {
            complexities_id.value = current_val.complexities_id != null ? JSON.parse(current_val.complexities_id) : [];
        });
        watch(() => complexities_id.value, (current_val, previous_val) => {
            valid.value = current_val.length > 0;
        });
        function select(id) {
            if (!complexities_id.value.includes(id)) {
                if(id === 0){
                    complexities_id.value=[0];
                }else{
                    if(complexities_id.value.includes(0)){
                        complexities_id.value=[];
                    }
                    complexities_id.value.push(id);
                }
            } else {
                complexities_id.value.splice(complexities_id.value.indexOf(id), 1);
            }
            context.emit("save-item-complexities", {
                detailingitem_id: props.detailingitem.id,
                complexities_id: JSON.stringify(complexities_id.value)
            });

        }
        function save() {
            context.emit("save-item-complexities", {
                detailingitem_id: props.detailingitem.id,
                step: 10
            });
            window.scrollTo(0,0);
        }
        function back() {
            context.emit("go-to-step", 8);
            window.scrollTo(0,0);
        }

        return {
            complexities_id,
            valid,
            select,
            save,
            back
        };
    },
}
</script>
<style scoped>
.box {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 8px;
    font-size: 18px;
    margin: 15px;
    background: #ffffff;
    box-shadow: 0px 0px 4px rgba(80, 80, 80, 0.2);
    border-radius: 4px;
}
.complexity {
    height: 100px;
    width: 115px;
    word-break: break-word;
    font-family: Gotham Rounded Light;
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 140%;
    display: flex;
    align-items: center;
    text-align: center;

    color: #47454b;
}
.complexity-name{
    font-family: 'Gotham Rounded Light';
    font-style: normal;
    font-weight: 400;
    line-height: 140%;
}
.box:hover {
    background-color: #d3e7cc;
    /* background-color: #47454b;
    color: #ffffff; */
}
.box.selected {
    background-color: #47454b;
    color: #ffffff;
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
</style>
