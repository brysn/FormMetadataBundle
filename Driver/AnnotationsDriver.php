<?php
/*
 * This file is part of the Form Metadata library
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brysn\FormMetadataBundle\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Brysn\FormMetadataBundle\Configuration\EventListener;
use Brysn\FormMetadataBundle\Configuration\EventSubscribers;
use Brysn\FormMetadataBundle\Configuration\Field;
use Brysn\FormMetadataBundle\Configuration\ModelTransformers;
use Brysn\FormMetadataBundle\Configuration\Type;
use Brysn\FormMetadataBundle\Configuration\ViewTransformers;
use \Brysn\FormMetadataBundle\FormMetadata;

/**
 * @author camm (camm@flintinteractive.com.au)
 */
class AnnotationsDriver implements MetadataDriverInterface
{
    /** @var AnnotationReader */
    private $reader;

    /**
     * Read the entity and create an associated metadata
     * @param $type
     * @return null|FormMetadata
     */
    public function getMetadata($type)
    {
        $metadata = new FormMetadata();
        $this->reader = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($type);

        /** @var Type $type */
        $type = $this->getFirstClassAnnotation($reflectionClass, 'Brysn\FormMetadataBundle\Configuration\Type');
        if (!$type) {
            return null;
        }
        $metadata->setType($type);

        /** @var ModelTransformers $modelTransformers */
        $modelTransformers = $this->getFirstClassAnnotation($reflectionClass, 'Brysn\FormMetadataBundle\Configuration\ModelTransformers');
        $metadata->setModelTransformers($modelTransformers);

        /** @var ViewTransformers $viewTransformers */
        $viewTransformers = $this->getFirstClassAnnotation($reflectionClass, 'Brysn\FormMetadataBundle\Configuration\ViewTransformers');
        $metadata->setViewTransformers($viewTransformers);

        /** @var EventSubscribers $eventSubscribers */
        $eventSubscribers = $this->getFirstClassAnnotation($reflectionClass, 'Brysn\FormMetadataBundle\Configuration\EventSubscribers');
        $metadata->setEventSubscribers($eventSubscribers);

        while (is_object($reflectionClass)) {
            /** @var \ReflectionProperty[] $properties */
            $properties = $reflectionClass->getProperties();
            foreach ($properties as $property) {
                /** @var Field $field */
                $field = $this->reader->getPropertyAnnotation($property, 'Brysn\FormMetadataBundle\Configuration\Field');
                if (!empty($field)) {
                    if (empty($field->name)) {
                        $field->name = $property->getName();
                    }
                    $metadata->addField($field);
                }
            }

            /** @var \ReflectionMethod[] $methods */
            $methods = $reflectionClass->getMethods();
            foreach ($methods as $method) {
                /** @var EventListener $eventListener */
                $eventListener = $this->reader->getMethodAnnotation($method, 'Brysn\FormMetadataBundle\Configuration\EventListener');
                if (!empty($eventListener)) {
                    if (empty($eventListener->method)) {
                        $eventListener->method = $method->getName();
                    }
                    $metadata->addEventListener($eventListener);
                }
            }

            $reflectionClass = $reflectionClass->getParentClass();
        }

        return $metadata;
    }

    private function getFirstClassAnnotation(\ReflectionClass $reflectionClass, $annotationName)
    {
        $classAnnotation = $this->reader->getClassAnnotation($reflectionClass, $annotationName);
        if (!$classAnnotation) {
            $parent = $reflectionClass->getParentClass();
            if ($parent) {
                return $this->{__FUNCTION__}($parent, $annotationName);
            }
        }

        return $classAnnotation;
    }
}
