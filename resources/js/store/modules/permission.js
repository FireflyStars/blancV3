import {
    DISPLAY_LOADER,
    HIDE_LOADER,
    LOADER_MODULE,
    PERMISSION_CHECKED,
    PERMISSION_GET_LIST,
    PERMISSION_LOAD_LIST,
    PERMISSION_SET_LIST,
    PERMISSION_UPDATE,
    PERMISSION_USER_PROFILE_CHECKED, PERMISSION_USER_PROFILE_UPDATE,
    TOASTER_MESSAGE,
    TOASTER_MODULE
} from "../types/types";
import axios from "axios";


export const permission= {
    namespaced:true,
    state: {
        permissions: [],
    },
    mutations: {
        [PERMISSION_SET_LIST]: (state,payload) => state.permissions = payload,
        [PERMISSION_UPDATE]:(state,payload)=>{

              const profile_authorization=  state.permissions.profile_authorizations.filter((item,index,array)=>{
                    if(item.authorization_id===payload.authorization_id&&item.profile_id===payload.profile_id) {
                        array[index].allow=payload.checked?1:0;
                        return true;
                    }
                    return false;
                });
              if(profile_authorization.length===0){
                  state.permissions.profile_authorizations.push(
                      {
                          allow:payload.checked?1:0,
                          authorization_id:payload.authorization_id,
                          profile_id:payload.profile_id
                      }
                  )
              }
        },
        [PERMISSION_USER_PROFILE_UPDATE]:(state,payload)=>{
            const user_profiles=  state.permissions.user_profiles.filter((item,index,array)=>{
                if(item.user_id===payload.user_id&&item.profile_id===payload.profile_id) {
                        array.splice(index,1);
                    return true;
                }
                return false;
            });
            if(user_profiles.length===0){
                state.permissions.user_profiles.push(
                    {
                        user_id:payload.user_id,
                        profile_id:payload.profile_id
                    }
                )
            }
        },
    },
    actions: {
        [PERMISSION_LOAD_LIST]:async({commit,state,dispatch},payload)=>{

            dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[true,'Loading permissions...'],{ root: true });




            return axios.get('/getpermissions', {

            })
                .then(function (response) {
                    commit(PERMISSION_SET_LIST, response.data);

                })
                .catch(function (error) {
                    if(typeof error.response !="undefined")
                        dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'},{ root: true });
                }).finally(function(){
                    dispatch(`${LOADER_MODULE}${HIDE_LOADER}`,{},{ root: true });
                });


        },

        [PERMISSION_CHECKED]:async({commit,state,dispatch},payload)=>{


            return axios.post('/setpermission', {
                authorization_id:payload.authorization_id,
                profile_id:payload.profile_id,
                allow:(payload.checked?1:0)
            })
                .then(function (response) {
                    if(response.data.ok==1) {
                        commit(PERMISSION_UPDATE, payload);
                        dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            message: `Permission updated`,
                            ttl: 3,
                            type: 'success'
                        }, {root: true});
                    }
                })
                .catch(function (error) {
                    if(typeof error.response !="undefined")
                        dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'},{ root: true });
                }).finally(function(){

                });


        },
        [PERMISSION_USER_PROFILE_CHECKED]:async({commit,state,dispatch},payload)=>{


            return axios.post('/setprofile', {
                user_id:payload.user_id,
                profile_id:payload.profile_id,
            })
                .then(function (response) {
                    if(response.data.ok==1) {
                        commit(PERMISSION_USER_PROFILE_UPDATE, payload);
                        dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            message: `User profile updated`,
                            ttl: 3,
                            type: 'success'
                        }, {root: true});
                    }
                })
                .catch(function (error) {
                    if(typeof error.response !="undefined")
                        dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:`An error has occured: ${error.response.status} ${error.response.statusText}`,ttl:5,type:'danger'},{ root: true });
                }).finally(function(){

                });


        },
    },
    getters: {
        [PERMISSION_GET_LIST]: state => state.permissions,

    }
}