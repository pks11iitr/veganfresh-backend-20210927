<?php

namespace App\Services\SMS;

class ConnectExpress
{
    public static function send($mobile, $message){

         $url='https://connectexpress.in/api/v3/index.php?method=sms&api_key=c20fd0b15d378b35500211e9d2ee9df1ff0206b9&to='.$mobile.'&sender=VEGANF&message='.urlencode($message).'&format=php';


        
        $ch = curl_init();
        $timeout = 5;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $data = curl_exec($ch);

        curl_close($ch);

        return $data;
    }
}
