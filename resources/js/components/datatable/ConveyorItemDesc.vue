<template>
    <div>
        <table class="table table-striped">
            <tr v-for="item in data.item_details">
     <!-- <td>{{item}}</td>-->
                <td width="100"><a href="javascript:void(0)" @click="loadHistory(item.barcode,data.ORDER)">{{item.barcode}}</a></td><td width="150">{{item.gardesc}}</td>
                <td width="300">{{item.type_op}} - {{item.dernier_statut}}</td>
                <td width="80"><span v-if="item.last_status !='U' && item.last_status !='M'">{{item.slot}}</span></td>
            </tr>
        </table>

    </div>
</template>

<script>
    export default {
        name: "ConveyorItemDesc",
        data(){
            return {
                history_list: '',
            };
        },
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
        created(){

        },
        mounted(){

        },
        methods:{
            loadHistory(barcode,order){
                localStorage.setItem('cur_barcode',barcode);
                localStorage.setItem('cur_order',order);
                $('#load_modal').click();
            },

        }
    }
</script>

<style scoped>

</style>