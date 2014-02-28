<?php

namespace Core\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\ShopBundle\Entity\Shipping;
use Core\ShopBundle\Form\ShippingType;
use Core\CommonBundle\Controller\TranslateController;

/**
 * Shipping controller.
 *
 */
class ShippingController extends TranslateController
{
    protected function saveTranslation($entity, $culture, $translation)
    {
        $em = $this->getDoctrine()->getManager();
        $entity->setName($translation->getName());
        $entity->setDescription($translation->getDescription());
        $entity->setTranslatableLocale($culture);
        $em->persist($entity);
        $em->flush();
    }

    /**
     * Lists all Shipping entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CoreShopBundle:Shipping')->findAll();

        return $this->render('CoreShopBundle:Shipping:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Shipping entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Shipping')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shipping entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreShopBundle:Shipping:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Shipping entity.
     *
     */
    public function newAction()
    {
        $entity = new Shipping();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new Shipping());
        $form   = $this->createForm(new ShippingType(), $entity, array('cultures' => $cultures));

        return $this->render('CoreShopBundle:Shipping:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'cultures' => $cultures,
        ));
    }

    /**
     * Creates a new Shipping entity.
     *
     */
    public function createAction()
    {
        $entity  = new Shipping();
        $request = $this->getRequest();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new Shipping());
        $form  = $this->createForm(new ShippingType(), $entity, array('cultures' => $cultures));
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->recalculate(false);
            $em->persist($entity);
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('shipping'));

        }

        return $this->render('CoreShopBundle:Shipping:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'cultures' => $cultures,
        ));
    }

    /**
     * Displays a form to edit an existing Shipping entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Shipping')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shipping entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm   = $this->createForm(new ShippingType(), $entity, array('cultures' => $cultures));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreShopBundle:Shipping:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures'    => $cultures,
        ));
    }

    /**
     * Edits an existing Shipping entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Shipping')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shipping entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm   = $this->createForm(new ShippingType(), $entity, array('cultures' => $cultures));
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $entity->recalculate(false);
            $em->persist($entity);
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('shipping_edit', array('id' => $id)));
        }

        return $this->render('CoreShopBundle:Shipping:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures'    => $cultures,
        ));
    }

    /**
     * Deletes a Shipping entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreShopBundle:Shipping')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Shipping entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('shipping'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
