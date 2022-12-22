<template>
    <div class="d-flex" @click="print">
        <div class="print-bag-tag-icon"></div>
        <div class="print-bag-tag-label">{{ label }}</div>
    </div>
    <qz-print ref="qz_printer"></qz-print>
</template>
<script>
    import {ref} from 'vue';
    import QzPrint from "../QzPrint";
    export default{
        name: 'PrintBagTag',
        props: {
            label: String,
            CustomerID: {
                type: String,
                required: true
            },
        },
        components: {
            QzPrint
        },
        setup(props){
            const qz_printer = ref(null);
            const print = ()=>{
                qz_printer.value.loadPrinterCustomer(props.CustomerID);
            }
            return{
                qz_printer,
                print
            }
        }
    }
</script>
<style scoped>
.print-bag-tag-icon{
    width: 21px;
    height: 24px;
    background-image: url(../../../img/print-bag-tag.svg);
    background-position: center;
    background-repeat: no-repeat;
    background-size: contain;
    cursor: pointer;
}
.print-bag-tag-label{
    margin-left: 10px;
    color: #42A71E;
    text-decoration: underline;
    font-family: 'Gotham Rounded';
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 19px;
    cursor: pointer;
}
</style>