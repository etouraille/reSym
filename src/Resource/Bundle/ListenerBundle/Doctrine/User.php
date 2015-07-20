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
            echo 'in cond';
            $args->getDocument()->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user->getPassword(),
                    $user->getSalt()
                )
            );
        }
    }

    public function preUpdate($eventArgs) {
        $user = $eventArgs->getDocument();
        if(get_class($user) === "Resource\Bundle\UserBundle\Document\User" ) {
            $eventArgs->getDocument()->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user->getPassword(),
                    $user->getSalt()
                )
            );
            $dm   = $eventArgs->getDocumentManager();
            $uow  = $dm->getUnitOfWork();
            $meta = $dm->getClassMetadata(get_class($user));
            $uow->recomputeSingleDocumentChangeSet($meta, $user); 
        }
    }
}
