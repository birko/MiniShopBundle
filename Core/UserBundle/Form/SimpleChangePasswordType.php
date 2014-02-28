<?php

namespace Core\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SimpleChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('new_password','repeated', array(
                    'first_name' => 'password',
                    'second_name' => 'confirm',
                    'invalid_message' => 'The password and confirm fields must match.',
                    'type' => 'password'
            ))
            ->setAttribute('show_legend', false);
    }

    public function getName()
    {
        return 'user';
    }
}
