<?php

namespace Core\MarketingBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Core\PriceBundle\Form\AbstractPriceType;

class BaseDiscountType extends AbstractPriceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('discount', 'percent', array(
                'required' => false,
                'label' =>  'Discount',
            ))
        ;
        parent::buildForm($builder, $options);
        if ($builder->has('price')) {
            $builder->get('price')->setRequired(false);
        }
        if ($builder->has('priceVAT')) {
            $builder->get('priceVAT')->setRequired(false);
        }
        if ($builder->has('VAT')) {
            $builder->get('VAT')->setRequired(false);
        }
    }

    public function getName()
    {
        return 'core_marketingbundle_basediscounttype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'Core\MarketingBundle\Entity\BaseDiscount',
        ));
    }
}
