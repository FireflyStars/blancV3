<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Twilio\Rest\Client;

class Notification extends Model
{


    public  function send()
    {
        if ($this->Sent == 0 && $this->JustSent==0) {// not already sent
            $this->JustSent=1;
            $this->save();
            $email_address = $this->Email;
            $phone = $this->Phone;
            if (setting('sendgrid.demo_email') == 1) {
                $email_address = setting('sendgrid.sendgrid_demo_email');
            }
            if (setting('sendgrid.demo_sms') == 1) {
                $phone = setting('sendgrid.sendgrid_demo_phone');
            }
            $NOTTIFICATION_TEMPLATE = NotificationTemplate::all()->where('template','=',$this->Template)->first();

            if ($NOTTIFICATION_TEMPLATE!=null&&$this->TypeNotification == "EMAIL"&&$NOTTIFICATION_TEMPLATE->TypeNotification=="EMAIL") { // send email with sendgrid

                $email = new \SendGrid\Mail\Mail();
             //   $email->setFrom(setting('sendgrid.sendgrid_from_email'), setting('sendgrid.sendgrid_from'));
                $email->setFrom($NOTTIFICATION_TEMPLATE->fromemail, setting('sendgrid.sendgrid_from'));
                $email->addTo($email_address);
                $email->setTemplateId($NOTTIFICATION_TEMPLATE->template_id);
                $email->addDynamicTemplateDatas(json_decode($this->Parametres));

                $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
                try {
                    $response = $sendgrid->send($email);

                    if ($response->statusCode() == "202") {
                        $this->Sent = 1;
                        $header = $response->headers();
                        $this->Sent_on = date('Y-m-d H:i:s', strtotime(str_replace('Date: ', '', $header[2])));
                        $this->Error_msg='';

                        //force send email to customer in mode demo
                        if($this->Force==1&&setting('sendgrid.demo_email') == 1){
                            $email_Force = new \SendGrid\Mail\Mail();
                            //$email_Force->setFrom(setting('sendgrid.sendgrid_from_email'), setting('sendgrid.sendgrid_from'));
                            $email_Force->setFrom($NOTTIFICATION_TEMPLATE->fromemail, setting('sendgrid.sendgrid_from'));
                            $email_Force->addTo($this->Email);
                            $email_Force->setTemplateId($NOTTIFICATION_TEMPLATE->template_id);
                            $email_Force->addDynamicTemplateDatas(json_decode($this->Parametres));

                            try {
                                $response_Force = $sendgrid->send($email_Force);

                                if ($response_Force->statusCode() == "202") {

                                } else {
                                    $this->Error_msg ='Force: '.json_encode($response_Force->headers());
                                }
                            } catch (Exception $e) {
                                $this->Error_msg = 'Caught exception Force: ' . $e->getMessage();

                            }
                        }
                    } else {
                        $this->Error_msg = json_encode($response->headers());
                    }

                } catch (Exception $e) {
                    $this->Error_msg = 'Caught exception: ' . $e->getMessage();

                }
                $this->save();
            }

            if ($NOTTIFICATION_TEMPLATE!=null&&$this->TypeNotification == "SMS"&&$NOTTIFICATION_TEMPLATE->TypeNotification=="SMS") { // send sms with twillio

                $PARAMS=json_decode($this->Parametres);
                $SMS_TEXT=$NOTTIFICATION_TEMPLATE->SMS_TEXT;
                foreach ($PARAMS as $key=> $PARAM){
                    if(in_array($key,array("link_to_order_tracking","book_delivery","link_to_payment_details","link_to_opening_hours_Google","url"))){
                        $SMS_TEXT = str_replace('{' . $key . '}', BitLy::shortenUrl($PARAM), $SMS_TEXT);
                    }else {
                        $SMS_TEXT = str_replace('{' . $key . '}', $PARAM, $SMS_TEXT);
                    }
                }
                $account_sid = getenv('TWILIO_ACCOUNT_SID');
                $auth_token = getenv('TWILIO_AUTH_TOKEN');

                $twilio_number = getenv('TWILIO_PHONE_NUMBER');
                if (setting('sendgrid.demo_sms') == 1) {
                    $account_sid = getenv('TWILIO_ACCOUNT_SID_TEST');
                    $auth_token = getenv('TWILIO_AUTH_TOKEN_TEST');
                    $twilio_number = getenv('TWILIO_PHONE_NUMBER_TEST');
                }
                $client = new Client($account_sid, $auth_token);
                try {
                    $x=$client->messages->create(
                        $phone,
                        array(
                            'from' => $twilio_number,
                            'body' => $SMS_TEXT
                        )
                    );
                    $x=$x->toArray();

                    $this->Sent = 1;

                    $this->Sent_on = date('Y-m-d H:i:s',strtotime($x['dateCreated']->format('Y-m-d H:i:s')));
                    $this->Error_msg=null;
                    //force send sms to customer in mode demo
                    if($this->Force==1&&setting('sendgrid.demo_sms') == 1){

                        try {
                            $x=$client->messages->create(
                                $this->Phone,
                                array(
                                    'from' => $twilio_number,
                                    'body' => $SMS_TEXT
                                )
                            );
                        } catch (Exception $e) {
                            $this->Error_msg = 'Caught exception Force: ' . $e->getMessage();

                        }
                    }

                }catch (\Exception $exception){

                    $this->Error_msg=$exception->getMessage();
                }
                $this->save();
            }
        }
    }
}
