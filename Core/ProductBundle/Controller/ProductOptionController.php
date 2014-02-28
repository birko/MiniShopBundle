<?php

namespace Core\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\ProductBundle\Entity\ProductOption;
use Core\ProductBundle\Form\ProductOptionType;

/**
 * ProductOption controller.
 *
 */
class ProductOptionController extends Controller
{
    /**
     * Lists all ProductOption entities.
     *
     */
    public function indexAction($product, $category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CoreProductBundle:ProductOption')->getOptionsByProductQuery($product)->getResult();

        return $this->render('CoreProductBundle:ProductOption:index.html.twig', array(
            'entities' => $entities,
            'product'=> $product,
            'category' => $category,
        ));
    }

    /**
     * Finds and displays a ProductOption entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:ProductOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductOption entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreProductBundle:ProductOption:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new ProductOption entity.
     *
     */
    public function newAction($product, $category = null)
    {
        $entity = new ProductOption();
        $flow = $this->get('core_product.form.flow.productOptionFlow'); // must match the flow's service id
        $flow->reset();
        $flow->bind($entity);

        // form of the current step
        $form = $flow->createForm(array());
        //$form   = $this->createForm(new ProductOptionType(), $entity, array());
        return $this->render('CoreProductBundle:ProductOption:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'flow'   => $flow,
            'product'=> $product,
            'category' => $category,
        ));
    }

    /**
     * Creates a new ProductOption entity.
     *
     */
    public function createAction($product, $category = null)
    {
        $entity  = new ProductOption();
        $flow = $this->get('core_product.form.flow.productOptionFlow'); // must match the flow's service id
        $flow->bind($entity);

        // form of the current step
        $form = $flow->createForm(array());
        $request = $this->getRequest();
        //$form   = $this->createForm(new ProductOptionType(), $entity, array());
        //$form->bind($request);

        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);
            if ($flow->nextStep()) {
                // form for the next step
                $form = $flow->createForm(array('attributeName' => $entity->getName()->getId()));
            } else {
                $em = $this->getDoctrine()->getManager();
                $productEntity = $em->getRepository('CoreProductBundle:Product')->find($product);
                if ($productEntity != null) {
                    $entity->setProduct($productEntity);
                }
                $em->persist($entity);
                $em->flush();
                $flow->reset();

                return $this->redirect($this->generateUrl('option', array('product'=> $product, 'category' => $category)));
            }
        }

        return $this->render('CoreProductBundle:ProductOption:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'flow'   => $flow,
            'product'=> $product,
            'category' => $category,
        ));
    }

    /**
     * Displays a form to edit an existing ProductOption entity.
     *
     */
    public function editAction($id, $product, $category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:ProductOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductOption entity.');
        }

        $flow = $this->get('core_product.form.flow.productOptionFlow'); // must match the flow's service id
        $flow->reset();
        $flow->bind($entity);

        // form of the current step
        $editForm = $flow->createForm(array());
        //$editForm   = $this->createForm(new ProductOptionType(), $entity, array());
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreProductBundle:ProductOption:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'flow'        => $flow,
            'delete_form' => $deleteForm->createView(),
            'product'=> $product,
            'category' => $category,
        ));
    }

    /**
     * Edits an existing ProductOption entity.
     *
     */
    public function updateAction($id, $product, $category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:ProductOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductOption entity.');
        }

        $flow = $this->get('core_product.form.flow.productOptionFlow'); // must match the flow's service id
        $flow->bind($entity);

        // form of the current step
        $editForm = $flow->createForm(array());
        //$editForm   = $this->createForm(new ProductOptionType(), $entity, array());
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        //$editForm->bind($request);

        if ($flow->isValid($editForm)) {
            $flow->saveCurrentStepData($editForm);
            if ($flow->nextStep()) {
                // form for the next step
                $editForm = $flow->createForm(array('attributeName' => $entity->getName()->getId()));
            } else {
                $em->persist($entity);
                $em->flush();
                $flow->reset();

                return $this->redirect($this->generateUrl('option_edit', array('id' => $id, 'product'=> $product, 'category' => $category,)));

            }
        }

        return $this->render('CoreProductBundle:ProductOption:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'flow'        => $flow,
            'delete_form' => $deleteForm->createView(),
            'product'=> $product,
            'category' => $category,
        ));
    }

    /**
     * Deletes a ProductOption entity.
     *
     */
    public function deleteAction($id, $product, $category = null)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreProductBundle:ProductOption')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ProductOption entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('option', array( 'product'=> $product, 'category' => $category,)));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function moveUpAction($id, $product, $position, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreProductBundle:ProductOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductOption entity.');
        }
        $position = ($position < 0) ? 0 : $position;
        $entity->setPosition($entity->getPosition() - $position);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('option', array(
            'product'=> $product,
            'category' => $category,
        )));
    }

    public function moveDownAction($id, $product, $position, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreProductBundle:ProductOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductOption entity.');
        }
        $position = ($position < 0) ? 0 : $position;
        $entity->setPosition($entity->getPosition() + $position);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('option', array(
            'product'=> $product,
            'category' => $category,
        )));
    }
}
