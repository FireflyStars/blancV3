<template>
    <div v-if="data.count_item==data.count_slot">
        <!--{{data.ORDER2}}-->
       <!-- <div class="mb-2">
            Count item: {{data.count_item}} |  Count slot: {{data.count_slot}}
        </div>-->
        <button type="button" class="btn-pickup btn-outline-dark float-left mr-4" @click="sendPickup($event,data.ORDER)"><span v-if="pickup_sent==1">Pickup sent</span><span v-else>Pickup and delete</span></button>
       <!-- <button type="button" class="btn-outline-dark float-left mr-4">Cancel</button>-->

       <!-- <a class="view_details d-block float-left" href="#"><span class="material-icons">info</span></a>-->
    </div>
</template>

<script>
    export default {
        name: "ConveyorButtons",
        data(){
          return {
            pickup_sent:0,
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
            sendPickup(event,order){
                let el = $(event.target);
                //el.hide();

                axios.post('/set-order-pickup',
                    {
                        order: order,
                    })
                    .then((res) => {
                        if (res.data.insert) {
                            //el.html('<span>Pickup sent</span>');
                            //el.hide();

                        }
                        this.pickup_sent = 1;
                    }).catch((error) => {
                    console.log(error);

                }).finally(() => {

                });
            }
        }
    }
</script>

<style scoped>
    .view_details{
        color:#333;
    }

</style>