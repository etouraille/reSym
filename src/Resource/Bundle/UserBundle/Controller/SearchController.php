<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resource\Bundle\UserBundle\Document\Search;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends Controller {

    public function hashtagAction($filter = null){

        $repository = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('ResourceUserBundle:Hashtag');
    
        if($filter){
            $regExp = '/'.$filter.'/';
            $hashtags = $repository->find(array('hashtag'=>$rexExp));
        }
        $hashtags = $repository->findAll();
        $ret = array();
        if(is_array($hashtags)){
            foreach($hashtags as $hashtag){
                $ret[] = $hashtag->getHashtag();
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
                $ret[] = $hashtag;
            }
        }
        return (new Response())->setContent(json_encode($ret));
    }

    public function updateSearchAction($hashtags = array()){
        $user = $this->get('security.context')
            ->getToken()
            ->getUser();
        $dm = $this->get('doctrine_mongodb')
            ->getManager();
        $repository = $dm->getRepository('ResourceUserBundle:Search');
        $repository->delete(array('userid'=>$user->getId()));
        
        $search = new Search();
        $search->setUserid($user->getId);
        foreach($hashtags as $hashtag){
            $search->addHashtag($hashtag);
        }
        $dm->persist($search);
        $dm->flush();
        return (new Response())->setContent(json_encode(array('success'=>true)));
    }
}
