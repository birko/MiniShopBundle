<?php

namespace Site\MarketingBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ContactMultiType extends ContactType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
                ->add('orderNumber', 'text', array(
                    'required' => true,
                    'label' => 'Order number',
                    'constraints' => array(
                        new Constraints\NotBlank(),
                    ),
                    'attr' => array(
                        'placeholder' => 'Order number*',
                    ),
                ))
                ->add('type', 'choice', array(
                    'required' => true,
                    'constraints' => array(
                        new Constraints\NotBlank(),
                    ),
                    'choices'=> array(
                        "Produkt" => 'Product',
                        "Objednávka" => 'Order',
                        "Faktúra" => 'Invoice',
                        "Iné" => 'Other',
                    ),
                ))
        ;
    }
}
