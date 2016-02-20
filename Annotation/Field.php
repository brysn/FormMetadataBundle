<?php

namespace Brysn\FormMetadataBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Contains the configuration elements for the field
 *
 * e.g. @Form\Field("text", foo="bar")
 *
 * @Annotation
 */
class Field
{
    /**
      * @var string
     */
    public $type;

    /**
      * @var array
     */
    public $options = array();

    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $this->type = $data['value'];
            unset($data['value']);
        }
        $this->options = $data;
    }
}