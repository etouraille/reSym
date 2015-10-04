<?php
// src/StarkIndustries/ClientBundle/Controller/AuthController.php
namespace Resource\Bundle\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use OAuth2;


class AuthController extends Controller
{
    /**
     * @Route("/authorize", name="auth")
     */
    public function authAction(Request $request)
    {

        $authorizeClient = $this->container->get('resource_client.authorize_client');

        if (!$request->query->get('code')) {

            return new RedirectResponse($authorizeClient->getAuthenticationUrl());
        }


        $authorizeClient->getAccessToken($request->query->get('code'));

        $authResource = $authorizeClient->fetch('http://localhost:8181/api/articles');
        return (new Response())->setContent(json_encode($authResource));
    }
}