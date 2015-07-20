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
     * @MongoDB\String
    */
    private $userid;

    /**
     * @MongoDB\Hash
    */
    private $hashtags = array();

    /**
    * distance in meters    
    * @MongoDB\String
    */
    private $distance;

    /**    
    * @MongoDB\String
    */
    private $address;

    /**
     * @MongoDB\EmbedOne(targetDocument="Geo")
     **/

    private $geo;

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

    public function getDistance() {
        return $this->distance;
    }

    public function getAddress() {
        return $this->address;
    }

    public function getGeo() {
        return $this->geo;
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

    public function setAddress($address) {
        $this->address = $address;
        return $this;
    
    }

    public function setGeo(Geo $geo) {
        $this->geo = $geo;
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

    public function setDistance($distance) {
        $this->distance = $distance;
        return $this;
    }

}
