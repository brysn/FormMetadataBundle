<?php

namespace Brysn\FormMetadataBundle\Metadata\Driver;

use Brysn\FormMetadataBundle\Annotation\EventListener;
use Brysn\FormMetadataBundle\Annotation\EventSubscribers;
use Brysn\FormMetadataBundle\Annotation\Field;
use Brysn\FormMetadataBundle\Annotation\ModelTransformers;
use Brysn\FormMetadataBundle\Annotation\Type;
use Brysn\FormMetadataBundle\Annotation\ViewTransformers;
use Brysn\FormMetadataBundle\Metadata\ClassMetadata;
use Brysn\FormMetadataBundle\Metadata\MethodMetadata;
use Brysn\FormMetadataBundle\Metadata\PropertyMetadata;
use Doctrine\Common\Annotations\Reader;
use Metadata\Driver\DriverInterface;

class AnnotationDriver implements DriverInterface
{
    /** @var Reader */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new ClassMetadata($class->getName());

        foreach ($this->reader->getClassAnnotations($class) as $annotation) {
            if ($annotation instanceof Type) {
                $classMetadata->setType($annotation->value, $annotation->options);
            } else if ($annotation instanceof EventSubscribers) {
                $classMetadata->setEventSubscribers($annotation->getValues());
            } else if ($annotation instanceof ModelTransformers) {
                $classMetadata->setModelTransformers($annotation->getValues());
            } else if ($annotation instanceof ViewTransformers) {
                $classMetadata->setViewTransformers($annotation->getValues());
            }
        }

        foreach ($class->getMethods() as $reflectionMethod) {
            foreach ($this->reader->getMethodAnnotations($reflectionMethod) as $annotation) {
                if ($annotation instanceof EventListener) {
                    $methodMetadata = new MethodMetadata($class->getName(), $reflectionMethod->getName());
                    $methodMetadata->addEventListener($annotation->event, $annotation->priority);
                    $classMetadata->addMethodMetadata($methodMetadata);
                }
            }
        }

        foreach ($class->getProperties() as $reflectionProperty) {
            foreach ($this->reader->getPropertyAnnotations($reflectionProperty) as $annotation) {
                if ($annotation instanceof Field) {
                    $propertyMetadata = new PropertyMetadata($class->getName(), $reflectionProperty->getName());
                    $propertyMetadata->setField($annotation->type, $annotation->options);
                    $classMetadata->addPropertyMetadata($propertyMetadata);
                }
            }
        }

        return $classMetadata;
    }
}