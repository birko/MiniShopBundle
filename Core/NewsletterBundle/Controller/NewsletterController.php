<?php

namespace Core\NewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\NewsletterBundle\Entity\Newsletter;
use Core\NewsletterBundle\Form\NewsletterType;
use Core\NewsletterBundle\Entity\SendNewsletter;
use Core\NewsletterBundle\Form\SendNewsletterType;
use Core\NewsletterBundle\Form\SendGroupNewsletterType;
use Core\NewsletterBundle\Form\SendEmailNewsletterType;

/**
 * Newsletter controller.
 *
 */
class NewsletterController extends Controller
{
    /**
     * Lists all Newsletter entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('CoreNewsletterBundle:Newsletter')
                ->createQueryBuilder('n')
                ->orderBy('n.createdAt', 'desc')
                ->getQuery();
        $page = $this->getRequest()->get("page", 1);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            200/*limit per page*/
        );

        return $this->render('CoreNewsletterBundle:Newsletter:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    /**
     * Finds and displays a Newsletter entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreNewsletterBundle:Newsletter')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreNewsletterBundle:Newsletter:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Newsletter entity.
     *
     */
    public function newAction()
    {
        $entity = new Newsletter();
        $form   = $this->createForm(new NewsletterType(), $entity);

        return $this->render('CoreNewsletterBundle:Newsletter:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Newsletter entity.
     *
     */
    public function createAction()
    {
        $entity  = new Newsletter();
        $request = $this->getRequest();
        $form    = $this->createForm(new NewsletterType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('newsletter'));

        }

        return $this->render('CoreNewsletterBundle:Newsletter:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Newsletter entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreNewsletterBundle:Newsletter')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }

        $editForm = $this->createForm(new NewsletterType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreNewsletterBundle:Newsletter:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Newsletter entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreNewsletterBundle:Newsletter')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }

        $editForm   = $this->createForm(new NewsletterType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('newsletter_edit', array('id' => $id)));
        }

        return $this->render('CoreNewsletterBundle:Newsletter:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Newsletter entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreNewsletterBundle:Newsletter')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Newsletter entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('newsletter'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function sendAction($id)
    {
        $entity = new SendNewsletter();
        $em = $this->getDoctrine()->getManager();
        $newsletter = $em->getRepository('CoreNewsletterBundle:Newsletter')->find($id);
        if (!$newsletter) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }
        $entity->setNewsletter($newsletter);

        $form   = $this->createForm(new SendNewsletterType(), $entity);

        return $this->render('CoreNewsletterBundle:Newsletter:send.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    public function doSendAction($id)
    {
        $entity = new SendNewsletter();
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $newsletter = $em->getRepository('CoreNewsletterBundle:Newsletter')->find($id);
        if (!$newsletter) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }
        $entity->setNewsletter($newsletter);
        $form   = $this->createForm(new SendNewsletterType(), $entity);
        $form->bind($request);
        if ($form->isValid()) {
            $emails =  $em->getRepository('CoreNewsletterBundle:NewsletterEmail')
                ->getEnabledEmailsQueryBuilder()
                ->select("ne.email")
                ->getQuery()
                ->getScalarResult();
            $newsletter = $entity->getNewsletter();

            return $this->sendNewsletter($newsletter, $emails);
        }

        return $this->render('CoreNewsletterBundle:Newsletter:send.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    public function sendGroupAction($id)
    {
        $entity = new SendNewsletter();
        $em = $this->getDoctrine()->getManager();
        $newsletter = $em->getRepository('CoreNewsletterBundle:Newsletter')->find($id);
        if (!$newsletter) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }
        $entity->setNewsletter($newsletter);

        $form   = $this->createForm(new SendGroupNewsletterType(), $entity);

        return $this->render('CoreNewsletterBundle:Newsletter:sendgroup.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    public function doSendGroupAction($id)
    {
        $entity = new SendNewsletter();
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $newsletter = $em->getRepository('CoreNewsletterBundle:Newsletter')->find($id);
        if (!$newsletter) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }
        $entity->setNewsletter($newsletter);
        $form   = $this->createForm(new SendGroupNewsletterType(), $entity);
        $form->bind($request);
        if ($form->isValid()) {
            $groups = array();
            $egroups = $entity->getGroups();
            if (!empty($egroups)) {
                foreach ($entity->getGroups() as $group) {
                    $groups[] = $group->getId();
                }
                if (!empty($groups)) {
                    $emails =  $em->getRepository('CoreNewsletterBundle:NewsletterEmail')
                        ->getEmailsInGroupsQueryBuilder($groups, $entity->isNot())
                        ->select("ne.email")
                        ->getQuery()
                        ->getScalarResult();
                    $newsletter = $entity->getNewsletter();

                    return $this->sendNewsletter($newsletter, $emails);
                }
            }
        }

        return $this->render('CoreNewsletterBundle:Newsletter:send.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    public function sendEmailAction($id)
    {
        $entity = new SendNewsletter();
        $em = $this->getDoctrine()->getManager();
        $newsletter = $em->getRepository('CoreNewsletterBundle:Newsletter')->find($id);
        if (!$newsletter) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }
        $entity->setNewsletter($newsletter);
        $query = $em->getRepository('CoreNewsletterBundle:NewsletterEmail')->getEnabledEmailsQuery();
        $page = $this->getRequest()->get("page", 1);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            200/*limit per page*/
        );
        $form   = $this->createForm(new SendEmailNewsletterType($pagination->getItems()), $entity);

        return $this->render('CoreNewsletterBundle:Newsletter:sendemail.html.twig', array(
            'entity' => $entity,
            'entities' => $pagination,
            'form'   => $form->createView(),
            'page'   => $page,
        ));
    }

    public function doSendEmailAction($id)
    {
        $entity = new SendNewsletter();
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $newsletter = $em->getRepository('CoreNewsletterBundle:Newsletter')->find($id);
        if (!$newsletter) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }
        $entity->setNewsletter($newsletter);
        $query = $em->getRepository('CoreNewsletterBundle:NewsletterEmail')->getEnabledEmailsQuery();
        $page = $this->getRequest()->get("page", 1);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            200/*limit per page*/
        );
        $form   = $this->createForm(new SendEmailNewsletterType($pagination->getItems()), $entity);
        $form->bind($request);
        if ($form->isValid()) {
            $emails =  $entity->getEmails();
            if (!empty($emails) && (count($emails) > 0)) {
                $newsletter = $entity->getNewsletter();

                return $this->sendNewsletter($newsletter, $emails, false);
            }
        }

        return $this->render('CoreNewsletterBundle:Newsletter:sendemail.html.twig', array(
            'entity' => $entity,
            'entities' => $pagination,
            'form'   => $form->createView(),
            'page'   => $page,
        ));
    }

    protected function sendNewsletter($newsletter, $emails, $isQuery = false)
    {
        $count = 0;
        $demails = $this->container->getParameter('default.emails');
        $body = $this->renderView('CoreNewsletterBundle:Email:newsletter.html.twig', array(
            'entity' => $newsletter,
        ));
        $title = $newsletter->getTitle();
        $sitetitle = $this->container->getParameter('site_title');
        $per_message = $this->container->getParameter('newsletter.emails.per_message');
        $i = 0;
        $first = array();
        $copy = array();
        foreach ($emails as $row) {
            $send = true;
            $email = ($isQuery) ? $row[0]: $row;
            if (is_object($email)) {
                if ($email->isEnabled()) {
                    $email = $email->getEmail();
                } else {
                    $send = false;
                }
            } elseif (is_array($email)) {
                $email = $email['email'];
            }

            if ($send) {
                $email = filter_var($email, FILTER_VALIDATE_EMAIL);
                if ($email !== false) {
                    $i ++;
                    if($i == 1) {
                        $first[]  = $email;
                    } else {
                        $copy[] = $email;
                    }
                    if ($i == $per_message) {
                        try {
                            $message = \Swift_Message::newInstance()
                                ->setSubject($title)
                                ->setFrom($demails['default'], $sitetitle)   //settings
                                ->setTo($first)
                                ->setBcc($copy)
                                ->setBody($body, 'text/html')
                                ->setContentType("text/html");
                            $this->get('swiftmailer.mailer.newsletter_mailer')->send($message);
                            $count++;
                            $message = null;
                        }
                        catch (\Exception $ex) {
                        }
                        $i = 0;
                        $first = array();
                        $copy = array();
                    }
                }
            }
        }
        
        if (!empty($first)) {
            try {
                $message = \Swift_Message::newInstance()
                    ->setSubject($title)
                    ->setFrom($demails['default'], $sitetitle)   //settings
                    ->setTo($first)
                    ->setBcc($copy)
                    ->setBody($body, 'text/html')
                    ->setContentType("text/html");
                $this->get('swiftmailer.mailer.newsletter_mailer')->send($message);
                $count++;
                $message = null;
            }
            catch (\Exception $ex) {
            }
        }

        return $this->render('CoreNewsletterBundle:Newsletter:sendsuccess.html.twig', array(
           'count' => $count,
        ));
    }
}
