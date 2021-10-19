<template>
    <div >
      <div class="odv container ">
       <!-- header -->
          <div v-if="!scan_barcode_bag && !scan_barcode_item" class="Scan-item">
                <svg  width="70" height="70">
                    <image xlink:href="/images/barcode.svg"  />
                </svg>
                <span class ="close" @click="close"></span>
          </div>  
       <!-- header bag -->
          <div v-if="scan_barcode_bag" class="Scan-item-store" :class="Bag.status.replace(' ','').toLowerCase()" >
                 <svg  width="40" height="40">
                    <image xlink:href="/images/barcode.svg" width="40" height="40" />
                </svg>
                <span class ="close" @click="close"></span>
                
                <div class='order_store'>
                  <span class="subtitle">Bag n°{{Bag.bag_id}}</span>
                   <div class='section_order' >
                        <span  class="body_small_bold">Linked to &nbsp;</span>
                        <a href="" class="body_small_bold">order n°{{Bag.infoOrder_id}}</a>
                        <tag :name="Bag.status.toLowerCase()"></tag>
                   </div>
                </div>
          </div>  
         <!-- End header bag -->

         <!-- header Item -->   
            <div v-if="scan_barcode_item" class="Scan-item-store" :class="Item.Status.replace(' ','').toLowerCase()">
                 <svg  width="40" height="40">
                    <image xlink:href="/images/barcode.svg" width="40" height="40" />
                </svg>
                <span class ="close" @click="close"></span>
                <div class='order_store'>
                  <span class="subtitle">Item n°{{Item.id_items}}</span>
                   <div class='section_order' >
                        <a v-if="Item.order_id" href="" class="body_small_bold">order n°{{Item.order_id}}</a>
                        <tag :name="Item.Status.toLowerCase()"></tag>
                   </div>
                </div>
          </div>
        <!-- End header Item -->
        <!-- end header -->

        <!-- body -->


          <div v-if="!scan_barcode_bag && !scan_barcode_item " class="Scan-message">
                <span v-if="!scan_barcode_empty" class="body_small_bold">Scan item or bag barcode to <br> see details</span>
                <div class ="input_barcode">
                <input type="text"  placeholder="Type barcode..." v-model="search" @keyup.prevent="submit"/> 
                </div>
                <span v-if="scan_barcode_empty" style="height:100%" class="body_bold">This bag isn’t linked to any <br> order or customer</span>
                <div v-if="!scan_barcode_empty" class="Scan-actions">
                    <a href="" class="link body_regular">Link to existing order</a>  
                    <button  class="button" @click="CreateOrder">Create order</button>  
               </div>  
          </div> 
         <!-- body bag -->
          <div v-if="scan_barcode_bag" class="Details_order_store">

             <div  class="col Section1">
                   <div class="info_customer col-6">
                       <span class="body_medium valign-middle">Customer details</span>
                       <div style="display:flex;">
                       <span class="body_regular">{{Bag.Name.replace(',','').toLowerCase()}}</span>
                        <tag style="left: 183px;"  v-if="Bag.Vip=='1'" :name="'VIP'" ></tag>
                       </div>
                       <span class="body_regular email">{{Bag.EmailAddress.toLowerCase()}}</span><br>
                 
                        <div v-for="phone in Bag.Phone.slice(0,1)" :key="phone">
                        <span class ="body_regular">+{{phone.replace('|',' ')}}</span>
                        </div>

                        <div v-if="Bag.TotalItems > 0" class="col" style="padding-top:17px">
                            <span class="body_medium valign-middle">Items linked</span>
                            <div>
                                <span v-if="Bag.TotalItems > 2" class="body_regular">{{Bag.TotalItems}} items</span>
                                <span v-if="Bag.TotalItems < 2" class="body_regular">{{Bag.TotalItems}} item</span>
                                <span class="body_regular"> @ {{Bag.Name.replace(',','').toLowerCase()}}</span>
                            </div> 
                        </div> 

                   </div>

                   <div  class="col-6">
                       
                        <div v-if="Bag.PromisedDate" class="col-9" style="display:flex;">
                            <span class="body_medium valign-middle">Promised date</span>
                            <svg width="32" height="32" class="calendaricon">
                                <image xlink:href="/images/calendar_icon.svg"  width="32" height="32"/>
                            </svg>
                            <div>
                            <span class="body_regular">{{formatDate(Bag.PromisedDate,'DAYL DD/MM').toLowerCase()}}</span><br>
                            <span class="body_regular">12-20pm</span>
                            </div>
                        </div>
                        <div v-if="Bag.GarmentInstruction" class="col" >
                            
                            <div >
                                <span class="body_regular">{{Bag.GarmentInstruction.DonateBags.replace('Bag','')}} : DonateBags </span><br>
                                 <span class="body_regular">{{Bag.GarmentInstruction.Wash[0].Instructions[0].replace('Bag','')}} : Wash & fold </span>
                            </div> 
                        </div> 
                   </div>
                    <div class="col">
                    <button class="button_Link" @click="LinkToOrder">Link to order</button>  
                    </div> 
             </div> 
              <div v-if="!Bag.PickupID && !Bag.CustomerID" class="col Section2">
                <span class="body_bold">This bag isn’t linked to any <br> order or customer</span>    
              </div>

          </div> 
        <!--end body bag -->
        <!-- body Item -->
          <div v-if="scan_barcode_item" class="Scan_Item">

             <div class="col Section1">
                   <div class="col-6">
                    
                            <span class="body_medium valign-middle">Customer details</span>

                            <div class="body_regular">{{Item.Name.replace(',','').toLowerCase()}}</div>
                            <div class="body_regular email">{{Item.EmailAddress.toLowerCase()}}</div>
                            <div v-for="phone in Item.Phone.slice(0,1)" :key="phone">
                             <span class ="body_regular">+{{phone.replace('|',' ')}}</span>
                            </div>
                            <div>
                            <tag   v-if="Item.Vip=='1'" :name="'VIP'" ></tag>
                            </div>

                            <div v-if="Item.PickupID" class="garment_instruction">
                                <span class="body_medium valign-middle">Garment instructions</span>
                                <div>
                                    <p  type="texte" >{{Item.GarmentInstruction}}  
                                    </p>    
                                </div> 
                            </div> 
                    </div>

                    <div  class="col-6">
                       <span class="body_medium valign-middle">Description</span>
                        <div v-if="Item.typeitem" class="col">
                            <div class="row">
                                    <div class="col-6">
                                      <span class= "body_small">Item type</span>
                                      <div v-if="Item.typeitem" class="body_small_medium">{{Item.typeitem}}</div>
                                      <div v-else>
                                        <div class="body_small">--</div>
                                      </div>
                                    </div>
                                    <div class="col-6">
                                      <span class= "body_small">Category</span>
                                      <div v-if="Item.DepartmentName" class="body_small_medium">{{Item.DepartmentName}}</div>
                                      <div v-else>
                                        <div class="body_small">--</div>
                                      </div>
                                    </div>
                            </div>
                            <div class="row">
                                    <div class="col-6">
                                      <span class= "body_small">Brand</span>
                                      <div v-if="Item.brand" class="body_small_medium">{{Item.brand}}</div>
                                      <div v-else>
                                        <div class="body_small">--</div>
                                      </div>
                                    </div>
                                    <div class="col-6">
                                      <span class= "body_small">Fabric</span>
                                      <div v-if="Item.Fabrics" class="body_small_medium">{{Item.Fabrics}}</div>
                                       <div v-else>
                                        <div class="body_small">--</div>
                                      </div>
                                    </div>
                            </div>
                            <div class="row">
                                    <div class="col-6">
                                      <span>Color</span>
                                      <color-tag   :colors="Item.Colors.toLowerCase()"></color-tag>
                                    </div>
                                    <div class="col-6">
                                      <span class= "body_small">Size</span>
                                      <div v-if="Item.Size" class="body_small_medium">{{Item.Size}}</div>
                                      <div v-else>
                                        <div class="body_small">--</div>
                                      </div>
                                     </div>
                            </div>
                            <div class="row">
                                    <div class="col-6">
                                      <span class= "body_small">Pattern</span>
                                      <div>
                                      <svg width="18" height="18" >
                                         <image xlink:href="/images/pattern.svg"  width="18" height="18"/>
                                      </svg>
                                      </div>
                                    </div>
                                   
                            </div>
                        </div>

                        <div  v-else class="col">
                            <button class= "no_description">No description</button>
                        </div>
                    
                   </div>
             </div> 

             <div class="col Section3">
               <button class="button_Link" @click="OpenItem">Open item</button> 
             </div> 

          </div> 
          <!-- end body Item -->
         <!--end body -->
           
      </div>
    </div>
</template>
<script>
import {ref,nextTick,computed , watch} from 'vue';
import Tag from  '../miscellaneous/Tag';
import {formatDate} from "../helpers/helpers";
import ColorTag from "../miscellaneous/ColorTag";
import {useRoute,useRouter} from 'vue-router';
 import {useStore} from 'vuex';
import {
  SCAN_MODULE,    
  SCAN_ITEM,
  SCAN_GET_ITEMS,
  SCAN_GET_BAGS
    } from "../../store/types/types";

export default ({
    name: "OrderBarcode",
    components:{Tag,ColorTag},
    setup(props,context) {
        const store =useStore();
        const search =ref('');
        const timeout =ref('');
        const scan_barcode_item= ref(false);
        const scan_barcode_bag= ref(false);
        const scan_barcode_empty= ref(false);

     
     function submit(e) { 
               clearTimeout(timeout.value);
               timeout.value = setTimeout(function(){
                     
                   nextTick(() => {
                     store.dispatch(`${SCAN_MODULE}${SCAN_ITEM}`,{info_tracking:e.target.value}).then((response)=>{
                     
                           if(response.data.item != undefined){
                               scan_barcode_item.value = true
                                scan_barcode_bag.value = false
                            }
                            if(response.data.bag != undefined){
                                scan_barcode_bag.value = true
                                scan_barcode_item.value = false
                            }
                     }).catch((error)=>{
                       
                    });
                });
               }  
              , 500)
            };
   

    
     function LinkToOrder(){
          scan_barcode_item.value = true
     }
      function OpenItem(){
          scan_barcode_item.value = false
          scan_barcode_empty.value = true
     }
     function close(){
         context.emit('scan_Barcode', false)
     }

    

            const Item =computed(()=>{
               return store.getters[`${SCAN_MODULE}${SCAN_GET_ITEMS}`];

            });

            const Bag =computed(()=>{
               return store.getters[`${SCAN_MODULE}${SCAN_GET_BAGS}`];

            });

      return{
          search,
          submit,
          LinkToOrder,
          scan_barcode_item,
          scan_barcode_bag,
          OpenItem,
          scan_barcode_empty,
          close,
          Item,
          Bag,
          formatDate,
          
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
        height: 500px;
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
        padding: 25px 25px 65px 25px;
        height: 160px;
        background: rgb(238 238 238 / 40%);
        border-radius: 6px;
        box-sizing: inherit;
    }
    .Scan-item-store{
        position: relative;
        left: unset;
        top: unset;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 24.5px 25px 17px 25px;
        height: 160px;
        background: rgb(238 238 238 / 40%);
        border-radius: 6px;
        box-sizing: inherit;
        flex-direction: column;
    }
    .order_store{
        width: 100%;
    }
    .section_order{
        display: flex;
        margin: 0 136px;
        justify-content: center;
    }
    .section_order a{
        padding-right:20px ;
    }
    .Scan-message{
        display: flex;
        width: 660px;
        padding: 0 25px 25px 25px;
        height: 340px;
        flex-direction: column;
        box-sizing: inherit;
    }
    .Scan-message span{
        display: flex;
        justify-content: center;
        text-align: center;
        align-items: center;
        height: 240px;
        box-sizing: inherit;
    }
    .Scan-actions{
        display: flex;
        justify-content: space-between;
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
    .email{
        width : 100%;
        overflow:hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .link{
        line-height: 19px;
        display: flex;
        align-items: center;
        text-align: center;
        justify-content: center;
        width: 240px;
        text-decoration-line: underline;
        color: #C3C3C3;
    }
    .order_store{
        display: block;
        margin-top: 16px;
        text-align: center;
    }
    .Details_order_store{
        padding: 24px 80px 24px 80px;
        height: 340px;
        display: flex;
    }
    .info_customer{
        width: 320px;
    }
    .Scan_Item{
        padding: 24px 40px 24px 80px;
        height: 385px;
    }
    .Section1{
        display: flex;
    }
     .Section2{
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
    .button_Link{
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        position: absolute;
        width: 240px;
        height: 56px;
        left: calc(50% - 240px/2);
        top: calc(50% - 56px/2);
        background: #47454B;
        border-radius: 4px;  
        margin-top: 199px;
    }
    .calendaricon{
        margin-right: 6px;
    }

    .garment_instruction{
        padding-top: 25px;
    }
    .garment_instruction p{
        padding: 16px;
        background: #F8F8F8;
        border: 1px solid #E0E0E0;
        box-sizing: border-box;
        border-radius: 5px;
        margin: 6px 0px;
        height: 69px;
        width: 236px;
        overflow-x: hidden;
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
    .no_description{
        width: 158px;
        height: 38px;
        margin-top: 11px;
        background: #E0E0E0;
        border-radius: 30px; 
    }
    .inprocess,.partpending,.partonhold{
    background: rgba(241, 210, 164, 0.5);
    }
    .fulfilled,.ready,.readyinstore{
    background: rgba(66, 167, 30, 0.2);
    }
    .assembling,.offloaded{
    background: rgba(212, 221, 247, 0.5);
    }
    .onvan, .deliveredtostore{
    background: rgba(234, 214, 247, 0.5);
    }   
    .instorage,.donatedtocharity{
    background: #FFFFFF;
    }
    ::-webkit-scrollbar {
    height: 8px;
    overflow: visible;
    width: 8px;
}
</style>
