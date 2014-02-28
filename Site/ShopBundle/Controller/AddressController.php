<?php

namespace Site\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Core\ShopBundle\Entity\Address;
use Core\ShopBundle\Form\AddressType;

/**
 * Address controller.
 *
 */
class AddressController extends ShopController
{
    /**
     * Lists all Address entities.
     *
     */
    public function indexAction()
    {
        $user = $this->getShopUser();
        if ($user === null) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('CoreShopBundle:Address')->getUserAddressQueryBuilder($user->getId())
            ->getQuery()->getResult();

        return $this->render('SiteShopBundle:Address:index.html.twig', array(
            'entities' => $entities,
            'user' => $user
        ));
    }

    /**
     * Finds and displays a Address entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundleSite:Address')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Address entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SiteShopBundle:Address:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Address entity.
     *
     */
    public function newAction()
    {
        $entity = new Address();
        $addressRequiredConfiguration = $this->container->getParameter("address.required");
        $form   = $this->createForm(new AddressType(), $entity, array('requiredFields' => $addressRequiredConfiguration));
        $user = $this->getUser();
        if ($user === null) {
            throw $this->createNotFoundException('Unable to find ShopUser entity.');
        }

        return $this->render('SiteShopBundle:Address:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Address entity.
     *
     */
    public function createAction()
    {
        $entity  = new Address();
        $request = $this->getRequest();
        $addressRequiredConfiguration = $this->container->getParameter("address.required");
        $form   = $this->createForm(new AddressType(), $entity, array('requiredFields' => $addressRequiredConfiguration));
        $form->bind($request);
        $user = $this->getShopUser();
        if ($user === null) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setUser($user);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('address'));

        }

        return $this->render('SiteShopBundle:Address:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Address entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Address')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Address entity.');
        }
        $user = $this->getShopUser();
        if ($user === null) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        if ($user->getId() != $entity->getUser()->getId()) {
            throw new AccessDeniedException();
        }

        $addressRequiredConfiguration = $this->container->getParameter("address.required");
        $editForm   = $this->createForm(new AddressType(), $entity, array('requiredFields' => $addressRequiredConfiguration));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SiteShopBundle:Address:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Address entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Address')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Address entity.');
        }
        $user = $this->getShopUser();
        if ($user === null) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        if ($user->getId() != $entity->getUser()->getId()) {
            throw new AccessDeniedException();
        }

        $addressRequiredConfiguration = $this->container->getParameter("address.required");
        $editForm   = $this->createForm(new AddressType(), $entity, array('requiredFields' => $addressRequiredConfiguration));
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('address_edit', array('id' => $id)));
        }

        return $this->render('SiteShopBundle:Address:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Address entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);
        $user = $this->getShopUser();
        if ($user === null) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreShopBundle:Address')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Address entity.');
            }

            if ($user->getId() != $entity->getUser()->getId()) {
                throw new AccessDeniedException();
            }
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('address'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function infoAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('CoreShopBundle:Address')->getUserAddressQueryBuilder($id)
            ->getQuery()->getResult();

        return $this->render('SiteShopBundle:Address:info.html.twig', array(
            'entities' => $entities
        ));
    }
}
