<?php

namespace Core\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['only_file']) {
            $builder
                ->add('title', 'text',array(
                    'required'    => false
                ))
                ->add('description', 'textarea',array(
                    'required'    => false,
                    'attr' => array(
                        'class' => 'wysiwyg'
                    )
                ));
        }
    }

    public function getName()
    {
        return 'core_mediabundle_mediatranslation';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\MediaBundle\Entity\Media',
            'only_file' => false,
        ));
    }
}
