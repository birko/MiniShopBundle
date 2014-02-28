<?php

namespace Core\ShopBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Core\MarketingBundle\Form\DiscountType;

class CouponType extends DiscountType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', 'text', array('required' => true));
        parent::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'core_shopbundle_coupontype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'Core\ShopBundle\Entity\Coupon',
        ));
    }
}
