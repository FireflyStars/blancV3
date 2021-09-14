<template>
    <div class="address-title body_small_medium ">{{title}}</div>
    <i v-if="address==null" class="body_small">No address</i>
    <template v-else>
        <div class="address1 body_small">{{address.address1}}</div>
        <div v-if="address.address2!=''&&address.address2!=null" class="address2 body_small">{{address.address2}}</div>
        <div class="postcode-city body_small">{{address.postcode}} {{address.Town}}</div>
    </template>
</template>

<script>
    export default {
        name: "AddressFormat",
        props:['address','title'],
        setup(){

        }
    }
</script>

<style scoped>
    .address-title{
        margin-bottom: 4px;
        color: #C3C3C3;
        line-height: 14px;
    }
    .address1,.address2,.postcode-city,i{
        font-size: 14px;
    }
</style>