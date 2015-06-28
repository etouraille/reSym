<?php
namespace Resource\Bundle\UserBundle\Service;

use Resource\Bundle\UserBundle\Service\Curl;

class ReverseGeoCoding {
    
    public static function address($lat, $lng ) {
        $apiKey = 'AIzaSyA_HxDJkh0VheLPqFU6pwMOtqg-cwrSSI4';
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$lng."&key=".$apiKey;
        $json = Curl::get($url,'GET','');
        $data = json_decode($json, true);
        $ret = '';
        if(isset($data['results'][0]['formatted_address'])) {
            $ret = $data['results'][0]['formatted_address'];   
        }
        return $ret;
    
    }
}
