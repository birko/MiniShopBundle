<?php
namespace Core\ProductBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Core\CommonBundle\Form\SearchType;

/**
 * Description of ProductFilterType
 *
 * @author Birko
 */
class FilterType extends SearchType
{

    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('vendor', 'entity',  array(
                'class' => 'CoreVendorBundle:Vendor',
                'property' => 'title' ,
                'label' => 'Značka',
                'required'    => false,
                'empty_value' => ' ',
                'empty_data'  => null,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('v')
                        ->orderBy('v.title', 'ASC');
                },
            ));
        $builder->add('order', 'choice', array(
                'choices' => array(
                    "p.id asc" => "ID vzostupne",
                    "p.id desc" => "ID zostupne",
                    "p.title asc" => "Názov vzostupne",
                    "p.title desc" => "Názov zostupne",
                    "p.createdAt asc" => "Pridanie vzostupne",
                    "p.createdAt desc" => "Pridanie zostupne",
                ),
                'label' => 'Zoradenie',
                'required'    => false,
                'empty_value' => '',
                'empty_data'  => null
            ));
    }

    public function getName()
    {
        return "filter";
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\ProductBundle\Entity\Filter',
        ));
    }
}
