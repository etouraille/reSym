<?php
namespace Resource\Bundle\UserBundle\Service;

use Resource\Bundle\UserBundle\Document\ResetToken;

class Forgotten {
    
    protected $odm;

    public function __construct($odm,$validityInDays=1) {
        $this->odm = $odm;
        $this->validityInDays = $validityInDays;
    }

    public function createToken($email) {
        $dm = $this->odm->getManager();
        $user = $dm->getRepository('ResourceUserBundle:User')->findOneByEmail($email);
        if(isset($user)) {
            $token = new ResetToken();
            $tokenValue = base64_encode(md5($email.time()).time());
            $token->setToken($tokenValue);
            $token->setUserid($user->getId());
            $token->setTimestamp(time()+24*3600*$this->validityInDays);
            $dm->persist($token);
            $dm->flush();
            return $tokenValue;
        } else {
            throw new \Exception('User Not Found');
        }
    }

    public function sendEmail($token, $email ) {
        $message = ' Pour réinitialiser votre mot de passe <a href="freePress://create-password?token='.$token.'">Clikez</a> la validité dans le temps de ce lien est limitée';
        $subject = "Réinitilisation de votre mot de passe";
        mail($emai,$subject,$message);
    }

    public function setPassword($token, $pass1, $pass2 ) {
        
        $dm = $this->odm->getManager();
        $token = $dm->getRepository('ResourceUserBundle:ResetToken')->findOneByToken($token);
        if(!isset($token)) {
            throw new \Exception('Invalid Token');
        }
        if(time()>$token->getTimestamp()) {
            throw new \Exception('Invalid Token');
        }
        $user = $dm->getRepository('ResourceUserBundle:User')->find($token->getUserid());

        if(!isset($user)) {
            throw new \Exception('Invalid User');
        }
        if($pass1 != $pass2) {
            throw new \Exception('Invalid password');
        }
        $user->setPassword($pass1);
        $dm->persist($user);
        $dm->flush();
        return true;

        
    }

}
