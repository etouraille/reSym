<?php
namespace Resource\Bundle\UserBundle\Service;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;


class Rabbit{

    public function send($id,$binding_key='index')
    {
        $connection = new AMQPConnection('objetspartages.org', 5672, 'toto', 'toto','toto');
        $channel = $connection->channel();
        $channel->exchange_declare('indexing', 'direct', false, false, false);
        $msg = new AMQPMessage($id);
        $channel->basic_publish($msg, 'indexing', $binding_key);
        $channel->close();
        $connection->close();
    }

}