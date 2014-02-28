<?php

namespace Core\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orderStatus','entity',  array(
                'class' => 'CoreShopBundle:OrderStatus',
                'label' => 'Order status',
                'property' => 'name' ,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')->orderBy('s.name', 'ASC');
                },
                'required'    => false,
                'empty_value' => 'Choose status',
                'empty_data'  => null))
            ->add('trackingId', 'text', array(
                'required'    => false,
                'label'       => 'Tracking ID',))
            ->add('shippingStatus','entity',  array(
                'class' => 'CoreShopBundle:ShippingStatus',
                'label' => 'Shipping status',
                'property' => 'name' ,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')->orderBy('s.name', 'ASC');
                },
                'required'    => false,
                'empty_value' => 'Choose status',
                'empty_data'  => null))
        ;
    }

    public function getName()
    {
        return 'core_shopbundle_ordertype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\ShopBundle\Entity\Order',
        ));
    }
}
