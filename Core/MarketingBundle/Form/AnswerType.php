<?php

namespace Core\MarketingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class AnswerType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('email', 'email', array(
                    'required' => true,
                    'constraints' => array(
                        new Constraints\NotBlank(),
                    ),
                    'attr' => array(
                        'placeholder' => 'Email*'
                    )
                ))
                ->add('message', 'textarea', array(
                    'required' => true,
                    'constraints' => array(
                        new Constraints\NotBlank(),
                    ),
                    'attr' => array(
                        'placeholder' => 'Text*',
                        'class' => 'wysiwyg',
                    )
                ))
        ;
    }

    public function getName()
    {
        return 'answer';
    }

}
