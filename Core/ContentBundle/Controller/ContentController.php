<?php

namespace Core\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\ContentBundle\Entity\Content;
use Core\ContentBundle\Form\ContentType;
use Core\CommonBundle\Controller\TranslateController;

/**
 * Content controller.
 *
 */
class ContentController extends TranslateController
{
    protected function saveTranslation($entity, $culture, $translation)
    {
        $em = $this->getDoctrine()->getManager();
        $entity->setTitle($translation->getTitle());
        $entity->setShortDescription($translation->getShortDescription());
        $entity->setLongDescription($translation->getLongDescription());
        $entity->setTranslatableLocale($culture);
        $em->persist($entity);
        $em->flush();
    }

    /**
     * Lists all Content entities.
     *
     */
    public function indexAction($category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $querybuilder = $em->getRepository('CoreContentBundle:Content')->findContentByCategoryQueryBuilder($category);
        $query = $querybuilder->orderBy('c.id')->getQuery();

        $paginator = $this->get('knp_paginator');
        $page = $this->getRequest()->get('page', 1);
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            100/*limit per page*/
        );

        $minishop  = $this->container->getParameter('minishop');

        return $this->render('CoreContentBundle:Content:index.html.twig', array(
            'entities' => $pagination,
            'category' => $category,
            'minishop' => $minishop,
        ));
    }

    /**
     * Finds and displays a Content entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreContentBundle:Content')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreContentBundle:Content:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Content entity.
     *
     */
    public function newAction($category = null)
    {
        $entity = new Content();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new Content());
        $form   = $this->createForm(new ContentType(), $entity, array('cultures' => $cultures));

        return $this->render('CoreContentBundle:Content:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'category' => $category,
            'cultures' => $cultures
        ));
    }

    /**
     * Creates a new Content entity.
     *
     */
    public function createAction($category = null)
    {
        $entity  = new Content();
        $request = $this->getRequest();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new Content());
        $form   = $this->createForm(new ContentType(), $entity, array('cultures' => $cultures));
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($category !== null) {
                $categoryEntity = $em->getRepository('CoreCategoryBundle:Category')->find($category);
                if ($categoryEntity != null) {
                    $entity->setCategory($categoryEntity);
                }
            }
            $em->persist($entity);
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('content', array('category'=> $category)));

        }

        return $this->render('CoreContentBundle:Content:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'category' => $category,
            'cultures' => $cultures,
        ));
    }

    /**
     * Displays a form to edit an existing Content entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreContentBundle:Content')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm = $this->createForm(new ContentType(), $entity, array('cultures' => $cultures));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreContentBundle:Content:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures'    => $cultures,
        ));
    }

    /**
     * Edits an existing Content entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreContentBundle:Content')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm = $this->createForm(new ContentType(), $entity, array('cultures' => $cultures));
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('content_edit', array('id' => $id)));
        }

        return $this->render('CoreContentBundle:Content:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Content entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);
        $category = null;
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreContentBundle:Content')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Content entity.');
            }

            $imageOptions = $this->container->getParameter('images');
            foreach ($entity->getMedia() as $media) {
                $media->getMedia()->setOptions($imageOptions);
                $entity->getMedia()->removeElement($media);
                $em->remove($media);
            }

            if ($entity->getCategory()) {
                $category = $entity->getCategory()->getId();
            }
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('content', array('category' => $category)));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
