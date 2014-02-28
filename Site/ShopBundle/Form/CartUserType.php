<?php

namespace Site\ShopBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Description of CartBaseType
 *
 * @author Birko
 */
class CartUserType extends CartUserAddressType
{
    protected $state = null;

    public function __construct($userid, $sameaddress = false, $state = null)
    {
        parent::__construct($userid, $sameaddress);
        $this->state = $state;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $state = $this->state;
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
            ->add("shipping", 'entity', array(
            'class' => 'CoreShopBundle:Shipping',
            'expanded' => false,
            'multiple' => false,
            'query_builder' => function (EntityRepository $er) use ($state) {
                $qb =  $er->getShippingQueryBuilder($state);

                return $qb;
            },
            'attr' => array(
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
        return "nws_shop_usercart";
    }
}
