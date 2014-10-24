<?php

namespace Core\ProductBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class ProductCategoriesType extends ProductTranslationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categories', 'entity', array(
                'class' => 'CoreCategoryBundle:Category',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'property'=> 'toOption',
                'group_by' => 'menu',
                'query_builder' => function (EntityRepository $er) {
                    return $er->getCategoriesQueryBuilder()
                        ->orderBy("c.menu")
                        ->addOrderBy("c.lft");
                },
            ))
        ;
    }

}
