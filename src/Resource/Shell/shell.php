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

    protected $wait = false;

    public function execute() {
        
        while(true){
            //listen to the brocker
        try{
            $connection = new AMQPConnection('objetspartages.org', 5672, 'toto', 'toto', 'toto',
            false, 'AMQPLAIN',null,'en_US', 3, 3, null, false, 2);
             $channel = $connection->channel();
             
            $channel->exchange_declare('indexing', 'direct', false, false, false);

            list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

                            
            
            $channel->queue_bind($queue_name, 'indexing', 'index');
            $channel->queue_bind($queue_name, 'indexing', 'update');


            $channel->basic_consume($queue_name, '', false, true, false, false, array($this, 'callBack'));

            $this->wait = false;
            while(count($channel->callbacks)) {
                   $channel->wait();
            }


            $channel->close();
            $connection->close(); 
        }
         catch(\Exception $e){
            $this->wait();
            echo $e->getMessage();
            var_dump($e->getTrace());
         } 
        }
    }

    public function callBack($msg){
        $elastic = new \Resource\Bundle\UserBundle\Service\Elastic();
        
        $data = $msg->body;
        $key  = $msg->delivery_info['routing_key'];
        switch($key){
            case : 'index'
                    $return = $elastic->index('resource','hashtag',$data);
                break;
            case : 'update'
                    $return = $elastic->update('resource','hashtag',$data);
                break;
        
        }

        echo $data;
        //read from database
       echo $return;
    }

    public function wait(){
        if(false === $this->wait){
            sleep(10);
            $this->wait = 10;
        }else{
            $this->wait = $this->wait/2;
            sleep($this->wait);
        }
    
    }
}
$shell = new ReceiverCommand();
$shell->execute();
