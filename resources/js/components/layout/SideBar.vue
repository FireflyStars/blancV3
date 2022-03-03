<template>
    <div class="col-lg-6 col-sm-3 side-bar-wrap d-flex flex-column align-items-center" :class="{slidein:slidesidebar}">
        <div class="d-flex flex-column side-bar align-items-center position-fixed">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons" :class="{active:route_name=='Statistics'}" @click="router.push({name:'Statistics'})">
                <rect width="32" height="32" rx="8" fill="white"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M7 16C6.44772 16 6 16.4477 6 17L6 24C6 24.5523 6.44772 25 7 25H9C9.55228 25 10 24.5523 10 24V17C10 16.4477 9.55228 16 9 16H7Z" stroke="#868686" stroke-linecap="round"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M15 7C14.4477 7 14 7.44772 14 8L14 24C14 24.5523 14.4477 25 15 25H17C17.5523 25 18 24.5523 18 24V8C18 7.44772 17.5523 7 17 7H15Z" stroke="#868686" stroke-linecap="round"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M23 11C22.4477 11 22 11.4477 22 12V24C22 24.5523 22.4477 25 23 25H25C25.5523 25 26 24.5523 26 24V12C26 11.4477 25.5523 11 25 11H23Z" stroke="#868686" stroke-linecap="round"/>
            </svg>
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons" :class="{active:route_name=='LandingPage'||route_name=='OrderDetails'}" @click="router.push({name:'LandingPage'})">
                <rect width="32" height="32" rx="8" fill="#42A71E"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.71045 11H23.1138C23.731 11 24.2009 11.5537 24.1005 12.1627L23.0843 18.3254C22.9251 19.2913 22.09 20 21.111 20H11.8917C10.9127 20 10.0776 19.2913 9.9183 18.3254L8.71045 11Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 25C12.5523 25 13 24.5523 13 24C13 23.4477 12.5523 23 12 23C11.4477 23 11 23.4477 11 24C11 24.5523 11.4477 25 12 25Z" stroke="white"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M21 25C21.5523 25 22 24.5523 22 24C22 23.4477 21.5523 23 21 23C20.4477 23 20 23.4477 20 24C20 24.5523 20.4477 25 21 25Z" stroke="white"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 11H6H10Z" fill="white"/>
                <path d="M10 11H6" stroke="white" stroke-linecap="round"/>
            </svg>
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons">
                <rect width="32" height="32" rx="8" fill="white"/>
                <path d="M12 9V25" stroke="#868686" stroke-linecap="round"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M24 7V25H10C8.89543 25 8 24.1046 8 23V9C8 7.89543 8.89543 7 10 7H24Z" stroke="#868686" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 21.0001C12 19.0001 15.3333 19.3334 16.6667 18.0001C17.3333 17.3334 15.3333 17.3334 15.3333 14.0001C15.3333 11.7781 16.222 10.6667 18 10.6667C19.778 10.6667 20.6667 11.7781 20.6667 14.0001C20.6667 17.3334 18.6667 17.3334 19.3333 18.0001C20.6667 19.3334 24 19.0001 24 21.0001" stroke="#868686" stroke-linecap="round"/>
            </svg>

            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons" :class="{active:route_name == 'AssemblyHome'}" @click="router.push({name:'Assembly'})">
                <rect width="32" height="32" rx="8" fill="white"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1827 18.003C9.96548 18.003 9.88583 17.8529 10.0052 17.6673L16.7844 7.12177C16.9036 6.93637 17.0003 6.96432 17.0003 7.19442V13.5886C17.0003 13.8141 17.1828 13.997 17.3937 13.997H21.4141C21.6314 13.997 21.711 14.1471 21.5917 14.3327L14.8124 24.8782C14.6932 25.0636 14.5966 25.0357 14.5966 24.8056V18.4114C14.5966 18.1859 14.414 18.003 14.2032 18.003H10.1827Z" stroke="#868686" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="side-icons">
                <rect width="32" height="32" rx="8" fill="white"/>
                <path d="M8 21H7C6.44772 21 6 20.5523 6 20V12C6 11.4477 6.44772 11 7 11H19C19.5523 11 20 11.4477 20 12V15C20 15.5523 20.4477 16 21 16H26M19 21H12" stroke="#868686" stroke-linecap="round"/>
                <path d="M19 21H20M20 12H23.382C23.7607 12 24.107 12.214 24.2764 12.5528L26 16V20C26 20.5523 25.5523 21 25 21H24" stroke="#868686" stroke-linecap="round"/>
                <circle cx="10" cy="21" r="2" stroke="#868686"/>
                <circle cx="22" cy="21" r="2" stroke="#868686"/>
            </svg>
        </div>
        <div class="user_initials body_bold" @click="showmenu">
            {{initials}}
        </div>
        <transition name="usermenu" >
            <div class="usermenu" v-if="dispmenu" >
                <button class="btn btn-link body_medium mb-3" v-if="hasRoles(['admin'])" @click="gotoPermissions">Permissions</button>
                <button class="btn btn-outline-dark body_medium"  data-bs-toggle="tooltip" data-bs-placement="right" title="Logout user" @click="logout">Sign out</button>
            </div>
        </transition>
    </div>
</template>

<script>

    import {ref,computed,watch} from 'vue';
    import { useRouter, useRoute } from 'vue-router'
    import {hasRoles} from "../helpers/helpers";
    import axios from 'axios';
    import {useStore} from 'vuex';
    import {
        DISPLAY_LOADER,
        HIDE_LOADER,
        LOADER_MODULE,
        SIDEBAR_GET_SLIDEIN,
        SIDEBAR_MODULE
    } from "../../store/types/types";

    export default {
        name: "SideBar",
        components:{},
        setup(){
            const store=useStore();
            const uname=ref(window.sessionStorage.getItem('name'));
            const initials= ref((uname.value!=null?uname.value.substr(0,2):''));
            const router = useRouter();
            const route = useRoute();
            const dispmenu=ref(false);
            const route_name=ref(route.name);
            watch(() =>route.name, (current_val, previous_val) => {
                route_name.value=current_val;
                // emit('checkbox-clicked', current_val,props.id,props.name)
            });
            function logout(){
                showmenu();
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[true,'Logging out, please wait...']);

                axios.get('/logout', {

                })
                    .then(function (response) {
                        if(response.data.ok==1) {
                            sessionStorage.clear();
                           // router.push({
                            //    name:'Login',

                            //})
                            window.location="/";
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
            }
            function showmenu() {
                dispmenu.value=!dispmenu.value;
            }
            const slidesidebar=computed(()=>{
                return store.getters[`${SIDEBAR_MODULE}${SIDEBAR_GET_SLIDEIN}`];
            });

            function gotoPermissions(){
                router.push({
                    name:'Permissions',
                    params: {

                    },});
            }
            return {
                uname,
                initials,
                logout,
                showmenu,
                dispmenu,
                slidesidebar,
                hasRoles,
                gotoPermissions,
                route_name,
                router
            }
        }
    }
</script>

<style scoped>
    .side-bar-wrap{
        width: 72px;
        transition: left ease-in-out 0.5s;
    }

.side-bar{
    background:#FBFBFB;
    box-shadow: inset 0px 0px 6px rgba(0, 0, 0, 0.25);
    width: 72px;
    height: 100%;
    z-index: 1;
}
.side-icons:first-child{
    margin-top:114px;
}
.side-icons{
    margin-bottom: 32px;
}
    .usermenu{
        background: #FFFFFF;

        /* Drop shadow */
        box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.12);
        border-radius: 4px;
        min-width: 184px;
        position: fixed;
        left: 16px;
        bottom: 79px;
        z-index: 2;
       padding:45px 1rem 37px 1rem;
        transform-origin: left bottom;
    }
    .usermenu .btn{
        min-width: 154px;
        margin: 0 auto;
        display: block;

    }
    .usermenu-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .usermenu-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .usermenu-enter-active{
        transition: all ease 0.2s;
    }
    .usermenu-leave-from{
        opacity: 1;
        transform: scale(1);
    }
    .usermenu-leave-to{
        opacity: 0;
        transform: scale(0.6);
    }
    .usermenu-leave-active{
        transition: all ease 0.2s;
    }
</style>