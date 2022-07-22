<?php

namespace App;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Conveyor extends Model
{
    //Some comments

    public static function mb_str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
    {
        $diff = strlen($input) - mb_strlen($input);
        return str_pad($input, $pad_length + $diff, $pad_string, $pad_type);
    }

    public static function createInFile($details,$store,$node=false)
    {

        $lines = [];
        $lines2 = [];

        $hung = "S"; //All hung

        $mag = "";

        /*->whereNotIn('infoInvoice.id_invoice',function($q){
            $q->select('id_invoice')->from('printed_conveyor_in');
        })*/


        $id_invoices = [];
        $id_items = [];
        $item_pos = [];

        $garment_only_op = ['A', 'R'];

        if (!empty($details)) {


            foreach ($details as $id => $val) {
                $invoice = [];


                if (in_array($val->map_status,$garment_only_op) && !in_array($val->InvoiceID, $id_invoices))                {
                    array_push($id_invoices, $val->InvoiceID);
                    $id_items[] = $val->id_items;

                    $item_pos[$val->id_items] = 0;

                }


                $val->NGARM = 0; //count($invoice);

                $val->GARMENT = 0;

                $colors = '';
                $brand = '';

                if ($val->map_status == 'R' || $val->map_status == 'A') {
                    $colors = $val->Colors;
                    $brand = $val->brand;
                }

                //$details[$id]->PERC = 100;//($svg_detail ? $svg_detail->PERC : 100);
                $details[$id]->HUNG = $hung;
                $details[$id]->STATUS = "";
                $details[$id]->MAG = $mag;
                $details[$id]->SLOT = "";
                $details[$id]->OUT = $val->OUT;
                $details[$id]->RFID = "";
                //$details[$id]->ARM = "";
                $details[$id]->LabelInfo = "";
                //$details[$id]->NoBag = 0;
                $details[$id]->IDLogo = "";
                $details[$id]->LabelBarcode = "";
                $details[$id]->Priority = "";
                $details[$id]->comment = $colors . ($brand != '' ? ", " . $brand : "");
            }


            $nb_garm = $item_pos;
            $item_perc = $item_pos;
            $item_comments = $item_pos;

            foreach ($item_comments as $k => $v) {
                $item_comments[$k] = '';
            }

            if(!empty($id_invoices)) {
                $invoice_items = DB::table('infoitems')
                    ->select('id_items', 'InvoiceID')
                    ->whereIn('InvoiceID', $id_invoices)
                    ->orderBy('id_items', 'ASC')
                    ->get();


                $grouped_by_invoice = [];
                foreach ($invoice_items as $k => $v) {
                    $grouped_by_invoice[$v->InvoiceID][] = $v->id_items;
                }

                foreach ($grouped_by_invoice as $k => $v) {
                    sort($v);

                    foreach ($v as $i => $x) {
                        $pos = $i + 1;
                        $item_pos[$x] = $pos;
                        $nb_garm[$x] = count($v);

                    }
                }
            }

            $all_lines = [];


            foreach ($details as $id => $val) {
                $bam_status = $val->bam_status;
                $ngarm = 0;
                $garment = 0;

                $storename = $val->StoreName;
                $storecode = Conveyor::getStoreCodeByName($storename);

                if ($val->id_items != 0 && in_array($val->map_status, $garment_only_op)) {
                    $details[$id]->NGARM = (isset($nb_garm[$val->id_items]) ? $nb_garm[$val->id_items] : 0);
                    $details[$id]->GARMENT = (isset($item_pos[$val->id_items]) ? $item_pos[$val->id_items] : 0);
                    $details[$id]->PERC = ($val->PERC == 0 ? 100 : $val->PERC);
                    //$details[$id]->comment = (isset($item_comments[$val->id_items]) ? $item_comments[$val->id_items] : 0);

                    $ngarm = $val->NGARM;
                    $garment = $val->GARMENT;
                }

                $out = "";
                $price = "";
                $order_type = "";

                $order_type = "0";
                $status_orders = ['D', 'P'];

                if ($bam_status == 'L') {
                    $out = "#001#";
                    $order_type = "0";
                    //$price = number_format($val->priceTotal,2);
                } else if (in_array($bam_status, $status_orders)) {
                    //$order_type = "5"; //O or 5 o 6
                }


                if ($val->bam_status != '' && !in_array($val->bam_status, $status_orders) && ($val->map_status == 'R' || $val->map_status == 'A' || $val->map_status == '')) { //
                    $rows_bam = [
                        'CODE_AT' => $storecode,
                        'ORDER' => $val->NumInvoice,
                        'N' => $ngarm, //$val->NGARM,
                        'G' => $garment, //$val->GARMENT,
                        'GarCode' => $val->ItemTrackingKey,
                        'GarDesc' => $val->typeitem,
                        'Pe' => $val->PERC,
                        'H' => $val->HUNG,
                        'S' => $val->bam_status,
                        'MA' => '',
                        'Slot' => '',
                        'Out' => $out,
                        'Customer' => '',
                        'OrderType' => $order_type,
                        'OrderKey' => '',
                        'ARM' => '',
                        'Price' => $price,
                    ];
                } else if (in_array($val->map_status, $status_orders) || in_array($val->bam_status, $status_orders)) {
                    $rows_bam = [
                        'CODE_AT' => $storecode,
                        'ORDER' => $val->NumInvoice,
                        'N' => '', //0,
                        'G' => '', //0,
                        'GarCode' => '',
                        'GarDesc' => '',
                        'Pe' => '',
                        'H' => $val->HUNG,
                        'S' => $val->bam_status,
                        'MA' => '',
                        'Slot' => '',
                        'Out' => '',
                        'Customer' => '',
                        'OrderType' => $order_type,
                        'OrderKey' => '',
                        'ARM' => (($val->fix==1 && $val->orderstop==0) || ($val->fix==0 && $val->orderstop !=0)?$val->ARM:''),
                        'Price' => '',
                    ];
                }

                $label_array = [];


                if ($val->map_status == 'R' ||  $val->map_status == 'A') {
                    $label_array = [
                        $storename,
                        $val->client,
                        ' ',
                        $val->NGARM,
                        ' GBP ' . number_format($val->priceTotal, 2),
                        date('d/m/Y H:i:s', strtotime($val->PromisedDate)),
                        'www.blancliving.co'
                    ];
                }

                $rows_map = [];

                if ($val->map_status != '') {
                    $rows_map = $rows_bam;

                    unset($rows_map['Price']);
                    unset($rows_map['ARM']);
                    unset($rows_map['OrderType']);
                    unset($rows_map['OrderKey']);

                    $rows_map['S'] = $val->map_status;

                    $rows_map = array_merge($rows_map, [
                        'Customer' => '',
                        'RFID' => '',
                        'ARM' => ($val->map_status == 'R' && $val->NoBag == 1 ? '006' : ''),
                        'LabelInfo' => '',
                        'B' => (!in_array($val->map_status, $status_orders)?$val->NoBag:''), //NoBag
                        'Lg' => '', //IDLOGO
                        'Price' => '',
                        'LabelBarcode' => '',
                        'Pr' => '',
                        'Comment' => '',
                        'CustomField' => '',
                        'PFID' => '',
                        'PFDest' => '',
                        'FRExit' => '',
                        'DT_DUE' => '',

                    ]);

                    if ($val->map_status == 'R' || $val->map_status == 'A') {

                        //$rows_map['ARM'] = '000';
                        $rows_map['LabelInfo'] = implode(";", $label_array) . ";";
                        //$rows_map['B'] = 1; //NoBag
                        $rows_map['Lg'] = ''; //IDLOGO
                        $rows_map['Price'] = number_format($val->priceTotal, 2);
                        $rows_map['LabelBarcode'] = '';
                        $rows_map['Pr'] = '';
                        $rows_map['Comment'] = str_replace('*', '', $val->comment);
                        $rows_map['CustomField'] = '';
                        $rows_map['PFID'] = '';
                        $rows_map['PFDest'] = '';
                        $rows_map['FRExit'] = '';
                        $rows_map['DT_DUE'] = date('YmdHis', strtotime($val->PromisedDate));
                        $rows_map['Out'] = $val->OUT;
                    }

                    $rows_index = [
                        'id_items' => $val->id_items,
                        'InvoiceID' => $val->InvoiceID,
                        'SubOrderID'=>$val->SubOrderID,
                    ];

                    $lines2[] = array_merge($rows_map, $rows_index);
                }

                if ($val->bam_status == '') {
                    $rows_bam = [];
                }

                $lines[] = $rows_bam;

            }



            $hist = [];
            $hist_order = [];
            $orders = [];
            $pickup_orders = [];



            foreach ($lines as $k => $v) {
                if (empty($v)) {
                    unset($lines[$k]);
                }
            }

            foreach ($lines2 as $k => $v) {
                if (empty($v)) {
                    unset($lines2[$k]);
                }
            }



            if (!empty($lines2)) {

                foreach ($lines2 as $k => $v) {
                    if ($v['GarCode'] != '') {
                        $hist[] = [
                            'type' => 'IN',
                            'conveyorType' => 'map',
                            'created_at' => date('Y-m-d H:i:s'),
                            'ORDER' => $v['ORDER'],
                            'NGARM' => $v['N'],
                            'GARMENT' => $v['G'],
                            'GARCODE' => $v['GarCode'],
                            'GARDESC' => $v['GarDesc'],
                            'HUNG' => $v['H'],
                            'PERC' => $v['Pe'],
                            'status' => $v['S'],
                            'Slot' => '',
                            'ARM' => $v['ARM'],
                            'LabelInfo' => str_replace('+', '', $v['LabelInfo']),
                            'DT_DUE' => $v['DT_DUE'],
                            'id_items' => $v['id_items'],
                            'SubOrderID' => $v['SubOrderID'],
                            'InvoiceID'=>$v['InvoiceID'],
                            'ConvStoreCode'=>$store,
                        ];
                    } else {
                        if (!in_array($v['ORDER'], $orders)) {
                            array_push($orders, $v['ORDER']);
                        }
                    }
                }
            }

            //TO UNCOMMENT

            if (!empty($orders)) {
                foreach($orders as $k=>$v){

                    $hist[] = [
                        'type' => 'IN',
                        'conveyorType' => 'map',
                        'created_at' => date('Y-m-d H:i:s'),
                        'ORDER' => $v,
                        'NGARM' => '',
                        'GARMENT' => '',
                        'GARCODE' => '',
                        'GARDESC' => '',
                        'HUNG' => '',
                        'PERC' => '',
                        'status' => 'P',
                        'Slot' => '',
                        'ARM' => '',
                        'LabelInfo' => '',
                        'DT_DUE' => '',
                        'id_items' => 0,
                        'SubOrderID' => '',
                        'InvoiceID'=>'',
                        'ConvStoreCode'=>$store
                    ];

                }
            }


           DB::table('conveyorhistories')->insert($hist);

            $stamp = strtotime('now');

            if (!empty($lines2)) {
                foreach ($lines2 as $k => $v) {
                    unset($lines2[$k]['id_items']);
                    unset($lines2[$k]['SubOrderID']);
                    unset($lines2[$k]['InvoiceID']);
                }
            }

            $in_file = '';



            if($node){
                $map_file = '';
                $bam_file = '';
                if (!empty($lines2)) {
                    $map_file = Conveyor::generateInFile('MAP', $lines2, $stamp,$store,$node);
                }
                if(!empty($lines)){
                    $bam_file = Conveyor::generateInFile('BAM', $lines, $stamp,$store,$node);
                }

                return [
                    'map_file'=>$map_file,
                    'bam_file'=>$bam_file,
                ];
            }else{
                if (!empty($lines2)) {
                    $in_file = Conveyor::generateInFile('MAP', $lines2, $stamp,$store);
                }

                if (!empty($lines)) {
                    $in_file = Conveyor::generateInFile('BAM', $lines, $stamp,$store);
                }

                return $in_file; //OR $bam_file
            }
        } else {
            return "No items";
        }
    }


    public static function generateInFile($conv_type, $lines, $stamp,$store,$node=false)
    {



        $bamexc_path = storage_path($store.'/bamexc/IN');
        $mapexc_path = storage_path($store.'/mapexc/IN');

        if (!is_dir($bamexc_path)) {
            mkdir($bamexc_path, 0777);
        }

        if (!is_dir($mapexc_path)) {
            mkdir($mapexc_path, 0777);
        }

        $other_stores_format = Conveyor::getStoreWithOtherFormats();


        $bam_padding_arr = [
            'CODE_AT' => (in_array($store,$other_stores_format)?0:5),
            'ORDER' => (in_array($store,$other_stores_format)?21:15),
            'N' => 2,
            'G' => 2,
            'GarCode' => 20,
            'GarDesc' => 30,
            'Pe' => 3,
            'H' => 1,
            'S' => 1,
            'MA' => 2,
            'Slot' => 5,
            'Out' => 5,
            'Customer' => 20,
            'OrderType' => 1,
            'OrderKey' => 20,
            'ARM' => 3,
            'Price' => 7
            /* 'LabelInfo' => 186,
             'B' => 1,
             'Lg' => 2,
             'Price' => 7,
             'LabelBarcode' => 16,
             'Pr' => 2,
             'Comment' => 30*/
        ];

        $map_padding_arr = [
            'CODE_AT' => 5,
            'ORDER' => 15,
            'N' => 2,
            'G' => 2,
            'GarCode' => 20,
            'GarDesc' => 30,
            'Pe' => 3,
            'H' => 1,
            'S' => 1,
            'MA' => 2,
            'Slot' => 5,
            'Out' => 5,
            'Customer' => 20,
            'RFID' => 20,
            'ARM' => 3,
            'LabelInfo' => 186,
            'B' => 1,
            'Lg' => 2,
            'Price' => 7,
            'LabelBarcode' => 16,
            'Pr' => 2,
            'Comment' => 30,
            'CustomField' => 390,
            'PFID' => 3,
            'PFDest' => 4,
            'FRExit' => 2,
            'DT_DUE' => 14

        ];


        $padding_arr = [];
        $folder = "";

        if ($conv_type == 'BAM') {
            $padding_arr = $bam_padding_arr;
            $folder = "bamexc";
            $filename = $bamexc_path . '/' . $stamp . 'AUTOMAT.IN';
        } elseif ($conv_type == 'MAP') {
            $padding_arr = $map_padding_arr;
            $folder = "mapexc";
            $filename = $mapexc_path . '/' . $stamp . 'AUTOMAT.IN';
        }


        $txt_line = [];
        $url = "";


        if (!empty($lines)) {
            $firstline = [];
            foreach ($map_padding_arr as $k => $v) {
                $firstline[$k] = ($k == 'CODE_AT' ? 'CODEA' : $k);
            }
            $first_line[] = $firstline;

            /*
            if($conv_type=='MAP') {
                $lines = array_merge($first_line, $lines);
            }*/

            /*echo "<pre>";
            print_r($lines);
            die();*/

          //echo "<pre>";

            foreach ($lines as $k => $v) {
                $padstring = " ";
                /* if ($k == 0) {
                     $padstring = "-";
                 }*/



                foreach ($v as $i => $x) {
                    $line = "";

                    if(in_array($store,$other_stores_format)){
                        $lines[$k]['CODE_AT'] = '';
                        $lines[$k]['ORDER'] = str_replace("-","",$lines[$k]['ORDER']);
                    }

                    if($store=='MB1'){
                        $lines[$k]['ARM'] = '';
                    }


                    if ($i == 'N' || $i == 'G') {
                        $x = sprintf('%02d', $x); //($k != 0 ? sprintf('%02d', $x) : $x);
                    } else if ($i == 'Pe') {
                        $x = sprintf('%03d', $x); //($k != 0 ? sprintf('%03d', $x) : $x);
                    } else if ($i == 'GarDesc' || $i == 'Comment') {
                        $x = substr($x, 0, 30);
                    }

                    if(in_array($store,$other_stores_format) && $i=='CODE_AT'){
                        //Do nothing
                    }else{
                        /*
                        if($store=='AT'){
                            $lines[$k][$i] = $x;
                        }else{
                            $lines[$k][$i] = Conveyor::mb_str_pad($x, $padding_arr[$i], $padstring, STR_PAD_RIGHT);
                        }*/
                        $lines[$k][$i] = Conveyor::mb_str_pad($x, $padding_arr[$i], $padstring, STR_PAD_RIGHT);
                    }
                }
            }


            /*
             * TO TEST
             * */

            $fields_to_replace = ['N', 'G', 'Pe', 'H'];
            $order_status = ['D', 'P'];
            foreach ($lines as $k => $v) {
                foreach ($v as $i => $x) {

                    if ($i == 'S' && in_array($x, $order_status)) {
                        foreach ($fields_to_replace as $id => $val) {
                            $lines[$k][$val] = str_replace('0', ' ', $lines[$k][$val]);
                        }
                        $lines[$k]['H'] = ' ';
                    }
                }
            }

            /*END
             * */

            foreach ($lines as $k => $v) {
                $txt_line[] = implode('', $v);
            }

            if(!$node){
                $file = fopen($filename, 'w+');

                //$lines = "";

                $lines_count = count($txt_line);
                foreach ($txt_line as $k=>$v) {
                    $line_num = $k+1;
                    $line = $v .($line_num==$lines_count?"":"\r\n");
                    fwrite($file, $line);

                    //$lines .= $line . "<br/>";
                }

                fclose($file);
            }
        }

        if($node){
            return $txt_line;
        }else{
            return basename($filename);
        }
    }

    public static function getStatusName()
    {

        $statut = [
            'map' => [
                'R' => 'New Record',
                'A' => 'Item cancelled',
                'D' => 'Cancel Order',
                'L' => 'Being handled / Ready',
                'C' => 'Commit received',
                'S' => 'Scanned at loading point',
                'B' => 'Bagged',
                'P' => 'Pickup and delete order',
                'U' => 'Offloaded off conveyor',
                'T' => 'After split, communicate with POS'
            ],
            'bam' => [
                'R' => 'New Record',
                'A' => 'Item cancelled',
                'D' => 'Cancel Order',
                'L' => 'Being handled / Ready',
                'M' => 'Manually retrieved',
                'U' => 'for retrieval/retrieved at automatic distribution gate',
                'P' => 'Pickup order from 24H order',
                'O' => 'Change order amount',
            ]
        ];

        return $statut;
    }

    public static function getPromisedDate($tracking)
    {
        $item = DB::table('infoitems')
            ->where('ItemTrackingKey', $tracking)
            ->latest('id_items')
            ->first();

        if ($item) {
            $date_promised = $item->PromisedDate;

            return $date_promised . " 00:00:00";
        }

        return "0000-00-00 00:00:00";
    }

    public static function addLineIn($tracking = '', $NumInvoice, $Type, $suborder_id = false)
    {
        $to_insert = [];
        $inv = false;
        $insert = false;

        if ($Type == 'CancelItem') {
            $inv = Db::table('infoInvoice')
                ->select('infoitems.id_items', 'infoitems.InvoiceID','infoitems.SubOrderID', 'infoInvoice.date_add AS created_at', 'infoInvoice.Client AS Customer', DB::raw('DATE_FORMAT(infoitems.PromisedDate,"%Y-%m-%d 00:00:00") AS DateDue'), 'infoitems.PriceTotal AS Price', 'infoitems.typeitem AS GarDesc', 'infoitems.ItemTrackingKey AS GARCODE', 'infoInvoice.NumInvoice AS ORDER', 'infoitems.PERC', 'infoInvoice.CustomerID')
                ->join('infoitems', 'infoInvoice.InvoiceID', 'infoitems.InvoiceID')
                ->where('infoInvoice.NumInvoice', '=', $NumInvoice)
                ->where('infoitems.ItemTrackingKey', '=', $tracking)
                ->first();
        } else if ($Type == 'PickupOrder' && $suborder_id) {
            $invoice = DB::table('conveyors')
                ->where('InvoiceID', $suborder_id)
                ->first();

            if ($invoice) {
                $inv = new \stdClass();
                //$inv->id_invoice = $invoice->id_invoice;
                $inv->SubOrderID = $invoice->SubOrderID;
                $inv->ORDER = $NumInvoice;
                $inv->GARCODE = '';
                $inv->map_status = "";
                $inv->bam_status = "D";
                $inv->Price = '';
                $inv->ARM = '';
                $inv->OrderType = 0;
                $inv->NoBag = 0;
                $inv->PERC = 0;
                $inv->CustomerID = $invoice->CustomerID;
                $inv->InvoiceID = $invoice->InvoiceID;
                $inv->created_at = date('Y-m-d H:i:s');

                $to_insert = (array)$inv;
            }
        }

        //$stamp = date('Y-m-d H:i:s');


        /*
        if($inv && $Type=='CancelItem'){
            if($Type == 'CancelItem') {
                $inv->map_status = "A";
                $inv->bam_status = "A";
                $inv->Price = number_format($inv->Price, 2);
                $inv->ARM = '000';
                $inv->OrderType = 0;
            }

            $to_insert[] = (array) $inv;
        }*/

        if ($Type == 'Split') {
            $item = DB::table('infoitems')
                ->select('infoitems.*', 'infoInvoice.NumInvoice', 'infoInvoice.Client')
                ->join('infoInvoice', 'infoitems.SubOrderID', 'infoInvoice.SubOrderID')
                ->where('ItemTrackingKey', $tracking)
                ->where('infoInvoice.NumInvoice', $NumInvoice)
                //->whereIn('nextpost',[23,29])
                ->first();



            if ($item) {

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
        if ($Type == 'New Record') {
            $item = DB::table('infoitems')
                ->select('infoitems.*', 'infoInvoice.NumInvoice', 'infoInvoice.Client', 'infoInvoice.CustomerID')
                ->join('infoInvoice', 'infoitems.InvoiceID', 'infoInvoice.InvoiceID')
                ->where('ItemTrackingKey', $tracking)
                ->where('infoInvoice.NumInvoice', $NumInvoice)
                ->first();

            if ($item) {
                $inv = new \stdClass();
                $inv->SubOrderID = $item->SubOrderID;
                $inv->InvoiceID = $item->InvoiceID;
                $inv->id_items = $item->id_items;
                $inv->ItemID = $item->ItemID;
                $inv->InvoiceID = $item->InvoiceID;
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
                $inv->CustomerID = $item->CustomerID;
                $to_insert = (array) $inv;
            }
        }


        if (!empty($to_insert)) {
            $insert = DB::table('conveyor_op_in')->insert($to_insert);
        }
        return $insert;
    }

    public static function processOutFile($data, $type, $file,$store)
    {
        $time_start = microtime(true);

        $updated_nextpost = [];
        $to_upsert = [];
        $to_delete = [];
        $hist_to_insert = [];

        $conveyor_lines = [];
        $last_conveyor_statut = [];
        $nb_inserted = 0;
        $nb_updated = 0;
        $nb_deleted = 0;
        $removed_assembly = [];
        $removed_at_no_bag = [];
        $items_to_reset = [];

        $storecodes = Conveyor::getAllStoreCodes(true);

        $store_storage_postes = [
            'AT'=>36,'CH1'=>49,'CH2'=>61,'MB1'=>52,'NH1'=>55,'NH2'=>64,'BS1'=>58,'BS2'=>67
        ];

        $store_offloaded_postes = [
            'AT'=>41,'CH1'=>50,'CH2'=>62,'MB1'=>53,'NH1'=>56,'NH2'=>65,'BS1'=>59,'BS2'=>68
        ];

        $store_nextpost = [
            49, //CH1
            61, //CH2
            52, //MB1
            55, //NH1
            64, //NH2
            58, //BS1
			49,61,52,55,64,58,67
        ];

        $excluded_post_at = array_merge($store_nextpost);
        //Ajout shelves
        for($i=69; $i<=76; $i++){
            $excluded_post_at[] = $i;
        }

        $excluded_post_at[] = 81;
        $excluded_post_at[] = 82;
        $excluded_post_at[] = 28; //FULFILLED
        $excluded_post_at[] = 29; //ON VAN
        $excluded_post_at[] = 39; //DELIVERED
		/*
		foreach($excluded_post_at as $k=>$v){
			if($v==41 || $v==36){
				unset($excluded_post_at[$k]);
			}
		}
        */


        $found = true;


        if (!empty($data)) {
            $slots = [];

            $conveyor_slots = DB::table('conveyors')
                ->get();

            if (!empty($conveyor_slots)) {
                foreach ($conveyor_slots as $k => $v) {
                    $key = str_replace('-', '', $v->ORDER) . "_" . trim($v->GARCODE);
                    $slots[$key] = [
                        'Slot' => trim($v->Slot),
                        'updated_at' => $v->updated_at,
                    ];
                }
            }


            $orders = [];

            $to_delete_status = ['U','M'];

            $other_stores_format = Conveyor::getStoreWithOtherFormats();

            foreach ($data as $k => $v) {


                $order = trim(substr($v, 5, 9));
                $store_code = trim(substr($v,0,5));

                if(in_array($store,$other_stores_format)){
                    $order_file = trim(substr($v, 0, 9));
                    $order = substr($order_file,0,2)."-".substr($order_file,2,6);

                    $store_code = "";
                }


                $status = substr($v, 78, 1);
                $slot = trim(substr($v, 81, 5));

                $tracking = trim(substr($v, 24, 8));

                $type_item = trim(substr($v,44,30));
                $arm = trim(substr($v,131,3));

                $deleted_from_memory = false;
                $removed_from_assembly = false;

                $first_two_tracking_char = trim(substr($v,24,2));

               if($first_two_tracking_char=='*A' && $status=='A'){
                   $tracking = trim(substr($v,26,8));
                   $deleted_from_memory = true;
               }

               if(!$deleted_from_memory && $type=='map' && in_array($status,['A','D'])){
                   $removed_from_assembly = true;
               }

                $suborderid = "";
                $item = false;
                $inv = false;
                $item_inv = false;

                if($order !='' && $tracking!=''){
                    $found = true;

                    $item_inv = DB::table('infoitems')
                        ->select('infoitems.id AS item_id','infoInvoice.id AS invoice_id')
                        ->join('infoInvoice','infoitems.InvoiceID','infoInvoice.InvoiceID')
                        ->where('infoitems.ItemTrackingKey',$tracking)
                        ->where('infoInvoice.NumInvoice',$order)
                        ->latest('infoInvoice.id')
                        ->first();

                    if($item_inv){
                        $item = DB::table('infoitems')->where('id',$item_inv->item_id)->first();

                        $inv = DB::table('infoInvoice')->where('id',$item_inv->invoice_id)->first();
                    }else{

                       // echo $tracking."\t".$order."\t".$type_item."<br/>";


                        if(!$deleted_from_memory && !$removed_from_assembly) {
                            $found = false;
                        }
                    }


                }else{
                    $item = DB::table('infoitems')->where('ItemTrackingKey', $tracking)->latest('id_items')->first();

                    $storename = (isset($storecodes[$store_code])?$storecodes[$store_code]:'');
                    $inv = DB::table('infoInvoice')
                        ->where('NumInvoice', $order)
                        ->where('StoreName',$storename)
                        ->latest('id')->first();
                }

                if ($item) {
                    $suborderid = $item->SubOrderID;
                }
                if ($inv) {
                    $suborderid = $inv->SubOrderID;
                }

                if ($slot != '' && $item) {
                    $conv_detail = new \stdClass();
                    $conv_detail->is_conveyor = 1;
                    $conv_detail->type = $type;
                    $conv_detail->status = $status;
                    $conv_detail->slot = $slot;
                    $conv_detail->arm = $arm;

                    $item_nextpost = 0;
                    $item_status = "";

                    if(!$deleted_from_memory && !$removed_from_assembly) {
                        if ($type == 'map') {
                            if($store=='AT'){
                                if($arm=='S06'){
                                    $item_nextpost = 42;
                                    $item_status = "OFFLOADED";
                                }else {
                                    $item_nextpost = 24;
                                    $item_status = "ASSEMBLING";
                                }
                            }
                        } elseif ($type == 'bam') {
                            if($status =='L') {
                                $item_nextpost = (isset($store_storage_postes[$store])?$store_storage_postes[$store]:36);
                                $item_status = ($store=='AT'?"READY":"READY IN STORE");
                            }elseif (in_array($status,['U','M'])){
                                $item_nextpost = (isset($store_offloaded_postes[$store])?$store_offloaded_postes[$store]:41);
                            }

                        }
                    }

                    if(($store=='AT' && $item->nextpost==29) || ($store !='AT' && $status!='L' && $item->nextpost==28) || ($store=='AT' && in_array($item->nextpost,[30,31,32]) && $item_nextpost==42) || ($store=='AT' && in_array($item->nextpost,$excluded_post_at)) || ($store=='AT' && in_array($item->Status,['ON VAN','READY IN STORE','DELIVERED','FULFILLED']))) {
                        //Do nothing
                    }else{
                        if ($status == 'L') {
                            DB::table('infoitems')->where('id',$item->id)->update(['Actif'=>1]);

                            Delivery::propagateItemStatus($item->id, $item_status, $item_nextpost);
                        } elseif ($type == 'bam' && in_array($status, ['U', 'M'])) {
                            /*
                            if($store=='AT' && (in_array($item->nextpost,$store_nextpost) || $item->Status=='READY IN STORE')){

                            }else{
                                Delivery::propagateItemStatus($item->id, 'OFFLOADED', (isset($store_offloaded_postes[$store])?$store_offloaded_postes[$store]:41));
                            }*/
                            if($store=='AT' && $item->nextpost==36){ //Only Atelier Storage
                                //Delivery::propagateItemStatus($item->id, 'OFFLOADED',41);
                            }
                        }

                        else if (!$deleted_from_memory && $item) {
                            Item::setConveyorHistory($item, $item_nextpost, 1, json_encode($conv_detail));
                        }
                    }

                    if($item && $tracking !='' && in_array($status,['L','U']) && (in_array($item_nextpost,$store_storage_postes) || in_array($item_nextpost,$store_offloaded_postes)) && $item->id_items !=416185){
                        Item::setConveyorHistory($item, $item_nextpost, 1, json_encode($conv_detail));
                    }


                    //Enlever les champs conveyor si c'est plus sur conveyor

                    if(in_array($status,$to_delete_status) && $item){
                        DB::table('infoitems')
                        ->where('id', $item->id)
                        ->update([
                            'convoyerINOUT' => 0,
                            'conveyorSlot' => '',
                            'conveyorType' => '',
                        ]);
                    }
                }


                $log_stamp = substr($v, -14);
                $date_created = $file['created_at']; //date('Y-m-d H:i:s',strtotime($log_stamp));

                if ($order != '') {
                    $orders[$order] = [];
                }

                if($item){
                    $updated_item = DB::table('infoitems')->where('id',$item->id)->first();
                    $updated_nextpost[$item->ItemTrackingKey] = $updated_item->nextpost;
                }

                $detail = [
                    'ORDER' => $order,
                    'NGARM' => substr($v, 20, 2),
                    'GARMENT' => substr($v, 22, 2),
                    'GARCODE' => $tracking,
                    //'GARDESC'=>'',//substr($v,44,30),
                    'PERC' => substr($v, 74, 3),
                    'HUNG' => substr($v, 77, 1),
                    'type' => 'OUT',
                    'conveyorType' => $type,
                    'Status' => $status,
                    'Slot' => $slot,
                    'ARM' => substr($v, 131, 3),
                    'labelinfo' => substr($v, 134, 186),
                    //'DT_DUE'=>substr($v,777,14),
                    'created_at' => $date_created,
                    'updated_at' => $date_created,
                    'line_info' => '', //trim(substr($v,0,-14)),
                    'id_items' => ($item ? $item->id_items : 0),
                    'id_invoice' => 0, //($item?$item->id_invoice:($inv?$inv->id_invoice:0)),
                    'SubOrderID' => ($item ? $item->SubOrderID : ($inv ? $inv->SubOrderID : '')),
                    'InvoiceID' => ($item ? $item->InvoiceID : ($inv ? $inv->InvoiceID : '')),
                    'StoreCode'=>$store_code,
                    'ConvStoreCode'=>$store
                ];

                $detail2 = $detail;

                unset($detail2['StoreCode']);

                if ($order != '') {
                    $hist_to_insert[] = $detail2;
                }

                if ($found  && in_array($status, $to_delete_status)) {
                    $to_delete[$tracking] = [
                        'status'=>$status,
                        'order'=>$order,
                        'StoreCode'=>$store_code,
                    ];
                }elseif(!$deleted_from_memory  && $type=='map' && in_array($status,['A','D'])){
                    //Removed from assembly using IN with map_status=D
                    $removed_assembly[] = [
                        'status'=>$status,
                        'order'=>$order,
                        'StoreCode'=>$store_code,
                    ];

                    if($item_inv){
                        if(!in_array($item_inv->item_id,$items_to_reset)){
                            array_push($items_to_reset,$item_inv->item_id);
                        }
                    }
                }

                if ($order != '' && $tracking != '' && $slot != '' && $status == 'L') {
                    if($type=='map' && $arm=='S06'){
                        $removed_at_no_bag[] = [
                          'GARCODE'=>$tracking,
                          'ORDER'=>$order,
                          'StoreCode'=>$store_code,
                        ];

                        if($item_inv){
                            if(!in_array($item_inv->item_id,$items_to_reset)){
                                array_push($items_to_reset,$item_inv->item_id);
                            }
                        }
                    }else {
                        $to_upsert[$k] = $detail;
                    }
                }
            }

            foreach ($to_upsert as $k => $v) {
                $o = $v['ORDER'];
                $t = $v['GARCODE'];
                if (isset($orders[$o])) {
                    if (!in_array($t, $orders[$o])) {
                        array_push($orders[$o], $t);
                    }
                }
            }

            foreach ($to_upsert as $k => $v) {
                if ($v['Slot'] != '') {
                    $v['isMulti'] = 0;
                    $v['GARCODE'] = '';

                    $perc = $v['PERC'];
                    if ($perc < 100 || $perc > 100) {
                        $v['isMulti'] = 1;
                    }
                    //$conveyor_lines[$k] = $v;
                }
            }

            $cur_keys = [];
            $conv_fields = ['Slot', 'Status', 'ORDER', 'GARCODE', 'GARDESC', 'PERC', 'created_at', 'updated_at', 'id_invoice', 'id_items', 'SubOrderID','StoreCode','InvoiceID'];
            foreach ($to_upsert as $k => $v) {
                foreach ($v as $i => $x) {
                    if (!in_array($i, $conv_fields)) {
                        unset($to_upsert[$k][$i]);
                    }
                }
                $to_upsert[$k]['Name'] = $type;
            }

            $deleted_status = ['P', 'D', 'U', 'A'];

            $to_insert = [];
            $inserted = [];
            $updated = [];

            $tracking_to_delete = [];

            if (!empty($to_delete)) {
                foreach($to_delete as $key=>$detail){
                    $item = DB::table('infoitems')
                            ->select('infoitems.id', 'conveyors.Name', 'infoitems.SubOrderID')
                            ->join('conveyors', 'infoitems.InvoiceID', 'conveyors.InvoiceID')
                            ->where('GARCODE', $key)
                            ->where('ORDER',$detail['order'])
                            //->where('StoreCode',$detail['StoreCode'])
                            ->first();

                    $suborder_offloaded = [];

                    if($item) {
                        $tracking_to_delete[] = $item->id;
                        //Delivery::propagateItemStatus($item->id, 'OFFLOADED');
                        /*if ($item->Name == 'bam') {
                            $suborder_offloaded[] = $item->InvoiceID;
                        }*/
                    }
                        //TO UNCOMMENT
                        DB::table('conveyors')
                            ->where('GARCODE', $key)
                            ->where('ORDER',$detail['order'])
                            ->where('StoreCode',$detail['StoreCode'])
                            ->delete();
                    //}
                }
            }


            $file_detail = [];

            if (!empty($to_upsert)) {
                $file_detail = $to_upsert;

                foreach($file_detail as $k=>$v){
                    $nb_inserted += DB::table('conveyors')->updateOrInsert(
                        [
                            'ORDER'=>$v['ORDER'],
                            'GARCODE'=>$v['GARCODE'],
                            'StoreCode'=>$v['StoreCode'],
                            'ConvStoreCode'=>$store
                        ],$v
                    );
                }

                foreach ($to_upsert as $k => $v) {
                    $new_key =  str_replace('-', '', $v['ORDER']) . "_" . trim($v['GARCODE']);

                    $np = 0;
                    if(isset($updated_nextpost[$v['GARCODE']])){
                        $np = $updated_nextpost[$v['GARCODE']];
                    }

                    if(in_array($np,$store_storage_postes) || $np==24){

                        DB::table('infoitems')
                            ->where('itemTrackingKey', $v['GARCODE'])
                            ->where('InvoiceID',$v['InvoiceID'])
                            ->update([
                                'Actif'=>1,
                                'convoyerINOUT' => 1,
                                'conveyorSlot' => $v['Slot'],
                                'conveyorType' => $type,
                            ]);
                    }

                }
            }

            //Test to uncomment
            if (!empty($tracking_to_delete)) {
                DB::table('infoitems')
                    ->whereIn('id', $tracking_to_delete)
                    ->update([
                        'convoyerINOUT' => 0,
                        'conveyorSlot' => '',
                        'conveyorType' => '',
                    ]);
            }

            if(!empty($removed_assembly)){
                foreach($removed_assembly as $k=>$v){
                    DB::table('conveyors')
                        ->where('Name','map')
                        ->where('ORDER',$v['order'])
                        ->where('StoreCode',$v['StoreCode'])
                        ->where('ConvStoreCode',$store)
                        ->delete();
                }
            }

            if(!empty($removed_at_no_bag)){
                foreach($removed_at_no_bag as $k=>$v){
                    DB::table('conveyors')
                        ->where('GARCODE',$v['GARCODE'])
                        ->where('ORDER',$v['ORDER'])
                        ->where('StoreCode',$v['StoreCode'])
                        ->where('ConvStoreCode',$store)
                        ->delete();
                }
            }

            //To log Uploaded file
        $file['ConvStoreCode'] = $store;
         DB::table('uploaded_out_files')->insert($file);

            $file_stamp = $file_stamp = str_replace('.txt', '', $file['filename']);

            DB::table('conveyor_file_log')->insert([
                'type_conveyor' => $type,
                'type_file' => 'OUT',
                'detail' => "Processed: " . $type . "exc" . $file['filename'] . ", Number of lines: " . count($data),
                'created_at' => date('Y-m-d H:i:s'),
                'ConvStoreCode'=>$store,
            ]);

            DB::table('conveyorhistories')->insert($hist_to_insert);
        }

        $time_end = microtime(true);
        $time_taken = $time_end - $time_start;

        $output = [
            'found'=>false,
            'result'=>'No files to read'
        ];

        if(isset($file['filename'])) {
            $output = [
                'found' => $found,
                'result' => $type . "exc/" . $file['filename'] . " done" . "\r\n Updated: " . $nb_updated . "\r\n Inserted: " . $nb_inserted . "\r\n Deleted: " . count($to_delete) . "\r\n" . "\r\n" . "Time Taken: " . gmdate('H:i:s', $time_taken)
            ];
        }

       return $output;


        //die($type."exc/".$file['filename']." done");
    }


/*Process out V2 - Node */

public static function processOutFileV2($data, $type_conv, $file,$store)
    {
        $time_start = microtime(true);

        $updated_nextpost = [];
        $to_upsert = [];
        $to_delete = [];
        $hist_to_insert = [];

        $conveyor_lines = [];
        $last_conveyor_statut = [];
        $nb_inserted = 0;
        $nb_updated = 0;
        $nb_deleted = 0;
        $removed_assembly = [];
        $removed_at_no_bag = [];
        $items_to_reset = [];

        $storecodes = Conveyor::getAllStoreCodes(true);

        $store_storage_postes = [
            'AT'=>36,'CH1'=>49,'CH2'=>61,'MB1'=>52,'NH1'=>55,'NH2'=>64,'BS1'=>58,'BS2'=>67
        ];

        $store_offloaded_postes = [
            'AT'=>41,'CH1'=>50,'CH2'=>62,'MB1'=>53,'NH1'=>56,'NH2'=>65,'BS1'=>59,'BS2'=>68
        ];

        $store_nextpost = [
            49, //CH1
            61, //CH2
            52, //MB1
            55, //NH1
            64, //NH2
            58, //BS1
			49,61,52,55,64,58,67
        ];

        $excluded_post_at = array_merge($store_nextpost);
        //Ajout shelves
        for($i=69; $i<=76; $i++){
            $excluded_post_at[] = $i;
        }

        $excluded_post_at[] = 81;
        $excluded_post_at[] = 82;
        $excluded_post_at[] = 28; //FULFILLED
        $excluded_post_at[] = 29; //ON VAN
        $excluded_post_at[] = 39; //DELIVERED
		/*
		foreach($excluded_post_at as $k=>$v){
			if($v==41 || $v==36){
				unset($excluded_post_at[$k]);
			}
		}
        */


        $found = true;


        if (!empty($data)) {
            $slots = [];

            $conveyor_slots = DB::table('conveyors')
                ->get();

            if (!empty($conveyor_slots)) {
                foreach ($conveyor_slots as $k => $v) {
                    $key = str_replace('-', '', $v->ORDER) . "_" . trim($v->GARCODE);
                    $slots[$key] = [
                        'Slot' => trim($v->Slot),
                        'updated_at' => $v->updated_at,
                    ];
                }
            }


            $orders = [];

            $to_delete_status = ['U','M'];

            $other_stores_format = Conveyor::getStoreWithOtherFormats();

            foreach ($data as $k => $v) {

                $deleted_from_memory = false;
                $removed_from_assembly = false;

                $type = $v->conv_type;
                $order = $v->order;
                $tracking = $v->tracking;
                $store_code = $v->storecode;
                $status = $v->statut;
                $slot = $v->slot;
                $type_item = $v->typeitem;
                $arm = $v->arm;
                $first_two_tracking_char = $v->first_two_tracking_char;


                if(in_array($store,$other_stores_format)){
                    $order_file = trim(substr($v, 0, 9));
                    $order = substr($order_file,0,2)."-".substr($order_file,2,6);

                    $store_code = "";
                }



               if($first_two_tracking_char=='*A' && $status=='A'){
                   $tracking = $v->cancelled_tracking;
                   $deleted_from_memory = true;
               }

               if(!$deleted_from_memory && $type=='map' && in_array($status,['A','D'])){
                   $removed_from_assembly = true;
               }

                $suborderid = "";
                $item = false;
                $inv = false;
                $item_inv = false;

                if($order !='' && $tracking!=''){
                    $found = true;

                    $item_inv = DB::table('infoitems')
                        ->select('infoitems.id AS item_id','infoInvoice.id AS invoice_id')
                        ->join('infoInvoice','infoitems.InvoiceID','infoInvoice.InvoiceID')
                        ->where('infoitems.ItemTrackingKey',$tracking)
                        ->where('infoInvoice.NumInvoice',$order)
                        ->latest('infoInvoice.id')
                        ->first();

                    if($item_inv){
                        $item = DB::table('infoitems')->where('id',$item_inv->item_id)->first();

                        $inv = DB::table('infoInvoice')->where('id',$item_inv->invoice_id)->first();
                    }else{

                       // echo $tracking."\t".$order."\t".$type_item."<br/>";


                        if(!$deleted_from_memory && !$removed_from_assembly) {
                            $found = false;
                        }
                    }


                }else{
                    $item = DB::table('infoitems')->where('ItemTrackingKey', $tracking)->latest('id_items')->first();

                    $storename = (isset($storecodes[$store_code])?$storecodes[$store_code]:'');
                    $inv = DB::table('infoInvoice')
                        ->where('NumInvoice', $order)
                        ->where('StoreName',$storename)
                        ->latest('id')->first();
                }

                if ($item) {
                    $suborderid = $item->SubOrderID;
                }
                if ($inv) {
                    $suborderid = $inv->SubOrderID;
                }

                if ($slot != '' && $item) {
                    $conv_detail = new \stdClass();
                    $conv_detail->is_conveyor = 1;
                    $conv_detail->type = $type;
                    $conv_detail->status = $status;
                    $conv_detail->slot = $slot;
                    $conv_detail->arm = $arm;

                    $item_nextpost = 0;
                    $item_status = "";

                    if(!$deleted_from_memory && !$removed_from_assembly) {
                        if ($type == 'map') {
                            if($store=='AT'){
                                if($arm=='S06'){
                                    $item_nextpost = 42;
                                    $item_status = "OFFLOADED";
                                }else {
                                    $item_nextpost = 24;
                                    $item_status = "ASSEMBLING";
                                }
                            }
                        } elseif ($type == 'bam') {
                            if($status =='L') {
                                $item_nextpost = (isset($store_storage_postes[$store])?$store_storage_postes[$store]:36);
                                $item_status = ($store=='AT'?"READY":"READY IN STORE");
                            }elseif (in_array($status,['U','M'])){
                                $item_nextpost = (isset($store_offloaded_postes[$store])?$store_offloaded_postes[$store]:41);
                            }

                        }
                    }

                    if(($store=='AT' && $item->nextpost==29) || ($store !='AT' && $item->nextpost==28) || ($store=='AT' && in_array($item->nextpost,[30,31,32]) && $item_nextpost==42) || ($store=='AT' && in_array($item->nextpost,$excluded_post_at)) || ($store=='AT' && in_array($item->Status,['ON VAN','READY IN STORE','DELIVERED','FULFILLED']))) {
                        //Do nothing
                    }else{
                        if ($status == 'L') {
                            Delivery::propagateItemStatus($item->id, $item_status, $item_nextpost);
                        } elseif ($type == 'bam' && in_array($status, ['U', 'M'])) {
                            /*
                            if($store=='AT' && (in_array($item->nextpost,$store_nextpost) || $item->Status=='READY IN STORE')){

                            }else{
                                Delivery::propagateItemStatus($item->id, 'OFFLOADED', (isset($store_offloaded_postes[$store])?$store_offloaded_postes[$store]:41));
                            }*/
                            if($store=='AT' && $item->nextpost==36){ //Only Atelier Storage
                                //Delivery::propagateItemStatus($item->id, 'OFFLOADED',41);
                            }
                        }

                        else if (!$deleted_from_memory && $item) {
                            Item::setConveyorHistory($item, $item_nextpost, 1, json_encode($conv_detail));
                        }
                    }

                    if($item && $tracking !='' && in_array($status,['L','U']) && (in_array($item_nextpost,$store_storage_postes) || in_array($item_nextpost,$store_offloaded_postes)) && $item->id_items !=416185){
                        Item::setConveyorHistory($item, $item_nextpost, 1, json_encode($conv_detail));
                    }


                    //Enlever les champs conveyor si c'est plus sur conveyor

                    if(in_array($status,$to_delete_status) && $item){
                        DB::table('infoitems')
                        ->where('id', $item->id)
                        ->update([
                            'convoyerINOUT' => 0,
                            'conveyorSlot' => '',
                            'conveyorType' => '',
                        ]);
                    }
                }


                //$log_stamp = substr($v, -14);
                $date_created = ($store !='AT'?$file['created_at']:date('Y-m-d H:i:s')); //date('Y-m-d H:i:s',strtotime($log_stamp));

                if ($order != '') {
                    $orders[$order] = [];
                }

                if($item){
                    $updated_item = DB::table('infoitems')->where('id',$item->id)->first();
                    $updated_nextpost[$item->ItemTrackingKey] = $updated_item->nextpost;
                }

                $detail = [
                    'ORDER' => $order,
                    'NGARM' => $v->ngarm,
                    'GARMENT' => $v->garment,
                    'GARCODE' => $tracking,
                    //'GARDESC'=>'',//substr($v,44,30),
                    'PERC' => $v->perc,
                    'HUNG' => $v->hung,
                    'type' => 'OUT',
                    'conveyorType' => $type,
                    'Status' => $status,
                    'Slot' => $slot,
                    'ARM' => $arm,
                    'labelinfo' => $v->labelinfo,
                    //'DT_DUE'=>substr($v,777,14),
                    'created_at' => $date_created,
                    'updated_at' => $date_created,
                    'line_info' => '', //trim(substr($v,0,-14)),
                    'id_items' => ($item ? $item->id_items : 0),
                    'id_invoice' => 0, //($item?$item->id_invoice:($inv?$inv->id_invoice:0)),
                    'SubOrderID' => ($item ? $item->SubOrderID : ($inv ? $inv->SubOrderID : '')),
                    'InvoiceID' => ($item ? $item->InvoiceID : ($inv ? $inv->InvoiceID : '')),
                    'StoreCode'=>$store_code,
                    'ConvStoreCode'=>$store
                ];

                $detail2 = $detail;

                unset($detail2['StoreCode']);

                if ($order != '') {
                    $hist_to_insert[] = $detail2;
                }

                if ($found  && in_array($status, $to_delete_status)) {
                    $to_delete[$tracking] = [
                        'status'=>$status,
                        'order'=>$order,
                        'StoreCode'=>$store_code,
                    ];
                }elseif(!$deleted_from_memory  && $type=='map' && in_array($status,['A','D'])){
                    //Removed from assembly using IN with map_status=D
                    $removed_assembly[] = [
                        'status'=>$status,
                        'order'=>$order,
                        'StoreCode'=>$store_code,
                    ];

                    if($item_inv){
                        if(!in_array($item_inv->item_id,$items_to_reset)){
                            array_push($items_to_reset,$item_inv->item_id);
                        }
                    }
                }

                if ($order != '' && $tracking != '' && $slot != '' && $status == 'L') {
                    if($type=='map' && $arm=='S06'){
                        $removed_at_no_bag[] = [
                          'GARCODE'=>$tracking,
                          'ORDER'=>$order,
                          'StoreCode'=>$store_code,
                        ];

                        if($item_inv){
                            if(!in_array($item_inv->item_id,$items_to_reset)){
                                array_push($items_to_reset,$item_inv->item_id);
                            }
                        }
                    }else {
                        $to_upsert[$k] = $detail;
                    }
                }
            }

            foreach ($to_upsert as $k => $v) {
                $o = $v['ORDER'];
                $t = $v['GARCODE'];
                if (isset($orders[$o])) {
                    if (!in_array($t, $orders[$o])) {
                        array_push($orders[$o], $t);
                    }
                }
            }

            foreach ($to_upsert as $k => $v) {
                if ($v['Slot'] != '') {
                    $v['isMulti'] = 0;
                    $v['GARCODE'] = '';

                    $perc = $v['PERC'];
                    if ($perc < 100 || $perc > 100) {
                        $v['isMulti'] = 1;
                    }
                    //$conveyor_lines[$k] = $v;
                }
            }

            $cur_keys = [];
            $conv_fields = ['Slot', 'Status', 'ORDER', 'GARCODE', 'GARDESC', 'PERC', 'created_at', 'updated_at', 'id_invoice', 'id_items', 'SubOrderID','StoreCode','InvoiceID'];
            foreach ($to_upsert as $k => $v) {
                foreach ($v as $i => $x) {
                    if (!in_array($i, $conv_fields)) {
                        unset($to_upsert[$k][$i]);
                    }
                }
                $to_upsert[$k]['Name'] = $type;
            }

            $deleted_status = ['P', 'D', 'U', 'A'];

            $to_insert = [];
            $inserted = [];
            $updated = [];

            $tracking_to_delete = [];

            if (!empty($to_delete)) {
                foreach($to_delete as $key=>$detail){
                    $item = DB::table('infoitems')
                            ->select('infoitems.id', 'conveyors.Name', 'infoitems.SubOrderID')
                            ->join('conveyors', 'infoitems.InvoiceID', 'conveyors.InvoiceID')
                            ->where('GARCODE', $key)
                            ->where('ORDER',$detail['order'])
                            //->where('StoreCode',$detail['StoreCode'])
                            ->first();

                    $suborder_offloaded = [];

                    if($item) {
                        $tracking_to_delete[] = $item->id;
                        //Delivery::propagateItemStatus($item->id, 'OFFLOADED');
                        /*if ($item->Name == 'bam') {
                            $suborder_offloaded[] = $item->InvoiceID;
                        }*/
                    }
                        //TO UNCOMMENT
                        DB::table('conveyors')
                            ->where('GARCODE', $key)
                            ->where('ORDER',$detail['order'])
                            ->where('StoreCode',$detail['StoreCode'])
                            ->delete();
                    //}
                }
            }


            $file_detail = [];

            if (!empty($to_upsert)) {
                $file_detail = $to_upsert;

                foreach($file_detail as $k=>$v){
                    $nb_inserted += DB::table('conveyors')->updateOrInsert(
                        [
                            'ORDER'=>$v['ORDER'],
                            'GARCODE'=>$v['GARCODE'],
                            'StoreCode'=>$v['StoreCode'],
                            'ConvStoreCode'=>$store
                        ],$v
                    );
                }

                foreach ($to_upsert as $k => $v) {
                    $new_key =  str_replace('-', '', $v['ORDER']) . "_" . trim($v['GARCODE']);

                    $np = 0;
                    if(isset($updated_nextpost[$v['GARCODE']])){
                        $np = $updated_nextpost[$v['GARCODE']];
                    }

                    if(in_array($np,$store_storage_postes) || $np==24){

                        DB::table('infoitems')
                            ->where('itemTrackingKey', $v['GARCODE'])
                            ->where('InvoiceID',$v['InvoiceID'])
                            ->update([
                                'Actif'=>1,
                                'convoyerINOUT' => 1,
                                'conveyorSlot' => $v['Slot'],
                                'conveyorType' => $type,
                            ]);
                    }

                }
            }

            //Test to uncomment
            if (!empty($tracking_to_delete)) {
                DB::table('infoitems')
                    ->whereIn('id', $tracking_to_delete)
                    ->update([
                        'convoyerINOUT' => 0,
                        'conveyorSlot' => '',
                        'conveyorType' => '',
                    ]);
            }

            if(!empty($removed_assembly)){
                foreach($removed_assembly as $k=>$v){
                    DB::table('conveyors')
                        ->where('Name','map')
                        ->where('ORDER',$v['order'])
                        ->where('StoreCode',$v['StoreCode'])
                        ->where('ConvStoreCode',$store)
                        ->delete();
                }
            }

            if(!empty($removed_at_no_bag)){
                foreach($removed_at_no_bag as $k=>$v){
                    DB::table('conveyors')
                        ->where('GARCODE',$v['GARCODE'])
                        ->where('ORDER',$v['ORDER'])
                        ->where('StoreCode',$v['StoreCode'])
                        ->where('ConvStoreCode',$store)
                        ->delete();
                }
            }

            DB::table('conveyorhistories')->insert($hist_to_insert);
        }

        $time_end = microtime(true);
        $time_taken = $time_end - $time_start;


        $output['found'] = $found;
        $output['processed'] =(count($data)>0?true:false);
        $output['result'] = "Updated: " . $nb_updated . ", Inserted: " . $nb_inserted . ", Deleted: " . count($to_delete) . "\r\n" . "Time Taken: " . gmdate('H:i:s', $time_taken);


       return $output;


        //die($type."exc/".$file['filename']." done");
    }



    public static function getStoreCodeByName($storename)
    {
        $store_codes = [
            'ATELIER' => '.ATAT',
            'CHELSEA' => '.CHCH',
            'MARYLEBONE' => '.MBMB',
            'NOTTING HILL' => '.NHNH',
            'SOUTH KEN' => '.BSBS',
        ];

        return (isset($store_codes[$storename]) ? $store_codes[$storename] : '');
    }

    public static function getAllStoreCodes($reverse=false){
        $store_codes = [
            'ATELIER' => '.ATAT',
            'CHELSEA' => '.CHCH',
            'MARYLEBONE' => '.MBMB',
            'NOTTING HILL' => '.NHNH',
            'SOUTH KEN' => '.BSBS',
        ];

        $arr = [];

        if($reverse){
            foreach($store_codes as $k=>$v){
                $arr[$v] = $k;
            }
        }else{
            $arr = $store_codes;
        }

        return $arr;
    }

    public static function checkConveyorIn()
    {
        $conv_in = DB::table('conveyor_op_in')
            ->where('sent', 0)
            ->where('map_status', 'R')
            ->where('bam_status', 'L')
            ->get();

        $group_by_order = [];
        $order_to_split = [];


        if (count($conv_in) > 0) {
            foreach ($conv_in as $k => $v) {
                $group_by_order[$v->SubOrderID][] = $v;
            }

            foreach ($group_by_order as $k => $v) {
                $nb_items = 0;
                $perc = 0;

                foreach ($v as $i => $x) {
                    $nb_items += 1;
                    $perc += $x->PERC;
                }

                if ($nb_items > 1 && $perc > 100) {
                    foreach ($v as $i => $x) {
                        $order_to_split[$k][] = [
                            'GARCODE' => $x->GARCODE,
                            'ItemID' => $x->ItemID,
                            'PERC' => $x->PERC,
                        ];
                    }
                }
            }



            if (!empty($order_to_split)) {
                foreach ($order_to_split as $k => $v) {
                    Conveyor::reArrangeSubOrder($k, $v);
                }
            }
        }

        //print_r($order_to_split);

    }

    public static function reArrangeSubOrder($old_suborder_id, $items_arr)
    {
        $total_perc = 0;

        foreach ($items_arr as $k => $v) {
            $total_perc += $v['PERC'];
        }

        $nb_bags = ceil($total_perc / 100);


        $filtered_arr = [];


        $perc = 0;
        $arr = [];

        for ($i = 0; $i < count($items_arr); $i++) {
            $item_id = $items_arr[$i]['ItemID'];
            $perc += $items_arr[$i]['PERC'];
            $arr[] = $item_id;

            if ($perc >= 100) {
                $filtered_arr[] = $arr;
                $perc = 0;
                $arr = [];
                continue;
            }
        }


        if (!empty($filtered_arr)) {
            foreach ($filtered_arr[0] as $k => $v) {
                if ($k > 0) {
                    Conveyor::splitSubOrder($old_suborder_id, $v);
                }
            }
        }

        die();
    }

    public static function splitSubOrder($old_suborder_id, $ItemID)
    {


        $inv = DB::table('infoInvoice')
            ->where('SubOrderID', $old_suborder_id)
            ->first();

        $id_old_inv = $inv->id;

        if ($inv) {
            $new_inv = $inv;
            $new_inv->id = '';
            $new_inv->id_invoice = 0;
            $new_inv->SubOrderID = '';
            $new_inv->InvoiceID = '';
            $new_inv->date_add = date('Y-m-d H:i:s');
            $new_inv->NumInvoice = '';

            $inv_to_insert = (array) $new_inv;

            $id_new_inv = DB::table('infoInvoice')->insertGetId($inv_to_insert);
            $new_invoice = DB::table('infoInvoice')->where('id', $id_new_inv)->first();

            $id_split = DB::table('split')->insertGetId([
                'old_suborder_id' => $id_old_inv,
                'suborder_id' => $id_new_inv,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $inv_part1 =  date('m');
            $inv_part2 = 900000 + $id_split;
            $num_invoice = $inv_part1 . "-" . $inv_part2;

            DB::table('infoInvoice')->where('id', $id_new_inv)
                ->update(['NumInvoice' => $num_invoice]);




            DB::table('infoitems')->where('ItemID', $ItemID)
                ->update(['SubOrderID' => $new_invoice->SubOrderID]);

            DB::table('conveyor_op_in')
                ->where('ItemID', $ItemID)
                ->where('SubOrderID', $old_suborder_id)
                ->where('sent', 0)
                ->update([
                    'SubOrderID' => $new_invoice->SubOrderID,
                    'ORDER' => $num_invoice,
                ]);
        }
    }

    public static function reArrangePickupList($sort_array, $lines)
    {
        $final_lines = [];

        foreach ($sort_array as $key) {
            foreach ($lines as $i) {
                if ($i['ORDER'] == $key) {
                    $final_lines[] = $i;
                }
            }
        }

        return $final_lines;
    }

    public static function getErrMap()
    {
        $conv_in = DB::table('conveyor_op_in')
            ->where('map_status', 'R')
            //->whereDate('created_at','2021-10-13')
            ->get();

        $details = [];
        $grouped_by_tracking = [];

        foreach ($conv_in as $k => $v) {
            $grouped_by_tracking[$v->GARCODE][] = $v->id;
        }

        $latest_ids = [];
        foreach ($grouped_by_tracking as $k => $v) {
            $latest_ids[] = $v[0];
        }

        $latest_conv_in = DB::table('conveyor_op_in')->whereIn('id', $latest_ids)->get();

        foreach ($latest_conv_in as $k => $v) {
            $details[] = [
                'id' => $v->id,
                'GARCODE' => $v->GARCODE,
                'ORDER' => $v->ORDER,
            ];
        }


        $err_recorded = [];
        $conveyor_items = [];
        $on_conveyor = DB::table('conveyors')->get();
        $conveyor_combi = [];
        $found = [];

        foreach ($on_conveyor as $k => $v) {
            $key = $v->GARCODE;
            $conveyor_items[] = $key;
            $conveyor_combi[$v->GARCODE] = $v->ORDER;
        }

        foreach ($details as $k => $v) {
            $key1 = $v['GARCODE'];

            if (in_array($key1, $conveyor_items)) {
                $found[] = $v;
            }
        }

        foreach ($found as $k => $v) {
            if (isset($conveyor_combi[$v['GARCODE']])) {
                if ($conveyor_combi[$v['GARCODE']] != $v['ORDER']) {
                    $v['OLD_ORDER'] = $conveyor_combi[$v['GARCODE']];
                    $err_recorded[] = $v;
                }
            }
        }

        $err_map = [];
        if (!empty($err_recorded)) {
            foreach ($err_recorded as $k => $v) {
                $err_map[$v['ORDER']] = $v['OLD_ORDER'];
            }
        }
    }

    public static function addLineInFromTrackingArray($garcodes)
    {
        $on_conveyor = [];
        $count_lines = 0;
        $count_lines_cancel = 0;
        $new_records_to_insert = [];
        $not_on_conveyor = [];

        if (!empty($garcodes)) {
            foreach ($garcodes as $k => $v) {
                $item = DB::table('infoitems')->where('ItemTrackingKey', $v)->orderBy('id', 'DESC')->first();
                if ($item) {
                    $inv = DB::table('infoInvoice')->where('SubOrderID', $item->SubOrderID)->first();
                    if ($inv) {
                        $has_line_on_conveyor = DB::table('conveyors')->where('GARCODE', $v)->first();

                        if ($has_line_on_conveyor) {
                            $on_conveyor[] = $has_line_on_conveyor;
                        } else {
                            $not_on_conveyor[] = $v;

                            $line_not_sent = DB::table('conveyor_op_in')
                                ->where('GARCODE', $v)
                                ->where('ORDER', $inv->NumInvoice)
                                ->where('sent', 0)
                                ->first();

                            if (!$line_not_sent) {
                                $obj = new \stdClass();
                                $obj->SubOrderID = $item->SubOrderID;
                                $obj->id_items = $item->id_items;
                                $obj->ItemID = $item->ItemID;
                                $obj->InvoiceID = $item->InvoiceID;
                                $obj->GarDesc = $item->typeitem;
                                $obj->PERC = $item->PERC;
                                $obj->ORDER = $inv->NumInvoice;
                                $obj->GARCODE = $item->ItemTrackingKey;
                                $obj->map_status = "R";
                                $obj->bam_status = "L";
                                $obj->Customer = $inv->Client;
                                $obj->Price = $item->priceTotal;
                                $obj->ARM = '';
                                $obj->OrderType = 0;
                                $obj->NoBag = $item->NoBag;
                                $obj->CustomerID = $inv->CustomerID;
                                $obj->created_at = date('Y-m-d H:i:s');

                                $new_records_to_insert[] = (array) $obj;
                            }
                        }
                    }
                }
            }


            if (!empty($new_records_to_insert)) {
                $count_lines = count($new_records_to_insert);
                DB::table('conveyor_op_in')->insert($new_records_to_insert);
            }
        }

        return $count_lines;
    }

    public static function readOutFiles($type,$limit){
        $path = storage_path('AT/'.$type.'exc/OUT/done');
        $imported_path = $path."/imported";
        $to_move = [];

        if(!is_dir($imported_path)){
            mkdir($imported_path,0777);
        }

        $files = glob($path.'/*.txt');

        $data = [];
        $to_move_old = [];

        /*
         *  usort($files, function ($a, $b) {
            return basename($a) > basename($b);
        });
         * */

        date_default_timezone_set('Europe/London');
        $stamp_less_five_minutes = (int) strtotime('now') - 3600;

        if(!empty($files)){
            foreach($files as $k=>$v) {
                if($k < $limit) {
                    if (($handle = fopen($v, "r")) !== FALSE) {
                        $filename =  basename($v);

                        $date = substr($filename,4,4)."-".substr($filename,8,2)."-".substr($filename,10,2);

                        $heure = substr($filename,12,2).":".substr($filename,14,2).":".substr($filename,16,2);

                        $stamp = $date." ".$heure;

                        $filestamp = strtotime($stamp);

                        if($date >= '2021-10-12') {
                            if($filestamp < $stamp_less_five_minutes){

                                while ($line = fgets($handle)) {
                                    $status = substr($line, 78, 1);
                                    $slot = trim(substr($line, 81, 5));
                                    $order = trim(substr($line, 5, 9));
                                    $tracking = trim(substr($line, 24, 8));
                                    $store_code = trim(substr($line, 0, 5));


                                    $data[] = [
                                        'status' => $status,
                                        'slot' => $slot,
                                        'order' => $order,
                                        'tracking' => $tracking,
                                        'StoreCode' => $store_code,
                                        'filename' => basename($v),
                                        'type' => $type,
                                        'created_at' => $stamp,//date('Y-m-d H:i:s'),
                                    ];
                                }
                                $to_move[] = $filename;
                            }
                        }else{
                            $to_move_old[] = $filename;
                        }
                        fclose($handle);
                    }
                }
            }
        }

        $inserted = false;
        if(!empty($data)){

            foreach($data as $k=>$v){
                $inserted = DB::table('out_file_details')->insert($v);
            }
            //Insert first

            if($inserted) {
                foreach ($to_move as $k => $v) {
                    rename($path . "/" . $v, $imported_path . "/" . $v);
                }
            }
        }

        if(!empty($to_move_old)) {
            foreach ($to_move_old as $k => $v) {
                rename($path . "/" . $v, $imported_path . "/" . $v);
            }
        }
    }


    public static function getStoreWithOtherFormats(){
        return ['BS1','BS2','CH1','CH2','NH1','NH2','MB1'];
    }

    public static function getStoreConveyorNames(){
        return [
            'ATELIER'=>[
                ['code'=>'AT','name'=>'ATELIER']
            ],
            'CHELSEA'=>[
                ['code'=>'CH1','name'=>'LOWER'],
                ['code'=>'CH2','name'=>'UPPER'],
            ],
            'MARYLEBONE'=>[
                ['code'=>'MB1','name'=>'Storage1']
            ],
            'NOTTING HILL'=>[
                ['code'=>'NH1','name'=>'Upper'],
                ['code'=>'NH2','name'=>'Lower'],
            ],
            'SOUTH KEN'=>[
                ['code'=>'BS1','name'=>'Storage 1'],
                ['code'=>'BS2','name'=>'Storage 2'],
            ],
        ];
    }

    public static function getStorageConvPostes(){
        return [
            'AT'=>36,
            'CH1'=>49, //Lower
            'CH2'=>61, //Upper
            'MB1'=>52,
            'NH1'=>55, //Upper,
            'NH2'=>64, //Lower,
            'BS1'=>58, //Lower
        ];

    }
}
