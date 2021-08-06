<template>
    <button class="btn btn-outline-dark position-absolute filter" :class="{active:showfilter}" @click="show">Filters <span></span></button>
    <transition name="trans-filter" >
    <div class="filters position-absolute" v-if="showfilter">
        <h2>Filter by</h2>
    </div>
    </transition>
</template>

<script>
    import {ref} from 'vue';
    export default {
        name: "Filters",
        setup(){
            const showfilter=ref(false);

            function show(){
                showfilter.value=!showfilter.value;
            }
            return {
                showfilter,
                show
            }
        }
    }
</script>

<style scoped>
    .filter{
        top:-62px;
        right: 22px;
        font-size: 14px;
        font-weight: bold;
        padding: 10px 16px;
        font-family: "Gotham Rounded";
    }
    .filter:focus{
        box-shadow: none;
    }
    .filter span{
        background: #47454B;
        width: 14px;
        height: 3px;
        border-radius: 5px;
        display: inline-block;
        position: relative;
        vertical-align: middle;
        left: 4px;
        margin: 0 5px 0 20px;
    }
    .filter span:before{
        background: #47454B;
        content: " ";
        width: 22px;
        height: 3px;
        border-radius: 5px;
        display: block;
        top: -6px;
        left: 50%;
        position: absolute;
        transform: translate(-50%);
    }
    .filter span:after{
        background: #47454B;
        content: " ";
        width: 7px;
        height: 3px;
        border-radius: 5px;
        display: block;
        left: 50%;
        position: absolute;
        transform: translate(-50%);
        top: 6px;

    }
    .filter:hover span, .filter:hover span:before, .filter:hover span:after,
    .filter.active span, .filter.active span:before, .filter.active span:after
    {
        background: #FFF;
    }
    .filters{
        right: 22px;
        background: #F8F8F8;
        box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.12);
        width:458px;
        min-height:445px;
        top:-8px;
        z-index: 1;
        transform-origin: top center;
    }

    .trans-filter-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-filter-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .trans-filter-enter-active{
        transition: all ease 0.2s;
    }
    .trans-filter-leave-from{
        opacity: 1;
        transform: scale(1);
    }
    .trans-filter-leave-to{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-filter-leave-active{
        transition: all ease 0.2s;
    }
</style>