<template>
    <div>

    <span v-for="item in data.barcodes">
        <span class="tp">{{item.barcode}}</span><br/>
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