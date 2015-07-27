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

    /**
    * @MongoDB\EmbedMany(
    *     strategy="addToSet",
    *     targetDocument="Resource"
    * )
    **/
    protected $resources = array();   

    public function __construct($lat=0,$lon=0)
    {
        $geo = new Geo($lat,$lon);
        $this->setGeo($geo);
    }

    public function initWithJson($json){
        $ret = false;
        if(is_array($data = json_decode($json,true))){
            $ret = true;
            isset($data['geo']['lat']) ? $lat = $data['geo']['lat'] : $ret = false;
            isset($data['geo']['lon']) ? $lon = $data['geo']['lon'] : $ret = false;
            isset($data['address']) ? $address = $data['address'] : $ret = false;
            isset($data['tag']) ? $tag = $data['tag'] : $ret = false;
            isset($data['id'])? $id = $data['id'] : $ret = false;
            if($ret) {
                $this->setGeo(new Geo($lat,$lon));
                $this->setTag($tag);
                $this->setAddress($address);
                $this->id = $id;
            }
        }
        return $ret;
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

    public function addResource(Resource $resource) {
        $this->resources[] = $resource;
        return $this;
    }

    public function getResources(){
        return $this->resources;
    }


    /**
     * Remove resource
     *
     * @param Resource\Bundle\UserBundle\Document\Resource $resource
     */
    public function removeResource(\Resource\Bundle\UserBundle\Document\Resource $resource)
    {
        $this->resources->removeElement($resource);
    }
}
