<template>
    <router-view />
    <toaster></toaster>
    <wave-loader :show_loader="SHOW_LOADER" :msg="LOADER_MSG"></wave-loader>
</template>

<script>
    import WaveLoader from './WaveLoader';
    import Toaster from './miscellaneous/Toaster';

    import {computed,watch} from 'vue';
    import {useStore} from 'vuex';

    import {GET_LOADER_MSG,GET_SHOW_LOADER,LOADER_MODULE} from '../store/types/types'
    export default {
        name: "App.vue",
        components:{Toaster, WaveLoader},
        setup(){

        const store=useStore();


        return{
            LOADER_MSG:computed(()=>store.getters[`${LOADER_MODULE}${GET_LOADER_MSG}`]),
            SHOW_LOADER:computed(()=>store.getters[`${LOADER_MODULE}${GET_SHOW_LOADER}`]),
        }

        }
    }
</script>

<style scoped>

</style>