<template>
  <div id="customerinvoicehistory">
    <h3 class="title">Invoice History</h3>

     <div class="container-fluid position-relative p-0">
        <table class="invoicehistorylist-table" v-if="INVOICEHISTORY_LIST.length>0" s>
            <thead>
                <tr>
                    <th class="tcol noselect body_small_medium"  v-for="(col,index) in tabledef" :key="index" :style="{width:col.width,'text-align':col.header_align}" :class="{'sortable': col.sortable,'check-box': col.type=='checkbox'}"  @click="sort(index,col.sortable)">
                      <span v-html="col.name"></span>
                        
                    </th>
                </tr>
            </thead>
            <transition-group tag="tbody"  name="list" appear>
                <tr class="trow" v-for="invoicehistory in INVOICEHISTORY_LIST" :key="invoicehistory.id" >
                    <template v-for="(col,index) in tabledef" :key="index">
                        <td class="tcol"  :style="{width:col.width}" :class="{'check-box': col.type=='checkbox',[index]:true}"  >

                            <tag v-if="col.type=='tag'" :name="invoicehistory[index]" >
                            
                            </tag>
                            <a :style="col.css" v-else-if="col.type=='pdf'" :href="`/inv-pdf?id=${invoicehistory.FactureID}`" target="_blank"><img src="/images/pdficon.svg" style="width:20px;"/></a>
                            <span v-else :style="col.css"  v-html="preprocess(col,invoicehistory[index],invoicehistory)"></span>
                        </td>
                    </template>
                </tr>
            </transition-group>
            <tfoot>
                <tr>
                    <td class="tcol" style="text-align: center" :colspan="Object.keys(tabledef).length">  <button class="btn btn-link" @click="loadMore">Show more</button></td>
                </tr>
            </tfoot>
        </table>
        <section class="nodata p-2" v-if="INVOICEHISTORY_LIST.length==0">
                <p v-if="!loader_running">No invoice histories.</p>
        </section>
     </div>

  </div>
</template>

<script>
    
    import Tag from  '../miscellaneous/Tag';
import { computed, onMounted, ref } from 'vue';
import { useStore } from 'vuex';
import {  INVOICE_HISTORY_GET_LIST, INVOICE_HISTORY_LOAD, INVOICE_HISTORY_MODULE } from '../../store/types/types';
import { formatDate, formatPrice } from '../helpers/helpers';
export default {
  name: "CustomerInvoiceHistory",
    components:{
   
      Tag
    },
      props: {
           CustomerID:{
                type:String,
                required:true,
                },
      },
  setup(props,context) {
 const store=useStore();
 const loader_running=ref(true);

           const INVOICEHISTORY_LIST=computed(()=>{ return store.getters[`${INVOICE_HISTORY_MODULE}${INVOICE_HISTORY_GET_LIST}`];});
          
 const tabledef=ref({
     
                NumFact:{
                   name:"Invoice N°",
                   width:"25%",
                   sortable:false
               },
              issuer:{
                   name:"Issuer",
                   width:"12%",
                   sortable:false
               },
                  issue_date:{
                   name:"Issue Date",
                   width:"10%",
                   sortable:false,
                   type:'date'
               },
              due_date:{
                   name:"Due Date",
                   width:"10%",
                   sortable:false,
                   type:'date'
               },

                 Paid:{
                   name:"Invoice status",
                   width:"10%",
                   sortable:false,
                    type:'tag'
               },
               
                 net:{
                   header_align:"right",
                   name:"Net",
                   width:"10%",
                   sortable:false,
                   type:'price',
                   css:'text-align:right;display:block;'
          
               },
                vat:{
                   header_align:"right",
                   name:"Vat",
                   width:"10%",
                   sortable:false,
                   type:'price',
                   css:'text-align:right;display:block;'
          
               },
                  Total:{
                   header_align:"right",   
                   name:"Total",
                   width:"10%",
                   sortable:false,
                   type:'price',
                   css:'font-weight:bold;text-align:right;display:block;'
          
               },
                  id:{
                   name:"Pdf<br/>Invoice",
                   width:"3%",
                   sortable:false,
                   type:'pdf',
                   header_align:"center",
                    css:'text-align:center;display:block;'
          
               },
           
            });

          onMounted(()=>{
            store.dispatch(`${INVOICE_HISTORY_MODULE}${INVOICE_HISTORY_LOAD}`,{CustomerID:props.CustomerID,showmore:false}).then(()=>{
              loader_running.value=false;
            });
          })

          function preprocess(def,val,invoicehistory) {
              
            
                if(typeof def.type!="undefined"&&def.type=="price"){
                    return formatPrice(val);
                }
                if(typeof def.type!="undefined"&&def.type=="date"){
                    return formatDate(val,'DD/MM/YYYY');
                }
               
                return val;
            }

            const loadMore=()=>{
               store.dispatch(`${INVOICE_HISTORY_MODULE}${INVOICE_HISTORY_LOAD}`,{CustomerID:props.CustomerID,showmore:true}).then(()=>{
             
               });
            }

    return {
      INVOICEHISTORY_LIST,
      tabledef,
      loader_running,
      preprocess,
      loadMore

    };
  },

  
};
</script>

<style lang="scss" scoped>

#customerinvoicehistory{


  background: #FFF;

  width: 1329px;
  margin: 37px auto 0 auto;
  border-radius: 6px;
  & h3{
    text-align: center;
    height: 138px;
    background: #F8F8F8;
    line-height: 138px;
  }
  .invoicehistorylist-table{

      & .tcol {
        font-size: 16px;
        line-height: 22px;
        font-weight: 400;
        font-style: normal;
        letter-spacing: 0em;
        color: #47454B;
        padding: 23px 10px 32px 20px;
        vertical-align: middle;
    }
    margin:65px 81px 21px 46px;

      & thead, & .trow, & tfoot {
        background: #FFFFFF;
        box-shadow: inset 0px -1px 0px rgb(168 168 168 / 25%);
        align-items: center;
    }
    & thead{
      background: #FFFFFF;
      box-shadow: none;
      align-items: center;
        & .tcol {
            font-size: 14px;
            color: #868686;
            font-weight: 600;
            white-space: nowrap;
        }
    }
    &  tfoot {
        box-shadow: inset 0px 1px 0px #e4e5e7;
    }

  & .trow:hover{
    transition: background-color 300ms ease-out;
    background: #F8F8F8;
}

    & tfoot .btn-link {
        font-family: SF Pro Text;
        font-size: 15px;
        color: #868686;
    }
  }

}
</style>