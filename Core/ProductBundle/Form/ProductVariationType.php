<?php

namespace Core\ProductBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductVariationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $pid = $options['product'];
        $builder
            ->add('variation', 'hidden',  array(
                'required' => true,
            ))
            ->add('amount', 'number',  array(
                'required' => false,
            ));
        parent::buildForm($builder, $options);
        $builder->add('options', 'collection', array(
                'type' => "entity",
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'show_legend' => false,
                'options' => array(
                    'class' => 'CoreProductBundle:ProductOption',
                    'required' => true,
                    'empty_value' => '',
                    'empty_data'  => 'Select option',
                    'label_render' => false,
                    'query_builder' => function (EntityRepository $er) use ($pid) {
                    return $er->createQueryBuilder('o')
                        ->andWhere('o.product = :pid')
                        ->orderBy('o.name', 'ASC')
                        ->addOrderBy('o.value', 'ASC')
                        ->setParameter('pid', $pid);
                    },
        )));
    }

    public function getName()
    {
        return 'core_productbundle_productvariationtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'Core\ProductBundle\Entity\ProductVariation',
            'product' => null,
        ));
    }
}
