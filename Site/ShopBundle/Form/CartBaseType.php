<?php

namespace Site\ShopBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Description of CartBaseType
 *
 * @author Birko
 */
class CartBaseType extends CartBaseAddressType
{
    protected $paymentState = null;
    protected $shippingState = null;

    public function __construct($sameaddress = false, $paymentState = null, $shippingState = null)
    {
        parent::__construct($sameaddress);
        $this->paymentState = $paymentState;
        $this->shippingState = $shippingState;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $state = $this->paymentState;
        $builder->add("payment", 'entity', array(
                'class' => 'CoreShopBundle:Payment',
                'expanded' => false,
                'multiple' => false,
                'query_builder' => function (EntityRepository $er) use ($state) {
                    $qb = $er->getPaymentQueryBuilder();

                    return $qb;
                },
                'attr' => array(
                    'class' => 'input-xlarge',
                )
            ))
        ;
        $state = $this->shippingState;
        $builder->add("shipping", 'entity', array(
            'class' => 'CoreShopBundle:Shipping',
            'expanded' => false,
            'multiple' => false,
            'query_builder' => function (EntityRepository $er) use ($state) {
                $qb =  $er->getShippingQueryBuilder($state);

                return $qb;
            },
            'attr' => array(
                'class' => 'input-xlarge',
            )
        ));
        $builder->add("comment", 'textarea', array(
            'required' => false,
            'attr' => array(
                'class' => 'input-xlarge',
                'rows' => 5,
                'placeholder' => 'Komentár'
            )
        ));
    }

    public function getName()
    {
        return "nws_shop_basecart";
    }
}
