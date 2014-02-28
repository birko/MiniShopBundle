<?php

namespace Core\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\ContentBundle\Entity\Content;
use Core\ContentBundle\Form\ContentMediaType;
use Core\MediaBundle\Entity\Media;
use Core\MediaBundle\Entity\Image;
use Core\MediaBundle\Entity\Video;
use Core\MediaBundle\Form\ImageType;
use Core\MediaBundle\Form\VideoType;
use Core\MediaBundle\Form\EditImageType;
use Core\MediaBundle\Form\EditVideoType;
use Core\CommonBundle\Controller\TranslateController;

/**
 * Content controller.
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

    /**
     * Lists all Content Media entities.
     *
     */
    public function indexAction($content, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('CoreContentBundle:Content')
                ->findMediaByContentQueryBuilder($content)
                ->getQuery()
                ->getResult();

        $entity = $em->getRepository('CoreContentBundle:Content')->find($content);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }

        return $this->render('CoreContentBundle:Media:index.html.twig', array(
            'entities' => $entities,
            'category' => $category,
            'content' => $entity,
        ));
    }

    /**
     * Displays a form to create a new Content entity.
     *
     */
    public function newAction($content, $type, $category = null)
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

        return $this->render('CoreContentBundle:Media:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'category' => $category,
            'content' => $content,
            'type' => $type,
            'cultures' => $cultures,
        ));
    }

    /**
     * Creates a new Content entity.
     *
     */
    public function createAction($content, $type, $category = null)
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
                        foreach ($this->container->getParameter('content.images') as $val) {
                            $opts[$val] = $imageOptions[$val];
                        }
                        $entity->setOptions($opts);
                    }
                    $em->persist($entity);
                    $em->flush();
                    $this->saveTranslations($entity, $cultures);
                }
                $contetEntity = $em->getRepository('CoreContentBundle:Content')->find($content);
                if ($contetEntity != null) {
                    $contentMedia = $contetEntity->addMedia($entity);
                    $em->persist($entity);
                    $em->persist($contentMedia);
                    $em->flush();
                }

                return $this->redirect($this->generateUrl('content_media', array('category' => $category, 'content' => $content)));
            }
        }

        return $this->render('CoreContentBundle:Media:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'category' => $category,
            'content' => $content,
            'type'    => $type,
            'cultures' => $cultures,
        ));
    }

    /**
     * Displays a form to edit an existing Content entity.
     *
     */
    public function editAction($id, $content, $type, $category = null)
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

        return $this->render('CoreContentBundle:Media:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'category' => $category,
            'content' => $content,
            'type' => $type,
            'cultures' => $cultures,
        ));
    }

    /**
     * Edits an existing Content entity.
     *
     */
    public function updateAction($id, $content, $type, $category = null)
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
                $editForm  = $this->createForm(new EditImageType(), $entity, array('cultures' => $cultures));
                break;
        }
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('content_media_edit', array('id' => $id, 'category' => $category, 'content' => $content, 'type' => $entity->getType())));
        }

        return $this->render('CoreContentBundle:Media:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'category' => $category,
            'content' => $content,
            'type' => $type,
            'cultures' => $cultures,
        ));
    }

    /**
     * Deletes a Content entity.
     *
     */
    public function deleteAction($id, $content, $type, $category = null)
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

            $contetEntity = $em->getRepository('CoreContentBundle:Content')->find($content);
            if ($contetEntity != null) {
                $contentMedia = $contetEntity->removeMedia($entity);
                if ($contentMedia) {
                    $em->remove($contentMedia);
                }
            }
            if ($entity->getUsedCount() == 0) {
                if ($entity->getType() == 'image') {
                    $imageOptions = $this->container->getParameter('images');
                    $entity->setOptions($imageOptions);
                }
                $em->remove($entity);
            } else {
                $em->persist($entity);
            }
            $em->flush();
        }

        return $this->redirect($this->generateUrl('content_media', array('category' => $category, 'content' => $content)));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function moveUpAction($id, $content, $position, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreContentBundle:Content')->find($content);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }
        $contentMedia = $entity->getContentMedia($id);
        if ($contentMedia) {
            $contentMedia->setPosition($contentMedia->getPosition() - $position);
            $em->persist($contentMedia);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('content_media', array('category' => $category, 'content' => $content,)));
    }

    public function moveDownAction($id, $content, $position, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreContentBundle:Content')->find($content);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }
        $contentMedia = $entity->getContentMedia($id);
        if ($contentMedia) {
            $contentMedia->setPosition($contentMedia->getPosition() + $position);
            $em->persist($contentMedia);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('content_media', array('category' => $category, 'content' => $content,)));
    }

    public function positionAction($id, $content, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreContentBundle:Content')->find($content);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }
        $contentMedia = $entity->getContentMedia($id);
        if (!$contentMedia) {
            throw $this->createNotFoundException('Unable to find Content Media entity.');
        }
        $form = $this->createForm(new ContentMediaType(), $contentMedia);

        return $this->render('CoreContentBundle:Media:position.html.twig', array(
            'content' => $content,
            'category' => $category,
            'id' => $id,
            'form' => $form->createView()
        ));
    }

    public function positionUpdateAction($id, $content, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreContentBundle:Content')->find($content);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }
        $contentMedia = $entity->getContentMedia($id);
        if (!$contentMedia) {
            throw $this->createNotFoundException('Unable to find Content Media entity.');
        }
        $form = $this->createForm(new ContentMediaType(), $contentMedia);
        $request = $this->getRequest();
        $form->bind($request);
        if ($form->isValid()) {
            $em->persist($contentMedia);
            $em->flush();

            return $this->redirect($this->generateUrl('content_media', array('category' => $category, 'content' => $content)));
        }

        return $this->render('CoreContentBundle:Media:position.html.twig', array(
            'content' => $content,
            'category' => $category,
            'id' => $id,
            'form' => $form->createView()
        ));
    }
}
