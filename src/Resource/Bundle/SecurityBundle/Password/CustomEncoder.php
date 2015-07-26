<?php
namespace Resource\Bundle\SecurityBundle\Password;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class CustomEncoder implements PasswordEncoderInterface
{

    protected $iteration;

    public function __construct($iteration){
        $this->iteration = $iteration;
    }
    
    public function encodePassword($raw, $salt)
    {
        $encodedPassword = $raw;
        for($i=0;$i<$this->iteration;$i++){
            $encodedPassword = hash('md5',$salt . $encodedPassword );
        }
        return $encodedPassword;
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded === $this->encodePassword($raw, $salt);
    }

}
