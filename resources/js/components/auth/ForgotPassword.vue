<template>
    <div class="container-fluid login-form p-0 pt-5">
        <div class="row justify-content-center mt-5">
            <h1 class="col h1 p-0 tile_h1">Forgot your password?</h1>
        </div>
        <div class="row justify-content-center mt-5">
            <div class="col p-0">

                <form>
                    <div class="mb-5 ">
                        <label for="exampleInputEmail1" class="form-label body_medium">Email</label>
                        <input type="email" class="form-control body_regular" id="exampleInputEmail1" aria-describedby="emailHelp" v-model="email" placeholder="Enter your email">

                    </div>

                    <button type="button" @click="authenticate" class="btn btn-dark mt-5 btn-login body_medium">Send Recovery Link</button>
                </form>

            </div>
        </div>
    </div>




    <wave-loader :show_loader="show_loader" msg="Sending recovery link, please wait..."></wave-loader>

</template>

<script>
    import WaveLoader from '../WaveLoader';
    import {ref} from 'vue';
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios';
    export default {
        name: "ForgotPassword",
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

            return {
                trans,
                email,
                password,
                show_loader,
                authenticate
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