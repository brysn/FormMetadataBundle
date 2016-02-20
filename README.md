# Form Metadata reader for Symfony 3

Facilitates the basic configuration of form fields from metadata that is defined elsewhere, such as through annotations
in the entity or with an external yaml file (TODO). Allows for more generic handling of form types through controllers,
making them able to deal with dynamic entity/forms (such as for use with CMS sites).

See the form fields [Annotations Reference](https://github.com/FlintLabs/FormMetadataBundle/wiki/Annotations-reference)

Note: People may want to consider the use of Symfony 3 Abstract Forms to configure their forms external to the controller
as a best practice.

## Annotations Example

**Standard form builder**

    use Symfony\Component\Form\Extension\Core\Type\DateType;
    // ...

    ->add('dueDate', DateType::class, array('widget' => 'single_text'))

**Using annotations in your entity**

    /**
     * @Form\Field("Symfony\Component\Form\Extension\Core\Type\DateType", widget="single_text")
     */

**Embedded Entity Example**

    /**
     * Refer to http://symfony.com/doc/current/book/forms.html#embedded-forms
     *
     * @Form\Field("MyBundle\Entity\Category")
     */

### Entity with some basic form annotations

    use Brysn\FormMetadataBundle\Annotation as Form;
    use Symfony\Bundle\Validator\Constraints as Assert;

    /**
     * @Form\Type(allow_extra_fields=true)
     */
    class Contact
    {
        /**
         * @Form\Field()
         * @Assert\NotBlank()
         */
        public $name;

        /**
         * @Form\Field("Symfony\Component\Form\Extension\Core\Type\TextareaType")
         */
        public $message;
    }

### Simple controller

    class MyController
    {
        public function contactAction(Request $request)
        {
            $form = $this->createForm(\MyBundle\Entity\Contact::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // perform some action, such as saving the task to the database

                return $this->redirectToRoute('task_success');
            }
        }
    }

## Installation

### Update your deps file

    [Form-Metadata]
        git=git@github.com:Brysn/FormMetadataBundle.git
        target=/Brysn/FormMetadataBundle

### Update your vendors

    php bin/vendors update

### Update your autoloader

    // vendor/composer/autoload_namespaces.php
    return array(
        // ...
        'Brysn\\FormMetadataBundle' => __DIR__.'/../vendor/bundles/',
        // ...
    );

### Register the bundle references

    // app/AppKernel.php
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Brysn\FormMetadataBundle\BrysnFormMetadataBundle(),
            // ...
        ];
        // ...
    }
