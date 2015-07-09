<?php
namespace Resource\Bundle\UserBundle\Document;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
/**
*  @MongoDB\Document
*  @MongoDB\Document(repositoryClass="UserRepository")
*/

class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @MongoDB\Id( options={"unique"="true"} )
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

    private $password2;

    /**
     * @MongoDB\String
    */
    private $email;

    /**
     * @MongoDB\String
    */
    private $androidNotificationId=0;


    /**
     * @MongoDB\Boolean
    */
    private $isActive;

    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->id = new \MongoId();
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
     * @inheritdoc
     */
    public function getpassword()
    {
        return $this->password;
    }

    /**
     * @inheritdoc
     */
    public function setAndroidNotificationId( $regId )
    {
        $this->androidNotificationId = $regId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAndroidNotificationId()
    {
        return $this->androidNotificationId;
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
     * Set username
     *
     * @param string $username
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return self
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function setPassword2($password)
    {
        $this->password2 = $password;
        return $this;
    }

    public function isPasswordEquals(){
        return $this->password === $this->password2;
    }


    /**
     * Set email
     *
     * @param string $email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return self
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean $isActive
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}
