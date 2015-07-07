<?php
namespace Resource\Shell;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPConnection;
use Resource\Bundle\UserBundle\Service\DeferResourceAddressSetting;

define('AMQP_DEBUG', true);

class ReceiverCommand extends ContainerAwareCommand {

    protected $oputput; 
    protected function configure(){
        
        $this
            ->setName('r:r')
            ->setDescription('Receive some data from the server');
        }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->output = $output;
        
        #thread
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
            $channel->queue_bind($queue_name, 'indexing', 'place');
            $channel->queue_bind($queue_name, 'indexing', 'percolate');


            $channel->basic_consume($queue_name, '', false, true, false, false, array($this, 'callBack'));

            $this->wait = false;
            while(count($channel->callbacks)) {
                   $channel->wait();
            }


            $channel->close();
            $connection->close(); 
        }
         catch(\Exception $e){ // todo we have only to catch the amq exception, or socket exception 
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
        $search_id = null;
        try{
            $headers = $msg->get('application_headers')->getNativeData();
            if(isset($headers['search_id'])) { 
                $search_id = $headers['search_id'];
            }
        } catch(\Exception $e){
            //NO HEADERS IS DEFINED
            //todo we can be more generic and define a document id as header.
        }
        switch($key){
            case  'index' :
                //we defer the call to the reverseGeoCoding API
                //and we update the resource datbase accordingly
                //we also 
                    $dataWithAddress = DeferResourceAddressSetting::defer(
                        $this->getContainer()->get('doctrine_mongodb'),
                        $data
                    );
                    $return = $elastic->index('resource','hashtag',$dataWithAddress);
                    $return = $elastic->percolate('resource', 'hashtag', $dataWithAddress );
                break;
            case  'update' :
                    $return = $elastic->update('resource','hashtag',$data);
                    break;
            case  'place' :
                    $return = $elastic->index('resource','place',$data);
                    break;

            case 'percolate' : 
                    $return = $elastic->percolator('resource', 'hashtag', $data, $search_id);
                    break;
        

        
        }

        echo $data;
        //read from database
       echo $return;
    }

    protected function wait(){
        if(false === $this->wait){
            sleep(10);
            $this->wait = 10;
        }else{
            $this->wait = $this->wait/2;
            sleep($this->wait);
        }
    }
}
