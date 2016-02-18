<?php

namespace FlintLabs\Bundle\FormMetadataBundle;

use FlintLabs\Bundle\FormMetadataBundle\Driver\MetadataDriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractExtension;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class FormExtension extends AbstractExtension
{
    /**
     * Drivers that will be used to obtaining metadata
     * @var MetadataDriverInterface[]
     */
    private $drivers = array();

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

    /**
     * Add an entity metadata reader to the readers
     * @param MetadataDriverInterface $driver
     * @return void
     */
    public function addDriver(MetadataDriverInterface $driver)
    {
        $this->drivers[] = $driver;
    }

    protected function getMetadata($name)
    {
        // Look to the readers to find metadata
        foreach ($this->drivers as $driver) {
            $metadata = $driver->getMetadata($name);
            if ($metadata) {
                $fields = $metadata->getFields();
                if (!empty($fields)) {
                    return $metadata;
                }
            }
        }

        return null;
    }
}