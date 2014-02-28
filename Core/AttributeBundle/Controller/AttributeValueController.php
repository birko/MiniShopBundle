<?php

namespace Core\AttributeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\AttributeBundle\Entity\AttributeValue;
use Core\AttributeBundle\Form\AttributeValueType;
use Core\CommonBundle\Controller\TranslateController;

/**
 * AttributeValue controller.
 *
 */
class AttributeValueController extends TranslateController
{

    /**
     * Lists all AttributeValue entities.
     *
     */
    public function indexAction($attribute = null, $all = false)
    {
        $em = $this->getDoctrine()->getManager();

        $attributeEntity = $em->getRepository('CoreAttributeBundle:AttributeName')->find($attribute);

        if (!$attributeEntity) {
            throw $this->createNotFoundException('Unable to find AttributeName entity.');
        }

        $query = $em->getRepository('CoreAttributeBundle:AttributeValue')->getValuesByNameQuery($attribute, $all);

        $page = $this->getRequest()->get("page", 1);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
           $query,
           $page /*page number*/,
           200 /*limit per page*/,
           array('distinct' => false)
       );

        return $this->render('CoreAttributeBundle:AttributeValue:index.html.twig', array(
            'entities' => $pagination,
            'attribute' => $attribute,
            'attributeEntity' => $attributeEntity
        ));
    }

    protected function saveTranslation($entity, $culture, $translation)
    {
        $em = $this->getDoctrine()->getManager();
        $entity->setValue($translation->getValue());
        $entity->setTranslatableLocale($culture);
        $em->persist($entity);
        $em->flush();
    }

    /**
     * Creates a new AttributeValue entity.
     *
     */
    public function createAction(Request $request, $attribute = null)
    {
        $em = $this->getDoctrine()->getManager();

        $attributeEntity = $em->getRepository('CoreAttributeBundle:AttributeName')->find($attribute);

        if (!$attributeEntity) {
            throw $this->createNotFoundException('Unable to find AttributeName entity.');
        }

        $entity = new AttributeValue();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new AttributeValue());
        $form = $this->createCreateForm($entity, $attribute);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setName($attributeEntity);
            $em->persist($entity);
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('attributevalue', array('attribute' => $attribute)));
        }

        return $this->render('CoreAttributeBundle:AttributeValue:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'cultures' => $cultures,
            'attribute' => $attribute,
        ));
    }

    /**
    * Creates a form to create a AttributeValue entity.
    *
    * @param AttributeValue $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(AttributeValue $entity, $attribute = null)
    {
        $cultures = $this->container->getParameter('core.cultures');
        $form = $this->createForm(new AttributeValueType(), $entity, array(
            'action' => $this->generateUrl('attributevalue_create', array('attribute'  => $attribute)),
            'method' => 'POST',
            'cultures' => $cultures
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));
        return $form;
    }

    /**
     * Displays a form to create a new AttributeValue entity.
     *
     */
    public function newAction($attribute = null)
    {
        $em = $this->getDoctrine()->getManager();

        $attributeEntity = $em->getRepository('CoreAttributeBundle:AttributeName')->find($attribute);

        if (!$attributeEntity) {
            throw $this->createNotFoundException('Unable to find AttributeName entity.');
        }
        $entity = new AttributeValue();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new AttributeValue());
        $form   = $this->createCreateForm($entity, $attribute);

        return $this->render('CoreAttributeBundle:AttributeValue:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'cultures' => $cultures,
            'attribute' => $attribute,
        ));
    }

    /**
     * Finds and displays a AttributeValue entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreAttributeBundle:AttributeValue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AttributeValue entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreAttributeBundle:AttributeValue:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing AttributeValue entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreAttributeBundle:AttributeValue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AttributeValue entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreAttributeBundle:AttributeValue:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures'    => $cultures,
        ));
    }

    /**
    * Creates a form to edit a AttributeValue entity.
    *
    * @param AttributeValue $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AttributeValue $entity)
    {
        $cultures = $this->container->getParameter('core.cultures');
        $form = $this->createForm(new AttributeValueType(), $entity, array(
            'action' => $this->generateUrl('attributevalue_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'cultures' => $cultures,
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }
    /**
     * Edits an existing AttributeValue entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreAttributeBundle:AttributeValue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AttributeValue entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('attributevalue_edit', array('id' => $id)));
        }

        return $this->render('CoreAttributeBundle:AttributeValue:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures' => $cultures,
        ));
    }
    /**
     * Deletes a AttributeValue entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        $attribute = null;
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreAttributeBundle:AttributeValue')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AttributeValue entity.');
            }
            if ($entity->getName()) {
                $attribute = $entity->getName()->getId();
            }
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('attributevalue', array('attribute' => $attribute)));
    }

    /**
     * Creates a form to delete a AttributeValue entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('attributevalue_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
