<?php

namespace Core\PriceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class AbstractPriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['vat']) {
            $builder->add('price', 'money', array('required' => true));
        } else {
            $builder->add('priceVAT', 'text', array(
                'required' => true,
                'label' => 'Price VAT'
            ));
        }
        $builder->add('VAT', 'entity', array(
            'required' => true,
            'label' => 'VAT',
            'class' => 'CorePriceBundle:VAT',
            'query_builder' => function (EntityRepository $repository) {
                return $repository->createQueryBuilder('v')
                                ->add('orderBy', 'v.id ASC');

            }
        ))
        ;
        $builder->add('Currency', 'entity', array(
            'required' => true,
            'label' => 'Currency',
            'class' => 'CorePriceBundle:Currency',
            'query_builder' => function (EntityRepository $repository) {
                return $repository->createQueryBuilder('c')
                        ->add('orderBy', 'c.id ASC');

            }
        ))
        ;
    }

    public function getName()
    {
        return 'core_pricebundle_abstractpricetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\PriceBundle\Entity\AbstractPrice',
            'vat' => true
        ));
    }
}
