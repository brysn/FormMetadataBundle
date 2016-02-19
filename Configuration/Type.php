<?php

namespace Brysn\FormMetadataBundle\Configuration;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Type extends Annotation
{
    /**
     * Default for when a type is not specified
     * @var string
     */
    public $value;

    /**
     * The options to pass through
     * @var array
     */
    public $options = array();

    /**
     * Magic method for passing options through the annotation that are undefined
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->options[$name] = $value;
    }
}