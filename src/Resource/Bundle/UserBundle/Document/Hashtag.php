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
    private $hashtag;

   

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
    public function getHashtag()
    {
        return $this->hashtag;
    }



    /**
     * Set hastag
     *
     * @param string $hastag
     * @return self
     */
    public function setHashtag($hastag)
    {
        $this->hashtag = $hastag;
        return $this;
    }

}
