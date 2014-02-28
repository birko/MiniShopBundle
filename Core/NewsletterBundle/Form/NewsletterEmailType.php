<?php

namespace Core\NewsletterBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class NewsletterEmailType extends BaseNewsletterEmailType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
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
