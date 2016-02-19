<?php

namespace FlintLabs\Bundle\FormMetadataBundle;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormType extends AbstractType
{
    private $class;

    /** @var FormMetadata */
    private $metadata;

    public function __construct($class, FormMetadata $metadata)
    {
        $this->class = $class;
        $this->metadata = $metadata;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $modelTransformers = $this->metadata->getModelTransformers();
        if ($modelTransformers) {
            foreach ($modelTransformers->getValues() as $modelTransformer) {
                $builder->addModelTransformer(new $modelTransformer);
            }
        }

        $viewTransformers = $this->metadata->getViewTransformers();
        if ($viewTransformers) {
            foreach ($viewTransformers->getValues() as $viewTransformer) {
                $builder->addViewTransformer(new $viewTransformer);
            }
        }

        $eventSubscribers = $this->metadata->getEventSubscribers();
        if ($eventSubscribers) {
            foreach ($eventSubscribers->getValues() as $eventSubscriber) {
                $builder->addEventSubscriber(new $eventSubscriber);
            }
        }

        $fields = $this->metadata->getFields();
        foreach ($fields as $field) {
            $builder->add($field->name, $field->value, $field->options);
        }

        $eventListeners = $this->metadata->getEventListeners();
        $class = $this->class;
        foreach ($eventListeners as $eventListener) {
            $method = $eventListener->method;
            $builder->addEventListener($eventListener->value, function(FormEvent $event) use ($builder, $class, $method) {
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
            }, $eventListener->priority);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $type = $this->metadata->getType();
        $options = array_merge(array(
            'data_class' => $this->class,
        ), $type->options);

        $resolver->setDefaults($options);
    }

    public function getBlockPrefix()
    {
        $type = $this->metadata->getType();
        if ($type->value) {
            return $type->value;
        }

        return parent::getBlockPrefix();
    }
}