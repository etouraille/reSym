<?php
namespace Resource\Bundle\UserBundle\Document;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
/**
 *  @MongoDB\Document
 */

class Search
{
    /**
     * @MongoDB\Id
    */    
    private $id;

   /**
     * @MongoDB\id
    */
    private $userid;

    /**
     * @MongoDB\Hash
    */
    private $hashtags = array();



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
    public function getUserid()
    {
        return $this->userid;
    }


     /**
     * @inheritDoc
     */
    public function getHashtags()
    {
        return $this->hashtags;
    }

    /**
     * set userid
     *
     * @param string $userid
     * @return self
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;
        return $this;
    }


    /**
     * add hastag
     *
     * @param string $hastag
     * @return self
     */
    public function addHashtag($hastag)
    {
        $this->hashtags[] = $hastag;
        return $this;
    }

}
