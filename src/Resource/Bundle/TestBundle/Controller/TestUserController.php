<?php

namespace Resource\Bundle\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resource\Bundle\UserBundle\Document\User;
use Symfony\Component\HttpFoundation\Response;

class TestUserController extends Controller
{
      public function addAction($username='edouard',$password='b1otope',$email='edouard.touraille@gmail.com') {
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

      public function deleteAction($username='edouard'){

            $repository = $this->get('doctrine_mongodb')
                ->getManager()
                ->getRepository('ResourceUserBundle:User');
            $user = $repository->deleteByUsername($username);

            $response = new Response();
            $response->setContent(json_encode(array('user'=>$user)));
            return $response;
      }

      public function wsseAction(){

          $password = 'b1otope';        
          $salt = 123;
          $nonce = 123;
          $encoder = $this->get('security.encoder.custom');
          $secret = $encoder->encodePassword($password,$salt);
          $expected =  base64_encode(
              hash('md5',
                  base64_decode($nonce).'2012-02-12 00:00:00'.$secret
              )
          );

          $chaine = 'UsernameToken Username="edouard", PasswordDigest="'.$expected.'", Nonce="123", Created="2012-02-12 00:00:00"';
          $response = new Response();
          $response->setContent(json_encode(array('xwsse'=>$chaine)));
      
          return $response;
      
      }
}
