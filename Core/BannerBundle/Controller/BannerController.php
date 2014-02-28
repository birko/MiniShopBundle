<?php

namespace Core\BannerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\BannerBundle\Entity\Banner;
use Core\BannerBundle\Form\BannerType;
use Core\BannerBundle\Form\EditBannerType;
use Core\BannerBundle\Form\BannerPositionType;
use Core\CommonBundle\Controller\TranslateController;

/**
 * Banner controller.
 *
 */
class BannerController extends TranslateController
{
    protected function saveTranslation($entity, $culture, $translation)
    {
        $em = $this->getDoctrine()->getManager();
        $entity->setTitle($translation->getTitle());
        $entity->setDescription($translation->getDescription());
        $entity->setLink($translation->getLink());
        $entity->setTranslatableLocale($culture);
        $em->persist($entity);
        $em->flush();
    }

    /**
     * Lists all Banner entities.
     *
     */
    public function indexAction($category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CoreBannerBundle:Banner')->getBanners($category);

        return $this->render('CoreBannerBundle:Banner:index.html.twig', array(
            'entities' => $entities,
            'category' => $category
        ));
    }

    /**
     * Finds and displays a Banner entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBannerBundle:Banner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreBannerBundle:Banner:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Banner entity.
     *
     */
    public function newAction($category)
    {
        $entity = new Banner();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new Banner());
        $form   = $this->createForm(new BannerType(), $entity, array('cultures' => $cultures));

        return $this->render('CoreBannerBundle:Banner:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'category' => $category,
            'cultures' => $cultures
        ));
    }

    /**
     * Creates a new Banner entity.
     *
     */
    public function createAction($category)
    {
        $entity  = new Banner();
        $request = $this->getRequest();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new Banner());
        $form   = $this->createForm(new BannerType(), $entity, array('cultures' => $cultures));
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $media = $entity->getMedia();
            if ($media) {
                $hash = trim($media->getHash());
                $source = trim($media->getSource());
                if (!empty($hash) || !empty($source)) {
                    $testEntity = $em->getRepository('CoreMediaBundle:Media')->findOneByHash($media->getHash());
                    if ($testEntity !== null) {
                        $media = $testEntity;
                    }
                    $em->persist($media);
                    $em->flush();
                    $entity->setMedia($media);
                }
            }
            if ($category !== null) {
                $categoryentity = $em->getRepository('CoreCategoryBundle:Category')->find($category);
                if ($categoryentity) {
                    $entity->setCategory($categoryentity);
                }
            }
            $em->persist($entity);
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('banner', array('category' => $category)));

        }

        return $this->render('CoreBannerBundle:Banner:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'category' => $category,
            'cultures' => $cultures
        ));
    }

    /**
     * Displays a form to edit an existing Banner entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBannerBundle:Banner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm   = $this->createForm(new EditBannerType(), $entity, array('cultures' => $cultures));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreBannerBundle:Banner:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures'    => $cultures,
        ));
    }

    /**
     * Edits an existing Banner entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBannerBundle:Banner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm   = $this->createForm(new EditBannerType(), $entity, array('cultures' => $cultures));
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('banner_edit', array('id' => $id)));
        }

        return $this->render('CoreBannerBundle:Banner:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures' => $cultures,
        ));
    }

    /**
     * Deletes a Banner entity.
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
            $entity = $em->getRepository('CoreBannerBundle:Banner')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Banner entity.');
            }
            $media = $entity->getMedia();
            if ($media) {
                $media->setUsedCount($media->getUsedCount() - 1);
                if ($media->getUsedCount() == 0) {
                    if ($media->getType() == 'image') {
                        $imageOptions = $this->container->getParameter('images');
                        $media->setOptions($imageOptions);
                    }
                    $em->remove($media);
                } else {
                    $em->persist($media);
                }
            }
            if ($entity->getCategory()) {
                $category = $entity->getCategory()->getID();
            }
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('banner', array('category' => $category)));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function moveUpAction($id, $position, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreBannerBundle:Banner')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }
        $entity->setPosition($entity->getPosition() - $position);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('banner', array('category' => $category)));
    }

    public function moveDownAction($id, $position, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreBannerBundle:Banner')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }
        $entity->setPosition($entity->getPosition() + $position);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('banner', array('category' => $category)));
    }

    public function positionAction($id, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreBannerBundle:Banner')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }
        $form = $this->createForm(new BannerPositionType(), $entity);

        return $this->render('CoreBannerBundle:Banner:position.html.twig', array(
            'category' => $category,
            'id' => $id,
            'form' => $form->createView()
        ));
    }

    public function positionUpdateAction($id, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreBannerBundle:Banner')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }
        $form = $this->createForm(new BannerPositionType(), $entity);
        $request = $this->getRequest();
        $form->bind($request);
        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('banner', array('category' => $category)));
        }

        return $this->render('CoreBannerBundle:Banner:position.html.twig', array(
             'category' => $category,
             'id' => $id,
             'form' => $form->createView()
         ));
    }
}
