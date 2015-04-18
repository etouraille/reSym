<?php
namespace Resouce\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
/**
 *  @MongoDB\Document
 *  @MongoDB\Document(repositoryClass="Acme\StoreBundle\Repository\UserRepository")
 */

class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @MongoDB\Id
    */    
    private $id;

    /**
     * @MongoDB\String
    */
    private $username;

    /**
     * @MongoDB\String
    */
    private $salt;
    
    /**
     * @MongoDB\String
    */
    private $password;

    /**
     * @MongoDB\String
    */
    private $email;

    /**
     * @MongoDB\Boolean
    */
    private $isActive;

    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
    // We probably don't need to persit user for the api : learning step.
   /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
        ) = unserialize($serialized);
    }
    // Not necessary for the moment
    public function isEqualTo(UserInterface $user)
    {
            return $this->username === $user->getUsername();
    }
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }
    // Not necessary for the moment
}
