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


    public function conversationsAction() {

        //todo remove, usefull for debug
        
        $userId = $this->get('security.context')
            ->getToken()
            ->getUser()
            ->getId();

         
        //$userId = '55ab75fbf08871c3048b4583';

        $q = $this->get('doctrine_mongodb')
            ->getManager()
            ->createQueryBuilder('ResourceUserBundle:Conversation');
            $q = $q->addOr(
                $q->expr()->field('from')->equals($userId)
            )->addOr(
                $q->expr()->field('to')->equals($userId)
            )
            ->sort('timestamp','desc')
            ->getQuery();

        $conversationsIterator = $q->execute(); 
        $ret = array();
        $success = false;
        $conversations = array();
        foreach($conversationsIterator as $conversation) {
            $conversations[] = $conversation;
        }
        if(count($conversations)>0) {
            $ret['conversations']=$conversations;
            $success = true;
        }
        $ret['success'] = $success; 

        return (new Response())->setContent(
            $this->get('jms_serializer')
            ->serialize($ret,'json')
        );
    }

    protected function getConversation($from, $to) {

        $dm = $this->get('doctrine_mongodb')
            ->getManager();
         $q = $dm->createQueryBuilder('ResourceUserBundle:Conversation');
         $q = $q->addOr(
                $q->expr()->addAnd(
                    $q->expr()->field('to')->equals($to)
                )->addAnd(
                    $q->expr()->field('from')->equals($from)
                )
             )->addOr(
                $q->expr()->addAnd(
                    $q->expr()->field('to')->equals($from)
                )->addAnd(
                    $q->expr()->field('from')->equals($to)
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
        $userRepository = $dm->getRepository('ResourceUserBundle:User');
        $userTo = $userRepository->findOneById($to);
        $userFrom = $userRepository->findOneById($from);

        if(!isset($userTo) || !isset($userFrom)) {
            return (new Response())->setContent(json_encode(array('success'=>false)));
        }
 
        $message = new Message();
         $message->setContent($content)
             ->setTimestamp($time=time())
             ->setFrom($from)
             ->setTo($to);
         if(!isset($conversation)) {
            $conversation = new Conversation();
            $conversation->setFrom($from);
            $conversation->setTo($to);
            $conversation->setFromName($userFrom->getUsername());
            $conversation->setToName($userTo->getUsername());
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
}
