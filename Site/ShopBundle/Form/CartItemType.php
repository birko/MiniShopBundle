<?php
namespace Site\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of CartItemType
 *
 * @author Birko
 */
class CartItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $subscriber = new CartItemTypeFieldSubscriber();
        $builder->addEventSubscriber($subscriber);
    }

    public function getName()
    {
        return "site_shop_cartitemtype";
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Site\ShopBundle\Entity\CartItem'
        ));
    }
}
