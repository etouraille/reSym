<?php
namespace Resource\WebSecurityBundle\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Resource\WebSecurityBundle\Authentication\Token\WebUserToken;

class WsseProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $cacheDir;
    private $encryptionService;

    public function __construct($tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function authenticate(TokenInterface $token)
    {
        $user = $this->tokenSerice->getUserForToken( $token->getTokenValue() );

        if ($user) { 
            $authenticatedToken = new WebUserToken($user->getRoles());
            $authenticatedToken->setUser($user);
            return $authenticatedToken;
        }

        throw new AuthenticationException('The Token authentication failed');
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof WebUserToken;
    }
}
