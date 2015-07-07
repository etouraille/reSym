<?php 

namespace Resource\Bundle\UserBundle\Service;

use Resource\Bundle\UserBundle\Service\ReverseGeoCoding;

class DeferResourceAddressSetting {

    public static function defer($dm, $resourceJson) {
        
        $resource = json_decode($resourceJson, true);
        $resourceId = $resource['id'];
        
        $resourceObject = $dm->getRespository('ResourceUserBundle:Resource')
            ->findOnById($resourceId);
        
        if(isset( $resourceObject) ) {
        
            $lat = $resourceObject->getGeo()->getLat();
            $lon = $resourceObject->getGeo()->getLon();
            $address = ReverseGeoCoding::address($lat, $lon);
            $resourceObject->setAddress($address);
            $dm->persist($resourceObject);
            $dm->flush();
            $resource['address'] = $address;
            
        }
        return json_encode($resource);
    }

}
