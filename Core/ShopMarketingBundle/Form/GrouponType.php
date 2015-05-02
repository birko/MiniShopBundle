<?php

namespace Core\ShopMarketingBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Core\MarketingBundle\Form\DiscountType;

class GrouponType extends DiscountType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', 'number');
        parent::buildForm($builder, $options);
        $builder->add("payment", 'entity', array(
                'class' => 'CoreShopBundle:Payment',
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->getPaymentQueryBuilder();

                    return $qb;
                },
            ))
            ->add("shipping", 'entity', array(
            'class' => 'CoreShopBundle:Shipping',
            'required' => false,
            'expanded' => false,
            'multiple' => false,
            'query_builder' => function (EntityRepository $er) {
                $qb =  $er->getShippingQueryBuilder();

                return $qb;
            },
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'Core\ShopMarketingBundle\Entity\Groupon'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'core_shopmarketingbundle_groupon';
    }
}
