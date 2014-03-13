<?php

namespace Core\NewsletterBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BaseNewsletterEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Newsletter email'
            )))
        ;
    }

    public function getName()
    {
        return 'nws_newsletterbundle_newsletteremailtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\NewsletterBundle\Entity\NewsletterEmail',
        ));
    }
}
