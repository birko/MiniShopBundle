<?php

namespace Core\CategoryBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class CategoryParentType extends CategoryType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', 'entity', array(
                'class' => 'CoreCategoryBundle:Category',
                'group_by' => 'menu',
                'empty_value' => " ",
                'property' => 'toOption',
                'required' =>  false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->addOrderBy('c.menu', 'ASC')
                        ->addOrderBy('c.lft', 'ASC');
                },

            ))
        ;
    }

    public function getName()
    {
        return 'categoryparent';
    }
}
