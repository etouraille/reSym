<?php
namespace Resource\WebSecurityBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Resource\WebSecurityBundle\Authentication\Token\WebToken;

class TokenListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if($request->getMethod() === 'OPTIONS'){
            $response = new Response();
            $response->setStatusCode(200);
            $event->setResponse($response);
            return;
        }
        // todo existe a more conventional way of naming : find it.
        $wsseRegex = '/barer "([^"]+)"/';
        if (!$request->headers->has('Authentication') || 1 !== preg_match($wsseRegex, $request->headers->get('Authentication'), $matches)) {
            return;
        }

        $token = new WebToken();
        $token->setTokenValue($matches[1]);


        try {
            $authToken = $this->authenticationManager->authenticate($token);

            $this->securityContext->setToken($authToken);
        } catch (AuthenticationException $failed) {
            // ... you might log something here

            // To deny the authentication clear the token. This will redirect to the login page.
            // $this->securityContext->setToken(null);
            // return;

            // Deny authentication with a '403 Forbidden' HTTP response
            $response = new Response();
            $response->setStatusCode(403);
            $event->setResponse($response);

        }
    }
}
