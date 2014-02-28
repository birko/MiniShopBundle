<?php

namespace Core\MarketingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type')
            ->add('message')
            ->add('answer')
            ->add('title')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('order')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\MarketingBundle\Entity\Message'
        ));
    }

    public function getName()
    {
        return 'core_marketingbundle_messagetype';
    }
}
