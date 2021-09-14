<template>
    <div class="container-fluid login-form p-0 pt-5">
        <div class="row justify-content-center mt-5">
            <h1 class="col h1 p-0 tile_h1">Enter a new password.</h1>
        </div>
        <div class="row justify-content-center mt-5">
            <div class="col p-0">

                <form>
                    <div class="mb-4 ">
                        <label for="exampleInputPassword1" class="form-label body_medium">Password</label>
                        <input type="password" class="form-control body_regular" id="exampleInputPassword1" v-model="password" placeholder="Enter your password">

                    </div>
                    <div class="mb-4 ">
                        <label for="exampleInputPassword2" class="form-label body_medium">Confirm Password</label>
                        <input type="password" class="form-control body_regular" id="exampleInputPassword2" v-model="password" placeholder="Confirm your password">
                    </div>
                    <button type="button" @click="authenticate" class="btn btn-dark mt-5 btn-login body_medium">Save</button>
                </form>

            </div>
        </div>
    </div>




    <wave-loader :show_loader="show_loader" msg="Please wait..."></wave-loader>

</template>

<script>
    import WaveLoader from '../WaveLoader';
    import {ref} from 'vue';
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios';
    export default {
        name: "NewPassword",
        components:{WaveLoader},
        setup(){
            const email= ref('');
            const password= ref('');
            const show_loader= ref(false);
            const trans= ref(false);

            const router = useRouter();
            const route = useRoute();

            function authenticate(){
                console.log('AUTH',email.value,password.value);
                show_loader.value=true;

                axios.post('/authenticate', {
                    email:email.value,
                    password:password.value
                })
                    .then(function (response) {
                        if(response.data.user!=null) {
                            window.sessionStorage.setItem('auth', window.btoa(email.value))
                            router.push({
                                name:'LandingPage',
                                query: {
                                    ...route.query,
                                },
                            })
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    }).finally(()=>{
                    show_loader.value=false;
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
                show_loader,
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