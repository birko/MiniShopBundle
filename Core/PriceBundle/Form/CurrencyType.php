<?php

namespace Core\PriceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CurrencyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('symbol', 'text')
            ->add('code', 'currency')
            ->add('rate', 'number')
            ->add('default', 'checkbox', array('required' => false))
        ;
    }

    public function getName()
    {
        return 'core_pricebundle_currencytype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\PriceBundle\Entity\Currency'
        ));
    }
}
