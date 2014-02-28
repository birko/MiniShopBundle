<?php

namespace Core\ProductBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\ProductBundle\Entity\Groupon;
use Core\ProductBundle\Form\GrouponType;

/**
 * Groupon controller.
 *
 */
class GrouponController extends Controller
{

    /**
     * Lists all Groupon entities.
     *
     */
    public function indexAction($product, $category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:Product')->find($product);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        $entities = $em->getRepository('CoreProductBundle:Groupon')->getProductGrouponsQueryBuilder($product)->getQuery()->getResult();

        return $this->render('CoreProductBundle:Groupon:index.html.twig', array(
            'entities' => $entities,
            'category' => $category,
            'product' => $entity,
        ));
    }
    /**
     * Creates a new Groupon entity.
     *
     */
    public function createAction(Request $request, $product, $category = null)
    {
        $entity = new Groupon();
        $form = $this->createCreateForm($entity, $product, $category);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $productEntity = $em->getRepository('CoreProductBundle:Product')->find($product);
            $entity->setProduct($productEntity);
            $entity->recalculate(false);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('groupon', array('category' => $category, 'product' => $product)));
        }

        return $this->render('CoreProductBundle:Groupon:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'category' => $category,
            'product' => $product,
        ));
    }

    /**
    * Creates a form to create a Groupon entity.
    *
    * @param Groupon $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Groupon $entity, $product, $category = null)
    {
        $form = $this->createForm(new GrouponType(), $entity, array(
            'action' => $this->generateUrl('groupon_create', array(
                'category' => $category,
                'product' => $product,)
            ),
            'method' => 'POST',
        ));

       // $form->add('submit', 'submit', array('label' => 'Create'));
        return $form;
    }

    /**
     * Displays a form to create a new Groupon entity.
     *
     */
    public function newAction($product, $category = null)
    {
        $entity = new Groupon();
        $form   = $this->createCreateForm($entity, $product, $category);

        return $this->render('CoreProductBundle:Groupon:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'category' => $category,
            'product' => $product,
        ));
    }

    /**
     * Finds and displays a Groupon entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:Groupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Groupon entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreProductBundle:Groupon:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Groupon entity.
     *
     */
    public function editAction($id, $product, $category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:Groupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Groupon entity.');
        }

        $editForm = $this->createEditForm($entity, $product, $category);
        $deleteForm = $this->createDeleteForm($id, $product, $category);

        return $this->render('CoreProductBundle:Groupon:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'category' => $category,
            'product' => $product,
        ));
    }

    /**
    * Creates a form to edit a Groupon entity.
    *
    * @param Groupon $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Groupon $entity, $product, $category = null)
    {
        $form = $this->createForm(new GrouponType(), $entity, array(
            'action' => $this->generateUrl('groupon_update', array(
                'id' => $entity->getId(),
                'category' => $category,
                'product' => $product
            )),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }
    /**
     * Edits an existing Groupon entity.
     *
     */
    public function updateAction(Request $request, $id, $product, $category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:Groupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Groupon entity.');
        }

        $deleteForm = $this->createDeleteForm($id, $product, $category);
        $editForm = $this->createEditForm($entity, $product, $category);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->recalculate(false);
            $em->flush();

            return $this->redirect($this->generateUrl('groupon_edit', array('id' => $id, 'category' => $category, 'product' => $product)));
        }

        return $this->render('CoreProductBundle:Groupon:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'category' => $category,
            'product' => $product,
        ));
    }
    /**
     * Deletes a Groupon entity.
     *
     */
    public function deleteAction(Request $request, $id, $product, $category = null)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreProductBundle:Groupon')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Groupon entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('groupon', array('category' => $category, 'product' => $product)));
    }

    /**
     * Creates a form to delete a Groupon entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id, $product, $category = null)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('groupon_delete', array(
                'id' => $id,
                'category' => $category,
                'product' => $product
            )))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
