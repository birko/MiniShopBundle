<?php

namespace Core\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\MediaBundle\Entity\Media;
use Core\MediaBundle\Entity\Image;
use Core\MediaBundle\Entity\Video;
use Core\MediaBundle\Form\ImageType;
use Core\MediaBundle\Form\VideoType;
use Core\MediaBundle\Form\EditImageType;
use Core\MediaBundle\Form\EditVideoType;
use Core\MediaBundle\Form\ImageSourceTranslationType;
use Core\MediaBundle\Form\VideoSourceTranslationType;
use Core\CommonBundle\Controller\TranslateController;

/**
 * Image controller.
 *
 */
class MediaController extends TranslateController
{
    protected function saveTranslation($entity, $culture, $translation)
    {
        $em = $this->getDoctrine()->getManager();
        $entity->setFile(null);
        $entity->setTitle($translation->getTitle());
        $entity->setDescription($translation->getDescription());
        $entity->setTranslatableLocale($culture);
        $em->persist($entity);
        $em->flush();
    }

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('CoreMediaBundle:Media')
                ->getMediaQueryBuilder()
                ->orderBy("m.createdAt", "desc")
                ->getQuery();
        $query = $em->getRepository('CoreMediaBundle:Media')->setHint($query);
        $paginator = $this->get('knp_paginator');
        $page = $this->getRequest()->get('page', 1);
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            100/*limit per page*/
        );

        return $this->render('CoreMediaBundle:Media:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    /**
     * Displays a form to create a new Media entity.
     *
     */
    public function newAction($type)
    {
        $cultures = $this->container->getParameter('core.cultures');
        switch ($type) {
            case "video":
                $entity = new Video();
                $this->loadTranslations($entity, $cultures, new Video());
                $form   = $this->createForm(new VideoType(), $entity, array('cultures' => $cultures));
                break;
            case "image":
            default:
                $entity = new Image();
                $this->loadTranslations($entity, $cultures, new Image());
                $form   = $this->createForm(new ImageType(), $entity, array('cultures' => $cultures));
                break;
        }

        return $this->render('CoreMediaBundle:Media:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'type' => $type,
            'cultures' => $cultures,
        ));
    }

    /**
     * Creates a new Media entity.
     *
     */
    public function createAction($type)
    {
        $cultures = $this->container->getParameter('core.cultures');
        switch ($type) {
            case "video":
                $entity = new Video();
                $this->loadTranslations($entity, $cultures, new Video());
                $form   = $this->createForm(new VideoType(), $entity, array('cultures' => $cultures));
                break;
            case "image":
            default:
                $entity = new Image();
                $this->loadTranslations($entity, $cultures, new Image());
                $form   = $this->createForm(new ImageType(), $entity, array('cultures' => $cultures));
                break;
        }
        $request = $this->getRequest();
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $hash = trim($entity->getHash());
            $source = trim($entity->getsource());
            if (!empty($hash) || !empty($source)) {
                $testEntity = $em->getRepository('CoreMediaBundle:Media')->findOneByHash($entity->getHash());
                if ($testEntity !== null) {
                    $entity = $testEntity;
                } else {
                    if ($type == 'image') {
                        $imageOptions = $this->container->getParameter('images');
                        $opts = array();
                        foreach (array('thumb') as $val) {
                            $opts[$val] = $imageOptions[$val];
                        }
                        $entity->setOptions($opts);
                    }
                    $em->persist($entity);
                    $em->flush();
                    $this->saveTranslations($entity, $cultures);
                }

                return $this->redirect($this->generateUrl('media'));
            }
        }

        return $this->render('CoreMediaBundle:Media:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'type'    => $type,
            'cultures' => $cultures,
        ));
    }

    /**
     * Displays a form to edit an existing Media entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreMediaBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        switch ($entity->getType()) {
            case "video":
                $editForm   = $this->createForm(new EditVideoType(), $entity, array('cultures' => $cultures));
                break;
            case "image":
            default:
                $editForm   = $this->createForm(new EditImageType(), $entity, array('cultures' => $cultures));
                break;
        }
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreMediaBundle:Media:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures' => $cultures,
        ));
    }

    /**
     * Edits an existing Media entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreMediaBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        switch ($entity->getType()) {
            case "video":
                $editForm   = $this->createForm(new EditVideoType(), $entity, array('cultures' => $cultures));
                break;
            case "image":
            default:
                $editForm   = $this->createForm(new EditImageType(), $entity, array('cultures' => $cultures));
                break;
        }
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('media_edit', array('id' => $entity->getId())));
        }

        return $this->render('CoreMediaBundle:Media:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures' => $cultures,
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
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreMediaBundle:Media')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Content entity.');
            }

            $cultures = $this->container->getParameter('core.cultures');
            $this->loadTranslations($entity, $cultures);

            if ($entity->getType() == 'image') {
                $imageOptions = $this->container->getParameter('images');
                $entity->setOptions($imageOptions);
            }
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('media'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function sourceAction($id, $type)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreMediaBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $locale = array_shift($cultures);
        $this->loadTranslations($entity, $cultures);
        $entity->setTranslatableLocale($locale);
        $em->refresh($entity);
        switch ($type) {
            case "video":
                $form   = $this->createForm(new VideoSourceTranslationType(), $entity, array('cultures' => $cultures));
                break;
            case "image":
            default:
                $form   = $this->createForm(new ImageSourceTranslationType(), $entity, array('cultures' => $cultures));
                break;
        }

        return $this->render('CoreMediaBundle:Media:source.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'type' => $type,
            'cultures' => $cultures,
        ));
    }

    public function sourceUpdateAction($id, $type)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreMediaBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $locale = array_shift($cultures);
        $this->loadTranslations($entity, $cultures);
        $entity->setTranslatableLocale($locale);
        $em->refresh($entity);
        switch ($type) {
            case "video":
                $form   = $this->createForm(new VideoSourceTranslationType(), $entity, array('cultures' => $cultures));
                break;
            case "image":
            default:
                $form   = $this->createForm(new ImageSourceTranslationType(), $entity, array('cultures' => $cultures));
                break;
        }

        $request = $this->getRequest();
        $form->bind($request);
        if ($request->isMethod("POST")) {
            if ($form->isValid()) {
                if (!empty($cultures) && is_array($cultures) && count($cultures) > 0) {
                    $translations = $entity->getTranslations();
                    $i = 0;
                    $source = $entity->getSource();
                    $imageOptions = $this->container->getParameter('images');
                    foreach ($translations as $translation) {
                        $newMedia = null;
                        switch ($type) {
                            case "video":
                                $newMedia = new Video();
                                if ($entity->getVideoType() != VideoTypes::FILE) {
                                    $newMedia->setSource($translation->getSource());
                                }
                                $newMedia->setVideoType($entity->getVideoType());
                                break;
                            case "image":
                                $newMedia = new Image();
                                $newMedia->setOptions($imageOptions);
                                break;
                        }
                        if ($newMedia) {
                            $newMedia->setFile($translation->getFile());
                            $newMedia->preUpload();
                            if ($newMedia->getSource()) {
                                $newMedia->upload();
                                $entity->setTranslatableLocale($cultures[$i]);
                                if ($source != $translation->getSource()) {
                                    switch ($type) {
                                        case "image":
                                            $translation->setOptions($imageOptions);
                                            break;
                                    }
                                    $translation->removeUpload(true);
                                }
                                $entity->setSource($newMedia->getSource());
                                $entity->setFilename($newMedia->getFilename());
                                $em->persist($entity);
                                $em->flush();
                            }
                        }
                        $i++;
                    }
                }

                return $this->redirect($this->generateUrl('media_source', array('id' => $entity->getId(), 'type' => $type)));
            }
        }

        return $this->render('CoreMediaBundle:Media:source.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'type' => $type,
            'cultures' => $cultures,
        ));
    }
}
