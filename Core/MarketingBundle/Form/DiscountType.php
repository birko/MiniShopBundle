<?php

namespace Core\MarketingBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DiscountType extends BaseDiscountType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('active', 'checkbox', array('required' => false))
        ;
    }

    public function getName()
    {
        return 'core_marketingbundle_discounttype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'Core\MarketingBundle\Entity\Discount',
        ));
    }
}
