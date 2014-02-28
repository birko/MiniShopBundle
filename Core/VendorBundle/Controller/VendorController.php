<?php

namespace Core\VendorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\VendorBundle\Entity\Vendor;
use Core\VendorBundle\Form\VendorType;
use Core\CommonBundle\Controller\TranslateController;

/**
 * Vendor controller.
 *
 */
class VendorController extends TranslateController
{
    protected function saveTranslation($entity, $culture, $translation)
    {
        $em = $this->getDoctrine()->getManager();
        $entity->setDescription($translation->getDescription());
        $entity->setTranslatableLocale($culture);
        $em->persist($entity);
        $em->flush();
    }

    /**
     * Lists all Vendor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $querybuilder = $em->getRepository('CoreVendorBundle:Vendor')
                    ->createQueryBuilder('v');
        $query = $querybuilder->addOrderBy('v.id')->getQuery();
        $paginator = $this->get('knp_paginator');
        $page = $this->getRequest()->get('page', 1);
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            100/*limit per page*/
        );

        return $this->render('CoreVendorBundle:Vendor:index.html.twig', array(
            'entities' => $pagination
        ));
    }

    /**
     * Finds and displays a Vendor entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreVendorBundle:Vendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreVendorBundle:Vendor:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Vendor entity.
     *
     */
    public function newAction()
    {
        $entity = new Vendor();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new Vendor());
        $form   = $this->createForm(new VendorType(), $entity, array('cultures' => $cultures));

        return $this->render('CoreVendorBundle:Vendor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'cultures' => $cultures,
        ));
    }

    /**
     * Creates a new Vendor entity.
     *
     */
    public function createAction()
    {
        $entity  = new Vendor();
        $request = $this->getRequest();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new Vendor());
        $form   = $this->createForm(new VendorType(), $entity, array('cultures' => $cultures));
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('vendor'));

        }

        return $this->render('CoreVendorBundle:Vendor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'cultures' => $cultures,
        ));
    }

    /**
     * Displays a form to edit an existing Vendor entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreVendorBundle:Vendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm = $this->createForm(new VendorType(), $entity, array('cultures' => $cultures));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreVendorBundle:Vendor:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures'    => $cultures,
        ));
    }

    /**
     * Edits an existing Vendor entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreVendorBundle:Vendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm = $this->createForm(new VendorType(), $entity, array('cultures' => $cultures));
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('vendor_edit', array('id' => $id)));
        }

        return $this->render('CoreVendorBundle:Vendor:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures'    => $cultures
        ));
    }

    /**
     * Deletes a Vendor entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreVendorBundle:Vendor')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Vendor entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('vendor'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
