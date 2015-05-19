<?php

namespace Resource\Shell;
include __DIR__.'/../../../vendor/autoload.php';
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPConnection;


define('AMQP_DEBUG', true);

class ReceiverCommand {

    public function execute() {
        
        //listen to the brocker
        $connection = new AMQPConnection('objetspartages.org', 5672, 'toto', 'toto', 'toto',
        false, 'AMQPLAIN',null,'en_US', 3, 3, null, false, 2);
         $channel = $connection->channel();
         

        $channel->exchange_declare('indexing', 'direct', false, false, false);

        list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

                        
        
        $channel->queue_bind($queue_name, 'indexing', 'index');


        $channel->basic_consume($queue_name, '', false, true, false, false, array($this, 'callBack'));

        while(count($channel->callbacks)) {
               $channel->wait();
        }

        $channel->close();
        $connection->close(); 
    }

    public function callBack($msg){
        $data = $msg->body;
        echo $data;
        //read from database
        $elastic = new \Resource\Bundle\UserBundle\Service\Elastic();
        $return = $elastic->index('resource','hashtag',$data);
        echo $return;
    }
}
$shell = new ReceiverCommand();
$shell->execute();
