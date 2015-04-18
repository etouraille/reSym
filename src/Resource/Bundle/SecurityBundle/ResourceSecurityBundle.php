<?php

namespace Resource\Bundle\SecurityBundle;

use Resource\Bundle\SecurityBundle\DependencyInjection\Security\Factory\WsseFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResourceSecurityBundle extends Bundle
{
    public function build(ContainerBuilder $container){
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new WsseFactory());
    }
}
