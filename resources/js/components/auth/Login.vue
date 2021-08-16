<template>
    <div class="container-fluid login-form p-0 pt-5">
    <div class="row justify-content-center mt-5">
        <h1 class="col h1 p-0">Welcome</h1>
    </div>
    <div class="row justify-content-center mt-5">
        <div class="col p-0">

    <form>
        <div class="mb-5 ">
            <label for="exampleInputEmail1" class="form-label">Email</label>
            <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model="email" placeholder="Enter your email">

        </div>
        <div class="mb-4 ">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input type="password" class="form-control" id="exampleInputPassword1" v-model="password" placeholder="Enter your password">
            <div class="form-text text-end"><button type="button" class="btn btn-link p-0" @click="forgotpassword">Forgot your password?</button></div>
        </div>
        <button type="button" @click="authenticate" class="btn btn-dark mt-5 btn-login">Login</button>
    </form>

        </div>
    </div>
    </div>






</template>

<script>

    import {ref} from 'vue';
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios';
    import {useStore} from 'vuex';
    import {DISPLAY_LOADER,HIDE_LOADER,LOADER_MODULE} from '../../store/types/types'
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

            function authenticate(){
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[true,'Authenticating, please wait...']);
                axios.post('/authenticate', {
                    email:email.value,
                    password:password.value
                })
                    .then(function (response) {
                       if(response.data.user!=null) {
                           window.sessionStorage.setItem('name',response.data.user.name);
                           window.sessionStorage.setItem('auth', window.btoa(email.value))
                           /*router.push({
                               name:'LandingPage',
                               query: {
                                   ...route.query,
                               },
                           })*/
                           window.location='/'
                       }
                    })
                    .catch(function (error) {
                        console.log(error);
                    }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
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