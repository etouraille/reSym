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
          
          $salt = '9320d97bc80eec01f366083a2bce5ef8';
          $nonce = 'NjdlZjE1MDRlYjgxZjZhN2RkYjE1OTE4NzZiOGY0Mzg=';
          $password = 'b1otope'; 
          $created = '2012-02-12 00:00:00';
          $encoder = $this->get('security.encoder.custom');
          $secret = $encoder->encodePassword($password,$salt);
          $expected =  $this->get('security.encryption')->getDigest($nonce, $created, $secret );         
            
          $chaine = 'UsernameToken Username="edouard", PasswordDigest="'.$expected.'", Nonce="123", Created="2012-02-12 00:00:00"';
          $response = new Response();
          $response->setContent(json_encode(array('secret'=>$secret,'password'=>$password,'salt'=>$salt, 'expected'=>$expected)));
      
          return $response;
      
      
      }

      public function digestAction($nonce=123,$created='2012-02-12 00:00:00', $secret = '123aTd58887y'){
          $digest = $this->get('security.encryption')->getDigest($nonce, $created, $secret );

          $response = new Response();
          $response->setContent(json_encode(array('digest'=>$digest)));
          return $response;
      
      }
}
