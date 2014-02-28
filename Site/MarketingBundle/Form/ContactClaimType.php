<?php

namespace Site\MarketingBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Core\ShopBundle\Form\BaseAddressType;

class ContactClaimType extends ContactType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('name');
        $builder->remove('phone');
        $builder->remove('email');
        $builder->add('address', new BaseAddressType(), array('required' => true))
            ->add('orderNumber', 'text', array(
                    'required' => true,
                    'label' => 'Order number',
                    'constraints' => array(
                        new Constraints\NotBlank(),
                    ),
                    'attr' => array(
                        'placeholder' => 'Order number',
                    ),
            ))
            ->add('productNumber', 'text', array(
                    'required' => false,
                    'label' => 'Product number',
                    'attr' => array(
                        'placeholder' => 'Product number',
                    ),
            ))
            ->add('accountNumber', 'text', array(
                    'required' => false,
                    'label' => 'Account number',
                    'attr' => array(
                        'placeholder' => 'Account number',
                    ),
            ))
        ;
    }
}
