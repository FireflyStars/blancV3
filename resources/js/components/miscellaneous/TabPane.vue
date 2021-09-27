<template>
    <div class="tp-tabs">
    <div v-for="(tab,index) in tabs" class="body_bold tp-tab" :class="{active:currenttab==index}"  @click="show(index)">{{tab}}</div>
    </div>
    <div>
        <template v-for="(tab,index) in tabs">
            <transition
                    enter-active-class="animate__animated animate__fadeIn"

            >
            <div v-if="currenttab==index" class="tab-view">
        <slot :name="index" ></slot>
            </div>
            </transition>
        </template>
    </div>
</template>

<script>
    import {ref} from 'vue';
    export default {
        name: "TabPane",
        props:['tabs','current'],
        setup(props){

            const currenttab=ref('');
            currenttab.value=props.current;
            function show(index){
                currenttab.value=index;
            }
            return {
                show,
                currenttab
            }
        }
    }
</script>

<style scoped>
    .tp-tabs{
        display: flex;
    }
 .tp-tab{
     min-width: 150px;
     height: 40px;
     padding: 10px;
     text-align: center;
     line-height: 20px;
     background-color: #EEEEEE;
     margin-right: 8px;
     cursor: pointer;
     border-radius: 6px 6px 0px 0px;
     font-size: 16px;
 }
 .tp-tab.active{
      background-color: #FFF;
      font-weight: bold;
      color:#42A71E;
  }
    .tab-view{
        min-height: 550px;
        align-items: center;justify-content: center;
        overflow-x: auto;
    }

</style>