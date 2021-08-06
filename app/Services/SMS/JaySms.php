<?php

namespace App\Services\SMS;

class JaySms
{

    public static function send($mobile, $message, $dlt_te_id){

        $url="http://sms.jayinegroup.com/api/pushsms.php?username=HOUSEGOODS&password=49722&sender=HGOODS&&numbers=".$mobile."&message=".urlencode($message)."&unicode=true&dlt_template_id=$dlt_te_id";

        //return true;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        //var_dump($err);die;
        if ($err) {
            return false;
        } else {
            return true;
        }
    }
}
