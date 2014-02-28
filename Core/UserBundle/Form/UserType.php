<?php

namespace Core\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'required' => true,
                'attr' => array(
            )))
            ->add('priceGroup','entity',  array(
                'class' => 'CoreUserBundle:PriceGroup',
                'label' => 'Price Group',
                'property' => 'name' ,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('pg')->orderBy('pg.name', 'ASC');
                },
                'required'    => true,
                'empty_value' => 'Choose Price Group',
                'empty_data'  => null
            ))
            ->add('enabled', 'checkbox', array('required' => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'core_userbundle_pricegrouptype';
    }
}
