<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resource\Bundle\UserBundle\Document\User;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function loginAction($username,$password)
    {
        //idée de login: 
        //appelée en https pour proteger login et mot de passe
        //revoie le token d'authentication.
    }

    public function subscribeAction($username='etouraille',$password='b1otope',$email='edouard.touraille@gmail.com'){
            $user = new User();
            // mettre en place un filtre de validation des paramètres.
            // je ne vois null part de filtrage des donnée : mise en place dans le validateur.
            $user->setUsername($username);
            $user->setPassword($password);
            $user->setEmail($email);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($user);
            $dm->flush();

            $response = new Response();
            $response->setContent(json_encode(array(
                'succes' => true,
                'creationUserId'=>$user->getId()
                )));
            $response->headers->set('Content-Type', 'application/json');

            return $response; 
    }
}
