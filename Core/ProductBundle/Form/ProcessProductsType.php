<?php

namespace Core\ProductBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProcessProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('products', 'hidden', array('required' => true, 'label' => false));
    }

    public function getName()
    {
        return 'core_productbundle_processproductstype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }
}
