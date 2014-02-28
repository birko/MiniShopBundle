<?php

namespace Core\UserTextBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserTextTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', 'textarea', array(
                'required' => false,
                'attr' => array(
                    'class' => 'wysiwyg'
                )
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\UserTextBundle\Entity\UserText'
        ));
    }

    public function getName()
    {
        return 'core_usertextbundle_usertexttranslationtype';
    }
}
