<?php

namespace Brysn\FormMetadataBundle;

use Brysn\FormMetadataBundle\Metadata\ClassMetadata;
use Brysn\FormMetadataBundle\Metadata\MethodMetadata;
use Brysn\FormMetadataBundle\Metadata\PropertyMetadata;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormType extends AbstractType
{
    private $class;

    /** @var ClassMetadata */
    private $metadata;

    public function __construct($class, ClassMetadata $metadata)
    {
        $this->class = $class;
        $this->metadata = $metadata;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $modelTransformers = $this->metadata->getModelTransformers();
        foreach ($modelTransformers as $modelTransformer) {
            $builder->addModelTransformer(new $modelTransformer);
        }

        $viewTransformers = $this->metadata->getViewTransformers();
        foreach ($viewTransformers as $viewTransformer) {
            $builder->addViewTransformer(new $viewTransformer);
        }

        $eventSubscribers = $this->metadata->getEventSubscribers();
        foreach ($eventSubscribers as $eventSubscriber) {
            $builder->addEventSubscriber(new $eventSubscriber);
        }

        /** @var PropertyMetadata[] $fields */
        $fields = $this->metadata->propertyMetadata;
        foreach ($fields as $field) {
            $builder->add($field->name, $field->type, $field->options);
        }

        /** @var MethodMetadata[] $methods */
        $methods = $this->metadata->methodMetadata;
        $class = $this->class;
        foreach ($methods as $methodName => $methodMetadata) {
            foreach ($methodMetadata->getEventListeners() as $event => $priority) {
                $builder->addEventListener($event, function(FormEvent $event) use ($builder, $class, $methodName) {
                    $data = $builder->getData();
                    if (!$data instanceof $class) {
                        $data = $event->getData();
                        if (!$data instanceof $class) {
                            return;
                        }
                    }

                    if (!method_exists($data, $methodName)) {
                        return;
                    }

                    $data->$methodName($event);
                }, $priority);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $options = array_merge(array(
            'data_class' => $this->class,
        ), $this->metadata->getOptions());
        $resolver->setDefaults($options);
    }

    public function getBlockPrefix()
    {
        $type = $this->metadata->getType();
        if ($type) {
            return $type;
        }

        return parent::getBlockPrefix();
    }
}