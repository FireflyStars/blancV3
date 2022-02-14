<template>
    <div class="picto">
        <item-picto :pictoname="typeitemPicto" face="all"></item-picto>
    </div>
    <div class="row">
        <div class="stains-title">Any stains ?</div>
        <div>
            To add a stain ti the description, please click on its position on the illustration above.
            <br />Then specify tge kind of stain.
        </div>
    </div>
    <div class="row buttons">
        <div class="col-10 text-align-right">
            <button class="btn btn-link btn-previous" @click="back">Previous</button>
        </div>
        <div class="col-2 text-align-right">
            <button class="btn btn-next text-white" :disabled="!valid" @click="save">Next</button>
        </div>
    </div>
</template>
<script>
import { ref, watch } from 'vue';
import ItemPicto from '../miscellaneous/ItemPicto.vue'
export default {
    name: "DetailingItemIssues",
    components: { ItemPicto },
    props: {
        detailingData: {},
        detailingitem: {},
        typeitemPicto: ''
    },
    emits: ['save-item-complexities', 'back-previous-step'],
    setup(props, context) {
        const valid = ref(false);

        function save() {
            context.emit("save-item-complexities", {
                detailingitem_id: props.detailingitem.id,
                step: 11
            });
        }
        function back() {
            context.emit("back-previous-step", 9);
        }

        return {
            valid,
            save,
            back
        };
    },
}
</script>
<style scoped>
.picto {
    padding: 40px;
}
.stains-title{
    display: flex;
    flex-direction: row;
    align-items: center;
    padding: 8px 16px;
    height: 38px;
    background: #FFFFFF;
    border-radius: 4px;
    font-size: 16px;
    line-height: 140%;
    color: #000000;
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
