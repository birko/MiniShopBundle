<?php
namespace Site\ShopBundle\Form;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Description ofTypeFieldSubscriber
 *
 * @author Birko
 */
class CartItemTypeFieldSubscriber implements EventSubscriberInterface
{
    public function __construct()
    {
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
        );
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        if ($data) {
            $attr = array();
            if (!$data->isChangeAmount()) {
                $attr['readonly'] = 'readonly';
            }
            $form->add('amount', 'number', array(
                'required' => true,
                'attr' => $attr
            ));
        }
    }
}
