<?php
// src/Acme/DemoBundle/Command/GreetCommand.php
namespace Resource\Shell;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Resource\Bundle\UserBundle\Service\Notification;

class SendNotification extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('notify')
            ->setDescription('Send a notification')
            ->addArgument('email', InputArgument::OPTIONAL, 'Qui voulez vous saluer??')
            ->addArgument('message', InputArgument::OPTIONAL, 'Que voulez vous lui dire')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $message = $input->getArgument('message');
        
        $user = $this->getContainer()
            ->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('ResourceUserBundle:User')
            ->findOneByEmail($email);
        $error = 'no user';
        if(isset($user)) {
            $regId = $user->getAndroidNotificationId();
            $error = 'no regId';
            if($regId) {
                Notification::send($regId, $message );                
                $output->writeln('message send !');
                return;
            }
        }
        $output->writeln(sprintf('message NOT send ! : %s',$error));
    }
}
