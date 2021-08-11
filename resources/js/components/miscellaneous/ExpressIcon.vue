<template>
    <svg v-if=isexpress width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M7.79199 3.64551V9.4789C7.79199 9.7013 7.89225 9.90912 8.06543 10.0477L11.7113 12.9644L12.6228 11.825L9.25031 9.12895V3.64555L7.79199 3.64551Z" fill="#EB5757"/>
        <circle cx="9" cy="9" r="8.5" stroke="#EB5757"/>
    </svg>

</template>

<script>
    import {ref,watch} from 'vue';
    export default {
        name: "ExpressIcon",
        props:['express_values'],
        setup(props){
            const isexpress=ref('false')
            let express_values=props.express_values.split(',');
        // STANDARD   0,2,3
        // EXPRESS 24  1,4,5
        // EXPRESS 48  6


            const express=["1","4","5","6"];
            let res=express_values.filter((item)=>{
                return express.includes(item)
            });
            isexpress.value=(res.length>0?true:false);

            watch(() => props.express_values, (current_val, previous_val) => {
                let express_values=current_val.split(',');
                let res=express_values.filter((item)=>{
                    return express.includes(item)
                });
                isexpress.value=(res.length>0?true:false);
            });

            return {
                isexpress
            }
        }
    }
</script>

<style scoped>

</style>