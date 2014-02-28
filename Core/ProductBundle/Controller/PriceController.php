<?php

namespace Core\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\ProductBundle\Entity\Price;
use Core\ProductBundle\Form\PriceType;

/**
 * Price controller.
 *
 */
class PriceController extends Controller
{
    /**
     * Lists all Price entities.
     *
     */
    public function indexAction($product, $category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CoreProductBundle:Price')->findByProduct($product);

        return $this->render('CoreProductBundle:Price:index.html.twig', array(
            'entities' => $entities,
            'product'=> $product,
            'category' => $category,
        ));
    }

    /**
     * Finds and displays a Price entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:Price')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Price entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreProductBundle:Price:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Price entity.
     *
     */
    public function newAction($product, $category = null)
    {
        $entity = new Price();
        $form   = $this->createForm(new PriceType(), $entity);

        return $this->render('CoreProductBundle:Price:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'product'=> $product,
            'category' => $category,
        ));
    }

    /**
     * Creates a new Price entity.
     *
     */
    public function createAction($product, $category = null)
    {
        $entity  = new Price();
        $request = $this->getRequest();
        $form    = $this->createForm(new PriceType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $productEntity = $em->getRepository('CoreProductBundle:Product')->find($product);
            if ($productEntity != null) {
                $entity->setProduct($productEntity);
            }
            $entity->recalculate(false);
            $em->persist($entity);
            $em->flush();
            if ($entity->isDefault()) {
                $em->getRepository('CoreProductBundle:Price')->updateDefaultPrice($entity->getProduct()->getId(), $entity->getId());
            }

            return $this->redirect($this->generateUrl('price', array('product'=> $product, 'category' => $category)));

        }

        return $this->render('CoreProductBundle:Price:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'product'=> $product,
            'category' => $category
        ));
    }

    /**
     * Displays a form to edit an existing Price entity.
     *
     */
    public function editAction($id, $product, $category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:Price')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Price entity.');
        }

        $editForm = $this->createForm(new PriceType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreProductBundle:Price:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'product'=> $product,
            'category' => $category,
        ));
    }

    /**
     * Edits an existing Price entity.
     *
     */
    public function updateAction($id, $product, $category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:Price')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Price entity.');
        }

        $editForm   = $this->createForm(new PriceType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $entity->recalculate(false);
            $em->persist($entity);
            $em->flush();
            if ($entity->isDefault()) {
                $em->getRepository('CoreProductBundle:Price')->updateDefaultPrice($entity->getProduct()->getId(), $entity->getId());
            }

            return $this->redirect($this->generateUrl('price_edit', array('id' => $id, 'product'=> $product, 'category' => $category)));
        }

        return $this->render('CoreProductBundle:Price:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'product'=> $product,
            'category' => $category,
        ));
    }

    /**
     * Deletes a Price entity.
     *
     */
    public function deleteAction($id, $product, $category = null)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreProductBundle:Price')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Price entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('price', array('product'=> $product, 'category' => $category)));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
