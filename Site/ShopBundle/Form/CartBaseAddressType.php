<?php

namespace Site\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Core\ShopBundle\Form\AddressType;
use Core\ShopBundle\Form\DeliveryAddressType;
/**
 * Description of CheckoutType
 *
 * @author Birko
 */
class CartBaseAddressType extends AbstractType
{
    protected $sameaddress;

    public function __construct($sameaddress = false)
    {
        $this->sameaddress = $sameaddress;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shippingAddress', new DeliveryAddressType(), array(
                'required' => !$this->sameaddress,
                'error_bubbling' => true,
                'requiredFields' => isset($options['address']['required']) ? $options['address']['required'] : array(),
            ))
            ->add('sameAddress', 'checkbox', array(
                'required' => false,
                'label'     => 'Is payment address same as shipping?',
            ))
            ->add('paymentAddress', new AddressType(), array(
                'required' => true,
                'error_bubbling' => true,
                'requiredFields' => isset($options['address']['required']) ? $options['address']['required'] : array(),
            ));
    }

    public function getName()
    {
        return "site_shop_baseaddress";
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Site\ShopBundle\Entity\Cart',
            'address' =>  array(),
        ));
    }
}
