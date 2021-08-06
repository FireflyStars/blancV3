<template>
    <span class="sort-arrows">
        <svg v-if="asc" width="8" height="6" viewBox="0 0 8 6" fill="none" xmlns="http://www.w3.org/2000/svg" class="asc">
            <path d="M3.59091 0.75C3.78336 0.416666 4.26449 0.416667 4.45694 0.75L7.05502 5.25C7.24747 5.58333 7.0069 6 6.622 6H1.42585C1.04095 6 0.800387 5.58333 0.992837 5.25L3.59091 0.75Z" fill="#47454B"/>
        </svg>
        <svg  v-if="desc" width="8" height="6" viewBox="0 0 8 6" fill="none" xmlns="http://www.w3.org/2000/svg" class="desc">
            <path d="M4.45694 5.25C4.26449 5.58333 3.78336 5.58333 3.59091 5.25L0.992837 0.749999C0.800387 0.416666 1.04095 -6.10471e-07 1.42585 -5.76822e-07L6.622 -1.2256e-07C7.0069 -8.8911e-08 7.24747 0.416666 7.05502 0.75L4.45694 5.25Z" fill="#47454B"/>
        </svg>

    </span>
</template>

<script>
    import {watch,ref } from 'vue';
    export default {
        name: "SortArrows",
        props:['sort','colname'],
        setup(props){
            const asc=ref(true);
            const desc=ref(true);
            if(props.sort.some(e => e[props.colname]==='DESC'))
                asc.value=false;
            if(props.sort.some(e => e[props.colname]==='ASC'))
                desc.value=false;


            watch(() => _.cloneDeep(props.sort), (toval, fromval) => {
                asc.value = true;
                desc.value=true;
                if(toval.some(e => e[props.colname]==='DESC'))
                    asc.value=false;
                if(toval.some(e => e[props.colname]==='ASC'))
                    desc.value=false;

            });
            return{
                desc,
                asc
            }
        }
    }
</script>

<style scoped>
.sort-arrows{
    position: relative;
    display: inline-block;
    width: 8px!important;
    height: 16px;
    left: 3px;
    top: 3px;
}
.sort-arrows .asc{
    position: absolute;
    top:0;
    left: 0;
}
    .sort-arrows .desc{
        position: absolute;
        bottom: 0;
        left: 0;
    }
</style>