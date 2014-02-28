<?php

namespace Core\MediaBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class VideoSourceTranslationType extends VideoType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!empty($options['cultures'])) {
            $builder->add('translations', 'collection', array(
                'type' => new VideoType(),
                'allow_add' => false,
                'allow_delete' => false,
                'prototype' => false,
                'by_reference' => false,
                'options' => array(
                    'required' => false,
                    'only_file' => true,
                    'change_type'     => false
            )));
        }
    }
}
