<?php

namespace Brysn\FormMetadataBundle;

use Brysn\FormMetadataBundle\Metadata\ClassMetadata;
use Metadata\MetadataFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractExtension;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class FormExtension extends AbstractExtension
{
    /** @var MetadataFactoryInterface */
    private $metadataFactory;

    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    public function getType($name)
    {
        $metadata = $this->getMetadata($name);
        if (!$metadata) {
            throw new InvalidArgumentException(sprintf('The type "%s" can not be loaded by this extension', $name));
        }

        return new FormType($name, $metadata);
    }

    public function hasType($name)
    {
        $metadata = $this->getMetadata($name);

        return $metadata ? true : false;
    }

    protected function getMetadata($class)
    {
        if (!class_exists($class)) {
            return null;
        }

        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->metadataFactory->getMetadataForClass($class);
        if (!$classMetadata->isForm()) {
            return null;
        }

        return $classMetadata;
    }
}