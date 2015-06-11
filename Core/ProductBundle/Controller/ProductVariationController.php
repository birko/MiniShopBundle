<?php

namespace Core\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\ProductBundle\Entity\ProductVariation;
use Core\ProductBundle\Form\ProductVariationType;

/**
 * ProductVariation controller.
 *
 */
class ProductVariationController extends Controller
{
    /**
     * Lists all StockVariation entities.
     *
     */
    public function indexAction($product)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CoreProductBundle:ProductVariation')->findByProduct($product);
        $category = $this->getRequest()->get("category", null);

        return $this->render('CoreProductBundle:ProductVariation:index.html.twig', array(
            'entities' => $entities,
            'product' => $product,
            'category' => $category
        ));
    }

    /**
     * Finds and displays a ProductVariation entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:ProductVariation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductVariation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $category = $this->getRequest()->get("category", null);

        return $this->render('CoreProductBundle:ProductVariation:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'category' => $category
        ));
    }

    /**
     * Displays a form to create a new StockVariation entity.
     *
     */
    public function newAction($product)
    {
        $entity = new ProductVariation();
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('CoreProductBundle:ProductVariation')->getProductVariationsCount($product) + 1;
        $entity->setVariation($result);
        $form   = $this->createForm(new ProductVariationType(), $entity, array('product' => $product));
        $category = $this->getRequest()->get("category", null);

        return $this->render('CoreProductBundle:ProductVariation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'category' => $category,
            'product' => $product
        ));
    }

    /**
     * Creates a new StockVariation entity.
     *
     */
    public function createAction($product)
    {
        $entity  = new ProductVariation();
        $request = $this->getRequest();
        $form    = $this->createForm(new ProductVariationType(), $entity, array('product' => $product));
        $form->bind($request);
        $category = $this->getRequest()->get("category", null);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $productEntity = $em->getRepository('CoreProductBundle:Product')->find($product);
            if (!$productEntity) {
                throw $this->createNotFoundException('Unable to find Product entity.');
            }
            $entity->setProduct($productEntity);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('product_variation', array('product' => $entity->getProduct()->getId(), 'category' => $category)));

        }

        return $this->render('CoreProductBundle:ProductVariation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'product' => $product,
            'category' => $category,
        ));
    }

    /**
     * Displays a form to edit an existing StockVariation entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:ProductVariation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StockVariation entity.');
        }

        $editForm = $this->createForm(new ProductVariationType(), $entity, array('product' => $entity->getProduct()->getId()));
        $deleteForm = $this->createDeleteForm($id);
        $category = $this->getRequest()->get("category", null);

        return $this->render('CoreProductBundle:ProductVariation:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'category' => $category
        ));
    }

    /**
     * Edits an existing StockVariation entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:ProductVariation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StockVariation entity.');
        }

        $editForm   = $this->createForm(new ProductVariationType(), $entity, array('product' => $entity->getProduct()->getId()));
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);
        $category = $this->getRequest()->get("category", null);
        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('product_variation_edit', array('id' => $id, 'category' => $category, 'product' => $entity->getProduct()->getId())));
        }

        return $this->render('CoreProductBundle:ProductVariation:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'category' => $category
        ));
    }

    /**
     * Deletes a StockVariation entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);
        $category = $this->getRequest()->get("category", null);
        $productid = null;
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreProductBundle:ProductVariation')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find StockVariation entity.');
            }
            $productid = $entity->getProduct()->getId();
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('product_variation', array('category' => $category, 'product' => $productid)));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
