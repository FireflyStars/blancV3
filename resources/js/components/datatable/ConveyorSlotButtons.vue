<template>
    <div>
        <button v-if="data.toCancel==0 && data.Status !='O' && data.Status != 'U'" type="button" class="btn-outline-dark float-left mr-4" :id="'btn_'+data.id" @click="cancelItem($event,data.id)">Cancel Item</button>
    </div>
</template>

<script>
    export default {
        name: "ConveyorSlotButtons",
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
            cancelItem(event,id){
                console.log(event);
                let cmp = this;
                let el = $(event.target);


                axios.post('/cancel-conveyor-slot',
                    {
                        id:id,
                    })
                    .then((res) => {
                        if(res.data.insert){
                            el.hide();
                        }
                    }).catch((error) => {
                    console.log(error);

                }).finally(() => {

                });
            }
        }
    }
</script>

<style scoped>

</style>