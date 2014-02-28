<?php

namespace Core\MediaBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class ImageType extends EditImageType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', array(
                'required'    => true
            ));
        parent::buildForm($builder, $options);
    }
}
