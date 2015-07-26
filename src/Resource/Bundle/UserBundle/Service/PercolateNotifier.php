<?php
namespace Resource\Bundle\UserBundle\Service;

class PercolateNotifier {
    
    protected $odm;
    protected $rabbit;
    
    public function __construct($odm, $rabbit) {
        $this->odm = $odm;
        $this->rabbit = $rabbit;
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
        $this->rabbit->sendPushMessage(
            $user,
            'Y en a pas loin', 
            array(
                'type'=>'around',
                'id'=>$docId
            )
        );
    }
}
