<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class AssociateTagController extends Controller {

    public function addTag($tagName, $userId) {
        // each new added tag must be associate to all the tag of the previous user
        $tags = $this->get('doctrine_mongodb')->getRespository()
            ->findBy(array('userId'=>$userId,'hashtag'=>$hashtag));
        foreach($tags as $tag) {
            $Elastic->associate($tagName,$tag);
        }
    }
}
