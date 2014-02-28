<?php

namespace Core\NewsletterBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class SendEmailNewsletterType extends SendNewsletterType
{
    protected $emails = array();
    public function __construct($emails = array())
    {
       $this->emails = $emails;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if (!empty($this->emails)) {
            $emails = array();
            foreach ($this->emails as $email) {
                $emails[$email->getEmail()] = $email;
            }
            $builder->add('emails', 'choice', array(
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'choices' => $emails,
            ));
        } else {
            $builder->add('emails', 'entity', array(
                'class' => 'CoreNewsletterBundle:NewsletterEmail',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'property' => 'email',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder("ne")
                            ->orderBy("ne.email", 'asc');
                 },
            ));
        }
    }

    public function getName()
    {
        return 'nws_newsletterbundle_sendnewslettertype';
    }
}
