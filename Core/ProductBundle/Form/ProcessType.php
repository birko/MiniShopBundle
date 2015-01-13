<?php

namespace Core\ProductBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ProcessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!empty($options['actions'])) {
            $actions = array();
            foreach ($options['actions'] as $key => $action) {
                $actions[$action['action']] = $action['name'];
            }
            $builder->add('action', 'choice', array(
                    'required' => false,
                    'choices' => $actions,
            ));
        }
        
        $builder->add('processProducts', 'collection', array(
                'type' => new ProcessProductType(),
                'label' => false,
                'required' => false,
                'allow_add' => true
        ));
    }

    public function getName()
    {
        return 'core_productbundle_processtype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\ProductBundle\Entity\Process',
            'actions' => array(),
        ));
    }
}
