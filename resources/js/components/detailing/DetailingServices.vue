<template>

    <div class="row buttons">
        <div class="col-10 text-align-right">
            <button class="btn btn-link btn-previous" @click="back">Previous</button>
        </div>
        <div class="col-2 text-align-right">
            <button class="btn btn-next text-white">Next</button>
        </div>
    </div>

</template>
<script>
export default {
    name: "DetailingServices",
    emits:['go-to-step'],
    setup(props,context) {


        function back(){
            context.emit("go-to-step", 10);
        }



        return {
            back
        };
    },
}
</script>
<style scoped>

</style>
