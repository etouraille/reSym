<?php
namespace Resource\WebSecurityBundle\Service;

use Resource\WebSecurityBundle\Document\WebToken;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\Exception\CredentialExpiredException;

class TokenService {

    protected $validity; // 0 for never expires, time in seconds ater creation date
    protected $odm;

    public function __construct($odm,$validity = 0 ){ 
        $this->validity = $validity;
        $this->odm = $odm;
    }
    
    public function createToken( $user ) {
        $webToken = new WebToken();
        $webToken->setUserId($user->getId());
        $webToken->setCreationDate(time());
        $webToken->setValidity($this->validity);
        $webToken->setValue($this->getValue($user->getId(), $user->getEmail()));
        $dm = $this->odm->getManager();
        $dm->persist($webToken);
        $dm->flush();

    }
    public function getUserForToken($tokenValue) {
        $token = 
            $this->odm
            ->getManager()
            ->getRepository('ResourceWebSecurityBundle:WebToken')
            ->findOneByValue($tokenValue);

        if(isset($token)) {
            if(!$this->isValid($token)) throw new CredentialExpiredException('The token is not valid anymore');
            $user = $this->orm
                ->getManager()
                ->getRepository('ResourceUserBundle:User')
                ->findOneById($token->getUserId());

            return $user;
        
        } else {
            throw new TokenNotFoundException('The given token does\'nt Exists !');
        }
    }

    protected function getValue($userId, $userEmail ) {
        return base64_encode(
            md5($userId . $userEmail . time ()) . $userEmail . time()
        );
    }
    
    protected function isValid(WebToken $token ) {
        $creationTimeStamp = $token->getCreationDate();
        $validity = $token->getValidity();
        if($validity == 0) return true;
        return $creationTimeStamp + $validity > time();
    }

}
