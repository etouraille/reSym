<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resource\Bundle\UserBundle\Document\Resource;
use Symfony\Component\HttpFoundation\Response;
use Resource\Bundle\UserBundle\Service\Elastic;
use Resource\Bundle\UserBundle\Service\Date;
use Resource\Bundle\UserBundle\Document\Place;

class ResourceController extends Controller
{
    public function addAction($tag='cool',$lat='0.0001', $lon='0.0001',$picture = '',
    $endInterval = 0, $place = null, $message = '', $category = 'the world is a vampire') {
            $success = true;
            $user = $this->get('security.context')->getToken()->getUser();
            $resource = new Resource($lat,$lon);
        
            $resource->setUserid($user->getId());
            //$resource->setUserid(123);
            $resource->setContent($tag);
            $resource->setPicture($picture);
            $resource->setStartDate($now = (new Date())->now());
            if($endInterval){
                $resource->setEndDate((new Date())->inMinutes($endInterval,$now));
            }
            $thereIsAPlace = false;
            if($json = $place) {
                $place = new Place();
                if($thereIsAPlace = $place->initWithJson($json)) {
                    $resource->setPlace($place);
                }
            }
            $resource->setMessage($message);
            $resource->setCategory($category);

            $categoryService = $this->get('category');
            $categoryService->add($tag, $category);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $hashtag = $dm->getRepository('ResourceUserBundle:Hashtag')->findOneByHashtag($tag);
        
            if(!isset($hashtag)) {
                $hash = new \Resource\Bundle\UserBundle\Document\Hashtag();
                $hash->setHashtag($tag);
                $dm->persist($hash);
            }
            $dm->persist($resource);
            $dm->flush();
            $ret = array(
                'success' => $success,
                'id'=>$resource->getId()
            );
            // todo : to much call to the database in the service
            // we could differ to later
            if($thereIsAPlace) {
                $this->get('place')->associateResourceToPlace($resource,$place->getId());
            }    
            $rabbit = new \Resource\Bundle\UserBundle\Service\Rabbit();
            $rabbit->send(
                $json = $this->get('jms_serializer')->serialize($resource,'json'),
                'index', 
                array(
                    'type'=>'hashtag',
                    'id'=> $resource->getId()
                )
            );
            $response = new Response();
            $response->setContent(json_encode($ret));
            return $response;
    }

    public function searchAction($content='',$lat='45.7677957',$lon='4.8731638',$distance = '10km' ) {
        
        //$user = $this->get('security.context')->getToken()->getUser();
        //$userId = $user->getId();
        $userId = 123;
        $elastic = new Elastic();
        //$ret = $elastic->mapping();
        $ret = $elastic->geoSearch($content,$lat,$lon,$distance, $userId);
        //$ret = $elastic->delete();
        return (new Response())->setContent($ret);
    }

    public function getAction($id=123) {
        $resource = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('ResourceUserBundle:Resource')
            ->findOneById($id);
        $ret = array('success'=>false);
        if(isset($resource)) {
             $ret = array('success'=>'true', 'content'=>$resource);
        }
        return  (new Response())->setContent(
            \Resource\Bundle\UserBundle\Service\JSONify::toString($ret)
        );
        
    }

    public function reserveAction( $resourceId ) {

        $dm = $this->get('doctrine_mongodb')
            ->getManager();
        $resource = $dm->getRepository('ResourceUserBundle:Resource')
            ->findOneById($resourceId);
        $user = $this->get('security.context')
            ->getToken()
            ->getUser();
        $ret = array('success'=>false);
        if(isset($resource) && isset($user)) {
            $resource->reserve($user->getId());
            $dm->persist($resource);
            $dm->flush();
            $rabbit = new \Resource\Bundle\UserBundle\Service\Rabbit();
            $rabbit->send(
                \Resource\Bundle\UserBundle\Service\JSONify::toString($resource),
                'update',
                array(
                    'id'=>$resource->getId(),
                    'type'=>'hashtag'
                )
            );
            $ret['success']=true;
        }
        return (new Response())->setContent(json_encode($ret));
    }

    public function reservedAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $dm = $this->get('doctrine_mongodb')->getManager();
        $myReservedResources = $dm->createQueryBuilder('ResourceUserBundle:Resource')
            ->field('reservedBy')
            ->equals($user->getId())
            ->getQuery()
            ->execute();
        $success = false;
        if($myReservedResources) $success = true;
        $ret = array('success'=>$success , 'resources' => $myReservedResources);
        
        return (new Response())->setContent(
            \Resource\Bundle\UserBundle\Service\JSONify::toString($ret)
        );

    }

    public function releaseAction($resourceId=123)
    {
        $dm = $this->get('doctrine_mongodb')
            ->getManager();
        $resource = $dm->getRepository('ResourceUserBundle:Resource')
            ->findOneById($resourceId);
        $success = false;
        if(isset($resource)) {
            $resource->release();
            $dm->persist($resource);
            $dm->flush();
            $rabbit = new \Resource\Bundle\UserBundle\Service\Rabbit();
            $rabbit->send(
                \Resource\Bundle\UserBundle\Service\JSONify::toString($resource),
                'update',
                array(
                    'id'=>$resource->getId(),
                    'type'=>'hashtag'
                )
            );
            $success = true;
        
        }
        $ret = array('success'=>$success);
        return (new Response())->setContent(json_encode($ret));
    }
    
}
