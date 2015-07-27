<?php

namespace Resource\Bundle\SecurityBundle\Authentication\Service;


class Encryption{
    
    
    public function getDigest($nonce,$created, $secret){
        return  base64_encode(
            hash(
                'md5',
                base64_decode($nonce).$created.$secret
            )
        );
    }
    
}
