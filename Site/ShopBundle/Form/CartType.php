<?php
namespace Site\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of CartType
 *
 * @author Birko
 */
class CartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("items", 'collection', array(
            'type' => new CartItemType(),
            'required' => true,
            'allow_add' => true,
            'allow_delete' => true,
            'widget_add_btn' => array(),
        ));
    }

    public function getName()
    {
        return "site_shop_carttype";
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Site\ShopBundle\Entity\Cart'
        ));
    }
}
