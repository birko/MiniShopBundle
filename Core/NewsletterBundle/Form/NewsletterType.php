<?php

namespace Core\NewsletterBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('required' => true))
            ->add('content', 'textarea', array(
                'required' => false,
                'attr' => array(
                    'class' => 'wysiwyg'
                )
            ))
        ;
    }

    public function getName()
    {
        return 'nws_newsletterbundle_newslettertype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\NewsletterBundle\Entity\Newsletter',
        ));
    }
}
