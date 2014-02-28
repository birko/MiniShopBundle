<?php

namespace Core\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\UserBundle\Entity\PriceGroup;
use Core\UserBundle\Form\PriceGroupType;

/**
 * PriceGroup controller.
 *
 */
class PriceGroupController extends Controller
{
    /**
     * Lists all PriceGroup entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CoreUserBundle:PriceGroup')->findAll();

        return $this->render('CoreUserBundle:PriceGroup:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a PriceGroup entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserBundle:PriceGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PriceGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreUserBundle:PriceGroup:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new PriceGroup entity.
     *
     */
    public function newAction()
    {
        $entity = new PriceGroup();
        $form   = $this->createForm(new PriceGroupType(), $entity);

        return $this->render('CoreUserBundle:PriceGroup:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new PriceGroup entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new PriceGroup();
        $form = $this->createForm(new PriceGroupType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            if ($entity->isDefault()) {
                $em->getRepository('CoreUserBundle:PriceGroup')->updateDefaultPriceGroup($entity->getId());
            }

            return $this->redirect($this->generateUrl('pricegroup'));
        }

        return $this->render('CoreUserBundle:PriceGroup:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing PriceGroup entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserBundle:PriceGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PriceGroup entity.');
        }

        $editForm = $this->createForm(new PriceGroupType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreUserBundle:PriceGroup:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing PriceGroup entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserBundle:PriceGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PriceGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PriceGroupType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            if ($entity->isDefault()) {
                $em->getRepository('CoreUserBundle:PriceGroup')->updateDefaultPriceGroup($entity->getId());
            }

            return $this->redirect($this->generateUrl('pricegroup_edit', array('id' => $id)));
        }

        return $this->render('CoreUserBundle:PriceGroup:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a PriceGroup entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreUserBundle:PriceGroup')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PriceGroup entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('pricegroup'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
