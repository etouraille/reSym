<?php
// src/Acme/DemoBundle/Command/GreetCommand.php
namespace Resource\Shell;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Resource\Bundle\UserBundle\Service\Elastic\Mappinp;
use Resource\Bundle\UserBundle\Service\Elastic\Delete;

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

        $elasticMapping = new Mappping();
        $elasticDelete = new Delete();

        switch($action) {
            case 'mapping' : 
                $elasticMapping->mapping();
                break;

            case 'delete':
                $elasticDelete->otherMapping();
                break;

            case 'populate' : 
            break;
        }
        
        $output->writeln(sprintf(' %s performed ', $action));
    }

    public function populate()
    {
        $resources = $this->getContainer()
            ->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('ResourceUserBundle:Resource')
            ->find();

        $elastic = $this->getContainer()->get('elastic');


    }
}
