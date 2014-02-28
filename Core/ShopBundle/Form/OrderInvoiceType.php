<?php

namespace Core\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderInvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('invoiceNumber','text',  array(
                'required'=> true,
                'label' => 'Invoice number',
             ))
            ->add('variableNumber','text',  array(
                'required'=> false,
                'label' => 'Variable number',
             ))
            ->add('dueDays','number',  array(
                'required'=> false,
                'label' => 'Due days',
             ))
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
