<?php
namespace Resource\Bundle\UserBundle\Document;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
/**
 *  @MongoDB\Document
 */

class Category
{
    /**
     * @MongoDB\Id
    */    
    private $id;

    /**
     * @MongoDB\String
    */
    private $tag;

    /**
     * @MongoDB\String
    */
    private $category;
    
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
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
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
}
