<template>
    <div class="form-group mb-0" :class="{ 'mt-3': index > 0}" v-for="(email, index) in emails" :key="index">
        <label :for="'multiple_email_'+(index+1)">Email address {{ index+1 }}</label>
        <div class="position-relative">
            <input type="email" v-model="emails[index]" :id="'multiple_email_'+(index+1)" placeholder="Email" class="form-control">
            <div class="add-email-btn position-absolute" @click="addEmail" v-if="index == 0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="12" fill="black"/>
                    <path d="M11.0897 14.472C11.0897 14.984 11.4897 15.384 12.0017 15.384C12.4977 15.384 12.9137 14.984 12.9137 14.472V12.248H15.1857C15.6657 12.248 16.0657 11.864 16.0657 11.368C16.0657 10.888 15.6657 10.488 15.1857 10.488H12.9137V8.264C12.9137 7.752 12.4977 7.352 12.0017 7.352C11.4897 7.352 11.0897 7.752 11.0897 8.264V10.488H8.81769C8.33769 10.488 7.93769 10.888 7.93769 11.368C7.93769 11.864 8.33769 12.248 8.81769 12.248H11.0897V14.472Z" fill="white"/>
                </svg>
            </div>
        </div>
    </div>
</template>
<script>
import { ref } from 'vue';
import { useStore } from 'vuex';
import {
  TOASTER_MESSAGE,
  TOASTER_MODULE
} from '../../store/types/types';

export default {
    setup(props, { emit }){
        const store = useStore();
        const emails = ref([]);
        if( props.modelValue.length != 0 ){
            emails.value = props.modelValue
        }else{
            emails.value = props.emailData
        }
        const addEmail = ()=>{
            if(emails.value.length == 10){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'You could add up to 10 emails',ttl:5,type:'danger'});
            }else{
                emails.value = [ ...emails.value, "" ];
                emit('update:modelValue', emails.value);
            }
        }
        return {
            emails,
            addEmail
        }
    },
    props: {
        modelValue: Array,
        limitCount: {
            type: Number,
            default: 10
        },
        emailData: {
            type: Array,
            default: [ "" ]
        }
    },
    
}
</script>
<style lang="scss" scoped>
.add-email-btn{
    top: 8%;
    left: 100%;
    margin-left: 15px;
    cursor: pointer;
}
</style>