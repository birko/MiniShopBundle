<?php

namespace Core\AttributeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\AttributeBundle\Entity\AttributeName;
use Core\AttributeBundle\Form\AttributeNameType;
use Core\CommonBundle\Controller\TranslateController;

/**
 * AttributeName controller.
 *
 */
class AttributeNameController extends TranslateController
{

    protected function saveTranslation($entity, $culture, $translation)
    {
        $em = $this->getDoctrine()->getManager();
        $entity->setName($translation->getName());
        $entity->setTranslatableLocale($culture);
        $em->persist($entity);
        $em->flush();
    }

    /**
     * Lists all AttributeName entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('CoreAttributeBundle:AttributeName')->getNamesQuery();

        $page = $this->getRequest()->get("page", 1);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
           $query,
           $page /*page number*/,
           200 /*limit per page*/,
           array('distinct' => false)
       );

        return $this->render('CoreAttributeBundle:AttributeName:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    /**
     * Creates a new AttributeName entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AttributeName();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new AttributeName());
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('attributename'));
        }

        return $this->render('CoreAttributeBundle:AttributeName:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'cultures' => $cultures,
        ));
    }

    /**
    * Creates a form to create a AttributeName entity.
    *
    * @param AttributeName $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(AttributeName $entity)
    {
        $cultures = $this->container->getParameter('core.cultures');
        $form = $this->createForm(new AttributeNameType(), $entity, array(
            'action' => $this->generateUrl('attributename_create'),
            'method' => 'POST',
            'cultures' => $cultures
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));
        return $form;
    }

    /**
     * Displays a form to create a new AttributeName entity.
     *
     */
    public function newAction()
    {
        $entity = new AttributeName();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new AttributeName());
        $form   = $this->createCreateForm($entity);

        return $this->render('CoreAttributeBundle:AttributeName:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'cultures' => $cultures,
        ));
    }

    /**
     * Finds and displays a AttributeName entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreAttributeBundle:AttributeName')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AttributeName entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreAttributeBundle:AttributeName:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing AttributeName entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreAttributeBundle:AttributeName')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AttributeName entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreAttributeBundle:AttributeName:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures'    => $cultures,
        ));
    }

    /**
    * Creates a form to edit a AttributeName entity.
    *
    * @param AttributeName $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AttributeName $entity)
    {
        $cultures = $this->container->getParameter('core.cultures');
        $form = $this->createForm(new AttributeNameType(), $entity, array(
            'action' => $this->generateUrl('attributename_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'cultures' => $cultures,
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }
    /**
     * Edits an existing AttributeName entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreAttributeBundle:AttributeName')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AttributeName entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('attributename_edit', array('id' => $id)));
        }

        return $this->render('CoreAttributeBundle:AttributeName:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures' => $cultures,
        ));
    }
    /**
     * Deletes a AttributeName entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreAttributeBundle:AttributeName')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AttributeName entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('attributename'));
    }

    /**
     * Creates a form to delete a AttributeName entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('attributename_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
