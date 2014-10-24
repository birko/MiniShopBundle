<?php

namespace Core\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ProcessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = array();
        if (isset($options['config']['labels']) && $options['config']['labels']) {
            $choices['labels'] = "Labels";
        }

        if (isset($options['config']['status']) && $options['config']['status']) {
            $choices['orderstatus'] = "Order status";
            $builder->add('orderStatus','entity',  array(
                 'class' => 'CoreShopBundle:OrderStatus',
                 'label' => 'Order status',
                 'property' => 'name' ,
                 'query_builder' => function (EntityRepository $er) {
                     return $er->createQueryBuilder('s')->orderBy('s.name', 'ASC');
                 },
                 'required'    => false,
                 'empty_value' => 'Choose status',
                 'empty_data'  => null
            ));
        }

        if (isset($options['config']['shipping']) && $options['config']['shipping']) {
            $choices['shippingstatus'] = "Shipping status";
            $builder->add('shippingStatus','entity',  array(
                'class' => 'CoreShopBundle:ShippingStatus',
                'label' => 'Shipping status',
                'property' => 'name' ,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')->orderBy('s.name', 'ASC');
                },
                'required'    => false,
                'empty_value' => 'Choose status',
                'empty_data'  => null
            ));
        }

        $exports = array();
        if (!empty($options['export']) && isset($options['config']['export']) && $options['config']['export']) {
            $choices['export'] = "Export";
            foreach ($options['export'] as $key => $export) {
                $exports[$key] = $export['name'];
            }
            $builder->add('export', 'choice', array(
                    'required' => false,
                    'choices' => $exports,
            ));
        }
        if (!empty($choices)) {
            $builder
                ->add('type', 'choice', array(
                    'required' => true,
                    'choices' => $choices
                ));
        }
        $builder->add('processOrders', 'collection', array(
                'type' => new ProcessOrderType(),
                'label' => false,
                'required' => false,
                'allow_add' => true
        ));

    }

    public function getName()
    {
        return 'core_shopbundle_processtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\ShopBundle\Entity\Process',
            'export' => array(),
            'config' => array(),
        ));
    }
}
