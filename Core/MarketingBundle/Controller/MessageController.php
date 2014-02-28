<?php

namespace Core\MarketingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\MarketingBundle\Entity\Message;
use Core\MarketingBundle\Form\MessageType;
use Core\MarketingBundle\Form\AnswerType;

/**
 * Message controller.
 *
 */
class MessageController extends Controller
{
    /**
     * Lists all Message entities.
     *
     */
    public function indexAction($type = null)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('CoreMarketingBundle:Message')->getMessagesByTypeQuery($type);
        $paginator = $this->get('knp_paginator');
        $page = $this->getRequest()->get('page', 1);
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            100/*limit per page*/
        );

        return $this->render('CoreMarketingBundle:Message:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    /**
     * Finds and displays a Message entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreMarketingBundle:Message')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Message entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $data = array();
        $ms = $entity->getMessage();
        $minishop  = $this->container->getParameter('minishop');
        if ((!empty($ms['orderId'])) && isset($minishop['shop']) && $minishop['shop']) {
            $order = $em->getRepository('CoreShopBundle:Order')->find($ms['orderId']);
            if ($order) {
                $data['email'] = $order->getInvoiceAddress()->getEmail();
            }
        } elseif (!empty($ms)) {
            if (isset($ms['email'])) {
                $data['email'] = $ms['email'];
            }
        }
        $form   = $this->createForm(new AnswerType(), $data);
        $request = $this->getRequest();
        if ($request->getMethod() == "POST") {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $entity->setAnswer(array(
                    'email' => $data['email'],
                    'message' => $data['message'],
                ));
                $entity->setUpdatedAt(new \DateTime());
                $em->persist($entity);
                $em->flush();
                $emails = $this->container->getParameter('default.emails');
                $frommail = $emails['default'];
                switch ($entity->getType()) {
                    case "claim":
                        $frommail = $emails['claim'];
                        break;
                    case "order":
                        $frommail = $emails['order'];
                        break;
                    case "contact":
                    case "contactmulti":
                        $frommail = $emails['contact'];
                        break;
                    default:
                        $frommail = $emails['default'];
                        break;
                }
                $message = \Swift_Message::newInstance()
                    ->setSubject('Re:'  .$entity->getTitle())
                    ->setFrom($frommail, $this->container->getParameter('site_title'))   //settings
                    ->setTo(array($data['email']))
                    ->setBody($this->renderView('CoreMarketingBundle:Email:answer.html.twig', array(
                        'data' => $data,
                    )), 'text/html')
                    ->setContentType("text/html");
                $this->get('swiftmailer.mailer.site_mailer')->send($message);
            }
        }

        return $this->render('CoreMarketingBundle:Message:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Message entity.
     *
     */
    public function newAction()
    {
        $entity = new Message();
        $form   = $this->createForm(new MessageType(), $entity);

        return $this->render('CoreMarketingBundle:Message:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Message entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Message();
        $form = $this->createForm(new MessageType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('message_show', array('id' => $entity->getId())));
        }

        return $this->render('CoreMarketingBundle:Message:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Message entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreMarketingBundle:Message')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Message entity.');
        }

        $editForm = $this->createForm(new MessageType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreMarketingBundle:Message:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Message entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreMarketingBundle:Message')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Message entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MessageType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('message_edit', array('id' => $id)));
        }

        return $this->render('CoreMarketingBundle:Message:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Message entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);
        $type = null;
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreMarketingBundle:Message')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Message entity.');
            }
            $type = $entity->getType();
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('message', array('type' => $type)));
    }

    public function updateDateAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreMarketingBundle:Message')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Message entity.');
        }
        $entity->setUpdatedAt(new \DateTime());
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('message_show', array('id' => $entity->getId())));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
