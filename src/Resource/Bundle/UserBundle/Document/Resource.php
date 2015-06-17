<?php
namespace Resource\Bundle\UserBundle\Document;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
/**
 *  @MongoDB\Document
 */

class Resource
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
    private $content;
    
    /**
     * @MongoDB\String
    */
    private $picture;

    /**
     * @MongoDB\EmbedOne(targetDocument="Geo")
     **/
    private $geo;

    /**
    * @MongoDB\Field(type="basic_date_time_type");
    **/
    private $startDate;

    /**
    * @MongoDB\Field(type="basic_date_time_type");
    **/
    private $endDate;

   /**
    * @MongoDB\Boolean
    */
    private $available;

    public function __construct($lat,$lon)
    {
        $geo = new Geo($lat,$lon);
        $this->setGeo($geo);
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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function getGeo()
    {
        return $this->geo;
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
     * Get startDate
     *
     * @return startDate $startDate
     */
    public function getSartDate()
    {
        return $this->startDate;
    }

    /**
     * Get endDate
     *
     * @return endDate $endDate
     */
    public function getEndDate()
    {
        return $this->endDate;
    }



    /**
     * Set userid
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
     * Get picture
     *
     * @return string $picture
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set userid
     *
     * @param string $picture
     * @return self
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
        return $this;
    }


    /**
     * Set content
     *
     * @param string $content
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;
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
     * Set startDate
     *
     * @param string $startDate 
     * format any : datetime, string, integer
     * @return self
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }


     /**
     * Set endDate
     *
     * @param string $endDate 
     * format any : datetime, string, integer
     * @return self
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }
}
