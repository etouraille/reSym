<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ResourceUserBundle:Default:index.html.twig', array('name' => $name));
    }
}
