<?php
namespace Core\CommonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of ProductFilterType
 *
 * @author Birko
 */
class SearchType extends AbstractType
{

    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('words', 'search',  array(
                'required' => false,
                'label' => 'Search',
                'attr' => array(
                    'placeholder' => "Search",
                )));
    }

    public function getName()
    {
        return "filter";
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\CommonBundle\Entity\Filter',
        ));
    }
}
