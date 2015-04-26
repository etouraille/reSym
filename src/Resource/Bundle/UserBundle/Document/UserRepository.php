<?php
namespace Resource\Bundle\UserBundle\Document;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ORM\NoResultException;

class UserRepository extends DocumentRepository implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')->find();
         $q = $q->addOr(
                $q->expr()->field('username')->equals($username),
                $q->expr()->field('password')->equals($username)
             )
            ->getQuery();

        try {
            // La méthode Query::getSingleResult() lance une exception
            // s'il n'y a pas d'entrée correspondante aux critères
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            throw new UsernameNotFoundException(sprintf('Unable to find an active admin AcmeUserBundle:User object identified by "%s".', $username), 0, $e);
        }

        return $user;
    }
    public function deleteByUsername($username)
    {
        $regExp = new \MongoRegex("/^{$username}/i");
        
        $q = $this->createQueryBuilder()->remove();
        $q->addOr(
            $q->expr()->field('username')->equals($regExp),
            $q->expr()->field('email')->equals($regExp)
             )
        ->getQuery()
        ->execute();
       
        return $q;
    }


    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}
