<?php

namespace Core\NewsletterBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class BatchNewsletterEmailType extends BaseNewsletterEmailType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'textarea', array(
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Email pre odber noviniek'
            )))
        ;
        $builder
            ->add('enabled', 'checkbox', array('required' => false))
            ->add('groups', 'entity', array(
                'class' => 'CoreNewsletterBundle:NewsletterGroup',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'property' => 'name',
            ))
        ;
    }
}
