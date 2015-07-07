<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resource\Bundle\UserBundle\Document\Search;
use Symfony\Component\HttpFoundation\Response;
use Resource\Bundle\UserBundle\Service\Elastic;
use Resource\Bundle\UserBundle\Service\Rabbit;

class SearchController extends Controller {


    public function aroundAction($lat,$lng,$email) {
        $dm = $this->get('doctrine_mongodb')
            ->getManager();
         $user = $dm->getRepository('ResourceUserBundle:User')
            ->findOneByEmail($email);
        $ret = '[]';
        if(isset($user)) {
            $hashtags = array();
            $search = $dm->getRepository('ResourceUserBundle:Search')
                ->findOneByUserid($user->getId());
                if(isset($search)) {
                    foreach($search->getHashtags() as $hashtag) {
                        $hashtags[] = $hashtag;
                    }
                }
            $elastic = new Elastic();
            //$elastic->mapping();
            $ret = $elastic->geoSearch($hashtags,$lat,$lng,'1km',$user->getId());
            
        }
        return (new Response())->setContent($ret);
    }    
    /*
     *Action to get all Hashtags with optional filter
     */
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

    /*
     * Get the hashtag of the user
     */
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

    /*
     * Update my hashtages, parameter is an array of hashtag.
     */
    public function updateSearchAction($hashtags = array(), $lat , $lon , $distance = '20km'){
        $hashtags = json_decode($hashtags,true);
        $user = $this->get('security.context')
            ->getToken()
            ->getUser();
        $dm = $this->get('doctrine_mongodb')
            ->getManager();
        $repository = $dm->getDocumentCollection('ResourceUserBundle:Search');
        $repository->remove(array('userid'=>$user->getId()));
        
        $search = new Search();
        $search->setUserid($user->getId());
        if(is_array($hashtags)){
            foreach($hashtags as $hashtag) {
                $search->addHashtag($hashtag);
            }
        }
        $dm->persist($search);
        $dm->flush();
        //percolate the search : key words arround my location in a geographic area.
        $elastic = new Elastic();
        $jsonToPercolate =  $elastic->geoSearchJson($hashtags,$lat,$lon, $distance, $user->getId() );
        // todo implement the percolation 
        // to read : how use headers to send more data, like searchid.
        // to do : refacto, especially elastic class. 

        $rabbit = new Rabbit();
        $rabbit->send($jsonToPercolate, 'percolate', array('search_id'=>$search->getId())); 
    
        return (new Response())->setContent(json_encode(array('success'=>true)));
    }
}
