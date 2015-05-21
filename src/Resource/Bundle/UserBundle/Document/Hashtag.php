<?php
namespace Resource\Bundle\UserBundle\Document;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
/**
 *  @MongoDB\Document
 */

class Hashtag
{
    /**
     * @MongoDB\Id
    */    
    private $id;

   /**
     * @MongoDB\String
    */
    private $hastag;

   

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

     /**
     * @inheritDoc
     */
    public function getHastag()
    {
        return $this->hastag;
    }



    /**
     * Set hastag
     *
     * @param string $hastag
     * @return self
     */
    public function setHastag($hastag)
    {
        $this->hastag = $hastag;
        return $this;
    }

}
