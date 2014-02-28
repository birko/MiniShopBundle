<?php

namespace Core\BannerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BannerTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('required' => false))
            ->add('link', 'text', array('required' => false))
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
        return 'nws_bannerbundle_bannertranslationtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\BannerBundle\Entity\Banner',
        ));
    }

}
