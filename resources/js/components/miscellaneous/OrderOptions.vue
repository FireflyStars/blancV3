<template>

    <div class="options position-absolute">
        <div class="row">
          
            <div  class="col-12 row-option"  >
                <img class="img-arrow" src="/images/payment.png" />
                <span>Take payment</span>
            </div>
            <div class="col-12 row-option" >
                <img class="img-arrow" src="/images/offload.png" />
                <span>Offload</span>
            </div>
            <div class="col-12 row-option" row-option>
                <img class="img-arrow" src="/images/link.svg" />
                <span>Copy link</span>
            </div>
             <div class="col-12 row-option" >
                <img class="img-arrow" src="/images/arrow.png" />
                <span>Reassign</span>
            </div>
             <div class="col-12 row-option" >
                <img class="img-arrow" src="/images/garbage.png" />
                <span>Delete</span>
            </div>
            <div class="col-12 row-option" >
                <img class="img-arrow" src="/images/erase.png" />
                <span>Void</span>
            </div>
        </div>
       

    </div>
</template>

<script>

    import {useStore} from 'vuex';
    export default {
        name: "OrderOptions",
        props:[],
        setup(){
           const store=useStore();
            return {
            }
        }
    }
</script>

<style scoped>

    .row-option{
        height: 56px;
        padding: 10px 32px;
    }
    .row-option span {
       vertical-align: middle;
    }
    .row-option:hover{
        background-color: #EEEEEE;
    }
    .options{
        top:56px;
        right: -28px;
        background: #F8F8F8;
        box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.12);
        width: 298px;
        min-height: auto;
        z-index: 2;
        transform-origin: top center;
        padding:0 10px;
    }
    img.img-arrow {
    margin-right: 25px;
    width: 25px;
    height: auto;
    object-fit: contain;
    object-position: center;
    }

      .custom-trans-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }

</style>