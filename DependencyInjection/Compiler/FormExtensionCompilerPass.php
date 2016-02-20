<?php

namespace Brysn\FormMetadataBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FormExtensionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('form.registry')) {
            return;
        }

        $definition = $container->findDefinition('form.registry');
        $extensions = $definition->getArgument(0);
        $extensions[] = new Reference('brysn_formmetadata.form.extension');
        $definition->replaceArgument(0, $extensions);
    }
}