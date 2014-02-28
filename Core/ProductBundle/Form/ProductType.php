<?php

namespace Core\ProductBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductType extends ProductTranslationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!empty($options['cultures'])) {
            $builder->add('translations', 'collection', array(
                'type' => new ProductTranslationType(),
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
        $builder
            ->add('vendor', 'entity',  array(
                'class' => 'CoreVendorBundle:Vendor',
                'property' => 'title' ,
                'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('v')->orderBy('v.title', 'ASC');
                },
                'required'    => false,
                'empty_value' => 'Choose Vendor',
                'empty_data'  => null))
            ->add('enabled', 'checkbox', array('required' => false))
        ;
        if (!empty($options['tags'])) {
            $tags = array();
            foreach ($options['tags'] as $tag) {
                $tags[$tag] = $tag;
            }

            $builder->add('tags', 'choice', array(
                'required' => false,
                'choices' => $tags,
                'multiple' => true,
                'expanded' => true,
            ));
        }
    }

    public function getName()
    {
        return 'core_productbundle_producttype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'cultures' => array(),
            'tags' => array(),
        ));
    }
}
