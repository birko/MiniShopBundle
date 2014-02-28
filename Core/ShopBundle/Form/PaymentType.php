<?php

namespace Core\ShopBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentType extends PaymentTranslationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!empty($options['cultures'])) {
            $builder->add('translations', 'collection', array(
                'type' => new PaymentTranslationType(),
                'allow_add' => false,
                'allow_delete' => false,
                'prototype' => false,
                'by_reference' => false,
                'options' => array(
                    'required' => false,
            )));
        } else {
            parent::buildForm($builder, $options);
        }
        $this->parentBuildForm($builder, $options);
        $builder->add('enabled', 'checkbox', array('required' => false));
    }

    public function getName()
    {
        return 'core_shopbundle_paymenttype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'cultures' => array(),
        ));
    }
}
