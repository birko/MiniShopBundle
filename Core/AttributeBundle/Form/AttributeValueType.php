<?php

namespace Core\AttributeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AttributeValueType extends AttributeValueTranslationType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!empty($options['cultures'])) {
            $builder->add('translations', 'collection', array(
                'type' => new AttributeValueTranslationType(),
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
        $builder->add('serialized', 'checkbox', array('required' => false));

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'cultures' => array(),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'core_attributebundle_attributevalue';
    }
}
