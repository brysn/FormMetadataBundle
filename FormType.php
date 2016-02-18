<?php

namespace FlintLabs\Bundle\FormMetadataBundle;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
        $fields = $this->metadata->getFields();
        foreach ($fields as $field) {
            $builder->add($field->name, $field->value, $field->options);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
        ));
    }
}