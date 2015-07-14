<?php

namespace Resource\WebSecurityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Resource\WebSecurityBundle\DependencyInjection\Security\Factory\TokenFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResourceWebSecurityBundle extends Bundle
{
    public function build(ContainerBuilder $container){
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new TokenFactory());
    }


}
