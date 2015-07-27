<?php
namespace Resource\Bundle\UserBundle\Document;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Resource\Bundle\UserBundle\Annotation\BasicDateTime;
use JMS\Serializer\Annotation as JMS;
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
    private $category;
    
    /**
     * @MongoDB\String
    */
    private $message;
    
    /**
     * @MongoDB\String
    */
    private $picture;

    /**
     * @MongoDB\EmbedOne(targetDocument="Geo")
     **/
    private $geo;

    /**
     * @JMS\Exclude
     * @MongoDB\EmbedOne(targetDocument="Place")
     **/
    private $place;

    /**
     * @MongoDB\String
     **/
    private $address;

    /**
     *@JMS\SerializedName("startDate")
    * @MongoDB\Field(type="basic_date_time_type");
    **/
    public $startDate;

    /**
     *@JMS\SerializedName("endDate")
    * @MongoDB\Field(type="basic_date_time_type");
    **/
    private $endDate;

   /**
    * @MongoDB\Boolean
    */
    private $reserved;

    /**
     * @JMS\SerializedName("reservedBy")
    * @MongoDB\String
    */
    private $reservedBy;

    public function __construct($lat,$lon)
    {
        $geo = new Geo($lat,$lon);
        $this->setGeo($geo);
        $this->reserved = false;
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
     * @inheritDoc
     */
    public function getPlace()
    {
        return $this->place;
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
     * Get startDate
     *
     * @return startDate $startDate
     */
    /*public function getStartDate()
    {
        return $this->startDate;
    }*/

    /**
     ** {@inheritDoc}
     **/
    public function getStartDate()
    {
        return $this->convertMongoDateInString(
            $this->startDate
        );
    }

    protected function convertMongoDateInString($date) {
        $ret = $date;
        if(is_object($date) && get_class($date) === 'MongoDate') {
            $ret = date('Ymd\THisP',$date->sec);
        }
        return $ret;
    }

    /**
     * Get endDate
     *
     * @return endDate $endDate
     */
    public function getEndDate()
    {
        return $this->convertMongoDateInString(
            $this->endDate
        );
    }

    /**
     * Get reserved
     *
     * @return reserved $reserved
     */
    public function getReserved()
    {
        return $this->reserved;
    }

    /**
     * Get reservedBy
     *
     * @return reservedBy $reservedBy
     */
    public function getReservedBy()
    {
        return $this->reservedBy;
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

    public function getCategory() {
        return $this->category;
    }

    public function setCategory($category) {
        $this->category = $category;
        return $this;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage( $message) {
        $this->message = $message;
        return $this;
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
     * Set place
     *
     * @param string $place
     * @return self
     */
    public function setPlace($place)
    {
        $this->place = $place;
        return $this;
    }

    /**
     * Set place
     *
     * @param string $address
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;
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

    public function reserve($userId)
    {
    
        $this->reserved = true;
        $this->reservedBy = $userId;

    
    }

    public function release() {
    
        $this->reserved = false;
        $this->reservedBy = null;
    
    }

    public function setReservedBy( $userId ){
    
        $this->reservedBy = $userId;
    
    }

    /**
     * Set reserved
     *
     * @param boolean $reserved
     * @return self
     */
    public function setReserved($reserved)
    {
        $this->reserved = $reserved;
        return $this;
    }
}
