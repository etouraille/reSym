<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resource\Bundle\UserBundle\Document\Resource;
use Symfony\Component\HttpFoundation\Response;
use Resource\Bundle\UserBundle\Service\Elastic;
class SearchController extends Controller {

    public function hashtagAction($filter = null){

        $repository = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('ResourceUserBundle:Hashtag');
    
        if($filter){
            $regExp = '/'.$filter.'/';
            $hashtags = $repository->find(array('hashtag'=>$rexExp));
        }
        $hashtags = $repository->find(array('hashtag'=>'/.*/'));
        $ret = array();
        if(is_array($hashtags)){
            foreach($hashtags as $hastag){
                $ret[] = array('content'=>$hashtag->getHashtag());
            }
        }
        return (new Response())->setContent(json_encode($ret));
    }

    public function myhashtagAction(){
        $user = $this->get('security.context')
            ->getToken()
            ->getUser();
        $search = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('ResourceUserBundle:Search')
            ->findOneByUserid($user->getId());
        $ret = array();
        if($search){
            foreach($search->getHashtags() as $hashtag){
                $ret[] = array('content'=>$hashtag);
            }
        }
        return (new Response())->setContent(json_encode($ret));
    }
}
