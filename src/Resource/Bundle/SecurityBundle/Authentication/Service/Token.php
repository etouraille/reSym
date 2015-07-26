<?php
/**
 * Created by PhpStorm.
 * User: Paulisse
 * Date: 22/07/2015
 * Time: 17:41
 */

namespace Resource\Bundle\SecurityBundle\Authentication\Service;

use Resource\Bundle\UserBundle\Document\User;
use Resource\Bundle\UserBundle\Document\UserRepository;

class Token
{

    public function createToken($email,$salt){
        $salt = substr(str_replace('+','.',base64_encode(md5(mt_rand(), true))),0,16);
// how many times the string will be hashed
        $rounds = 10000;
// pass in the password, the number of rounds, and the salt
// $5$ specifies SHA256-CRYPT, use $6$ if you really want SHA512
        echo crypt('password123', sprintf('$5$rounds=%d$%s$', $rounds, $salt));
    }

    public function checkToken(array $tokenSession, $token){

        if(time() - $tokenSession["date"] <= 300) {
            if($tokenSession == $token ) {
                echo "reset your password now";
                return true;
            }
        }

    }
}