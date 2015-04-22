<?php

namespace Resource\Bundle\LoggedControllerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ResourceLoggedControllerBundle:Default:index.html.twig', array('name' => $name));
    }
}
