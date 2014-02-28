<?php

namespace Core\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\ShopBundle\Entity\OrderStatus;
use Core\ShopBundle\Form\OrderStatusType;

/**
 * OrderStatus controller.
 *
 */
class OrderStatusController extends Controller
{
    /**
     * Lists all OrderStatus entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CoreShopBundle:OrderStatus')->findAll();

        return $this->render('CoreShopBundle:OrderStatus:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a OrderStatus entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:OrderStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderStatus entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreShopBundle:OrderStatus:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new OrderStatus entity.
     *
     */
    public function newAction()
    {
        $entity = new OrderStatus();
        $form   = $this->createForm(new OrderStatusType(), $entity);

        return $this->render('CoreShopBundle:OrderStatus:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new OrderStatus entity.
     *
     */
    public function createAction()
    {
        $entity  = new OrderStatus();
        $request = $this->getRequest();
        $form    = $this->createForm(new OrderStatusType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('orderstatus'));

        }

        return $this->render('CoreShopBundle:OrderStatus:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing OrderStatus entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:OrderStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderStatus entity.');
        }

        $editForm = $this->createForm(new OrderStatusType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreShopBundle:OrderStatus:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing OrderStatus entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:OrderStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderStatus entity.');
        }

        $editForm   = $this->createForm(new OrderStatusType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('orderstatus_edit', array('id' => $id)));
        }

        return $this->render('CoreShopBundle:OrderStatus:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a OrderStatus entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreShopBundle:OrderStatus')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find OrderStatus entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('orderstatus'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
