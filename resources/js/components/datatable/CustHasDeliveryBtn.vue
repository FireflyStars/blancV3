<template>
    <div><span class="material-icons is_ok" v-if="data.DeliverybyDay==1 || data.dates_ask">check_circle_outline</span><span class="material-icons is_not_ok" v-else>highlight_off</span></div>
</template>

<script>
    export default {
        name: "CustHasDeliveryBtn",
        props: {
            data: {
            },
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
    }
</script>

<style scoped>

</style>