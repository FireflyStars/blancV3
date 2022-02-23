<template>
    <div>
        <a href="javascript:void(0)" @click.stop="$router.push({name:'edit-delivery',query: {id_delivery: data.id}})"><span class="material-icons">edit</span></a>
        <!--{{data}}-->
    </div>
</template>

<script>
    export default {
        name: "ConveyorButtons",
        props: {
            data: {},
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
            /*sendPickup(event,order){
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
                    }).catch((error) => {
                    console.log(error);

                }).finally(() => {

                });
            }*/
        }
    }
</script>

<style scoped>
    .view_details{
        color:#333;
    }

</style>