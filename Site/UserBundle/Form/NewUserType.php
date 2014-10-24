<?php

namespace Site\UserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Core\UserBundle\Form\NewUserType as BaseUserType;
use Core\ShopBundle\Form\AddressType;

class NewUserType extends BaseUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        if (!empty($options['address'])) {
            $builder->add('addresses', 'collection', array(
                'mapped'   => false,
                'type' => new AddressType(),
                'allow_add' => true,
                'allow_delete' => true,
                'widget_add_btn' => array('label' => 'Add'),
                'show_legend' => false,
                'prototype' => true,
                'by_reference' => false,
                'options' => array(
                    'required' => true,
                    'widget_remove_btn' => array('label' => 'Remove'),
                    'label_render' => false,
                    'requiredFields' => isset($options['address']['required']) ? $options['address']['required'] : array(),
                )));
        }
        $builder->remove('enabled');
        $builder->remove('priceGroup');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'address' => array(),
        ));
    }
}
