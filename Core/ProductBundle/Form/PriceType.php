<?php

namespace Core\ProductBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Core\PriceBundle\Form\AbstractPriceType;

class PriceType extends AbstractPriceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('type', 'choice', array(
                'choices' => array_combine($options['types'],$options['types']) ,
                'required'    => true,
                'empty_value' => 'Choose price type',
                'empty_data'  => null));
        parent::buildForm($builder, $options);
        $builder ->add('priceGroup','entity',  array(
                'class' => 'CoreUserBundle:PriceGroup',
                'label' => 'Price group',
                'property' => 'name' ,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('pg')->orderBy('pg.name', 'ASC');
                },
                'required'    => true,
                'empty_value' => 'Choose Price Group',
                'empty_data'  => null))
            ->add('priceAmount', 'number', array(
                'required' => false,
                'label' => 'Price amount',
            ))
            ->add('default', 'checkbox', array('required' => false))
        ;
    }

    public function getName()
    {
        return 'core_productbundle_pricetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'Core\ProductBundle\Entity\Price',
            'types' => array(),
        ));
    }
}
