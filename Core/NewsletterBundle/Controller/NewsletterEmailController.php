<?php

namespace Core\NewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\NewsletterBundle\Entity\NewsletterEmail;
use Core\NewsletterBundle\Form\NewsletterEmailType;
use Core\NewsletterBundle\Form\BatchNewsletterEmailType;
use Core\CommonBundle\Entity\Filter;
use Core\CommonBundle\Form\SearchType;

/**
 * NewsletterEmail controller.
 *
 */
class NewsletterEmailController extends Controller
{
    /**
     * Lists all NewsletterEmail entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        //filter
        $request = $this->getRequest();
        $session = $request->getSession();
        $filter = $session->get('adminnewsletterfilter', new Filter());
        if (empty($filter)) {
            $filter = new Filter();
            $session->set('adminnewsletterfilter', $filter);
        }
        $page = $this->getRequest()->get("page", $filter->getPage());
        $filter->setPage($page);
        $session->set('adminnewsletterfilter', $filter);
        $form   = $this->createForm(new SearchType(), $filter);

        if ($request->getMethod() == "POST") {
            $form->bind($request);
            if ($form->isValid()) {
                $filter->setPage(1);
                $page = 1;
                $session->set('adminnewsletterfilter', $filter);
            }
        }

        $queryBuilder = $em->getRepository('CoreNewsletterBundle:NewsletterEmail')->getEmailsQueryBuilder();
        $queryBuilder = $em->getRepository('CoreNewsletterBundle:NewsletterEmail')->filterQueryBuilder($queryBuilder, $filter);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $queryBuilder ->getQuery(),
            $page/*page number*/,
            200/*limit per page*/
        );

        return $this->render('CoreNewsletterBundle:NewsletterEmail:index.html.twig', array(
            'entities' => $pagination,
            'filter' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a NewsletterEmail entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreNewsletterBundle:NewsletterEmail')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NewsletterEmail entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreNewsletterBundle:NewsletterEmail:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new NewsletterEmail entity.
     *
     */
    public function newAction()
    {
        $entity = new NewsletterEmail();
        $form   = $this->createForm(new NewsletterEmailType(), $entity);

        return $this->render('CoreNewsletterBundle:NewsletterEmail:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new NewsletterEmail entity.
     *
     */
    public function createAction()
    {
        $entity  = new NewsletterEmail();
        $request = $this->getRequest();
        $form    = $this->createForm(new NewsletterEmailType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (!$em->getRepository('CoreNewsletterBundle:NewsletterEmail')->findOneByEmail($entity->getEmail())) {
                $em->persist($entity);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('newsletter_email'));

        }

        return $this->render('CoreNewsletterBundle:NewsletterEmail:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing NewsletterEmail entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreNewsletterBundle:NewsletterEmail')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NewsletterEmail entity.');
        }

        $editForm = $this->createForm(new NewsletterEmailType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreNewsletterBundle:NewsletterEmail:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing NewsletterEmail entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreNewsletterBundle:NewsletterEmail')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NewsletterEmail entity.');
        }

        $editForm   = $this->createForm(new NewsletterEmailType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('newsletter_email_edit', array('id' => $id)));
        }

        return $this->render('CoreNewsletterBundle:NewsletterEmail:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a NewsletterEmail entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreNewsletterBundle:NewsletterEmail')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find NewsletterEmail entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('newsletter_email'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function batchAction()
    {
        $entity = new NewsletterEmail();
        $entity->setEnabled(true);
        $form   = $this->createForm(new BatchNewsletterEmailType(), $entity);

        return $this->render('CoreNewsletterBundle:NewsletterEmail:batch.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    public function batchCreateAction()
    {
        $entity = new NewsletterEmail();
        $entity->setEnabled(true);
        $form   = $this->createForm(new BatchNewsletterEmailType(), $entity);
        $form->bind($this->getRequest());
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $emails = $entity->getEmail();
            $emails = preg_split('/([\s\,:;\/\(\)\[\]{}<>\r\n"])/', $emails, null, PREG_SPLIT_NO_EMPTY);
            $imported = array();
            if (!empty($emails)) {
                foreach ($emails as $value) {
                    $email = trim($value);
                    if (!in_array($email, $imported)) {
                        if (!empty($email)) {
                            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
                            if ($email !== false) {
                                $entity2 = $em->getRepository('CoreNewsletterBundle:NewsletterEmail')->getEmail($email);
                                if (!$entity2) {
                                    $entity2 = new NewsletterEmail();
                                    $entity2->setEmail($email);
                                    $entity2->setEnabled($entity->isEnabled());
                                }
                                if ($entity2->isEnabled()) {
                                    foreach ($entity->getGroups() as $g) {
                                        $entity2->addGroup($g);
                                    }
                                    $em->persist($entity2);
                                }
                                $imported[] = $email;
                            }
                        }
                    }
                }
                $em->flush();
            }

            return $this->redirect($this->generateUrl('newsletter_email'));
        }

        return $this->render('CoreNewsletterBundle:NewsletterEmail:batch.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
}
