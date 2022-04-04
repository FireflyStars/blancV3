<template>
    <Teleport to="body">
        <div class="import-layer d-flex justify-content-center position-absolute" v-if="showModal">
            <transition name="list" appear>
                <div class="import-modal bg-white">
                    <div class="import-modal-header text-center position-relative">
                        Invoice customer
                        <svg @click="closeModal" class="close-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.78812 5.2973C6.3976 4.90481 5.76444 4.90481 5.37392 5.2973C4.98339 5.6898 4.98339 6.32616 5.37392 6.71865L10.5883 11.9594L5.29289 17.2816C4.90237 17.6741 4.90237 18.3105 5.29289 18.703C5.68341 19.0955 6.31657 19.0955 6.7071 18.703L12.0025 13.3808L17.293 18.6979C17.6835 19.0904 18.3166 19.0904 18.7072 18.6979C19.0977 18.3054 19.0977 17.6691 18.7072 17.2766L13.4167 11.9594L18.6261 6.7237C19.0167 6.33121 19.0167 5.69485 18.6261 5.30235C18.2356 4.90986 17.6025 4.90986 17.2119 5.30235L12.0025 10.5381L6.78812 5.2973Z" fill="black"/>
                        </svg>
                    </div>
                    <div class="import-modal-body">
                        <p class="text-center">
                            Do you want to import the account’s details to populate the customer’s billing details?
                        </p>
                    </div>
                    <div class="import-modal-footer d-flex justify-content-between">
                        <button class="btn-cancel text-center" @click="closeModal">NO, leave blank</button>
                        <button class="btn-ok text-center" @click="importAccountDetail">YES, import</button>
                    </div>
                </div>
            </transition>
        </div>
    </Teleport>
</template>
<script>

import {nextTick, ref} from 'vue';
import axios from 'axios';
import {
    LOADER_MODULE,
    DISPLAY_LOADER,
    HIDE_LOADER,
} from '../../store/types/types';
import { useStore } from 'vuex';

export default {
    name: 'AccountImportModal',
    props: {
        // modelValue: Boolean
    },
    components:{
    },
    setup(props){
        const showModal = ref(false);
        const store = useStore();
        const openModal = ()=>{
            const height = document.querySelector('body').clientHeight;
            console.log(height);
            setTimeout(()=>{
                document.querySelector('.import-layer').style.height = height+'px';
            }, 1);
            showModal.value = true;
        }
        const closeModal = ()=>{
            document.querySelector('.import-layer').style.height = '100%';
            showModal.value = false;
        }
        const importAccountDetail = ()=>{
            alert('not implemented yet');
        }
        return {
            showModal,
            openModal,
            closeModal,
            importAccountDetail
        }
    }

}
</script>
<style lang="scss" scoped>
.list-enter-from{
    opacity: 0;
    transform: scale(0.6);
}
.list-enter-to{
    opacity: 1;
    transform: scale(1);
}
.list-enter-active{
    transition: all 1s ease;
}

.list-leave-from{
    opacity: 1;
    transform: scale(1);
}
.list-leave-to{
    opacity: 0;
    transform: scale(0.6);
}
.list-leave-active{
    transition: all 1s ease;
    position: absolute;
    width: 100%;
}
.list-move{
    transition:all 0.9s ease;
}
.import-layer{
    width: 100%;
    height: 100%;
    top: 0;
    z-index: 30;
    background: rgba(0, 0, 0, 0.3);
    padding-top: 10%;
    .import-modal{
        width: 660px;
        height: 450px;   
        border-radius: 6px; 
        overflow: hidden;    
        .import-modal-header{
            height: 160px;
            background: #F8F8F8;
            padding: 80px 0 50px;
            font-family: 'Gilroy';
            font-style: normal;
            font-weight: 800;
            font-size: 22px;
            line-height: 110%;            
            .close-icon{
                position: absolute;
                cursor: pointer;
                top: 20px;
                right: 23px;
            }
        }    
        .import-modal-body{
            padding: 60px 185px 60px;
            p{
                font-family: 'Gotham Rounded Medium';
                font-size: 16px;
                line-height: 140%;
                color: #151920;
            }
        }
        .import-modal-footer{
            padding: 0 65px 24px 90px;
            .btn-cancel{
                font-family: 'Gotham Rounded';
                font-size: 16px;
                line-height: 140%;                                
                width: 240px;
                height: 56px;
                padding: 18px 0;
                background: #FFFFFF;
                border: 1px solid #EB5757;
                border-radius: 4px;
                color: #EB5757;
                &:hover{
                    color: white;
                    background: #EB5757;
                }
            }
            .btn-ok{
                font-family: 'Gotham Rounded';
                font-size: 16px;
                line-height: 140%;                
                width: 240px;
                height: 56px;
                padding: 18px 0;
                background: #42A71E;
                border-radius: 4px;
                color: #FFFFFF;
                outline: none;
                border: none;
                &:hover{
                    background: white;
                    color: #42A71E;
                    border: solid 1px #42A71E;
                }
            }
        }
    }
}
</style>