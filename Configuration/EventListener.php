<?php

namespace Brysn\FormMetadataBundle\Configuration;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class EventListener
{
    /**
     * Default for when a type is not specified
     * @var string
     */
    public $value;

    /**
     * @var string
     */
    public $method;

    /**
     * @var int
     */
    public $priority = 0;

    public function __construct(array $values)
    {
        if (empty($values['value'])) {
            throw new \InvalidArgumentException('@EventListener needs a form event specified.');
        }

        if (!defined('Symfony\Component\Form\FormEvents::' . $values['value'])) {
            throw new \InvalidArgumentException(sprintf('@EventListener invalid event "%s".', $values['value']));
        }

        $this->value = constant('Symfony\Component\Form\FormEvents::' . $values['value']);
        unset($values['value']);

        if (isset($values['priority'])) {
            if (!is_integer($values['priority'])) {
                throw new \InvalidArgumentException('@EventListener invalid priority.');
            }
            $this->priority = $values['priority'];
            unset($values['priority']);
        }

        foreach ($values as $key => $value) {
            throw new \InvalidArgumentException(sprintf('@EventListener invalid argument "%s".', $key));
        }
    }
}