<template>
    <div >
      <div class="odv container ">
          <div class="Scan-item">
                <svg  width="70" height="70">
                    <image xlink:href="/images/barcode.svg"  />
                </svg>
                <span class ="close" @click="close"></span>
          </div>  
          <div  class="Scan-message">
                <span class="body_small_bold"> Scan Barcode </span>
                <div class ="input_barcode">
                <input type="text"  placeholder="Type barcode..." v-model="search" @keyup.enter="submit"/> 
                </div>  
          </div> 
           
      </div>
    </div>
</template>
<script>
import {ref} from 'vue';
import {useRouter} from 'vue-router';
import { useStore } from 'vuex';
import {
        TOASTER_MODULE,
        TOASTER_MESSAGE

    } from "../../store/types/types";
export default ({
    name: "ScanBarcode",
    components:{},
    setup(props,context) {
        const search =ref('');
        const router = useRouter();
        const store = useStore();
        
  
    
        function submit(e) { 

            if(e.target.value.length == 8){

                axios.post('/get-item-barcode',{
                    Barcode: e.target.value
               }).then((res)=>{

                if(res.data.item_id != null){
                    router.push({
                        name:'ItemDetails',
                        params: {
                            item_id:res.data.item_id,
                        },
                    })
                    close();
                } else {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{ message: "Invalid Barcode",ttl:5, type:'danger'},{ root: true });
                }    
               });
            }
            else if (e.target.value.length == 9){
                console.log(e.target.value )
                axios.post('/get-order-barcode',{
                    Barcode: e.target.value
               }).then((res)=>{
                if(res.data.order_id != null){
                    router.push({
                        name:'OrderDetails',
                        params: {
                            order_id:res.data.order_id,
                        },
                    })
                  close();
                } else {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{ message: "Invalid Barcode",ttl:5, type:'danger'},{ root: true });
                }    
               });
            } else if (e.target.value.length == 14){
                axios.post('/get-order-barcode',{
                    Barcode: e.target.value.substring(5)
               }).then((res)=>{
                if(res.data.order_id != null){
                    router.push({
                        name:'OrderDetails',
                        params: {
                            order_id:res.data.order_id,
                        },
                    })
                  close();
                } else {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{ message: "Invalid Barcode",ttl:5, type:'danger'},{ root: true });
                }    
               });
            }
             else {
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{ message: "Invalid Barcode",ttl:5, type:'danger'},{ root: true });
            }
           
        };

        function close(){
            context.emit('scan_Barcode', false)
        }
     


      return{
          search,
          submit,
          close 
      }  
    },
})
</script>

<style scoped>
    .odv{
        position: absolute;
        display: flex;
        flex-direction: column;
        padding: 0;
        width: 660px;
        height: 350px;
        left: 0;
        right: 0;
        top: 290px;
        background: #FFF;
        box-shadow: 0px 8px 36px rgba(0, 0, 0, 0.16);
        border-radius: 6px;
    }
    .Scan-item{
        
        position: relative;
        left: unset;
        top: unset;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 25px 25px 25px 25px;
        height: 150px;
        background: rgb(238 238 238 / 40%);
        border-radius: 6px;
        box-sizing: inherit;
    }
    .Scan-message{
        display: flex;
        width: 660px;
        padding: 0 25px 25px 25px;
        height: 200px;
        flex-direction: column;
        box-sizing: inherit;
    }
    .Scan-message span{
        display: flex;
        justify-content: center;
        text-align: center;
        align-items: center;
        height: 100px;
        box-sizing: inherit;
    }
    .close{
        display: flex;
        width:14px;
        height:13px;
        position: absolute;
        top: 35px;
        right: 25px;
        background-image: url(/images/close.svg);
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        cursor: pointer;
    }
    button{
        display: flex;
        justify-content: center;
        align-items: center;
        width: 240px;
        height: 56px;
        left: calc(50% - 240px/2 + 146px);
        top: calc(50% - 56px/2);
        background: #E0E0E0;
        border-radius: 4px;
        border: transparent;
        font-family: Gotham Rounded;
        font-style: normal;
        font-weight: normal;
        font-size: 16px;
        line-height: 140%;
        color: #868686;
    }
    
    
    
    
    
    .Scan_Item{
        padding: 24px 40px 24px 80px;
        height: 385px;
    }
    
    .calendaricon{
        margin-right: 6px;
    }

    input{
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        padding: 16px;
        background: #F8F8F8;
        border: 1px solid #E0E0E0;
        box-sizing: border-box;
        border-radius: 5px;
        margin: 6px 0px;
    }
    .input_barcode{
        display: flex;
        justify-content: center;
        text-align: center;
        align-items: center;
    }
    
    
    ::-webkit-scrollbar {
    height: 8px;
    overflow: visible;
    width: 8px;
}
</style>
