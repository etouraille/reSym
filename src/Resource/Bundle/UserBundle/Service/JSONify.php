<?php
namespace Resource\Bundle\UserBundle\Service;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;



class JSONify {

    public static function toString($object){
        
        //serialize
        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        $data = $serializer->serialize($object,'json');
        
        return $data;
    }

}
