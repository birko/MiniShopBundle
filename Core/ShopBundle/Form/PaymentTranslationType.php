<?php

namespace Core\ShopBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Core\PriceBundle\Form\AbstractPriceType;

class PaymentTranslationType extends AbstractPriceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => true));
        $builder->add('description', 'textarea', array('required' => false))
        ;
    }

    protected function parentBuildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'core_shopbundle_paymenttranslationtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'Core\ShopBundle\Entity\Payment',
        ));
    }
}
