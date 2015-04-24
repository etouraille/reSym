<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resource\Bundle\UserBundle\Document\User;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function saltAction($username) {
        $repository = $this->get('doctrine_mongo')
            ->getManager()
            ->getRepository('ResourceUserBundle:Product');
        $user = $repository->findOneByUsername($username);
        $ret = array('success'=>false);
        if($user){
            $success = true;
            $ret = array(
                'salt'=>$user->getSalt(),
                'success'=>$success,
            );
        }
        $response = new Response();
        $response->setContent(json_encode($ret));
        return $response;
    }

    public function subscribeAction($username='etouraille',$password='b1otope',$email='edouard.touraille@gmail.com') {
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
                'succes' => true,
                'creationUserId'=>$user->getId()
                );
            $response = new Response();
            $response->setContent(json_encode($ret));
            return $response;
    }
}
