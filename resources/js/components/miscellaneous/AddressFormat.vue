<template>
    <div class="address-title h7">{{title}}</div>
    <i v-if="address==null">No address</i>
    <template v-else>
        <div class="address1">{{address.address1}}</div>
        <div v-if="address.address2!=''&&address.address2!=null" class="address2">{{address.address2}}</div>
        <div class="postcode-city">{{address.postcode}} {{address.Town}}</div>
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
    }
    .address1,.address2,.postcode-city,i{
        font-size: 14px;
    }
</style>