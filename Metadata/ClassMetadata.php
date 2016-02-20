<?php

namespace Brysn\FormMetadataBundle\Metadata;

use Metadata\MergeableClassMetadata;

class ClassMetadata extends MergeableClassMetadata
{
    protected $eventSubscribers = array();

    protected $modelTransformers = array();

    protected $type;

    protected $options = array();

    protected $viewTransformers = array();

    protected $isForm = false;

    public function getType()
    {
        return $this->type;
    }

    public function setType($type, array $options = array())
    {
        $this->type = $type;
        $this->options = $options;
        $this->isForm = true;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getEventSubscribers()
    {
        return $this->eventSubscribers;
    }

    public function setEventSubscribers($eventSubscribers)
    {
        $this->eventSubscribers = $eventSubscribers;
    }

    public function getModelTransformers()
    {
        return $this->modelTransformers;
    }

    public function setModelTransformers($modelTransformers)
    {
        $this->modelTransformers = $modelTransformers;
    }

    public function getViewTransformers()
    {
        return $this->viewTransformers;
    }

    public function setViewTransformers($viewTransformers)
    {
        $this->viewTransformers = $viewTransformers;
    }

    public function isForm()
    {
        return $this->isForm;
    }

    public function serialize()
    {
        return serialize(array(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $this->type,
            $this->options,
            $this->isForm,
            $this->eventSubscribers,
            $this->modelTransformers,
            $this->viewTransformers,
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $this->type,
            $this->options,
            $this->isForm,
            $this->eventSubscribers,
            $this->modelTransformers,
            $this->viewTransformers
        ) = unserialize($str);

        $this->reflection = new \ReflectionClass($this->name);
    }
}