<?php
namespace Site\ShopBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Description of CartItemType
 *
 * @author Birko
 */
class CartItemAddType extends CartItemType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('name', 'hidden');
        $builder->add('description', 'hidden');
        $builder->add('price', 'hidden');
        $builder->add('priceVAT', 'hidden');
        $builder->add('productId', 'hidden');
        if ($options['variations'] > 0) {
            $product = $options['product'];
            $builder->add('variations', 'entity', array(
                'label'   => false,
                'expanded' => $options['expandedOptions'],
                'class'=> "CoreProductBundle:ProductVariation",
                'required' => $options['requireVariations'],
                'query_builder' => function (EntityRepository $er) use ($product) {
                        $qb = $er->getProductVariationsQueryBuilder($product);

                        return $qb;
                    },
            ));
        } elseif (!empty($options['options'])) {
            $builder->add('options', new OptionCartItemAddType(), array(
                'required' => $options['requireOptions'],
                'expanded' => $options['expandedOptions'],
                'multiple' => $options['multipleOptions'],
                'options' => $options['options'],
                'product' => $options['product'],
                'label' => false,
            ));
        }
    }

    public function getName()
    {
        return "core_shop_cartitemaddtype";
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'options' => array(),
            'product' => null,
            'requireOptions' => true,
            'expandedOptions' => false,
            'multipleOptions' => false,
            'variations' => false,
            'requireVariations' => true,
        ));
    }
}
