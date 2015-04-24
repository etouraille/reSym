<?php

namespace Resource\Bundle\LoggedControllerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Components\HttpFoudation\Response;

class AuthenticationController extends Controller
{
    public function pingAction($name)
    {
        return new Response();
    }
}
