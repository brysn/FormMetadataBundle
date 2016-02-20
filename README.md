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

### @Form\EventSubscribers, @Form\ViewTransformers and @Form\ModelTransformers

    /**
     * @Form\Type()
     * @Form\EventSubscribers({"MyBundle\Form\EventSubscriber\MyEventSubscriber"})
     * @Form\ModelTransformers({"MyBundle\Form\DataTransformer\MyModelTransformer"})
     * @Form\ViewTransformers({"MyBundle\Form\DataTransformer\MyViewTransformer"})
     */
    class Contact
    {
        // ...

If dependency injection is needed for event subscribers or data transformers then define the class as a service and tag it using one of the following tags.

_brysn.form\_metadata.event\_subscriber_
_brysn.form\_metadata.model\_transformer_
_brysn.form\_metadata.view\_transformer_

    mybundle.form.event_subscriber.my_event_subscriber:
        class: MyBundle\Form\EventSubscriber\MyEventSubscriber
        arguments: ["argument"]
        tags:
            - { name: brysn.form_metadata.event_subscriber }

### @Form\EventListener

    /**
     * @Form\EventListener("PRE_SET_DATA", priority=0)
     */
    public function preSetData($event)
    {
        // perform some action
    }

Most event listener methods will need the entity to be passed as the default data when creating the form.  If the entity is not passed as the default data then the entity will not be created until the SUBMIT event and any event prior to the SUBMIT event will not be called on the entity. The SUBMIT and POST_SUBMIT events will still work as expected.

    // The preSetData method WILL NOT be called because the Contact entity will not have been created when the event fires.
    $form = $this->createForm(\MyBundle\Entity\Contact::class);

    // The preSetData method WILL be called because the Contact entity was set as the default form data.
    $form = $this->createForm(\MyBundle\Entity\Contact::class, new \MyBundle\Entity\Contact);


If it is necessary to create the form without setting the entity as the default form data and one of the early form
events is needed then use **@Form\EventSubscribers**.

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