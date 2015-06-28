<?php
namespace Resource\Bundle\UserBundle\Document;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
/**
 *  @MongoDB\Document
 */

class Place
{
    /**
     * @MongoDB\Id
    */    
    private $id;

    /**
     * @MongoDB\String
    */
    private $createdBy;

    /**
     * @MongoDB\String
    */
    private $tag;
    
    /**
     * @MongoDB\String
    */
    private $address;

    /**
     * @MongoDB\EmbedOne(targetDocument="Geo")
     **/
    private $geo;

    public function __construct($lat,$lon)
    {
        $geo = new Geo($lat,$lon);
        $this->setGeo($geo);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @inheritDoc
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @inheritDoc
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Geo
     *
     * @return startDate $startDate
     */
    public function getGeo()
    {
        return $this->geo;
    }

    /**
     * Set createdBy
     *
     * @param string $createdBy
     * @return self
     */
    public function setCreatedBy($userId)
    {
        $this->createdBy = $userId;
        return $this;
    }
   
    /**
     * Set tag
     *
     * @param string $tag
     * @return self
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * Set geo
     *
     * @param string $geo
     * @return self
     */
    public function setGeo($geo)
    {
        $this->geo = $geo;
        return $this;
    }

     /**
     * Set address
     *
     * @param string $address
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

}
