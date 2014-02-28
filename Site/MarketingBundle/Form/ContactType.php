<?php

namespace Site\MarketingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class ContactType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', 'text', array(
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Meno a priezvisko'
                    )
                ))
                ->add('phone', 'text', array(
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Telefón'
                    )
                ))
                ->add('email', 'email', array(
                    'required' => true,
                    'constraints' => array(
                        new Constraints\NotBlank(),
                        new Constraints\Email(),
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
                        'placeholder' => 'Message'
                    )
                ))
                ->add('copy', 'checkbox', array(
                    'required' => false,
                    'label' => 'Send copy to your email',
                ))
                ->add('verification_code', 'text', array(
                    'required' => true,
                    'label' => 'Verification code',
                    'constraints' => array(
                        new Constraints\NotBlank(),
                    ),
                    'attr' => array(
                        'placeholder' => 'Verification code'
                    )
                ))
        ;
    }

    public function getName()
    {
        return 'contact';
    }

}
