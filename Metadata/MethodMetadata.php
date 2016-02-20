<?php

namespace Brysn\FormMetadataBundle\Metadata;

use Metadata\MethodMetadata as BaseMethodMetadata;

class MethodMetadata extends BaseMethodMetadata
{
    protected $eventListeners = array();

    public function getEventListeners()
    {
        return $this->eventListeners;
    }

    public function addEventListener($event, $priority = 0)
    {
        $this->eventListeners[$event] = $priority;
    }

    public function serialize()
    {
        return serialize(array(
            $this->class,
            $this->name,
            $this->eventListeners,
        ));
    }

    public function unserialize($str)
    {
        list($this->class, $this->name, $this->eventListeners) = unserialize($str);

        $this->reflection = new \ReflectionMethod($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}