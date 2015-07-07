<?php
namespace Resource\Bundle\UserBundle\Service;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;


class Rabbit{

    public function send($data,$binding_key='index', $headers = array())
    {
        $connection = new AMQPConnection('objetspartages.org', 5672, 'toto', 'toto','toto');
        $channel = $connection->channel();
        $channel->exchange_declare('indexing', 'direct', false, false, false);
        $msg = new AMQPMessage($data);
        if(count($headers) > 0){
            $hdrs = new AMQPTable($headers);
            $msg->set('application_headers',$hdrs);
        }
        $channel->basic_publish($msg, 'indexing', $binding_key);
        $channel->close();
        $connection->close();
    }

}
