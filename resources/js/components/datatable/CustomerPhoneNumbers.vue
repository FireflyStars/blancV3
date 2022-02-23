<template>
    <div>
        <div v-if="data.Phone!=''">
            <div v-for="p in data.Phone">{{p}}</div>
        </div>
    </div>
    
</template>

<script>
    export default {
        name: "CustomerPhoneNumbers",
        props:{
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
        }
    }
</script>

<style scoped>

</style>