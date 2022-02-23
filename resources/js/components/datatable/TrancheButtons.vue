<template>
    <div class="btn-wrapper text-right">
 <!--       <a class="inline-btn" href="javascript:void(0)" @click.stop="$router.push({name:'new-customer-pickup-delivery-planning',query: {id_customer: data.id_customer,from_url:$route.name}})" v-if="$route.name=='cust-no-detail' && !data.id_address"><span class="material-icons">edit</span></a><a v-else class="inline-btn"></a>-->

        <a class="inline-btn" href="javascript:void(0)" @click.stop="$router.push({name:'edit-recurrent-planning',query: {id_customer: data.CustomerID,from_url:$route.name}})" :class="{'d-none':($route.name=='cust-no-detail' && !data.id_address) || ($route.name=='customer-list' && data.postcode=='')}"><span class="material-icons">calendar_today</span></a>

        <a class="inline-btn" href="javascript:void(0)" @click.stop="$router.push({name:'edit-pickup-delivery-planning',query: {id_customer: data.CustomerID,from_url:$route.name}})" :class="{'d-none':($route.name=='cust-no-detail' && !data.id_address) || ($route.name=='customer-list' && data.postcode=='')}"><span class="material-icons">view_week</span></a>


        <a class="inline-btn" href="javascript:void(0)" @click.stop="$router.push({name:'edit-customer',query: {id_customer: data.CustomerID,from_url:$route.name,id_address:data.address_id}})" v-if="$route.name=='customer-list' || $route.name=='cust-no-detail'"><span class="material-icons">edit</span></a><a v-else class="inline-btn"></a>

    </div>

</template>

<script>
    export default {
        name: "TrancheButtons",
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
        data(){
            return {};
        },
        created(){

        },
        beforeMount(){
        },
        mounted(){

        },
        methods:{

        }
    }
</script>

<style scoped>
    .btn-wrapper{
        display: flex;
    }
.inline-btn{
   flex:1;
    margin:5px 10px;
}
</style>