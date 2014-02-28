<?php

namespace Core\NewsletterBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class SendGroupNewsletterType extends SendNewsletterType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('groups', 'entity', array(
                'class' => 'CoreNewsletterBundle:NewsletterGroup',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'property' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder("ng")
                            ->orderBy("ng.name", 'asc');
                 },
            ))
            ->add('not', 'checkbox', array('required' => false));
        ;
    }

    public function getName()
    {
        return 'nws_newsletterbundle_sendnewslettertype';
    }
}
