<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    use HasFactory;

    public static function getTypeService(){
        return array(
          0=>'STANDARD',
          1=>'EXPRESS',
          2=>'NO RUSH',
          3=>'FREE EXPRESS',
		  4=>'RUSH / MUST BE READY',
		  5=>'RUSH - 24h',
		  6=>'RUSH - 48h',
        );
    }

    public static function propagateItemStatus($item_id,$status,$nextpost=false){

        $item = DB::table('infoitems')->where('id',$item_id)->first();

        $has_same_item_status = [];
        $has_same_inv_status = [];
        $is_on_van = false;

        if($item) {


            DB::table('infoitems')->where('id', $item_id)->update(['Status' => $status]);

            if($status=='READY IN STORE'){ //Si dans store conveyor
                DB::table('infoitems')->where('id', $item_id)->update(['Actif' => 0]);
            }

            if ($status == 'OFFLOADED') {
                DB::table('infoitems')
                    ->where('id', $item_id)
                    ->update([
                        'convoyerINOUT' => 0,
                        'conveyorSlot' => '',
                        'conveyorType' => '',
                        'nextpost' => $nextpost,
                    ]);
            }

            if ($nextpost) {
                DB::table('infoitems')->where('id', $item_id)->update(['nextpost' => $nextpost]);
            }

            $other_items = DB::table('infoitems')
                ->where('id', '!=', $item_id)
                ->where('InvoiceID', $item->InvoiceID)
                ->get();

            if (count($other_items) > 0) {
                foreach ($other_items as $k => $v) {
                    if ($v->Status == $status) {
                        $has_same_item_status[] = $v->ItemTrackingKey;
                    }
                }
            }

            if (count($has_same_item_status) == count($other_items)) {
                //echo "all items on $status<br/>";
                $inv = DB::table('infoInvoice')->where('InvoiceID', $item->InvoiceID)->first();
                if ($inv) {
                    DB::table('infoInvoice')->where('InvoiceID', $item->InvoiceID)->update(['Status' => $status]);

                    $other_inv = DB::table('infoInvoice')
                        ->where('id', '!=', $inv->id)
                        ->where('OrderID', $inv->OrderID)
                        ->get();

                    $has_suborder_ready = [];

                    if ($status == 'READY') {
                        $has_suborder_ready[] = $inv->NumInvoice;
                    }

                    if (count($other_inv) > 0) {
                        foreach ($other_inv as $k => $v) {
                            if ($v->Status == $status) {
                                $has_same_inv_status[] = $v->NumInvoice;
                            }


                        }
                    }

                    if (count($has_same_inv_status) == count($other_inv)) {
                        //echo "all invoice on $status<br/>";

                        DB::table('infoOrder')->where('OrderID', $inv->OrderID)->update(['Status' => $status]);

                    }
                    /*
                    else{
                        if(count($has_same_inv_status) < count($other_inv) && count($has_suborder_ready) > 0){
                            DB::table('infoOrder')->where('OrderID',$inv->OrderID)->update(['Status'=>'In process']);
                        }
                    }
                    */
                }

            } else {

            }

        }

    }

}
