<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resource\Bundle\UserBundle\Document\User;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function saltAction($username='edouard') {
        $repository = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('ResourceUserBundle:User');
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

    public function subscribeAction($username='etouraille',$password1='b1otope', $password2='b1otope',$email='edouard.touraille@gmail.com') {
            $success = true;
            
            $user = new User();
            
            $user->setUsername($username);
            $user->setPassword($password1);
            $user->setPassword2($password2);
            $user->setEmail($email);

            $validator = $this->get('validator');
            $errorList = $validator->validate($user);
            if(count($errorList) > 0 ){
                $success = false;
                $messages = array();
                    foreach($errorList as $value){
                        $message = $value->getMessage();
                        $property = $value->getPropertyPath();
                        if( isset($messages[$property]) && is_array($messages[$property]) ) $messsages[$property][] = $message;
                        else $messages[$property] = array($message);
                }
            }
            else
            {
                $dm = $this->get('doctrine_mongodb')->getManager();
                $dm->persist($user);
                $dm->flush();
                $messages = 'OK creation';
            }
            $ret = array(
                'succes' => $success,
                'message'=> $messages
                );
            $response = new Response();
            $response->setContent(json_encode($ret));
            return $response;
    }
}
