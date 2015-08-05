<?php
namespace Resource\Bundle\SecurityBundle\Document;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
/**
 *  @MongoDB\Document
 */

class WebToken
{
    /**
     * @MongoDB\Id
    */    
    private $id;

    /**
     * @MongoDB\Integer : timestamp unix
    */
    private $creationDate;

    /**
    * @MongoDB\Integer
    * 0 for no validity limit
    */
    private $validity;
    
    /**
     * @MongoDB\String
    */
    private $userId;
    /* Get id
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
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function getValidity() {
        return $this->validity;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setCreationDate($timestamp) {
        $this->creationDate = $timestamp;
        return $this;
    }

    public function setValidity ( $validity ) {
        $this->validity = $validity;
        return $this;
    } 

    public function userId ($userId ) {
        $this->userId = $userId;
        return $this;
    }
}
