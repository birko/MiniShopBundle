<?php

namespace Core\ProductBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('required' => true))

            ->add('shortDescription', 'textarea', array(
                'required' => false,
                'label' => 'Short description',
                'attr' => array(
                    'class' => 'wysiwyg'
                )
            ))
            ->add('longDescription', 'textarea', array(
                'required' => false,
                'label' => 'Long description',
                'attr' => array(
                    'class' => 'wysiwyg'
                )
            ))
        ;
    }

    public function getName()
    {
        return 'core_productbundle_producttranslationtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\ProductBundle\Entity\Product',
        ));
    }
}
