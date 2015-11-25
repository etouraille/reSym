<?php

namespace Resource\Bundle\UserBundle\Controller;

use Resource\Bundle\UserBundle\Service\Word;
use Resource\Bundle\UserBundle\Document\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
class UserController extends Controller
{
    public function saltAction($username='edouard.touraille@gmail.com') {
        $repository = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('ResourceUserBundle:User');
        $user = $repository->loadUserByUsername($username);
        $ret = array('success'=>false);
        if($user){
            $success = true;
            $ret = array(
                'id'=>$user->getId(),
                'salt'=>$user->getSalt(),
                'success'=>$success,
            );
        }
        $response = new Response();
        $response->setContent(json_encode($ret));
        return $response;
    }

    public function existsAction($email='edouard.touraille@gmail.co'){
         $repository = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('ResourceUserBundle:User');
         $user = $repository->findOneByEmail($email);
         
         $inDatabase = false;
         if(isset($user)){
            $inDatabase = true;
         }
         $response = new Response();
         $response->setContent(json_encode(array('success'=>$inDatabase)));
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
            $messages = array();
            if(count($errorList) > 0 ) { 
                $success = false;
                $messages = array();
                    foreach($errorList as $value){
                        $message = $value->getMessage();
                        $property = $value->getPropertyPath();

                        if( isset($messages[$property]) && is_array($messages[$property]) ) {
                            $messsages[$property][] = $message;
                        }
                        else {
                            $messages[$property] = array($message);
                        }
                }
            }
            else {
                $dm = $this->get('doctrine_mongodb')->getManager();
                $dm->persist($user);
                $dm->flush();
                $messages['salt'] = $user->getSalt();
            }
            $ret = array(
                'success' => $success,
                'message'=> $messages
                );
            $response = new Response();
            $response->setContent(json_encode($ret));
            return $response;
    }

    // in case of email subscription only, we generate a random password and user name
    // and create the account, and send back the datas ...
    // preferabily insert a captcha on the website ...
    public function subscribeWithEmailOnlyAction($email='lifnucucI@gmail.com',$username='anonymous'){
        
        $password = Word::getRandom();

        //todo : send an email to the user with is clear password.
        $success = false;
        $json = $this->subscribeAction($username,$password,$password,$email)->getContent();
        $return_array = json_decode($json,true);
        $success = $return_array['success'];
        $return = array();
        if($success) {
            $repository = $this->get('doctrine_mongodb')
                ->getManager()
                ->getRepository('ResourceUserBundle:User');
        $user = $repository->loadUserByUsername($email);
            $salt = $return_array['message']['salt'];
            $return['salt'] = $salt;
            $return['email'] = $email;
            $return['password'] = $password;
            $return['id'] = $user->getId(); 
        }
        $return['success'] = $success;
        return (new Response)->setContent(json_encode($return));
    } 

    public function notificationRegisterAction($device='android', $regId ='123') {
        
        $user = $this->get('security.context')->getToken()->getUser();
        //$user = new \Resource\Bundle\UserBundle\Document\User();
        $success = false;
        if(isset($user)) {
            $dm = $this->get('doctrine_mongodb')->getManager();
            switch($device) {
                case 'android': 
                        $user->setAndroidNotificationId($regId);
                        break;
                default : 
                    break;

            } 
            $dm->persist($user);
            $dm->flush();
            $success = true;
        }
        return (new Response())->setContent(json_encode(array('success'=>$success)));

    }
}
