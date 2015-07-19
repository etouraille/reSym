<?php
namespace Resource\Bundle\UserBundle\Document;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
/**
 *  @MongoDB\Document
 */

class ResetToken
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
     * @MongoDB\String
    */
    private $token;

    /**
     * @MongoDB\String
    */
    private $timestamp;

    /**
     * @MongoDB\Bool
    */
    private $used;



    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    public function getUserid()
    {
        return $this->userid;
    }

    public function getToken() {
        return $this->token;
    }

    public function getTimestamp(){
        return $this->timestamp;
    }

    public function getUsed(){
        return $this->used;
    }
    public function setUserid($userid ) {
        $this->userid = $userid;
        return $this;
    }


    public function setToken($token ) {
        $this->token = $token;
        return $this;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
        return $this;
    }
    public function setUsed($used) {
        $this->used = $used; 
        return $this;
        
    }
}
