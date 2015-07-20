<?php
namespace Resource\Bundle\UserBundle\Document;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
/**
 *  @MongoDB\Document
 */

class Conversation
{
    /**
     * @MongoDB\Id
    */    
    private $id;

   /**
     * @MongoDB\String
    */
    private $from;

    /**
     * @MongoDB\String
    */
    private $to;

    /**
    * @MongoDB\EmbedMany(
    *     strategy="set",
    *     targetDocument="Message"
    * )
    **/
    protected $messages=array();

     /**
     * @MongoDB\String
    */
    private $timestamp;

    public function __construct()
    {
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set from
     *
     * @param string $from
     * @return self
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * Get from
     *
     * @return string $from
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param string $to
     * @return self
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * Get to
     *
     * @return string $to
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Add message
     *
     * @param Resource\Bundle\UserBundle\Document\Message $message
     */
    public function addMessage(\Resource\Bundle\UserBundle\Document\Message $message)
    {
        $this->messages[] = $message;
    }

    /**
     * Remove message
     *
     * @param Resource\Bundle\UserBundle\Document\Message $message
     */
    public function removeMessage(\Resource\Bundle\UserBundle\Document\Message $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection $messages
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set timestamp
     *
     * @param string $timestamp
     * @return self
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * Get timestamp
     *
     * @return string $timestamp
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
