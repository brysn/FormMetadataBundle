<?php

namespace Brysn\FormMetadataBundle\Metadata;

use Metadata\PropertyMetadata as BasePropertyMetadata;

class PropertyMetadata extends BasePropertyMetadata
{
    public $type;

    public $options = array();

    public function setField($type, array $options = array())
    {
        $this->type = $type;
        $this->options = $options;
    }

    public function serialize()
    {
        return serialize(array(
            $this->class,
            $this->name,
            $this->type,
            $this->options,
        ));
    }

    public function unserialize($str)
    {
        list($this->class, $this->name, $this->type, $this->options) = unserialize($str);

        $this->reflection = new \ReflectionProperty($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}