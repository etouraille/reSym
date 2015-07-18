<?php
namespace Resource\Bundle\UserBundle\Service;

use Resource\Bundle\UserBundle\Document\Category as Cat;

class Category {

    protected $odm;
    
    public function __construct($odm) {
        $this->odm = $odm;
    }

    public function add($tag, $category ){
        $categories = $this->odm
            ->getManager()
            ->getRepository('ResourceUserBundle:Category')
            ->findBy(array('tag'=>$tag, 'category'=>strtolower($category)));

        if(isset($categories) && count($categories) > 0) {
            return false;
        } else {
            $cat = new Cat();
            $cat->setTag($tag);
            $cat->setCategory(strtolower($category));
            $dm = $this->odm->getManager();
            $dm->persist($cat);
            $dm->flush();
            return true;
        }
    
    }

}
