<?php

namespace Brysn\FormMetadataBundle\Configuration;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class ViewTransformers
{
    protected $values = array();

    public function __construct(array $values)
    {
        if (empty($values['value']) || !is_array($values['value'])) {
            throw new \InvalidArgumentException('@ViewTransformers should be an array of Data Transformer class names.');
        }

        foreach ($values['value'] as $class) {
            if (!class_exists($class)) {
                throw new \InvalidArgumentException(sprintf('@ViewTransformers could not load the class "%s".', $class));
            }

            $this->values[] = $class;
        }
    }

    public function getValues()
    {
        return $this->values;
    }
}