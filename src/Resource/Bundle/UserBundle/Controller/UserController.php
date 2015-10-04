<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resource\Bundle\UserBundle\Document\User;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class UserController extends Controller
{

    private $sessionStorage;
    private $session;

    public function saltAction($username="edouard.touraille@gmail.com") {
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



    public function existsAction($email="clemansles@gmail.com") {
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

    public function subscribeAction($username,$password1,$password2,$email) {
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
            }
            $ret = array(
                'success' => $success,
                'message'=> $messages
                );
            $response = new Response();
            $response->setContent(json_encode($ret));
            return $response;
    }





    public function createClientAction(){

        $clientManager = $this->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris(array('http://192.168.33.10/resource/reSym/web/app_dev.php/callBack'));
        $client->setAllowedGrantTypes(array('token', 'authorization_code'));
        $clientManager->updateClient($client);

        return $this->redirect($this->generateUrl('fos_oauth_server_authorize', array(
            'client_id'     => $client->getPublicId(),
            'redirect_uri'  => 'http://192.168.33.10/resource/reSym/web/app_dev.php/callBack',
            'response_type' => 'code'
        )));
    }

    public function clientAction() {

        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        //$clientManager->getC
        return new Response("auth",200);

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
