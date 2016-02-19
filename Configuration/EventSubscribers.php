<?php

namespace FlintLabs\Bundle\FormMetadataBundle\Configuration;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class EventSubscribers
{
    protected $values = array();

    public function __construct(array $values)
    {
        if (empty($values['value']) || !is_array($values['value'])) {
            throw new \InvalidArgumentException('@EventSubscribers should be an array of Event Subscriber class names.');
        }

        foreach ($values['value'] as $class) {
            if (!class_exists($class)) {
                throw new \InvalidArgumentException(sprintf('@EventSubscribers could not load the class "%s".', $class));
            }

            $this->values[] = $class;
        }
    }

    public function getValues()
    {
        return $this->values;
    }
}