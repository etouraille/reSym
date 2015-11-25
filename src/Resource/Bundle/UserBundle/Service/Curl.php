<?php
namespace Resource\Bundle\UserBundle\Service;

class Curl {

    public static function get($url,$method, $data=null, $timeout = 10){
         $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if(isset($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response  = curl_exec($ch);

        if( $response == false ) 
        {
            throw new \Exception(curl_error($ch).curl_errno($ch));
        }

        curl_close($ch);
        return $response;
   }



}
