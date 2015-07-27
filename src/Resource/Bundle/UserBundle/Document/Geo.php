<?php
namespace Resource\Bundle\UserBundle\Document;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
/**
 *  @MongoDB\Document
 */

class Geo
{
    /**
     * @JMS\Exclude
     * @MongoDB\Id
    */    
    private $id;

   /**
     * @MongoDB\Float
    */
    private $lat;

    /**
     * @MongoDB\Float
    */
    private $lon;

    public function __construct($lat,$lon)
    {
        $this->lat = $lat;
        $this->lon = $lon;
    }

    /**
     * @inheritDoc
     */
    public function getLat()
    {
        return $this->lat;
    }

     /**
     * @inheritDoc
     */
    public function getLon()
    {
        return $this->lon;
    }



   /**
     * Set lat
     *
     * @param string $lat
     * @return self
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
        return $this;
    }

     /**
     * Set lon
     *
     * @param string $lon
     * @return self
     */
    public function setLon($lon)
    {
        $this->lon = $lon;
        return $this;
    }

}
