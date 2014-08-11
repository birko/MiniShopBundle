<?php

namespace Core\PriceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\PriceBundle\Entity\Currency;
use Core\PriceBundle\Form\CurrencyType;

/**
 * Currency controller.
 *
 */
class CurrencyController extends Controller
{
    /**
     * Lists all Currency entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CorePriceBundle:Currency')->findAll();

        return $this->render('CorePriceBundle:Currency:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Currency entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CorePriceBundle:Currency')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Currency entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CorePriceBundle:Currency:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Currency entity.
     *
     */
    public function newAction()
    {
        $entity = new Currency();
        $form   = $this->createForm(new CurrencyType(), $entity);

        return $this->render('CorePriceBundle:Currency:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Currency entity.
     *
     */
    public function createAction()
    {
        $entity  = new Currency();
        $request = $this->getRequest();
        $form    = $this->createForm(new CurrencyType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            if ($entity->isDefault()) {
                $em->getRepository('CorePriceBundle:Currency')->updateDefaultCurrency($entity->getId());
            }

            return $this->redirect($this->generateUrl('currency_edit', array('id' => $entity->getId())));

        }

        return $this->render('CorePriceBundle:Currency:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Currency entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CorePriceBundle:Currency')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Currency entity.');
        }

        $editForm = $this->createForm(new CurrencyType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CorePriceBundle:Currency:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Currency entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CorePriceBundle:Currency')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Currency entity.');
        }

        $editForm   = $this->createForm(new CurrencyType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            if ($entity->isDefault()) {
                $em->getRepository('CorePriceBundle:Currency')->updateDefaultCurrency($entity->getId());
            }

            return $this->redirect($this->generateUrl('currency_edit', array('id' => $id)));
        }

        return $this->render('CorePriceBundle:Currency:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Currency entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CorePriceBundle:Currency')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Currency entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('currency'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
