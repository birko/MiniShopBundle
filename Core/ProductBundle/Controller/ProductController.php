<?php

namespace Core\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\ProductBundle\Entity\Product;
use Core\ProductBundle\Entity\Price;
use Core\ProductBundle\Entity\ProductOption;
use Core\ProductBundle\Entity\Attribute;
use Core\ProductBundle\Entity\Filter;
use Core\ProductBundle\Form\ProductType;
use Core\ProductBundle\Form\FilterType;
use Core\ProductBundle\Form\ProductCategoryType;
use Core\CommonBundle\Controller\TranslateController;

/**
 * Product controller.
 *
 */
class ProductController extends TranslateController
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
     * Lists all Product entities.
     *
     */
    public function indexAction($category = null)
    {
        $em = $this->getDoctrine()->getManager();
        //filter
        $request = $this->getRequest();
        $session = $request->getSession();
        $filter = $session->get('adminproductfilter', new Filter());
        if (empty($filter)) {
            $filter = new Filter();
            $session->set('adminproductfilter', $filter);
        }
        if ($filter->getCategory() !== $category) {
            $filter = new Filter();
            $filter->setCategory($category);
            $session->set('adminproductfilter', $filter);
        }
        if ($filter->getVendor() != null) {
            $filter->setVendor($em->merge($filter->getVendor()));
        }
        $page = $this->getRequest()->get("page", $filter->getPage());
        $filter->setPage($page);
        $session->set('adminproductfilter', $filter);
        $form   = $this->createForm(new FilterType(), $filter);

        if ($request->getMethod() == "POST") {
            $form->bind($request);
            if ($form->isValid()) {
                $filter->setPage(1);
                $page = 1;
                $session->set('adminproductfilter', $filter);
            }
        }

        $querybuilder = $em->getRepository('CoreProductBundle:Product')
                    ->findByCategoryQueryBuilder($category);
        $query = $em->getRepository('CoreProductBundle:Product')->filterQueryBuilder($querybuilder, $filter)->getQuery();
        $query = $em->getRepository('CoreProductBundle:Product')->setHint($query);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            100 /*limit per page*/
        );

        $minishop  = $this->container->getParameter('minishop');

        return $this->render('CoreProductBundle:Product:index.html.twig', array(
            'entities' => $pagination,
            'category' => $category,
            'filter' => $form->createView(),
            'minishop' => $minishop,
        ));
    }

    /**
     * Finds and displays a Product entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreProductBundle:Product:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Product entity.
     *
     */
    public function newAction($category = null)
    {
        $entity = new Product();
        $tags = $this->container->getParameter('product.tags');
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new Product());
        $form   = $this->createForm(new ProductType(), $entity, array('cultures' => $cultures, 'tags' => $tags));

        return $this->render('CoreProductBundle:Product:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'category' => $category,
            'cultures' => $cultures,
        ));
    }

    /**
     * Creates a new Product entity.
     *
     */
    public function createAction($category = null)
    {
        $entity  = new Product();
        $request = $this->getRequest();
        $tags = $this->container->getParameter('product.tags');
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new Product());
        $form   = $this->createForm(new ProductType(), $entity, array('cultures' => $cultures, 'tags' => $tags));
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            if ($category !== null) {
                $categoryEntity = $em->getRepository('CoreCategoryBundle:Category')->find($category);
                if ($categoryEntity != null) {
                    $productCategory = $entity->addCategory($categoryEntity);
                    $em->persist($productCategory);
                    $em->flush();
                }
            }
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('product', array('category' => $category)));

        }

        return $this->render('CoreProductBundle:Product:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'category' => $category,
            'cultures' => $cultures,
        ));
    }

    /**
     * Displays a form to edit an existing Product entity.
     *
     */
    public function editAction($id, $category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $tags = $this->container->getParameter('product.tags');
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm   = $this->createForm(new ProductType(), $entity, array('cultures' => $cultures, 'tags' => $tags));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreProductBundle:Product:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'category' => $category,
            'cultures' => $cultures,
        ));
    }

    /**
     * Edits an existing Product entity.
     *
     */
    public function updateAction($id, $category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreProductBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $tags = $this->container->getParameter('product.tags');
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm   = $this->createForm(new ProductType(), $entity, array('cultures' => $cultures, 'tags' => $tags));
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('product_edit', array('id' => $id)));
        }

        return $this->render('CoreProductBundle:Product:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'category' => $category,
            'cultures' => $cultures,
        ));
    }

    /**
     * Deletes a Product entity.
     *
     */
    public function deleteAction($id, $category = null)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreProductBundle:Product')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Product entity.');
            }

            foreach ($entity->getPrices() as $price) {
                $entity->getPrices()->removeElement($price);
                $em->remove($price);
            }

            foreach ($entity->getOptions() as $option) {
                $entity->getOptions()->removeElement($option);
                $em->remove($option);
            }

            foreach ($entity->getAttributes() as $attribute) {
                $entity->getAttributes()->removeElement($attribute);
                $em->remove($attribute);
            }

            $imageOptions = $this->container->getParameter('images');
            foreach ($entity->getMedia() as $media) {
                $media->getMedia()->setOptions($imageOptions);
                $entity->getMedia()->removeElement($media);
                $em->remove($media);
            }

            foreach ($entity->getProductCategories() as $productcategory) {
                $em->remove($productcategory);
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('product', array('category' => $category)));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function copyAction($id, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('CoreProductBundle:Product')->find($id);
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($product, $cultures);
        $entity  = new Product();
        $entity->setTitle($product->getTitle());
        $entity->setEnabled($product->isEnabled());
        $entity->setTags($product->getTags());
        $entity->setShortDescription($product->getShortDescription());
        $entity->setLongDescription($product->getLongDescription());
        $entity->setVendor($product->getVendor());
        $em->persist($entity);
        $em->flush();
        foreach($product->getTranslations() as $translation) {
            $translation->setSlug(null);
        }
        
        $entity->setTranslations($product->getTranslations());
        $this->saveTranslations($entity, $cultures);
        foreach ($product->getPrices() as $productprice) {
            $price = new Price();
            $price->setProduct($entity);
            $price->setType($productprice->getType());
            $price->setPrice($productprice->getPrice());
            $price->setPriceVAT($productprice->getPriceVAT());
            $price->setVAT($productprice->getVAT());
            $price->setPriceGroup($productprice->getPriceGroup());
            $price->setPriceAmount($productprice->getPriceAmount());
            $price->setDefault($productprice->isDefault());
            $em->persist($price);
        }

        foreach ($product->getOptions() as $productoption) {
            $option = new ProductOption();
            $option->setProduct($entity);
            $option->setName($productoption->getName());
            $option->setValue($productoption->getValue());
            $option->setAmount($productoption->getAmount());
            $option->setPosition($productoption->getPosition());
            $em->persist($option);

        }

        foreach ($product->getAttributes() as $productattribute) {
            $attribute = new Attribute();
            $attribute->setProduct($entity);
            $attribute->setName($productattribute->getName());
            $attribute->setValue($productattribute->getValue());
            $attribute->setGroup($productattribute->getGroup());
            $attribute->setPosition($productattribute->getPosition());
            $em->persist($attribute);
        }

        foreach ($product->getProductCategories() as $productcategory) {
            $productCategory = $entity->addCategory($productcategory->getCategory());
            $em->persist($productCategory);
        }

        $em->flush();

        return $this->redirect($this->generateUrl('product_edit', array('id' => $entity->getId(), 'category' => $category)));
    }

    public function addListAction($category)
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $session = $request->getSession();
        $filter = $session->get('adminproductfilteradd', new Filter());
        if (empty($filter)) {
            $filter = new Filter();
            $session->set('adminproductfilteradd', $filter);
        }
        if ($filter->getCategory() !== $category) {
            $filter = new Filter();
            $filter->setCategory($category);
            $session->set('adminproductfilteradd', $filter);
        }
        if ($filter->getVendor() != null) {
            $filter->setVendor($em->merge($filter->getVendor()));
        }
        $page = $this->getRequest()->get("page", $filter->getPage());
        $filter->setPage($page);
        $session->set('adminproductfilteradd', $filter);
        $form   = $this->createForm(new FilterType(), $filter);

        if ($request->getMethod() == "POST") {
            $form->bind($request);
            if ($form->isValid()) {
                $filter->setPage(1);
                $page = 1;
                $session->set('adminproductfilteradd', $filter);
            }
        }

        $queryBuilder = $em->getRepository('CoreProductBundle:Product')->findNotInCategoryQueryBuilder($category, true);
        $queryBuilder = $em->getRepository('CoreProductBundle:Product')->filterQueryBuilder($queryBuilder, $filter, "p2");
        $query = $queryBuilder->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $filter->getPage()/*page number*/,
            200,/*limit per page*/
            array('distinct' => false)
        );

        return $this->render('CoreProductBundle:Product:addlist.html.twig', array(
            'entities' => $pagination,
            'category' => $category,
            'filter' => $form->createView(),
        ));
    }

    public function addAction($id, $category)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreProductBundle:Product')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        $categoryEntity  = $em->getRepository('CoreCategoryBundle:Category')->find($category);
        if ($categoryEntity != null) {
            $productCategory = $entity->addCategory($categoryEntity);
            $em->persist($productCategory);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('product_addlist', array('category' => $category)));
    }

    public function removeAction($id, $category)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('CoreProductBundle:ProductCategory')->removeProductCategory($category, $id);

        return $this->redirect($this->generateUrl('product', array('category' => $category)));
    }

    public function moveUpAction($id, $position, $category)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreProductBundle:Product')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        $productCategory = $entity->getProductCategory($category);
        if ($productCategory) {
            $productCategory->setPosition($productCategory->getPosition() - $position);
            $em->persist($productCategory);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('product', array('category' => $category)));
    }

    public function moveDownAction($id, $position, $category)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreProductBundle:Product')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        $productCategory = $entity->getProductCategory($category);
        if ($productCategory) {
            $productCategory->setPosition($productCategory->getPosition() + $position);
            $em->persist($productCategory);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('product', array('category' => $category)));
    }

    public function positionAction($id, $category)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreProductBundle:Product')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        $productCategory = $entity->getProductCategory($category);
        if (!$productCategory) {
            throw $this->createNotFoundException('Unable to find Product Category entity.');
        }
        $form = $this->createForm(new ProductCategoryType(), $productCategory);

        return $this->render('CoreProductBundle:Product:position.html.twig', array(
            'entity' => $entity,
            'category' => $category,
            'form' => $form->createView()
        ));
    }

    public function positionUpdateAction($id, $category)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreProductBundle:Product')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        $productCategory = $entity->getProductCategory($category);
        if (!$productCategory) {
            throw $this->createNotFoundException('Unable to find Product Category entity.');
        }
        $form = $this->createForm(new ProductCategoryType(), $productCategory);
        $request = $this->getRequest();
        $form->bind($request);
        if ($form->isValid()) {
            $em->persist($productCategory);
            $em->flush();

            return $this->redirect($this->generateUrl('product', array('category' => $category)));
        }

        return $this->render('CoreProductBundle:Product:position.html.twig', array(
            'entity' => $entity,
            'category' => $category,
            'form' => $form->createView()
        ));
    }
}
