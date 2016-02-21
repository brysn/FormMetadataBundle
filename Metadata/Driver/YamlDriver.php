<?php

namespace Brysn\FormMetadataBundle\Metadata\Driver;

use Brysn\FormMetadataBundle\Metadata\ClassMetadata;
use Brysn\FormMetadataBundle\Metadata\MethodMetadata;
use Brysn\FormMetadataBundle\Metadata\PropertyMetadata;
use Metadata\Driver\AbstractFileDriver;
use Symfony\Component\Yaml\Yaml;

class YamlDriver extends AbstractFileDriver
{
    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        $classMetadata = new ClassMetadata($class->getName());
        $config = Yaml::parse(file_get_contents($file));
        $defaults = array(
            'name' => null,
            'options' => array(),
            'fields' => array(),
            'event_listeners' => array(),
            'event_subscribers' => array(),
            'model_transformers' => array(),
            'view_transformers' => array(),
        );
        $config = array_merge($defaults, $config);

        $classMetadata->setType($config['name'], $config['options']);
        unset($config['name'], $config['options']);

        $classMetadata->setEventSubscribers($config['event_subscribers']);
        $classMetadata->setModelTransformers($config['model_transformers']);
        $classMetadata->setViewTransformers($config['view_transformers']);
        unset($config['event_subscribers'], $config['model_transformers'], $config['view_transformers']);

        foreach ($class->getMethods() as $reflectionMethod) {
            $name = $reflectionMethod->getName();
            if (array_key_exists($name, $config['event_listeners'] ?: array())) {
                $method = $config['event_listeners'][$name];
                $methodMetadata = new MethodMetadata($class->getName(), $reflectionMethod->getName());
                $methodMetadata->addEventListener($method['event'], $method['priority']);
                $classMetadata->addMethodMetadata($methodMetadata);
            }
        }
        unset($config['event_listeners']);

        $fieldDefaults = array('type' => null, 'options' => array());
        foreach ($class->getProperties() as $reflectionProperty) {
            $name = $reflectionProperty->getName();
            if (array_key_exists($name, $config['fields'] ?: array())) {
                $field = array_merge($fieldDefaults, $config['fields'][$name] ?: array());
                $propertyMetadata = new PropertyMetadata($class->getName(), $reflectionProperty->getName());
                $propertyMetadata->setField($field['type'], $field['options']);
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }
        }
        unset($config['fields']);

        foreach ($config as $key => $value) {
            throw new \RuntimeException(sprintf('Unknown configuration "%s" for class %s in %s.', $key, $class->getName(), realpath($file)));
        }

        return $classMetadata;
    }

    protected function getExtension()
    {
        return 'yml';
    }
}