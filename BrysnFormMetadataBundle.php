<?php

namespace Brysn\FormMetadataBundle;

use Brysn\FormMetadataBundle\DependencyInjection\Compiler\FormExtensionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BrysnFormMetadataBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FormExtensionCompilerPass());
    }
}