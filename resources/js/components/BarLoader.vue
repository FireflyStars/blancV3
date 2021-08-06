<template>
    <div class="barloader"></div>
</template>

<script>
    export default {
        name: "BarLoader"
    }
</script>

<style scoped>
.barloader{
    max-width: 375px;
    width: 100%;
    height: 8px;
    background: #EEEEEE;
    position: relative;
    margin:20px auto;
    display: block;
}
.barloader:after{
    content: " ";
    background: linear-gradient(
            270deg
            , #42A71E 0%, #D0E9C7 51.08%, #42A71E 100%);
    width: 225px;
    position: absolute;
    left: 50%;
    height: 8px;
    z-index: 7;
    top: 0;
    transform: translate(-50%);
    animation: w 2s linear 0s infinite alternate-reverse;

}

@keyframes w {
    0%   {width: 225px;}
    25%  {width: 325px;}
    50%  {width: 280px;}
    100% {width: 375px;}
}
</style>