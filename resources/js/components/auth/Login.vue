<template>
    <div class="container-fluid login-form p-0 pt-5">
    <div class="row justify-content-center mt-5">
        <h1 class="col h1 p-0 tile_h1">Welcome</h1>
    </div>
    <div class="row justify-content-center mt-5">
        <div class="col p-0">

    <form>
        <div class="mb-5 ">
            <label for="exampleInputEmail1" class="form-label body_medium">Email</label>
            <input type="email" class="form-control body_small" id="exampleInputEmail1" aria-describedby="emailHelp" v-model="email" placeholder="Enter your email">

        </div>
        <div class="mb-4 ">
            <label for="exampleInputPassword1" class="form-label body_medium">Password</label>
            <input type="password" class="form-control body_small" id="exampleInputPassword1" v-model="password" placeholder="Enter your password">
            <div class="form-text text-end"><button type="button" class="btn btn-link p-0 body_regular" @click="forgotpassword">Forgot your password?</button></div>
        </div>
        <button type="button" @click="authenticate" class="btn btn-dark mt-5 btn-login body_medium">Login</button>
    </form>

        </div>
    </div>
    </div>






</template>

<script>

import {computed, ref} from 'vue';
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios';
    import {useStore} from 'vuex';
import {
    DISPLAY_LOADER,
    HIDE_LOADER,
    LOADER_MODULE, TOASTER_GET_ALL,
    TOASTER_MESSAGE,
    TOASTER_MODULE,
    TOASTER_REMOVE_TOAST
} from '../../store/types/types'


    export default {
        name: "Login",
        components:{},
        setup(){
            const email= ref('');
            const password= ref('');
            const trans= ref(false);

            const router = useRouter();
            const route = useRoute();
            const store = useStore();

            function authenticate() {
                let err = false;
                let err_txt = [];
                let email_regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,8})+$/;
                let email_addr = email.value.replace(/ /g,'');

                if(email_addr==''){
                    err = true;
                    err_txt.push("Please enter email address");
                }else if(email_addr !='' && !email_regex.test(email_addr)){
                    err = true;
                    err_txt.push("Email address format is not valid");
                }
                if(password.value.replace(/ /g,'')==''){
                    err = true;
                    err_txt.push("Please enter password");
                }

                if(err){
                    let i = 0;
                    for(i=0; i<err_txt.length; i++) {
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: err_txt[i],
                            ttl: 5,
                        });
                    }
                }else{
                    store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Authenticating, please wait...']);


                    axios.post('/authenticate', {
                        email: email.value,
                        password: password.value
                    })
                        .then(function (response) {
                            if (response.data.user != null) {
                                window.sessionStorage.setItem('name', response.data.user.name);
                                window.sessionStorage.setItem('auth', window.btoa(email.value));
                                window.sessionStorage.setItem('roles', window.btoa(JSON.stringify(response.data.roles)));
                                window.sessionStorage.setItem('profile_permissions', window.btoa(JSON.stringify(response.data.profile_permissions)));
                                console.log(response.data);
                                /*router.push({
                                   name:'LandingPage',
                                   query: {
                                       ...route.query,
                                   },
                               })*/
                                // window.location = '/newcustomer'
                                window.location = '/'
                            } else {
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    type: 'danger',
                                    message: "User not found",
                                    ttl: 5,
                                });
                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                        }).finally(() => {
                        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    });
                }
            }

            function forgotpassword() {
                router.push({
                    name:'ForgotPassword'
                })
            }

            return {
                trans,
                email,
                password,
                authenticate,
                forgotpassword
            }
        }
    }
</script>

<style scoped>
    .login-form{
        width: 375px;
    }
    .btn-login{
        width: 100%;
    }
</style>
