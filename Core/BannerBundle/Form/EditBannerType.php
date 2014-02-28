<?php

namespace Core\BannerBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EditBannerType extends BannerTranslationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!empty($options['cultures'])) {
                $builder->add('translations', 'collection', array(
                    'type' => new BannerTranslationType(),
                    'allow_add' => false,
                    'allow_delete' => false,
                    'prototype' => false,
                    'by_reference' => false,
                    'options' => array(
                        'required' => false,
                )));
        } else {
            parent::buildForm($builder, $options);
        }
    }

    public function getName()
    {
        return 'nws_bannerbundle_bannertype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'cultures' => array(),
        ));
    }

}
