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
    protected $wait = 10;
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
            $channel->queue_bind($queue_name, 'indexing', 'percolator');
            $channel->queue_bind($queue_name, 'indexing', 'send');
            $channel->queue_bind($queue_name, 'indexing', 'associate');


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
         } 
        }

    }

    public function callBack($msg){
        $elastic = new \Resource\Bundle\UserBundle\Service\Elastic();
        
        $data = $msg->body;
        $key  = $msg->delivery_info['routing_key'];
        $id = null;
        $type = null;
        $userid = null;
        $likeword = null;

        try{
            $headers = $msg->get('application_headers')->getNativeData();
            if(isset($headers['type'])) { 
                $type = $headers['type'];
            }
            if(isset($headers['id'])) {
                $id = $headers['id'];
            }
            if(isset($headers['userid'])) {
                $userid = $headers['userid'];
            }
            if(isset($headers['associateTag'])) {
                $associateTag = $headers['associateTag'];
            }                        
            if(isset($headers['tag'])) {
                $tag = $headers['tag'];
            }                        
            


            
        } catch(\Exception $e){
            //NO HEADERS IS DEFINED
            //todo we can be more generic and define a document id as header.
        }
        switch($key){
        case  'index' :
                // $type = hashtag
                //we defer the call to the reverseGeoCoding API
                //and we update the resource datbase accordingly
                //we also associate the word with the other associate  
                    $doc = json_encode(array('doc'=>json_decode($data,true)));
                    $dataWithAddress = DeferResourceAddressSetting::defer(
                        $this->getContainer()->get('doctrine_mongodb')->getManager(),
                        $data
                    );
                    $return = $elastic->index('resource',$type,$dataWithAddress,$id);
                    if($type = 'hashtag') {
                        $return = $elastic->percolate('resource', $type, $doc );
                    
                        $this->getContainer()
                            ->get('percolate_notifier')
                            ->process($return, $dataWithAddress);
                    }
                break;
            case  'update' :
                    $return = $elastic->update('resource',$type , $data, $id );
                    break;
                        

            case 'percolator' : 
                    $return = $elastic->percolator('resource', $type, $data, $id);
                    break;

            case 'message' : 
                
                $user = $this->getContainer()->get('doctrine_mongodb')
                    ->getManager()
                    ->getRepository('ResourceUserBundle:User')
                    ->getOneById($userid);
                
                if(isset($user)) { 
                    $this->getContext()
                        ->get('notification')
                        ->send($userid,$data, 
                        array(
                            'type'=>$type,
                            'id'=>$id
                        ));
                }


                break;
             
             case 'associate' : 

                 // in this case it can be a new index or an update but we must associate 
                 // all the linked word by the same person
                 //

                 $elastic->associate($tag,$id,$associateTag);
                 echo $tag;
                 echo "\n";
                 echo $associateTag;

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
