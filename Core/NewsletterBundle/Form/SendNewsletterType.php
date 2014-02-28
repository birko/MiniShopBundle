<?php

namespace Core\NewsletterBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class SendNewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('newsletter', 'entity', array(
                'class' => 'CoreNewsletterBundle:Newsletter',
                'multiple' => false,
                'expanded' => false,
                'required' => true,
                'property' => 'title',
                'empty_value' => '',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder("n")
                            ->orderBy("n.createdAt", 'desc');
                 },
            ))
        ;
    }

    public function getName()
    {
        return 'nws_newsletterbundle_sendnewslettertype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\NewsletterBundle\Entity\SendNewsletter',
        ));
    }
}
