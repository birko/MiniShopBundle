<?php

namespace Core\ContentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentTranslationType extends AbstractType
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
        return 'core_contentbundle_contenttranslationtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\ContentBundle\Entity\Content',
        ));
    }
}
