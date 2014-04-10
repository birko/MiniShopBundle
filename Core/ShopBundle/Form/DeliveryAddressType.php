<?php

namespace Core\ShopBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class DeliveryAddressType extends BaseAddressType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('email', 'email', array(
                'required' => isset($options['requiredFields']['email']) ? $options['requiredFields']['email'] : true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
                'attr' => array(
                    'placeholder' => 'Email',
                ))
            )
            ->add('phone', 'text', array(
                'required' => isset($options['requiredFields']['phone']) ? $options['requiredFields']['phone'] : false,
                'attr' => array(
                    'placeholder' => 'Phone',
                ))
            );
    }

}
