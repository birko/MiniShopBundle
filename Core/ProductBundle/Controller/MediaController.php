<?php

namespace Core\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\ProductBundle\Entity\Product;
use Core\ProductBundle\Form\ProductMediaType;
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
    public function indexAction($product, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('CoreProductBundle:Product')->findMediaByProduct($product);

        $entity = $em->getRepository('CoreProductBundle:Product')->find($product);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        return $this->render('CoreProductBundle:Media:index.html.twig', array(
            'entities' => $entities,
            'category' => $category,
            'product' => $entity,
        ));
    }

    /**
     * Displays a form to create a new Content entity.
     *
     */
    public function newAction($product, $type, $category = null)
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

        return $this->render('CoreProductBundle:Media:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'category' => $category,
            'product' => $product,
            'type' => $type,
            'cultures' => $cultures,
        ));
    }

    /**
     * Creates a new Content entity.
     *
     */
    public function createAction($product, $type, $category = null)
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
                        foreach ($this->container->getParameter('product.images') as $val) {
                            $opts[$val] = $imageOptions[$val];
                        }
                        $entity->setOptions($opts);
                    }
                    $em->persist($entity);
                    $em->flush();
                    $this->saveTranslations($entity, $cultures);
                }
                $productEntity = $em->getRepository('CoreProductBundle:Product')->find($product);
                if ($productEntity != null) {
                    $productMedia = $productEntity->addMedia($entity);
                    $em->persist($entity);
                    $em->persist($productMedia);
                    $em->flush();
                }

                return $this->redirect($this->generateUrl('product_media', array('category' => $category, 'product' => $product)));
            }
        }

        return $this->render('CoreProductBundle:Media:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'category' => $category,
            'product' => $product,
            'type' => $type,
            'cultures' => $cultures
        ));
    }

    /**
     * Displays a form to edit an existing Content entity.
     *
     */
    public function editAction($id, $product, $type, $category = null)
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

        return $this->render('CoreProductBundle:Media:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'category' => $category,
            'product' => $product,
            'type' => $type,
            'cultures' => $cultures,
        ));
    }

    /**
     * Edits an existing Content entity.
     *
     */
    public function updateAction($id, $product, $type, $category = null)
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

            return $this->redirect($this->generateUrl('product_media_edit', array('id' => $id, 'category' => $category, 'product' => $product)));
        }

        return $this->render('CoreProdctBundle:Media:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'category' => $category,
            'product' => $product,
            'type' => $type,
            'cultures' => $cultures,
        ));
    }

    /**
     * Deletes a Content entity.
     *
     */
    public function deleteAction($id, $product, $type, $category = null)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreMediaBundle:Media')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ' . $type . ' entity.');
            }
            $productEntity = $em->getRepository('CoreProductBundle:Product')->find($product);
            if ($productEntity != null) {
                $productMedia = $productEntity->removeMedia($entity);
                if ($productMedia) {
                    $em->remove($productMedia);
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

        return $this->redirect($this->generateUrl('product_media', array('category' => $category, 'product' => $product)));
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
        $entity = $em->getRepository('CoreProductBundle:Product')->find($product);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        $productMedia = $entity->getProductMedia($id);
        if ($productMedia) {
            $productMedia->setPosition($productMedia->getPosition() - $position);
            $em->persist($productMedia);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('product_media', array('category' => $category, 'product' => $product)));
    }

    public function moveDownAction($id, $product, $position, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreProductBundle:Product')->find($product);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        $productMedia = $entity->getProductMedia($id);
        if ($productMedia) {
            $productMedia->setPosition($productMedia->getPosition() + $position);
            $em->persist($productMedia);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('product_media', array('category' => $category, 'product' => $product)));
    }

    public function positionAction($id, $product, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreProductBundle:Product')->find($product);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        $productMedia = $entity->getProductMedia($id);
        if (!$productMedia) {
            throw $this->createNotFoundException('Unable to find Product Media entity.');
        }
        $form = $this->createForm(new ProductMediaType(), $productMedia);

        return $this->render('CoreProductBundle:Media:position.html.twig', array(
            'product' => $product,
            'category' => $category,
            'id' => $id,
            'form' => $form->createView()
        ));
    }

    public function positionUpdateAction($id, $product, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreProductBundle:Product')->find($product);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        $productMedia = $entity->getProductMedia($id);
        if (!$productMedia) {
            throw $this->createNotFoundException('Unable to find Product Media entity.');
        }
        $form = $this->createForm(new ProductMediaType(), $productMedia);
        $request = $this->getRequest();
        $form->bind($request);
        if ($form->isValid()) {
            $em->persist($productMedia);
            $em->flush();

            return $this->redirect($this->generateUrl('product_media', array('category' => $category, 'product' => $product)));
        }

        return $this->render('CoreProductBundle:Media:position.html.twig', array(
            'product' => $product,
            'category' => $category,
            'id' => $id,
            'form' => $form->createView()
        ));
    }
}
