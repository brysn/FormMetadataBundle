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

        $definition = $container->findDefinition('brysn_formmetadata.form.extension');

        $eventSubscribers = array();
        foreach ($container->findTaggedServiceIds('brysn.form_metadata.event_subscriber') as $serviceId => $tag) {
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new \InvalidArgumentException(sprintf('The service "%s" must be public as form metadata event subscribers are lazy-loaded.', $serviceId));
            }
            $eventSubscribers[$serviceDefinition->getClass()] = $serviceId;
        }
        $definition->replaceArgument(2, $eventSubscribers);

        $modelTransformers = array();
        foreach ($container->findTaggedServiceIds('brysn.form_metadata.model_transformer') as $serviceId => $tag) {
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new \InvalidArgumentException(sprintf('The service "%s" must be public as form metadata model transformers are lazy-loaded.', $serviceId));
            }
            $modelTransformers[$serviceDefinition->getClass()] = $serviceId;
        }
        $definition->replaceArgument(3, $modelTransformers);

        $viewTransformers = array();
        foreach ($container->findTaggedServiceIds('brysn.form_metadata.view_transformer') as $serviceId => $tag) {
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new \InvalidArgumentException(sprintf('The service "%s" must be public as form metadata view transformers are lazy-loaded.', $serviceId));
            }
            $viewTransformers[$serviceDefinition->getClass()] = $serviceId;
        }
        $definition->replaceArgument(4, $viewTransformers);
    }
}