<?php

namespace Core\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\ShopBundle\Entity\ShippingStatus;
use Core\ShopBundle\Form\ShippingStatusType;

/**
 * ShippingStatus controller.
 *
 */
class ShippingStatusController extends Controller
{
    /**
     * Lists all ShippingStatus entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CoreShopBundle:ShippingStatus')->findAll();

        return $this->render('CoreShopBundle:ShippingStatus:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a ShippingStatus entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:ShippingStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ShippingStatus entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreShopBundle:ShippingStatus:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new ShippingStatus entity.
     *
     */
    public function newAction()
    {
        $entity = new ShippingStatus();
        $form   = $this->createForm(new ShippingStatusType(), $entity);

        return $this->render('CoreShopBundle:ShippingStatus:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new ShippingStatus entity.
     *
     */
    public function createAction()
    {
        $entity  = new ShippingStatus();
        $request = $this->getRequest();
        $form    = $this->createForm(new ShippingStatusType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('shippingstatus'));

        }

        return $this->render('CoreShopBundle:ShippingStatus:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing ShippingStatus entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:ShippingStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ShippingStatus entity.');
        }

        $editForm = $this->createForm(new ShippingStatusType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreShopBundle:ShippingStatus:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing ShippingStatus entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:ShippingStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ShippingStatus entity.');
        }

        $editForm   = $this->createForm(new ShippingStatusType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('shippingstatus_edit', array('id' => $id)));
        }

        return $this->render('CoreShopBundle:ShippingStatus:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ShippingStatus entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreShopBundle:ShippingStatus')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ShippingStatus entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('shippingstatus'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
