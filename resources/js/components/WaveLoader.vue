<template>
    <transition
            enter-active-class="animate__animated animate__fadeIn"
            leave-active-class="animate__animated animate__fadeOut"
    >
    <div v-if="show_loader" style="background:rgba(224, 224, 224,0.6); position: fixed;top: 0; left:0; height: 100%;width: 100%;z-index: 10002;display: flex;align-items: center;justify-content: center;">
    <div style="text-align: center;background-color: #fff;width: 420px; display: block;padding: 30px 10px 30px 10px; border-radius: 8px;">
        <bar-loader></bar-loader>
    <p style="display: block;" class="body_medium">{{msg.charAt(0).toUpperCase() + msg.slice(1)}}<br/>{{timer/1000}}s</p>
    </div>
    </div>
    </transition>
</template>

<script>
    import {watch ,ref} from 'vue';
    import BarLoader from './BarLoader';
    export default {
        name: "WaveLoader",
        components: {BarLoader},
        props:{
            show_loader:{
                type:Boolean
            },
            msg:{
                type: String
            },

        },
        setup(props,context){


            const timer=ref(0);
            let t;
            watch(() => props.show_loader, (toval, fromval) => {
                if(toval==true){
                    t=setInterval(()=>{
                        timer.value+=100;
                    },100)
                }else{
                    clearInterval(t);
                    timer.value=0;
                }

            });


            return {
                timer,

            }
        }

    }
</script>

<style scoped>

</style>