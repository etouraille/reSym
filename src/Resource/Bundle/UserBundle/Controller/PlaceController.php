<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resource\Bundle\UserBundle\Document\Search;
use Symfony\Component\HttpFoundation\Response;
use Resource\Bundle\UserBundle\Service\Elastic\PlaceAround;
use Resource\Bundle\UserBundle\Service\ReverseGeoCoding;
use Resource\Bundle\UserBundle\Document\Place;
class PlaceController extends Controller {


    public function searchAction( $lat='45.7677957',$lng='4.8731638') {
        $elastic = new Elastic();
        //$elastic->mapping();
        $ret = $elastic->placeAround($lat,$lng,'1km');
        return (new Response())->setContent($ret);
    }

    public function tagAction($lat, $lng , $tag , $address ) {
        $user = $this->get('security.context')->getToken()->getUser();
        $dm = $this->get('doctrine_mongodb')->getManager();
        $place = new Place($lat, $lng);
        $place->setTag($tag)
            ->setCreatedBy($user->getId())
            ->setAddress($address);
        $dm->persist($place);
        $dm->flush();
        $placeId = $place->getId();
        $rabbit = new \Resource\Bundle\UserBundle\Service\Rabbit();
        $rabbit->send(
            \Resource\Bundle\UserBundle\Service\JSONify::toString($place),
            'index', 
            array( 'type'=>'place', 'id'=>$place->getId())
        );
        return (new Response())->setContent(json_encode(array('success'=>true,'id'=>$placeId)));
    
    }
    public function addressAction($lat='45.7677957',$lng='4.8731638') {
        $address = ReverseGeoCoding::address($lat, $lng);
        return (new Response())->setContent(json_encode(array('address'=>$address)));
}
}
