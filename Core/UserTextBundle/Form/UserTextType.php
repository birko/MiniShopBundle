<?php

namespace Core\UserTextBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class UserTextType extends EditUserTextType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
        ;
        parent::buildForm($builder, $options);
    }
}
