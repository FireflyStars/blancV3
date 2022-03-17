<template>
    <div class="odv container customer-detail">
        <h5>Customer Detail Page</h5>
    </div>
</template>
<script>
import { 
    ORDERDETAIL_UPDATE
    } from '../../store/types/types';
import { ref, computed, onMounted, nextTick, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useStore } from 'vuex';

export default {
    name: "CustomerDetail",
    components:{

    },
    setup(){
        const route =useRoute();
        onMounted(()=>{
        })
        return{
        }
    }   
}
</script>
<style scoped>
.odv{
    max-width: 684px;
    background: #FFF;
    height: calc(100% - var(--mainlogoheight));
    position: fixed;
    top: var(--mainlogoheight);
    overflow-y: auto;
    right: 0;
    z-index: 10001;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.18);
    padding: 0;
}
</style>