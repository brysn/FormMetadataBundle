<?php
/*
 * This file is part of the Form Metadata library
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintLabs\Bundle\FormMetadataBundle;

use FlintLabs\Bundle\FormMetadataBundle\Configuration\EventListener;
use FlintLabs\Bundle\FormMetadataBundle\Configuration\EventSubscribers;
use FlintLabs\Bundle\FormMetadataBundle\Configuration\Field;
use FlintLabs\Bundle\FormMetadataBundle\Configuration\ModelTransformers;
use FlintLabs\Bundle\FormMetadataBundle\Configuration\Type;
use FlintLabs\Bundle\FormMetadataBundle\Configuration\ViewTransformers;

/**
 * The meta data containing the configuration of the form
 * @author camm (camm@flintinteractive.com.au)
 */
class FormMetadata
{
    /** @var EventListener[] */
    protected $eventListeners = array();

    /** @var EventSubscribers */
    protected $eventSubscribers;

    /**
     * @var Field[]
     */
    protected $fields = array();

    /**
     * TODO: Add in support for field groups
     * @var array
     */
    protected $groups = array();

    /**
     * @var ModelTransformers
     */
    protected $modelTransformers;

    /**
     * @var Type
     */
    protected $type;

    /**
     * @var ViewTransformers
     */
    protected $viewTransformers;

    /**
     * Add a field configuration
     * @param Field $field
     * @return void
     */
    public function addField(Field $field)
    {
        $this->fields[] = $field;
    }

    /**
     * @return array|Configuration\Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param EventListener $eventListener
     */
    public function addEventListener(EventListener $eventListener)
    {
        $this->eventListeners[] = $eventListener;
    }

    /**
     * @return array|Configuration\EventListener[]
     */
    public function getEventListeners()
    {
        return $this->eventListeners;
    }

    /**
     * @param Type $type
     */
    public function setType(Type $type)
    {
        $this->type = $type;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return ModelTransformers
     */
    public function getModelTransformers()
    {
        return $this->modelTransformers;
    }

    /**
     * @param ModelTransformers $modelTransformers
     */
    public function setModelTransformers($modelTransformers)
    {
        $this->modelTransformers = $modelTransformers;
    }

    /**
     * @return EventSubscribers
     */
    public function getEventSubscribers()
    {
        return $this->eventSubscribers;
    }

    /**
     * @param EventSubscribers $eventSubscribers
     */
    public function setEventSubscribers($eventSubscribers)
    {
        $this->eventSubscribers = $eventSubscribers;
    }

    /**
     * @return ViewTransformers
     */
    public function getViewTransformers()
    {
        return $this->viewTransformers;
    }

    /**
     * @param ViewTransformers $viewTransformers
     */
    public function setViewTransformers($viewTransformers)
    {
        $this->viewTransformers = $viewTransformers;
    }
}