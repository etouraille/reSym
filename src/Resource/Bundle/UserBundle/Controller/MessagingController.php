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


    public function conversationsAction( $userId) {

        $q = $this->get('doctrine_mongodb')
            ->getManager()
            ->createQueryBuilder('ResourceUserBundle:Conversation')->find();
         $q = $q->addOr(
                $q->expr()->field('from')->equals($userId),
                $q->expr()->field('to')->equals($userId)
            )
            ->sort('timestamp','desc')
            ->getQuery();

        $conversations = $q->execute();



    }

    public function messageAction($from, $to , $content ) {
        
         $dm = $this->get('doctrine_mongodb')
            ->getManager();
         $q = $dm->createQueryBuilder('ResourceUserBundle:Conversation')->find();
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
    
    }
    public function addressAction($lat='45.7677957',$lng='4.8731638') {
    }
}
