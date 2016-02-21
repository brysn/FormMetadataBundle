<?php

namespace Brysn\FormMetadataBundle\Form;

use Brysn\FormMetadataBundle\Metadata\ClassMetadata;
use Brysn\FormMetadataBundle\Metadata\MethodMetadata;
use Brysn\FormMetadataBundle\Metadata\PropertyMetadata;
use Metadata\MetadataFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractExtension;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class Extension extends AbstractExtension
{
    /** @var ContainerInterface */
    private $container;

    /** @var MetadataFactoryInterface */
    private $metadataFactory;

    /** @var array */
    private $eventSubscribers;

    /** @var array */
    private $modelTransformers;

    /** @var array */
    private $viewTransformers;

    /** @var array */
    private $metadata = array();

    public function __construct(ContainerInterface $container, MetadataFactoryInterface $metadataFactory, array $eventSubscribers, array $modelTransformers, array $viewTransformers)
    {
        $this->container = $container;
        $this->metadataFactory = $metadataFactory;
        $this->eventSubscribers = $eventSubscribers;
        $this->modelTransformers = $modelTransformers;
        $this->viewTransformers = $viewTransformers;
    }

    public function getType($name)
    {
        $metadata = $this->getMetadata($name);
        if (!$metadata) {
            throw new InvalidArgumentException(sprintf('The type "%s" can not be loaded by this extension', $name));
        }

        $type = $metadata->getType();
        $options = $metadata->getOptions();

        /** @var PropertyMetadata[] $propertyMetadata */
        $propertyMetadata = $metadata->propertyMetadata;
        $fields = array();
        foreach ($propertyMetadata as $field) {
            $fields[] = array($field->name, $field->type, $field->options);
        }

        /** @var MethodMetadata[] $methodMetadata */
        $methodMetadata = $metadata->methodMetadata;
        $eventListeners = array();
        foreach ($methodMetadata as $methodName => $method) {
            foreach ($method->getEventListeners() as $event => $priority) {
                $eventListeners[] = array($methodName, $event, $priority);
            }
        }

        $eventSubscribers = $this->getObjects($metadata->getEventSubscribers(), $this->eventSubscribers);
        $modelTransformers = $this->getObjects($metadata->getModelTransformers(), $this->modelTransformers);
        $viewTransformers = $this->getObjects($metadata->getViewTransformers(), $this->viewTransformers);

        return new Type($name, $type, $options, $fields, $eventListeners, $eventSubscribers, $modelTransformers, $viewTransformers);
    }

    public function hasType($name)
    {
        $metadata = $this->getMetadata($name);

        return $metadata ? true : false;
    }

    /**
     * @param $class
     * @return ClassMetadata|null
     */
    protected function getMetadata($class)
    {
        if (array_key_exists($class, $this->metadata)) {
            return $this->metadata[$class];
        }

        if (!class_exists($class)) {
            $this->metadata[$class] = null;

            return null;
        }

        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->metadataFactory->getMetadataForClass($class);
        if (!$classMetadata->isForm()) {
            $this->metadata[$class] = null;

            return null;
        }

        $this->metadata[$class] = $classMetadata;

        return $classMetadata;
    }

    protected function getObjects($classes, $services)
    {
        $objects = array();
        foreach ($classes as $class) {
            if (array_key_exists($class, $services)) {
                $object = $this->container->get($services[$class]);
            } else {
                $object = new $class;
            }
            $objects[] = $object;
        }

        return $objects;
    }
}