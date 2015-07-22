<?php

namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resource\Bundle\UserBundle\Document\Conversation;
use Resource\Bundle\UserBundle\Document\Message;
use Symfony\Component\HttpFoundation\Response;
use Resource\Bundle\UserBundle\Service\Elastic;
use Resource\Bundle\UserBundle\Service\ReverseGeoCoding;
use Resource\Bundle\UserBundle\Document\Place;

class MessagingController extends Controller {


    public function conversationsAction( $userId ) {

        $q = $this->get('doctrine_mongodb')
            ->getManager()
            ->createQueryBuilder('ResourceUserBundle:Conversation');
         $q = $q->addOr(
                $q->expr()->field('from')->equals($userId),
                $q->expr()->field('to')->equals($userId)
            )
            ->sort('timestamp','desc')
            ->getQuery();

        $conversations = $q->execute();



    }

    protected function getConversation($from, $to) {

        $dm = $this->get('doctrine_mongodb')
            ->getManager();
         $q = $dm->createQueryBuilder('ResourceUserBundle:Conversation');
         $q = $q->addOr(
             $q->expr()->addAnd(
                 $q->expr()->field('from')->equals($from),
                 $q->expr()->field('to')->equals($to)
             ),
             $q->expr()->addAnd(
                 $q->expr()->field('from')->equals($to),
                 $q->expr()->field('to')->equals($from)
             )
            )
            ->getQuery();

         $conversation = $q->getSingleResult();

         return $conversation;
    
    }

    public function conversationAction($to) {
        $user = $this->get('security.context')
            ->getToken()
            ->getUser();
        $success = false;
        $ret = array();
        if(isset($user)) {
            $conversation = $this->getConversation($user->getId(),$to);
            $ret['me'] = $user->getId();
            if(isset($conversation)){
                $ret['messages'] = $conversation->getMessages();
                $success = true;
            }
        }
        $ret['success'] = $success;
        return (new Response())->setContent(
            $this->get('jms_serializer')->serialize($ret,'json')
        );
    }

    public function sendAction( $to , $content ) {
        
        $from = $this->get('security.context')->getToken()->getUser()->getId();
        
        $conversation = $this->getConversation($from, $to);
        
        $dm = $this->get('doctrine_mongodb')->getManager();
        
        $message = new Message();
         $message->setContent($content)
             ->setTimestamp($time=time())
             ->setFrom($from)
             ->setTo($to);
         if(!isset($conversation)) {
            $conversation = new Conversation();
            $conversation->setFrom($from);
            $conversation->setTo($to);            
         
         }
         $conversation->setTimestamp($time);
         $conversation->addMessage($message);
         $dm->persist($conversation);
         $dm->flush();


         // Messaging send the message to device ...
         $userTo = $dm->getRepository('ResourceUserBundle:User')
             ->findOneById($to);
         if(isset($userTo)) {
            $this->get('notification')->send($userTo,$content, array('from'=>$from));
         }
         return (new Response())->setContent(json_encode(array('success'=>true)));
    
    }
    public function meAction() {
        $user = $this->get('security.context')
            ->getToken()
            ->getUser();
        $ret = array();
        $success = false;
        if(isset($user)) 
        {
            $ret['me']=$user->getId();
            $success = true;
        }
        $ret['success'] = $success;
        return (new Response())->setContent(json_encode($ret));
    }
}
