<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Conveyor extends Model
{
    use HasFactory;

    //copied from blancpos
    public static function addLineIn($tracking='',$NumInvoice,$Type,$suborder_id=false){
        $to_insert = [];
        $inv = false;
        $insert = false;

        if($Type=='CancelItem') {
            $inv = Db::table('infoInvoice')
                ->select('infoitems.id_items', 'infoitems.SubOrderID', 'infoInvoice.date_add AS created_at', 'infoInvoice.Client AS Customer', DB::raw('DATE_FORMAT(infoitems.PromisedDate,"%Y-%m-%d 00:00:00") AS DateDue'), 'infoitems.PriceTotal AS Price', 'infoitems.typeitem AS GarDesc', 'infoitems.ItemTrackingKey AS GARCODE', 'infoInvoice.NumInvoice AS ORDER','infoitems.PERC')
                ->join('infoitems', 'infoInvoice.SubOrderID', 'infoitems.SubOrderID')
                ->where('infoInvoice.NumInvoice', '=', $NumInvoice)
                ->where('infoitems.ItemTrackingKey', '=', $tracking)
                ->first();
        }
        else if($Type=='PickupOrder' && $suborder_id){
            $invoice = DB::table('infoInvoice')
                ->where('NumInvoice',$NumInvoice)
                ->where('SubOrderID',$suborder_id)
                ->first();


            $inv = new \stdClass();
            //$inv->id_invoice = $invoice->id_invoice;
            $inv->SubOrderID = $invoice->SubOrderID;
            $inv->ORDER = $NumInvoice;
            $inv->GARCODE = '';
            $inv->map_status = "P";
            $inv->bam_status = "D";
            $inv->Price = '';
            $inv->ARM = '';
            $inv->OrderType = 0;
            $inv->NoBag = 0;
            $inv->PERC = 0;
            $inv->created_at = date('Y-m-d H:i:s');

            $to_insert = (array) $inv;
        }

        //$stamp = date('Y-m-d H:i:s');



        if($inv && $Type=='CancelItem'){
            if($Type == 'CancelItem') {
                $inv->map_status = "A";
                $inv->bam_status = "A";
                $inv->Price = number_format($inv->Price, 2);
                $inv->ARM = '000';
                $inv->OrderType = 0;
            }

            $to_insert[] = (array) $inv;
        }

        if($Type=='Split'){
            $item = DB::table('infoitems')
                ->select('infoitems.*','infoInvoice.NumInvoice','infoInvoice.Client')
                ->join('infoInvoice','infoitems.SubOrderID','infoInvoice.SubOrderID')
                ->where('ItemTrackingKey',$tracking)
                ->where('infoInvoice.NumInvoice',$NumInvoice)
                //->whereIn('nextpost',[23,29])
                ->first();



            if($item){

                $inv = new \stdClass();
                $inv->SubOrderID = $item->SubOrderID;
                $inv->ItemID = $item->ItemID;
                $inv->PERC = $item->PERC;
                $inv->id_items = $item->id_items;
                $inv->ORDER = $item->NumInvoice;
                $inv->GarDesc = $item->typeitem;
                $inv->GARCODE = $item->ItemTrackingKey;
                $inv->Customer = $item->Client;
                $inv->map_status = "A";
                $inv->bam_status = "A";
                $inv->Price = '';
                $inv->ARM = '';
                $inv->OrderType = 0;
                $inv->NoBag = 0;
                $inv->PERC = 0;
                $inv->created_at = date('Y-m-d H:i:s');
                $item_cancelled = (array)$inv;

                $insert = DB::table('conveyor_op_in')->insert($item_cancelled);

            }
        }
        if($Type=='New Record'){
            $item = DB::table('infoitems')
                ->select('infoitems.*','infoInvoice.NumInvoice','infoInvoice.Client')
                ->join('infoInvoice','infoitems.SubOrderID','infoInvoice.SubOrderID')
                ->where('ItemTrackingKey',$tracking)
                ->where('infoInvoice.NumInvoice',$NumInvoice)
                ->first();

            if($item){
                $inv = new \stdClass();
                $inv->SubOrderID = $item->SubOrderID;
                $inv->id_items = $item->id_items;
                $inv->ItemID = $item->ItemID;
                $inv->GarDesc = $item->typeitem;
                $inv->PERC = $item->PERC;
                $inv->ORDER = $item->NumInvoice;
                $inv->GARCODE = $item->ItemTrackingKey;
                $inv->map_status = "R";
                $inv->bam_status = "L";
                $inv->Customer = $item->Client;
                $inv->Price = $item->priceTotal;
                $inv->ARM = '';
                $inv->OrderType = 0;
                $inv->NoBag = $item->NoBag;
                $inv->created_at = date('Y-m-d H:i:s');
                $to_insert = (array) $inv;

            }
        }


        if(!empty($to_insert)) {
            $insert = DB::table('conveyor_op_in')->insert($to_insert);
        }
        return $insert;
    }
}
