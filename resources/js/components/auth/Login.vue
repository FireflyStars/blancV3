<template>
    <div class="row justify-content-center mt-5">
        <h1 class="col-4 h1">Blanc Detailing</h1>
    </div>
    <div class="row justify-content-center mt-5">
        <div class="col-4">

    <form>
        <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Email address</label>
            <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" v-model="email">
            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
        </div>
        <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input type="password" class="form-control" id="exampleInputPassword1" v-model="password">
        </div>
        <button type="button" @click="authenticate" class="btn btn-primary">Submit</button>
    </form>

        </div>
    </div>
        <wave-loader v-if="show_loader"></wave-loader>
</template>

<script>
    import WaveLoader from '../WaveLoader';
    import {ref} from 'vue';

    import axios from 'axios';
    export default {
        name: "Login",
        components:{WaveLoader},
        setup(){
            const email= ref('');
            const password= ref('');
            const show_loader= ref(false);

            function authenticate(){
                console.log('AUTH',email.value,password.value);
                show_loader.value=true;

                axios.post('/authenticate', {
                    email:email.value,
                    password:password.value
                })
                    .then(function (response) {
                        console.log(response);
                    })
                    .catch(function (error) {
                        console.log(error);
                    }).finally(()=>{
                        show_loader.value=false;
                });
            }

            return {
                email,
                password,
                show_loader,
                authenticate
            }
        }
    }
</script>

<style scoped>

</style>