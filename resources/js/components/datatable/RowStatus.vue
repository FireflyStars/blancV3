<template>
    <div>
    <span v-for="item in data.status" >
       <span v-for="(tag, color)  in item" :key="tag"   :style="'background:'+color+';'" class="tp" :class="{hidden:tag==''}">
         {{tag}}
       </span><br/>
    </span>
    </div>
</template>
<script>
export default {
props: {
    data: {},
    name: {},
    click: {
    type: Function,
    default: () => {}
    },
    classes: {
        type: Object,
        default: () => ({
            'btn': true,
            'btn-primary': true,
            'btn-sm': true,
        }),
    },
    },

    created(){
    }
}
</script>