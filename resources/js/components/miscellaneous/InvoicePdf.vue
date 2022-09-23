<template>
    <button class="btn btn-dark" @click="loadPdf">PDF</button>

</template>
<script>
//import wkhtmltopdf from 'wkhtmltopdf';
export default {
    name:"InvoicePdf",
    setup() {

        const loadPdf = ()=>{
            console.log('load pdf');

        }

        return {
            loadPdf,
        }

    },
}
</script>
