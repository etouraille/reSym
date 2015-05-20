<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resource\Bundle\UserBundle\Document\Resource;
use Symfony\Component\HttpFoundation\Response;
use Resource\Bundle\UserBundle\Service\Elastic;
class ResourceController extends Controller
{
    public function addAction($content='cool',$lat='0.0001', $lon='0.0001') {
            $success = true;
            //$user = $this->get('security.context')->getToken()->getUser();
            $resource = new Resource($lat,$lon);
            
            //$resource->setUserid($user->getId());
            $resource->setUserid(123);
            $resource->setContent($content);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($resource);
            $dm->flush();
            $ret = array(
                'success' => $success,
                'id'=>$resource->getId()
            );
            $rabbit = new \Resource\Bundle\UserBundle\Service\Rabbit();
            $rabbit->send(\Resource\Bundle\UserBundle\Service\JSONify::toString($resource));
            $response = new Response();
            $response->setContent(json_encode($ret));
            return $response;
    }

    public function searchAction($content='',$lat='45.7677957',$lon='4.8731638',$distance = '1km'){
        $elastic = new Elastic();
        $ret = $elastic->geoSearch($content,$lat,$lon,$distance);
        //$ret = $elastic->delete();
        //$ret = $elastic->mapping();
        return (new Response())->setContent($ret);
    }

    public function timeAction(){
        $ret = json_encode(array('microtime'=>time().'000'));
        return (new Response())->setContent($ret);
    } 
}
