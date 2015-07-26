<?php
namespace Resource\Bundle\ListenerBundle\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class User
{

    protected $passwordEncoder;
    
   
    public function __construct(PasswordEncoderInterface $passwordEncoder)
    {

        $this->passwordEncoder = $passwordEncoder;
    
    }
    
    public function prePersist($args)
    {
        $user = $args->getDocument();
        if(get_class($user) === "Resource\Bundle\UserBundle\Document\User" ) {
            $args->getDocument()->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user->getPassword(),
                    $user->getSalt()
                )
            );
        }
    }
}
