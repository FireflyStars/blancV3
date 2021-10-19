<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ScanController extends Controller
{
   



public function ScanItemAndBag(Request $request)
{
    $info_tracking =$request['info_tracking'];
    

    if( $info_tracking[0] == 'B'){
        
        $id_bagroute = strtok( $info_tracking, 'B' );
        $bag =DB::table('bagRoute')
        ->select('*', 'bagroute.id as bag_id')
        ->join('infoCustomer','bagRoute.CustomerID','=','infoCustomer.CustomerID')
        ->where('bagRoute.id','=' , $id_bagroute)
        ->first();

        if($bag != null){

            
                $order = DB::table('infoOrder')->select('infoOrder.OrderID')->where('infoOrder.id' ,$bag-> infoOrder_id)->get();
                
                $bag->order_Id = $order;
                $bag->order_Id = $bag->order_Id[0]->OrderID;
                

                $items =DB::table('infoInvoice')->where('infoInvoice.OrderID','=',$bag->order_Id)->get();
                $bag->TotalItems = count($items);
                
                if($bag->Phone!=""){
                    $bag->Phone=json_decode($bag->Phone);
                    }

                $array =[];
                if($bag->PickupID != ''){
 
                        $bag->GarmentInstruction = json_decode($bag->GarmentInstruction) ;
                          
                }
              

                    return response()->json(['bag'=>$bag,'message'=>'bags exist']);


        } else {

            return response()(['bag'=>$bag,'message'=>'bags not exist']);
        }
    }

    else {

     
         $item=DB::table('infoItems')
         ->Where('infoItems.id_items',$info_tracking)
         ->join('infoInvoice','infoItems.SubOrderID','=','infoInvoice.SubOrderID')
         ->join('infoCustomer','infoInvoice.CustomerID','=','infoCustomer.CustomerID')
         ->first();

        if($item != null){
         if($item->OrderID != ""){
            $order=DB::table('infoOrder')->select('infoOrder.id' , 'infoOrder.PickupID')->Where( 'infoOrder.OrderID',$item->OrderID)->get();
            $item->order_id = $order[0]->id;
            $item->PickupID = $order[0]->PickupID;
        
     
            if($item->PickupID != ""){

             $search  = ["{", "}","\"","[","]",":" ,"Item", "Instructions","Comment","Brand"];
             $replace = ["", "","","","", "","","","",""];
             $GarmentInstruction = "";  

             $pickup = DB::table('pickup')->where( 'pickup.PickupID',$item->PickupID)->get();
             $item->GarmentInstruction= json_decode($pickup[0]->GarmentInstruction);

             foreach($item->GarmentInstruction as $key=>$instruction){
               $instruction = json_encode($instruction);

             if(strlen($instruction)>2){
                   $delimeter = ":";
               }else{
                   $delimeter = "";
               }
               
               $GarmentInstruction .= $key." ".$delimeter." ".str_replace($search , $replace, $instruction)." / ";
             };

             $item->GarmentInstruction = $GarmentInstruction;

            }
            
         }
         if($item->Phone!=""){
            $item->Phone=json_decode($item->Phone);

         }
         return response()->json(['item'=>$item,'message'=>'item exist']);
        } else {
            return response()->json(['item'=>$item,'message'=>'item not exist']);
        }

    }
    

}

}