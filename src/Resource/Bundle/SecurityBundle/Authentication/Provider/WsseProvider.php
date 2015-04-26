<?php
namespace Resource\Bundle\SecurityBundle\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Resource\Bundle\SecurityBundle\Authentication\Token\WsseUserToken;

class WsseProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $cacheDir;

    public function __construct(UserProviderInterface $userProvider, $cacheDir)
    {
        $this->userProvider = $userProvider;
        $this->cacheDir     = $cacheDir;
    }

    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getUsername());

        if ($user && $this->validateDigest($token->digest, $token->nonce, $token->created, $user->getPassword())) {
            $authenticatedToken = new WsseUserToken($user->getRoles());
            $authenticatedToken->setUser($user);
            return $authenticatedToken;
        }

        throw new AuthenticationException('The WSSE authentication failed.'.$token->getUsername());
    }

    protected function validateDigest($digest, $nonce, $created, $secret)
    {
        // Expire le timestamp après 5 minutes
        if (time() - strtotime($created) > 300) {
            return false;
        }

        // Valide que le nonce est unique dans les 5 minutes
        if (file_exists($this->cacheDir.'/'.$nonce) && file_get_contents($this->cacheDir.'/'.$nonce) + 300 > time()) {
            throw new NonceExpiredException('Previously used nonce detected');
        }
        //todo uncomment and create the nonce directory on settup symfony or in a batch
        //file_put_contents($this->cacheDir.'/'.$nonce, time());

        // Valide le Secret
        $expected = base64_encode(md5(base64_decode($nonce).$created.$secret));

        return $digest === $expected;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof WsseUserToken;
    }
}
