<?php

namespace Core\ProductBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\ProductBundle\Entity\Attribute;
use Core\ProductBundle\Form\AttributeType;

/**
 * Attribute controller.
 *
 */
class AttributeController extends Controller
{
    /**
     * Lists all Attribute entities.
     *
     */
    public function indexAction($product, $category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CoreProductBundle:Attribute')->getAllAttributesByProductQuery($product)->getResult();

        return $this->render('CoreProductBundle:Attribute:index.html.twig', array(
            'entities' => $entities,
            'product'=> $product,
            'category' => $category,
        ));
    }

    /**
     * Finds and displays a Attribute entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:Attribute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Attribute entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreProductBundle:Attribute:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Attribute entity.
     *
     */
    public function newAction($product, $category = null)
    {
        $entity = new Attribute();
        $flow = $this->get('core_product.form.flow.attributeFlow'); // must match the flow's service id
        $flow->reset();
        $flow->bind($entity);

        // form of the current step
        $form = $flow->createForm(array());
        //$form   = $this->createForm(new AttributeType(), $entity);
        return $this->render('CoreProductBundle:Attribute:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'flow' => $flow,
            'product'=> $product,
            'category' => $category,
        ));
    }

    /**
     * Creates a new Attribute entity.
     *
     */
    public function createAction(Request $request, $product, $category = null)
    {
        $entity  = new Attribute();
        $flow = $this->get('core_product.form.flow.attributeFlow'); // must match the flow's service id
        $flow->bind($entity);

        // form of the current step
        $form = $flow->createForm(array());
        //$form   = $this->createForm(new AttributeType(), $entity);
        //$form->bind($request);

        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);
            if ($flow->nextStep()) {
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

                return $this->redirect($this->generateUrl('attribute', array('product'=> $product, 'category' => $category)));
            }
        }

        return $this->render('CoreProductBundle:Attribute:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'flow'   => $flow,
            'category' => $category,
            'product' => $product,
        ));
    }

    /**
     * Displays a form to edit an existing Attribute entity.
     *
     */
    public function editAction($id, $product, $category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:Attribute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Attribute entity.');
        }

        $flow = $this->get('core_product.form.flow.attributeFlow'); // must match the flow's service id
        $flow->reset();
        $flow->bind($entity);

        // form of the current step
        $editForm = $flow->createForm(array());
        //$editForm   = $this->createForm(new AttributeType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreProductBundle:Attribute:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'flow'        => $flow,
            'delete_form' => $deleteForm->createView(),
            'product'=> $product,
            'category' => $category,
        ));
    }

    /**
     * Edits an existing Attribute entity.
     *
     */
    public function updateAction(Request $request, $id, $product, $category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:Attribute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Attribute entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $flow = $this->get('core_product.form.flow.attributeFlow'); // must match the flow's service id
        $flow->bind($entity);

        // form of the current step
        $editForm = $flow->createForm(array());
        //$editForm   = $this->createForm(new AttributeType(), $entity);
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

                return $this->redirect($this->generateUrl('attribute_edit', array('id' => $id, 'product'=> $product, 'category' => $category,)));

            }
        }

        return $this->render('CoreProductBundle:Attribute:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'flow'        => $flow,
            'delete_form' => $deleteForm->createView(),
            'product'=> $product,
            'category' => $category,
        ));
    }

    /**
     * Deletes a Attribute entity.
     *
     */
    public function deleteAction(Request $request, $id, $product, $category = null)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreProductBundle:Attribute')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Attribute entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('attribute', array('product'=> $product, 'category' => $category,)));
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
        $entity = $em->getRepository('CoreProductBundle:Attribute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find attribute entity.');
        }
        $position = ($position < 0) ? 0 : $position;
        $entity->setPosition($entity->getPosition() - $position);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('attribute', array(
            'product'=> $product,
            'category' => $category,
        )));
    }

    public function moveDownAction($id, $product, $position, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreProductBundle:Attribute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Attribute entity.');
        }
        $position = ($position < 0) ? 0 : $position;
        $entity->setPosition($entity->getPosition() + $position);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('attribute', array(
            'product'=> $product,
            'category' => $category,
        )));
    }
}
