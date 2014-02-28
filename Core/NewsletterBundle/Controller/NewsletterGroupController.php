<?php

namespace Core\NewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\NewsletterBundle\Entity\NewsletterGroup;
use Core\NewsletterBundle\Form\NewsletterGroupType;

/**
 * NewsletterGroup controller.
 *
 */
class NewsletterGroupController extends Controller
{
    /**
     * Lists all NewsletterGroup entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CoreNewsletterBundle:NewsletterGroup')->findAll();

        return $this->render('CoreNewsletterBundle:NewsletterGroup:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a NewsletterGroup entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreNewsletterBundle:NewsletterGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NewsletterGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreNewsletterBundle:NewsletterGroup:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new NewsletterGroup entity.
     *
     */
    public function newAction()
    {
        $entity = new NewsletterGroup();
        $form   = $this->createForm(new NewsletterGroupType(), $entity);

        return $this->render('CoreNewsletterBundle:NewsletterGroup:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new NewsletterGroup entity.
     *
     */
    public function createAction()
    {
        $entity  = new NewsletterGroup();
        $request = $this->getRequest();
        $form    = $this->createForm(new NewsletterGroupType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('newsletter_group'));

        }

        return $this->render('CoreNewsletterBundle:NewsletterGroup:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing NewsletterGroup entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreNewsletterBundle:NewsletterGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NewsletterGroup entity.');
        }

        $editForm = $this->createForm(new NewsletterGroupType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreNewsletterBundle:NewsletterGroup:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing NewsletterGroup entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreNewsletterBundle:NewsletterGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NewsletterGroup entity.');
        }

        $editForm   = $this->createForm(new NewsletterGroupType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('newsletter_group_edit', array('id' => $id)));
        }

        return $this->render('CoreNewsletterBundle:NewsletterGroup:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a NewsletterGroup entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreNewsletterBundle:NewsletterGroup')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find NewsletterGroup entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('newsletter_group'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
