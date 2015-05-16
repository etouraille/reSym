<?php
namespace Resource\Shell;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPConnection;
class ReceiverCommand extends ContainerAwareCommand {

    protected $oputput; 
    protected function configure(){
        
        $this
            ->setName('r:r')
            ->setDescription('Receive some data from the server');
        }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->output = $output;
        
        //listen to the brocker
         $connection = new AMQPConnection('objetspartages.org', 5672, 'toto', 'toto', 'toto');
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
        $this->output->writeln($data);
        //read from database
        $elastic = new \Resource\Bundle\UserBundle\Service\Elastic();
        $elastic->index('resource','hastag',$data);
    
    }



}
