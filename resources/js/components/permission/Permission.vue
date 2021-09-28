<template>
    <transition
            enter-active-class="animate__animated animate__fadeIn"
    >
        <div class="container-fluid h-100 bg-color" v-if="showcontainer">
            <main-header></main-header>
            <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax">
            <side-bar></side-bar>
                <div class="col main-view p-5">
                    <h2>Permissions</h2>
                    <!--{{coord}}
                    {{coordItem}}
                    {{itemPos}}-->
                    <tab-pane :tabs="{profile:'User profile',authorise:'Profile permissions'}" current="profile">
                        <template v-slot:profile>
                            <table class="table" >
                                <thead>
                                <tr>
                                    <th></th><th class="body_small_medium" v-for="profile in PERMISSIONS.profiles ">{{profile.name}}</th>
                                </tr>
                                </thead>
                                <tbody>

                                    <template v-for="user in PERMISSIONS.users">
                                        <tr  class="user-line"  >

                                            <td >{{user.email}}</td>
                                            <td v-for="profile in PERMISSIONS.profiles ">
                                                <check-box :id="`u_${user.id}_${profile.id}`" :name="`u_${user.id}_${profile.id}`" :checked_checkbox="isCheckedProfile(user.id,profile.id)" @checkbox-clicked="chkbxprofileclicked"></check-box>

                                            </td>
                                        </tr>

                                    </template>

                                </tbody>
                            </table>
                        </template>

                        <template v-slot:authorise>

                    <table class="table" >
                        <thead>
                        <tr>
                            <!--<th></th>--> <th></th><th class="body_small_medium" v-for="profile in PERMISSIONS.profiles ">{{profile.name}}</th>
                        </tr>
                        </thead>
                        <tbody @mousemove="updateCoordinates">
                        <template v-for="authorizationgroup in PERMISSIONS.authorization_groups">
                            <tr class="authorization-group body_medium"><td :colspan="PERMISSIONS.profiles.length+2">{{authorizationgroup.name}}</td></tr>
                            <template v-for="authorization in PERMISSIONS.authorizations">
                                    <tr v-if="authorization.authorization_group_id==authorizationgroup.id" class="grabable " :class="{grabbed:isgrabbed(authorization.id),up:direction=='up',down:direction=='down'}" :style="{top:`${itemPos.top}px`,left:`${itemPos.left}px`}"  >
                                        <!--<td @mousedown="updateCoordinatesItem($event,authorization.id)" @mouseup="releaseCoordinatesItem(authorization.id)"  >#</td>-->
                                        <td class="desc">{{authorization.description}}</td>
                                        <td v-for="profile in PERMISSIONS.profiles ">
                                            <check-box :id="`perm_${authorization.id}_${profile.id}`" :name="`perm_${authorization.id}_${profile.id}`" :checked_checkbox="isChecked(authorization.id,profile.id)" @checkbox-clicked="chkbxcliked"></check-box>

                                        </td>
                                    </tr>
                                   <!-- <tr  v-if="authorization.authorization_group_id==authorizationgroup.id" class="receiver" :class="{receiving:isreceiver(authorization.id)&&itemPos.left!=null}" @mouseover="showReceiver(authorization.id)" @mouseoout="hideReceiver()"><td :colspan="PERMISSIONS.profiles.length+2"></td></tr>
                                <tr  v-if="authorization.authorization_group_id==authorizationgroup.id" class="receiver-s" ><td :colspan="PERMISSIONS.profiles.length+2"></td></tr>-->
                            </template>
                        </template>
                        </tbody>
                    </table>
                        </template>
                    </tab-pane>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import {ref,onMounted,nextTick,computed,onUpdated} from 'vue';
    import MainHeader from '../layout/MainHeader';
    import SideBar from '../layout/SideBar'
    import TabPane from '../miscellaneous/TabPane'
    import SelectOptions from '../miscellaneous/SelectOptions'

    import {useStore} from 'vuex';
    import {
        PERMISSION_CHECKED,
        PERMISSION_GET_LIST,
        PERMISSION_LOAD_LIST,
        PERMISSION_MODULE, PERMISSION_USER_PROFILE_CHECKED
    } from "../../store/types/types";
    import CheckBox from '../miscellaneous/CheckBox'
    export default {
        name: "Permission",
        components: { SideBar, MainHeader,CheckBox,TabPane},
        setup(props,context){
            const showcontainer=ref(false);
            const store=useStore();
            const direction=ref('');
            const coord=ref({
                x:0,
                y:0
            })
            const grabbedId=ref('');
            const receiverId=ref('');
            const coordItem=ref({
                x:null,
                y:null
            })
            let initialY=null;
            const itemPos=ref({
                top:null,
                left:null
            })

            onMounted(()=>{
                console.log('mount');
                nextTick(()=>{
                    showcontainer.value=true;

                });

            });

            store.dispatch(`${PERMISSION_MODULE}${PERMISSION_LOAD_LIST}`);

            const PERMISSIONS=computed(()=>{
                return store.getters[`${PERMISSION_MODULE}${PERMISSION_GET_LIST}`];
            });
            const updateCoordinates=(event)=>{
                // pass event object, bound to mouse move with updat
                coord.value.x = event.clientX;
                coord.value.y = event.clientY;
                if(grabbedId.value!=''){


                    if(initialY>coord.value.y)
                        direction.value='up';
                    if(initialY<coord.value.y)
                        direction.value='down';
                    initialY=coord.value.y;

                    itemPos.value.top=coord.value.y-coordItem.value.y;
                    itemPos.value.left=coord.value.x-coordItem.value.x;

                    console.log( window.innerHeight,(0.9*window.innerHeight),coord.value.y);

                    if(coord.value.y>(0.9*window.innerHeight))
                        window.scrollTo(0,coord.value.y);
                }
            }
            const updateCoordinatesItem=(event,id)=>{
                coordItem.value.x = event.clientX;
                coordItem.value.y = event.clientY;
                grabbedId.value=id;

            }
            const releaseCoordinatesItem=(id)=>{
                coordItem.value.x = null;
                coordItem.value.y = null;
                itemPos.value.top=null;
                itemPos.value.left=null;
                grabbedId.value='';
                initialY=null;
            }
            const isgrabbed=(id)=>{
               return grabbedId.value==id;
            }
            const showReceiver=(id)=>{
                receiverId.value=id;
            }
            const isreceiver=(id)=>{
                return receiverId.value==id;
            }
            const hideReceiver=()=>{
                receiverId.value='';
            }
            const chkbxprofileclicked= (value,id,name)=>{
                const userprofie=name.split('_');

                store.dispatch(`${PERMISSION_MODULE}${PERMISSION_USER_PROFILE_CHECKED}`,{user_id:parseInt(userprofie[1]),profile_id:parseInt(userprofie[2])});
            }
            const chkbxcliked=(value,id,name)=>{
                const perm=name.split('_');
                store.dispatch(`${PERMISSION_MODULE}${PERMISSION_CHECKED}`,{authorization_id:parseInt(perm[1]),profile_id:parseInt(perm[2]),checked:value});
            }
            const isChecked=(authorization_id,profile_id)=>{

                const profile_authorization=PERMISSIONS.value.profile_authorizations.filter(item=>item.authorization_id===authorization_id&&item.profile_id===profile_id);
                if(profile_authorization.length>0){
                    if(profile_authorization[0].allow==1)
                        return true;
                }
                return false;
            }
            const isCheckedProfile=(user_id,profile_id)=>{
                const users=PERMISSIONS.value.user_profiles.filter(item=>item.user_id===user_id&&item.profile_id===profile_id);
                if(users.length>0){
                        return true;
                }
                return false;
            }
            return {

                showcontainer,
                PERMISSIONS,
                updateCoordinates,
                updateCoordinatesItem,
                releaseCoordinatesItem,
                coord,
                coordItem,
                isgrabbed,
                direction,
                itemPos,
                showReceiver,
                isreceiver,
                hideReceiver,
                chkbxcliked,
                isChecked,
                isCheckedProfile,
                chkbxprofileclicked


            }
        }
    }
</script>

<style scoped>
.main-view {
    transition: all 1s ease-in-out;
}
    .hmax{
        height: calc(100% - var(--mainlogoheight));
        padding-top:var(--mainlogoheight) ;
    }

    .grabbed{
        background-color: white;
        transform: rotate(4deg);
        cursor: grabbing;
        position: relative;

        box-shadow: 0 12px 24px -6px #091e4240,0 0 1px 0 #091e4214;
    }
    .grabbed.up{
        transform: rotate(4deg);
    }
    .grabbed.down{
        transform: rotate(-4deg);
    }
    .grabable{
        transition: transform  0.2s ease-in-out;
        transform-origin:  left center;
        z-index: 1;
    }
    .receiver{
        border: transparent;
        position: relative;
        z-index: 2;
    }
    .receiver td{
        padding: 1px 0 0 0;
    }
    .receiver.receiving{

    }
    .receiver-s{
        background-color: transparent;
        border: transparent;
    }
    .receiver-s td{
        padding: 1px 0 0 0;
    }
    .receiver.receiving + .receiver-s{
        background-color: #43a71e;
        -webkit-box-shadow:0px 0px 7px 2px rgba(67,167,30,0.9);
        -moz-box-shadow: 0px 0px 7px 2px rgba(67,167,30,0.9);
        box-shadow: 0px 0px 7px 2px rgba(67,167,30,0.9);
    }
    table td{
        vertical-align: middle;

    }
    .desc{
        white-space: nowrap;
    }
    table thead th{
        border:none;
        color: #868686;
        text-transform: uppercase;
    }
    table tbody{
        position: relative;
        top:3px;
        background: #FFFFFF;
    }
    table thead, table tr.grabable, table tr.user-line{
        background: #FFFFFF;
        box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.25);
        align-items: center;
    }

    table tr.grabable,table tr.user-line{
        transition:background-color 300ms ease-out;
    }
    table tr.grabable:hover,table tr.user-line:hover{
        background-color: #F8F8F8;
    }
</style>