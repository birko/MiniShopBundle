<?php

namespace Core\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\ShopBundle\Entity\Coupon;
use Core\ShopBundle\Form\CouponType;

/**
 * Coupon controller.
 *
 */
class CouponController extends Controller
{
    /**
     * Lists all Coupon entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('CoreShopBundle:Coupon')
                ->createQueryBuilder('c')
                ->getQuery();
        $paginator = $this->get('knp_paginator');
        $page = $this->getRequest()->get('page', 1);
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            100/*limit per page*/
        );

        return $this->render('CoreShopBundle:Coupon:index.html.twig', array(
            'entities' => $pagination
        ));
    }

    /**
     * Finds and displays a Coupon entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Coupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Coupon entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreShopBundle:Coupon:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Coupon entity.
     *
     */
    public function newAction()
    {
        $entity = new Coupon();
        $form   = $this->createForm(new CouponType(), $entity);

        return $this->render('CoreShopBundle:Coupon:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Coupon entity.
     *
     */
    public function createAction()
    {
        $entity  = new Coupon();
        $request = $this->getRequest();
        $form    = $this->createForm(new CouponType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->recalculate(false);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('coupon'));

        }

        return $this->render('CoreShopBundle:Coupon:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Coupon entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Coupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Coupon entity.');
        }

        $editForm = $this->createForm(new CouponType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreShopBundle:Coupon:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Coupon entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Coupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Coupon entity.');
        }

        $editForm   = $this->createForm(new CouponType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $entity->recalculate(false);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('coupon_edit', array('id' => $id)));
        }

        return $this->render('CoreShopBundle:Coupon:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Coupon entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreShopBundle:Coupon')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Coupon entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('coupon'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function productsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Coupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Coupon entity.');
        }

        return $this->render('CoreShopBundle:Coupon:products.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function productsAddListAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Coupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Coupon entity.');
        }
        $query = $em->getRepository('CoreShopBundle:Coupon')->getNotCouponsProductsQuery($entity->getId());
        $paginator = $this->get('knp_paginator');
        $page = $this->getRequest()->get('page', 1);
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            100/*limit per page*/,
            array('distinct' => false)
        );

        return $this->render('CoreShopBundle:Coupon:productsaddlist.html.twig', array(
            'entity'      => $entity,
            'products' => $pagination
        ));
    }

    public function productAddAction($id, $product)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Coupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Coupon entity.');
        }

        $productEntity = $em->getRepository('CoreProductBundle:Product')->find($product);

        if (!$productEntity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        if (!$entity->getProducts()->contains($productEntity)) {
            $entity->getProducts()->add($productEntity);
            $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('coupon_products_addlist', array('id' => $entity->getId())));
    }

    public function productRemoveAction($id, $product)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Coupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Coupon entity.');
        }

        $productEntity = $em->getRepository('CoreProductBundle:Product')->find($product);

        if (!$productEntity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        if ($entity->getProducts()->contains($productEntity)) {
            $entity->getProducts()->removeElement($productEntity);
            $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('coupon_products', array('id' => $entity->getId())));
    }
}
