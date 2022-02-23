<template>
    <div>
        <span class="tp"><a href="javascript:void(0)" @click="loadHistory(data.barcode,data.id_items)" class="assembly_hist_link">{{data.barcode}}</a></span>
    </div>
</template>

<script>
    export default {
        //name: "SingleRowBarcode"
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
        methods:{
            loadHistory(tracking,id_item){
                localStorage.setItem('scanned_item',id_item)

                this.$router.push({
                    name: 'item-history',
                    query: {item: tracking},
                }).catch(err=>{ //Prevent Navigation duplicated error
                    if (err.name != "NavigationDuplicated") {
                        throw err;
                    }
                });
            }
        }
    }
</script>

<style scoped>

</style>