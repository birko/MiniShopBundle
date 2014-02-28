<?php
namespace Site\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of CartPaymentShippingType
 *
 * @author Birko
 */
class CartOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("comment", 'textarea', array('required' => false,));
    }

    public function getName()
    {
        return "nws_shop_order";
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Site\ShopBundle\Entity\Cart',
        ));
    }
}
