<?php
namespace Resource\Bundle\UserBundle\Service;

class PercolateNotifier {
    
    protected $odm;
    protected $notification;
    
    public function __construct($odm, $notification) {
        $this->odm = $odm;
        $this->notification = $notification;
    }

    public function process($json, $document) {
        $data = json_decode($json, true);
        $matches = $data['matches'];
        foreach($matches as $match ) {
            $idSearch = $match['_id'];
            $search = $this->odm
                ->getManager()
                ->getRepository('ResourceUserBundle:Search')
                ->findOneById($idSearch);
            if(isset($search)) {
                $userId = $search->getUserid();
                $user = $this->odm->getManager()
                    ->getRepository('ResourceUserBundle:User')
                    ->findOneById($userId);
                if(isset($user)) {
                    $this->notify($user, $search, $document);
                }
            }
        }
    
    }

    protected function notify($user, $search, $document ) {
        //todo
        //test user os
        //addapt notification service to various os
        $doc = json_decode($document, true);
        $docId = $doc['id'];
        $this->notification->send(
            $user,
            $message = 'Some Newspaper is available around',
            array(
                'id'=>$docId, 
                'type'=>'around'
            ) 
        );
    }

}
