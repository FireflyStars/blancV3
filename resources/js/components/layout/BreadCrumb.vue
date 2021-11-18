<template>

    <div class="breadcrumb">
        <svg class="logo" width="37" height="37" style="margin:0 58px 0 0" viewBox="0 0 37 37" fill="none" xmlns="http://www.w3.org/2000/svg" @click="slideinMenu">
            <path d="M18.29 1.3597C8.96036 1.3597 1.36949 8.90656 1.36949 18.1824C1.36949 27.4582 8.95058 35.0051 18.29 35.0051C27.6294 35.0051 35.213 27.4582 35.213 18.1824C35.213 8.90656 27.6319 1.3597 18.29 1.3597ZM18.29 36.3648C8.2047 36.3648 0 28.209 0 18.1824C0 8.15578 8.2047 0 18.29 0C28.3753 0 36.58 8.15823 36.58 18.1824C36.58 28.2065 28.3753 36.3648 18.29 36.3648Z" fill="#47454B"/>
            <path d="M17.0401 17.9452C18.1318 18.7093 19.4321 19.1191 20.7646 19.1191C22.0972 19.1191 23.3974 18.7093 24.4891 17.9452C23.3992 17.1767 22.0982 16.7642 20.7646 16.7642C19.431 16.7642 18.13 17.1767 17.0401 17.9452ZM11.9828 6.30941V29.5638H20.7695C22.4721 29.5671 24.1065 28.8948 25.3141 27.6946C26.5217 26.4943 27.2039 24.864 27.211 23.1614C27.2139 21.5696 26.6196 20.0347 25.5456 18.8598C24.0875 19.9751 22.2849 20.5446 20.4507 20.4696C18.6165 20.3945 16.8665 19.6796 15.5043 18.4489C15.4356 18.3855 15.3808 18.3086 15.3433 18.2229C15.3059 18.1373 15.2865 18.0448 15.2865 17.9513C15.2865 17.8578 15.3059 17.7653 15.3433 17.6797C15.3808 17.594 15.4356 17.5171 15.5043 17.4536L15.5288 17.4316C16.8867 16.1986 18.6345 15.4818 20.4671 15.4063C22.2997 15.3307 24.1006 15.9013 25.5554 17.0183C26.6344 15.8447 27.2332 14.3085 27.233 12.7142C27.2252 11.012 26.5428 9.38241 25.3352 8.18268C24.1277 6.98295 22.4937 6.31105 20.7915 6.3143L11.9828 6.30941ZM20.7695 30.9235H11.7211H11.2785C11.0992 30.9216 10.9279 30.8492 10.8016 30.722C10.6753 30.5947 10.6041 30.4229 10.6035 30.2437V5.62956C10.6048 5.44903 10.6776 5.27637 10.8059 5.1494C10.9343 5.02243 11.1077 4.9515 11.2883 4.95215H20.9114C22.9508 4.98542 24.8959 5.81687 26.3293 7.26805C27.7627 8.71924 28.57 10.6745 28.578 12.7142C28.5791 14.6499 27.8509 16.515 26.5385 17.9378C27.8504 19.361 28.5786 21.2258 28.578 23.1614C28.5696 25.2255 27.7428 27.202 26.2789 28.6572C24.815 30.1124 22.8336 30.9274 20.7695 30.9235Z" fill="#47454B"/>
        </svg>

        <template v-for="(path,index) in paths">
            <span class="sep" v-if="index!=0"></span>
            <div class="path" @click="router.push({name:path.route,params:path.params,})" :class="{body_bold:paths.length==index+1}">{{path.name}}</div>
        </template>
    </div>
</template>

<script>
    import {useRouter} from 'vue-router'
    import {useStore} from 'vuex'
    import {SIDEBAR_MODULE, SIDEBAR_SET_SLIDEIN} from "../../store/types/types";
    export default {
        name: "BreadCrumb",
        props:{
            paths:{
                type: Array,
                required: true
            },
        },
        setup(){
            const store=useStore();
            const slideinMenu=()=>{
                store.commit(`${SIDEBAR_MODULE}${SIDEBAR_SET_SLIDEIN}`);
            }
          const router=useRouter();

            return{
                slideinMenu,
                router
            }
        }
    }
</script>

<style scoped>
.breadcrumb{
    box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.25);
    min-height: 82px;
    background: #FFF;
    display: flex;
    align-items: center;
    padding: 21px 0 21px 18px;
    margin-left: -70px;
}
    .path{
        cursor: pointer;
        margin-right: 15px;
        text-align: center;

        border-radius: 10px;
    }
    .path:hover{
        text-decoration: underline;
    }
    .sep{
        margin-right: 36px;
        position: relative;
        width: 6.67px;
        height: 13.33px;
        transform: scale(1.2);
    }
    .sep:after{
        content: " ";
        height: 2px;
        display: block;
        width: 8px;
        background: #868686;
        border-radius: 10px;
        transform: rotate(-50deg);
        right: -1px;
        position: absolute;
        top:9px;
    }

.sep:before{
    content: " ";
    height: 2px;
    display: block;
    width: 8px;
    background: #868686;
    border-radius: 10px;
    transform: rotate(50deg);
    right: -1px;
    position: absolute;
    top:4px;
}
    .path:last-child{
        background: rgba(217, 237, 210, 0.2);
        padding: 9px 20px;
    }
    svg.logo{
        z-index: 4;
        cursor: pointer;
    }
</style>