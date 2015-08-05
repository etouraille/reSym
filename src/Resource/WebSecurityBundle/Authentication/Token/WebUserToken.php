<?php

namespace Resource\WebSecurityBundle\Authentication\Token; 

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class WebUserToken extends AbstractToken
{
    public $tokenValue;

    public function __construct(array $roles = array()) {

        parent::__construct($roles);

        // Si l'utilisateur a des rôles, on le considère comme authentifié
        $this->setAuthenticated(count($roles) > 0);
    }

    public function getCredentials()
    {
        
        return '';
        
    }

    public function setTokenValue($tokenValue) {
        $this->tokenValue = $tokenValue;
    }

    public function getTokenValue() {
        return $this->tokenValue;
    }
}
