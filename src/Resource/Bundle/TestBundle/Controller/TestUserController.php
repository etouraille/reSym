<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resource\Bundle\UserBundle\Document\User;
use Symfony\Component\HttpFoundation\Response;

class TestUserController extends Controller
{
      public function addAction($username,$password,$email) {
            $user = new User();
            // mettre en place un filtre de validation des paramètres.
            // je ne vois null part de filtrage des donnée : mise en place dans le validateur.
            $user->setUsername($username);
            $user->setPassword($password);
            $user->setEmail($email);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($user);
            $dm->flush();
            
            $ret = array(
                'username' => $user->getUsername(),
                'password'=>$user->getPassword(),
                'salt'=>$user->getSalt(),
                );
            $response = new Response();
            $response->setContent(json_encode($ret));
            return $response;
      }

      public function deleteAction($username){

            $repository = $this->get('doctrine_mongo')
                ->getManager()
                ->getRepository('ResourceUserBundle:Product');
                $user = $repository->deleteOneByUsername($username);

      }
}
