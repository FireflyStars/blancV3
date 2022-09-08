<?php

namespace App\Http\Controllers;

//use App\BitLy;
use App\Notification;
use Illuminate\Http\Request;


class NotificationController extends Controller
    {
        //
        /**
         * @param $template String Nom du template sendgrid
         * @param $params array les parametres de replacement pour le template
         * @param $email String email address
         * @param $phone String phone number to send sms
         * @param $type_notification String BOTH |EMAIL
         * @param $send_now boolean Try to send notification immediately
         * @param null int $infoOrder_id
         */
        public static function Notify($email,$phone,$email_template='',$sms_template='',$params=[],$send_now=false,$infoOrder_id=0,$customeruuid=false){
                $sent = false;
                if(trim($email_template)==''&&trim($sms_template)==''){
                    throw new \Exception('At least one template should be provided');
                }

                if($email_template!='') {

                    $notification = new Notification();
                    $notification->Template = $email_template;
                    $notification->Parametres = json_encode($params);
                    $notification->Email = $email;
                    $notification->Phone = '';
                    $notification->TypeNotification ='EMAIL';
                    $notification->InfoOrder_id = $infoOrder_id;
                    $notification->CustomerID = ($customeruuid?$customeruuid:"");
                    $notification->save();
                    $sent = true;
                    if($send_now){
                        $notification->send();
                        $sent = true;
                    }
                }
            if($sms_template!='') {

                $notification = new Notification();
                $notification->Template = $sms_template;
                $notification->Parametres = json_encode($params);
                $notification->Email = '';
                $notification->Phone = $phone;
                $notification->TypeNotification ='SMS';
                $notification->InfoOrder_id = $infoOrder_id;
                $notification->save();
                if($send_now){
                    $notification->send();
                }
            }

            return $sent;
        }

        public static function sendAll(){
                $notifications=Notification::all()->where('Sent','=',0);
                foreach ($notifications as $notification){
                    $notification->send();
                }
        }

        public function testNotification(Request $request){


          // NotificationController::sendAll();
          //  NotificationController::Notify('reyewat@vpc-gestion.com','+23057520441','test_rey','xxxx',['first_name'=>'Reyewat','last_name'=>'Bissessur'],false);

//echo BitLy::shortenUrl('https://stackoverflow.com/questions/ask');
        }

    }
