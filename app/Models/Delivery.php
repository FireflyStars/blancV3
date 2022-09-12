<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Delivery extends Model
{
    use HasFactory;

    public static function getAddressByCustomerUUID($uuid,$billing=false){
        $addr = false;

        $cust = DB::table('infoCustomer')->where('CustomerID',$uuid)->first();

        if($cust){

            if($cust->CustomerIDMaster !=''){ //Si sous compte
                $master_cust = DB::table('infoCustomer')->where('CustomerID',$cust->CustomerIDMaster)->first();

                //Si TypeDelivery de master n'est pas 'DELIVERY'
                if($master_cust->TypeDelivery !='DELIVERY'){
                    $addr = DB::table('address')->where('status',$master_cust->TypeDelivery)->first();
                }else {
                    $addr = DB::table('address')->where('CustomerID', $cust->CustomerIDMaster)->where('status', ($billing?'BILLING':'DELIVERY'))->first();
                }
            }else{ //Si Master ou autre
                if($cust->TypeDelivery !='DELIVERY'){
                    $addr = DB::table('address')->where('status',$cust->TypeDelivery)->first();
                }else{
                    $addr = DB::table('address')->where('status',($billing?'BILLING':'DELIVERY'))->where('CustomerID',$uuid)->first();
                }
            }

        }

        return $addr;
    }

    public static function getDeliveryAskStatusToInclude(){
        return  ['NEW', 'API', 'PMS', 'DONE', 'PMS-DONE', 'API-DONE', 'REC', 'REC-DONE', 'REC-NOK', 'PMS-NOK', 'API-NOK','OP'];
    }

    public static function getPickupStatutToInclude(){
        return ['NEW','API','PMS','DONE', 'PMS-DONE', 'API-DONE','REC','REC-DONE','REC-NOK','PMS-NOK','API-NOK'];
    }



}
