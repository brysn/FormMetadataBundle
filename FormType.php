<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintLabs\Bundle\FormMetadataBundle;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormType extends AbstractType
{
    /** @var FormMetadata */
    private $metadata;

    public function __construct(FormMetadata $metadata)
    {
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
    }
}