<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ForgottenController extends Controller {


    public function createTokenAction($email = 'edy@free.fr') {
        $dm = $this->get('doctrine_mongodb')
            ->getManager();
         $user = $dm->getRepository('ResourceUserBundle:User')
             ->findOneByEmail($email);

        if(!isset($user)){
            $ret = 'The email doesn\'t Exists';
            $success = false;
        } else {
            $forgotten = $this->get('forgotten');
            $token = $forgotten->createToken($email);
            $forgotten->sendEmail($email,$token);
            $ret = "Email Send";
            $success = true;

        }
        return (new Response())->setContent(json_encode(array('success'=>$success,'ret'=>$ret)));
    }    
    /*
     *Action to get all Hashtags with optional filter
     */
    public function changePasswordAction($token='YWQ2Y2IxOTZjNzQxYjRhMjQyOTBlOGMxOTdlMzNlN2YxNDM3MjQ2ODY2', $password1='b1otope', $password2='b1otope') {

        $forgotten = $this->get('forgotten');

        try {

            $success = $forgotten->setPassword($token, $password1, $password2);
            $message = 'OK!';
        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
        }
        

        return (new Response())->setContent(json_encode(array('success'=>$success,'message'=>$message)));
    } 

}
