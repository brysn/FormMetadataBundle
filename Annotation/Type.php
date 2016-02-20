<?php

namespace Brysn\FormMetadataBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Type
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

    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $this->value = $data['value'];
            unset($data['value']);
        }
        $this->options = $data;
    }
}