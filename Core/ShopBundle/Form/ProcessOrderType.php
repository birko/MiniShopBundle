<?php

namespace Core\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProcessOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('include', 'checkbox', array('required' => false, 'label' => false))
        ->add('orderId', 'hidden', array('required' => true, 'label' => false));

    }

    public function getName()
    {
        return 'core_shopbundle_processordertype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\ShopBundle\Entity\ProcessOrder'
        ));
    }
}
