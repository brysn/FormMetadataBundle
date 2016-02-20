<?php
/*
 * This file is part of the Form Metadata library
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brysn\FormMetadataBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Contains the configuration elements for the field
 *
 * e.g. @Form\Field("text", foo="bar")
 *
 * @author camm (camm@flintinteractive.com.au)
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