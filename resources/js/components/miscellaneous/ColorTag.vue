<template>
    <div :style="style">
    <template  v-for="(color,index) in colortags" >
    <span v-if="typeof colors[color.replace(/ /g, '')]!='undefined'" class="tool-tip colortag" :class="{colortagbordered:colors[color.replace(/ /g, '')].color=='#FFFFFF'}" :data-tooltip="colors[color.replace(/ /g, '')].tooltip" :style="{backgroundColor:colors[color.replace(/ /g, '')].color}"></span>
        <span v-else  class="tool-tip colortag undefinedcolor" :data-tooltip="`undefined ${color.replace(/ /g, '')}`"></span>
    </template>
    </div>
</template>

<script>
    import {_COLORS_} from "../../static/defaultvalues";
    export default {
        name: "ColorTag",
        props:['colors','style'],
        setup(props){
            const colortags=props.colors.split(',')
            const colors=_COLORS_;
            return {
                colors,
                colortags
            }
        }
    }
</script>

<style scoped>
    .colortag{
        display: inline-block;
        width: 15px!important;
        height: 15px;
        border-radius: 50%;
        margin: 0 2px;
    }
    .undefinedcolor{
        border:1px dotted #0a58ca;
        background: white;
    }
    .colortagbordered{
        border: 1px solid #DDD;
    }
</style>