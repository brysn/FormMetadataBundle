<?php

namespace Brysn\FormMetadataBundle\Form;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Type extends AbstractType
{
    /** @var string */
    private $class;

    /** @var string */
    private $type;

    /** @var array */
    private $options;

    /** @var array */
    private $fields;

    /** @var array */
    private $eventListeners;

    /** @var EventSubscriberInterface[] */
    private $eventSubscribers;

    /** @var DataTransformerInterface[] */
    private $modelTransformers;

    /** @var DataTransformerInterface[] */
    private $viewTransformers;

    public function __construct($class, $type, array $options, array $fields, array $eventListeners, array $eventSubscribers, array $modelTransformers, array $viewTransformers)
    {
        $this->class = $class;
        $this->type = $type;
        $this->options = $options;
        $this->fields = $fields;
        $this->eventListeners = $eventListeners;
        $this->eventSubscribers = $eventSubscribers;
        $this->modelTransformers = $modelTransformers;
        $this->viewTransformers = $viewTransformers;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->fields as $field) {
            list($name, $type, $options) = $field;
            $builder->add($name, $type, $options);
        }

        $class = $this->class;
        foreach ($this->eventListeners as $eventListener) {
            list($method, $event, $priority) = $eventListener;
            $builder->addEventListener($event, function(FormEvent $event) use ($builder, $class, $method) {
                $data = $builder->getData();
                if (!$data instanceof $class) {
                    $data = $event->getData();
                    if (!$data instanceof $class) {
                        return;
                    }
                }

                if (!method_exists($data, $method)) {
                    return;
                }

                $data->$method($event);
            }, $priority);
        }

        foreach ($this->eventSubscribers as $eventSubscriber) {
            $builder->addEventSubscriber($eventSubscriber);
        }

        foreach ($this->modelTransformers as $modelTransformer) {
            $builder->addModelTransformer($modelTransformer);
        }

        foreach ($this->viewTransformers as $viewTransformer) {
            $builder->addViewTransformer($viewTransformer);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $options = array_merge(array(
            'data_class' => $this->class,
        ), $this->options);
        $resolver->setDefaults($options);
    }

    public function getBlockPrefix()
    {
        if ($this->type) {
            return $this->type;
        }

        return StringUtil::fqcnToBlockPrefix($this->class);
    }
}