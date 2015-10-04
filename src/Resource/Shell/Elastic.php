<?php
// src/Acme/DemoBundle/Command/GreetCommand.php
namespace Resource\Shell;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Resource\Bundle\UserBundle\Service\Elastic as ElasticService;

use Resource\Bundle\UserBundle\Service\Notification;

class Elastic extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('elastic')
            ->setDescription('Various stuff on elastic')
            ->addArgument('action', InputArgument::OPTIONAL, 'What do you want to do on elastic')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');

        $elastic = new ElasticService();

        switch($action) {
            case 'mapping' : 
                $elastic->mapping();
            break;
        }
        
        $output->writeln(sprintf(' %s performed ', $action));
    }
}
