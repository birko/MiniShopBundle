<?php
namespace Site\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class OptionCartItemAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $product = $options['product'];
        if (!empty($options['options'])) {
            foreach ($options['options'] as $key => $name) {
                $builder->add($key, 'entity',array(
                    'class'=> "CoreProductBundle:ProductOption",
                    'label' => $name,
                    'required' => $options['required'],
                    'expanded' => $options['expanded'],
                    'multiple' => $options['multiple'],
                    'property' => 'value',
                    'empty_value' => (!$options['expanded']) ? $name : false,
                    'query_builder' => function (EntityRepository $er) use ($product, $name) {
                            $qb = $er->getOptionsByProductQueryBuilder($product);
                            $qb->andWhere($qb->expr()->orX(
                                    $qb->expr()->isNull("po.amount"),
                                    $qb->expr()->gt("po.amount", 0)
                                ))
                                ->andWhere("an.name = :name")
                                ->setParameter('name', $name)
                            ;

                            return $qb;
                        },
                ));
            }
        }
    }

    public function getName()
    {
        return "site_shop_optioncartitemaddtype";
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'options' => array(),
            'product' => null,
            'expanded' => false,
            'multiple' => false,
        ));
    }
}
