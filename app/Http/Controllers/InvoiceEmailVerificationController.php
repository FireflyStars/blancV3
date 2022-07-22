<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceEmailVerificationController extends Controller
{
    //
    public $connection;

    public function index(Request $request){


        //init base spot
        //voir /config/database.php

        $this->connection = DB::connection('mysqlblancspot');

        $newemail=$request->get('newemail');
        $CustomerID=$request->get('CustomerID');
        if($newemail!==null&&$CustomerID!==null){
            $this->connection->table('suggested_emails')->where('CustomerID','=',$CustomerID)->update([
                'email'=>$newemail
            ]);
            return redirect()->route('customer-email-verification',['suggestedemail' => 'ok']);
        }
        $data=array();
        // parcourt de invoicest
        $invoicest=$this->connection->table('invoicest');
        $invoicest->leftJoin('suggested_emails', 'suggested_emails.CustomerID', '=', 'invoicest.CustomerID');
        $invoicest->leftJoin('email',  function($join){
            $join->on('email.email', '=', 'suggested_emails.email');
        });
        $invoicest->where('CustomerEmailAddress','=','');
        $invoicest->where('date_add','>','2020-03-01');
        $invoicest->groupBy('invoicest.CustomerID');
        $data=$invoicest->get(array('invoicest.CustomerID','CustomerName','InvoiceKey','suggested_emails.email','email.error'));

        $toupdate=array();
        foreach ($data as $k=> $d){
            if($d->email==""){
                $d->email=uniqid()."@noemail.com";
                $toupdate[]=array('CustomerID'=>$d->CustomerID,'email'=>$d->email);
            }
            if($d->error=="OK"){
                unset($data[$k]);
            }
        }

        $this->connection->table('suggested_emails')->insert($toupdate);
        return view('invoiceemailverification.index',compact('data'));
    }


    public function checkbademail(Request $request){

        //init base spot
        //voir /config/database.php

        $this->connection = DB::connection('mysqlblancspot');
        $data=array();
        // parcourt de invoicest
        $invoicest=$this->connection->table('email');
        $invoicest->join('invoicest','email.email','=','invoicest.CustomerEmailAddress');
        $invoicest->where('email.datelastorder','>','2020-03-20');
        $invoicest->where('email.error','<>','OK');
        $invoicest->where('email.error','<>','NOK');
        $invoicest->where('email.email', 'NOT LIKE', '%noemail.com%');
        $invoicest->where('email.email', 'NOT LIKE', '%test.com%');
        $invoicest->groupBy('email.email');
        $data=$invoicest->get(array('email.email','email.error','email.CustomerID','invoicest.CustomerName','invoicest.InvoiceKey'));



        return view('invoiceemailverification.checkbademail',compact('data'));

    }

    public function postprocess(Request $request){
        $newEmail=$request->get('newemail');
        $previousEmail=$request->get('previousEmail');
        $customerID=$request->get('customerid');
        if($newEmail==''){
         return redirect()->route('bad-customer-email-edit')->with('status', 'Email cannot be empty for customer ID:'.$customerID)
             ->with('err_cus_id', $customerID);
        }
        $this->connection = DB::connection('mysqlblancspot');
        //$this->connection->table('email')->where('email', $previousEmail)->update(['email' => $newEmail]);
        $this->connection->table('email')->where('email', $previousEmail)->update(['error' => "NOK"]);
        $this->connection->table('invoicest')->where('CustomerEmailAddress', $previousEmail)->update(['CustomerEmailAddress' => $newEmail]);
        return redirect('https://blancspot.vpc-direct-service.com/import-2-client-jour-v6-recupEmail.php?email='.$newEmail);
    }

    public function customerWithoutAddress(Request $request){
        
        $data=array();
        $customerwithoutaddress=DB::table('infoInvoice')->where('id_address','=',0)->where('date_add','>','2020-08-11')->select(['Client', DB::raw('group_concat( DISTINCT  NumInvoice) as NumInvoice'),'id_customer'])->groupBy('id_customer')->orderBy('Client');
        $data=$customerwithoutaddress->get();
        return view('invoiceemailverification.customerwithoutaddress',compact('data'));
    }


    public function restoreCustomer(Request $request){
        return view('invoiceemailverification.restorecustomer');
    }
}
