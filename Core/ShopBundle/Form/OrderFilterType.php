<?php
namespace Core\ShopBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Core\CommonBundle\Form\SearchType;

/**
 * Description of OrderFilterType
 *
 * @author Birko
 */
class OrderFilterType extends SearchType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent:: buildForm($builder, $options);
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
            ->add('shippingState','entity',  array(
                'class' => 'CoreShopBundle:State',
                'label' => 'Shipping state',
                'property' => 'name' ,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')->orderBy('s.name', 'ASC');
                },
                'required'    => false,
                'empty_value' => 'Choose state',
                'empty_data'  => null))
        ;
    }

    public function getName()
    {
        return 'core_shopbundle_orderfilertype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\ShopBundle\Entity\OrderFilter',
        ));
    }
}
