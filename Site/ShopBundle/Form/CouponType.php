<?php

namespace Site\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CouponType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', 'text', array(
                'required' => true,
                'label' => 'Coupon code',
                'attr' => array(
                    'placeholder' => 'Coupon code'
                )
            ))
        ;
    }

    public function getName()
    {
        return 'site_shopbundle_coupontype';
    }
}
