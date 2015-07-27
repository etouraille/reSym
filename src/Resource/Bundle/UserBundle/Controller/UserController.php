<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resource\Bundle\UserBundle\Document\User;
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

    public function saltAction($username='edouard.touraille@gmail.com') {
        $repository = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('ResourceUserBundle:User');

        $user = $repository->loadUserByUsername($request->get('username'));
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

    public function tokenAction(Request $request) {
        $salt = self::saltAction($request);
        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $repository = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('ResourceUserBundle:User');

        $user = $repository->loadUserByUsername($request->get('username'));
        $serializer = new Serializer($normalizers, $encoders);
        $user = json_decode($serializer->serialize($user,"json"));
        //$user[]
        $salt = $user->salt;
        //begin service token//
        $token = $this->get('security.token');
        $tokenVal = $token->createToken($request->get('username'), $salt);

        $subject = "reset password #@t";
        $body = "please follow this link to reset your password <a href='ec2-52-24-103-97.us-west-2.compute.amazonaws.com/app_dev.php/reset/".$tokenVal."'>".$tokenVal."</a>";
        $dest = "clemansles@gmail.com";
        $from = "resource@objet-partages.org";
        $headers = "From:".$from." \r\n".
            "Reply-To: ".$from. "\r\n".
            "X-Mailer: PHP/". phpversion();
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
         mail($dest,$subject,$body,$headers);

        $this->sessionStorage = new NativeSessionStorage(array("cookie_lifetime"=>1400));
        $this->session = new Session($this->sessionStorage);
        $this->session->start();

        $arrToken = array();
        $arrToken["date"] = time();
        $arrToken["mail"] = $request->get('username');
        $arrToken["token"] = $tokenVal;
        $this->session->set("token",$arrToken);
        return new Response(var_dump($this->session->get("token")));
    }
    

    public function resetAction($token){

        $token = $this->get('security.token');
        $sessionStorage = new NativeSessionStorage(array("cookie_lifetime"=>1400));



        $response = new Response();
        $response->setContent(var_dump($this->session->get("token")));
    }
    public function existsAction(Request $request) {
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

    public function subscribeAction(Request $request) {
            $success = true;
            
            $user = new User();

            $username = $request->get('username');
            $password1 = $request->get('password1');
            $password2 = $request->get('password2');
            $email = $request->get('email');

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
