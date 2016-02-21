<?php

namespace Brysn\FormMetadataBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class BrysnFormMetadataExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $directories = array();
        foreach ($config['directories'] as $directory) {
            $directories[$directory['namespace_prefix']] = $directory['path'];
        }

        $container
            ->getDefinition('brysn_form_metadata.metadata.file_locator')
            ->replaceArgument(0, $directories)
        ;

        $definition = $container->getDefinition('brysn_form_metadata.metadata.factory');
        if ($config['debug']) {
            $definition->removeMethodCall('setCache');
        } else {
            $cacheDirectory = $container->getDefinition('brysn_form_metadata.metadata.cache')->getArgument(0);
            $cacheDirectory = $container->getParameterBag()->resolveValue($cacheDirectory);
            if (!is_dir($cacheDirectory)) {
                mkdir($cacheDirectory, 0777, true);
            }
        }
        $definition->replaceArgument(2, $config['debug']);
    }
}