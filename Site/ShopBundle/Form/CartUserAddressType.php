<?php

namespace Site\ShopBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
/**
 * Description of CheckoutType
 *
 * @author Birko
 */
class CartUserAddressType extends  CartBaseAddressType
{
    protected $userId = null;
    public function __construct($userid, $sameaddress = false)
    {
       parent::__construct($sameaddress);
       $this->userId = $userid;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $this->userId;
        $builder
            ->add('shippingAddress', 'entity', array(
            'required' => true,
            'error_bubbling' => !$this->sameaddress,
            'class' => 'CoreShopBundle:Address',
            'query_builder' => function (EntityRepository $er) use ($id) {
                        return $er->getUserAddressQueryBuilder($id);
                    },
                ))
            ->add('sameAddress', 'checkbox', array(
                'required' => false,
                'label'     => 'Is payment address same as shipping?',
                ))
            ->add('paymentAddress', 'entity', array(
            'required' => true,
            'error_bubbling' => true,
            'class' => 'CoreShopBundle:Address',
            'query_builder' => function (EntityRepository $er) use ($id) {
                        return $er->getUserAddressQueryBuilder($id);
                    },
                ));
    }
}
