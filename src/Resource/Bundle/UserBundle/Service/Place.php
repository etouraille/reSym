<?php
namespace Resource\Bundle\UserBundle\Service;

use Resource\Bundle\UserBundle\Document\Resource;

class Place {

    protected $odm;
    protected $jsonify;
    protected $rabbit;

    public function __construct($odm,$jsonify,$rabbit) {
        
        $this->odm = $odm;
        $this->jsonify = $jsonify;
        $this->rabbit = $rabbit;
    
    }

    public function associateResourceToPlace(Resource $resource, $placeId ) {
        $place = $this->odm
            ->getManager()
            ->getRepository('ResourceUserBundle:Place')
            ->findOneById($placeId);

        if(isset($place)) {
            $place->addResource($resource);
            $dm = $this->odm->getManager();
            $dm->persist($place);
            $dm->flush();
            $this->rabbit->send(
                $this->jsonify->serialize($place,'json'),
                'update', 
                array(
                    'id'=>$place->getId(),
                    'type'=>'place'
                )
            );
            return true;
        } else {
            return false;
        }
    }
}
