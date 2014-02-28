<?php

namespace Core\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;
use Doctrine\ORM\EntityRepository;

class BaseAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 3)),
                ),
                'attr' => array(
                    'placeholder' => 'Name',
                )
            ))
            ->add('surname', 'text', array(
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 3)),
                ),
                'attr' => array(
                    'placeholder' => 'Surname',
                )
            ))
            ->add('company', 'text', array(
                'required' => isset($options['requiredFields']['company']) ? $options['requiredFields']['company'] : false,
                'attr' => array(
                    'placeholder' => 'Company',
                )
            ))
            ->add('street', 'text', array(
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
                'attr' => array(
                    'placeholder' => 'Street',
                )
            ))
             ->add('houseNumber', 'text', array(
                'required' => true,
                'label' => 'Number',
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
                'attr' => array(
                    'placeholder' => 'Number',
                    'size' => 10
                )
            ))
            ->add('city', 'text', array(
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
                'attr' => array(
                    'placeholder' => 'City',
                )
            ))
            ->add('ZIP', 'text', array(
                'required' => true,
                'label' => 'ZIP',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 5)),
                ),
                'attr' => array(
                    'placeholder' => 'ZIP',
                )
            ))
            ->add('state','entity',  array(
                'class' => 'CoreShopBundle:State',
                'label' => 'State',
                'property' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')->orderBy('s.name', 'ASC');
                },
                'required'    => true,
                'empty_value' => 'Choose state',
                'empty_data'  => null,
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
                'attr' => array(
                )
            ))
        ;
    }

    public function getName()
    {
        return 'core_shopbundle_addresstype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\ShopBundle\Entity\Address',
            'requiredFields' => array(),
        ));
    }

}
