<?php

namespace Core\ProductBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProcessProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('include', 'checkbox', array('required' => false, 'label' => false))
        ->add('productId', 'hidden', array('required' => true, 'label' => false));

    }

    public function getName()
    {
        return 'core_productbundle_processproducttype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\ProductBundle\Entity\ProcessProduct'
        ));
    }
}
