<?php

namespace Core\VendorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VendorTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', 'textarea', array(
                'required' => false,
                'attr' => array(
                    'class' => 'wysiwyg'
                )
            ))
        ;
    }

    public function getName()
    {
        return 'core_vendorbundle_vendortranslationtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\VendorBundle\Entity\Vendor',
        ));
    }
}
