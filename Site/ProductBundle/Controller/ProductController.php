<?php

namespace Site\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Gedmo\Sluggable\Util\Urlizer as GedmoUrlizer;
use Site\ShopBundle\Controller\ShopController;
use Core\ProductBundle\Entity\Filter;

class ProductController extends ShopController
{

    public function indexAction($slug)
    {
        $minishop  = $this->container->getParameter('minishop');
        $priceGroup = $this->getPriceGroup();
        $currency = $this->getCurrency();
        $em = $this->getDoctrine()->getManager();
        $product  = $em->getRepository("CoreProductBundle:Product")->getBySlug($slug, false);
        if (!$product || (!$product->isEnabled() && !$this->container->getParameter("site.product.show_disabled"))) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        $ordered  = array();
        $media = $em->getRepository("CoreProductBundle:ProductMedia")->getProductsMediasArray(array($product->getId()));
        $attributes = $em->getRepository("CoreProductBundle:Attribute")->getGroupedAttributesByProducts(array($product->getId()),  array(), $this->getRequest()->get('_locale'));
        $stocks = $em->getRepository("CoreProductBundle:Stock")->getStocksArray(array($product->getId()));
        $prices = $em->getRepository("CoreProductBundle:Price")->getPricesArray(array($product->getId()));
        if (array_key_exists("shop", $minishop) && $minishop['shop']) {
            $ordered  = $em->getRepository("CoreShopBundle:OrderItem")->getProductsOrderCount(array($product->getId()));
        }
        $minishop  = $this->container->getParameter('minishop');

        return $this->render('SiteProductBundle:Product:index.html.twig', array(
            'product'       => $product,
            'pricegroup'    => $priceGroup,
            'currency'      => $currency,
            'attributes'    => $attributes,
            'prices'        => $prices,
            'media'         => $media,
            'ordered'       => $ordered,
            'stock'         => $stocks,
            'minishop'      => $minishop,
        ));
    }

    public function listAction($category, $page = 1)
    {
        $minishop  = $this->container->getParameter('minishop');
        $priceGroup = $this->getPriceGroup();
        $currency = $this->getCurrency();
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository("CoreProductBundle:Product")->findByCategoryQuery($category, $this->container->getParameter("site.product.recursive"), true, false, false);
       
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page /*page number*/,
            $this->container->getParameter("site.product.perpage") /*limit per page*/
        );
        $productIds = array();
        foreach ($pagination->getItems() as $entity) {
            $productIds[] = $entity->getId();
        }
        
        $media = array();
        $ordered = array();
        $stocks = array();
        $prices = array();
        if (!empty($productIds)) {
            $media = $em->getRepository("CoreProductBundle:ProductMedia")->getProductsMediasArray($productIds);
            $stocks = $em->getRepository("CoreProductBundle:Stock")->getStocksArray($productIds);
            $prices = $em->getRepository("CoreProductBundle:Price")->getPricesArray($productIds);
            if (array_key_exists("shop", $minishop) && $minishop['shop']) {
                $ordered  = $em->getRepository("CoreShopBundle:OrderItem")->getProductsOrderCount($productIds);
            }
        }

        return $this->render('SiteProductBundle:Product:list.html.twig', array(
            'entities'      => $pagination,
            'pricegroup'    => $priceGroup,
            'currency'      => $currency,
            'category'      =>$category,
            'media'         => $media,
            'ordered'       => $ordered,
            'stock'         => $stocks,
            'prices'        => $prices,
            'recursive'     => $this->container->getParameter("site.product.recursive"),
        ));
    }

    public function vendorAction($vendor, $page = 1)
    {
        $minishop  = $this->container->getParameter('minishop');
        $priceGroup = $this->getPriceGroup();
        $currency = $this->getCurrency();
        $em = $this->getDoctrine()->getManager();
        $filter = new Filter();
        $filter->setVendor($vendor);
        $querybuilder  = $em->getRepository("CoreProductBundle:Product")->findByCategoryQueryBuilder(null, $this->container->getParameter("site.product.recursive"), true, false, false);
        $query  = $em->getRepository("CoreProductBundle:Product")->filterQueryBuilder($querybuilder, $filter)->getQuery();
        $query =  $em->getRepository("CoreProductBundle:Product")->setHint($query);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page /*page number*/,
            $this->container->getParameter("site.product.perpage") /*limit per page*/
        );
        $productIds = array();
        foreach ($pagination->getItems() as $entity) {
            $productIds[] = $entity->getId();
        }
        
        $media = array();
        $ordered = array();
        $stocks = array();
        $prices = array();
        if  (!empty($productIds)) {
            $media = $em->getRepository("CoreProductBundle:ProductMedia")->getProductsMediasArray($productIds);
            $stocks = $em->getRepository("CoreProductBundle:Stock")->getStocksArray($productIds);
            $prices = $em->getRepository("CoreProductBundle:Price")->getPricesArray($productIds);
            if (array_key_exists("shop", $minishop) && $minishop['shop']) {
                $ordered  = $em->getRepository("CoreShopBundle:OrderItem")->getProductsOrderCount($productIds);
            }
        }

        return $this->render('SiteProductBundle:Product:list.html.twig', array(
            'entities'      => $pagination,
            'pricegroup'    => $priceGroup,
            'currency'      => $currency,
            'media'         => $media,
            'stock'         => $stocks,
            'ordered'       => $ordered,
            'prices'        => $prices,
        ));
    }

    public function searchAction()
    {
        $minishop  = $this->container->getParameter('minishop');
        $priceGroup = $this->getPriceGroup();
        $currency = $this->getCurrency();
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        if ($request->getMethod() == "POST") {
            $post = $request->get('search', array());
            $filter = new Filter();
            $filter->setWords($post['query']);
            $request->getSession()->set('product-search', $filter);
        }
        $filter = $request->getSession()->get('product-search' , new Filter());
        $querybuilder =  $em->getRepository("CoreProductBundle:Product")->findByCategoryQueryBuilder(null, false, true, false, false);
        $query  = $em->getRepository("CoreProductBundle:Product")->filterQueryBuilder($querybuilder, $filter)->getQuery();
        $query =  $em->getRepository("CoreProductBundle:Product")->setHint($query);
        $page = $request->get('page', 1);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            $this->container->getParameter("site.product.perpage") /*limit per page*/,
            array('disctinct' => false)
        );
        $productIds = array();
        foreach ($pagination->getItems() as $entity) {
            $productIds[] = $entity->getId();
        }
        
        $media = array();
        $ordered = array();
        $stocks = array();
        $prices = array();
        if  (!empty($productIds)) {
            $media = $em->getRepository("CoreProductBundle:ProductMedia")->getProductsMediasArray($productIds);
            $stocks = $em->getRepository("CoreProductBundle:Stock")->getStocksArray($productIds);
            $prices = $em->getRepository("CoreProductBundle:Price")->getPricesArray($productIds);
            if (array_key_exists("shop", $minishop) && $minishop['shop']) {
                $ordered  = $em->getRepository("CoreShopBundle:OrderItem")->getProductsOrderCount($productIds);
            }
        }

        return $this->render('SiteProductBundle:Product:search.html.twig', array(
            'entities'      => $pagination,
            'pricegroup'    => $priceGroup,
            'currency'      => $currency,
            'media'         => $media,
            'stock'         => $stocks,
            'ordered'       => $ordered,
            'prices'        => $prices,            
        ));
    }

    public function topAction()
    {
        $minishop  = $this->container->getParameter('minishop');
        $priceGroup = $this->getPriceGroup();
        $currency = $this->getCurrency();
        $em = $this->getDoctrine()->getManager();
        $query  = $em->getRepository("CoreProductBundle:Product")->findByCategoryQueryBuilder(null, false, true, false, false)
                ->orderBy("p.createdAt", "desc")
                ->distinct()
                ->getQuery();
        $entities = $em->getRepository("CoreProductBundle:Product")->setHint($query)
                ->setMaxResults(6)
                ->getResult();

        $productIds = array();
        foreach ($entities as $e) {
            $productIds[] = $e->getId();
        }
        
        $media = array();
        $ordered = array();
        $stocks = array();
        $prices = array();
        if  (!empty($productIds)) {
            $media = $em->getRepository("CoreProductBundle:ProductMedia")->getProductsMediasArray($productIds);
            $stocks = $em->getRepository("CoreProductBundle:Stock")->getStocksArray($productIds);
            $prices = $em->getRepository("CoreProductBundle:Price")->getPricesArray($productIds);
            if (array_key_exists("shop", $minishop) && $minishop['shop']) {
                $ordered  = $em->getRepository("CoreShopBundle:OrderItem")->getProductsOrderCount($productIds);
            }
        }
        
        return $this->render('SiteProductBundle:Product:top.html.twig', array(
            'entities'      => $entities,
            'pricegroup'    => $priceGroup,
            'currency'      => $currency,
            'media'         => $media,
            'stock'         => $stocks,
            'ordered'       => $ordered,
            'prices'        => $prices,
       ));
    }

    public function tagAction($tag, $limit = null)
    {
        $minishop  = $this->container->getParameter('minishop');
        $priceGroup = $this->getPriceGroup();
        $currency = $this->getCurrency();
        $em = $this->getDoctrine()->getManager();
        $qb  = $em->getRepository("CoreProductBundle:Product")->findByCategoryQueryBuilder(null, false, true, false, false);
        $qb->andWhere($qb->expr()->like('p.tags', ":tag"))
           ->setParameter('tag', '%'.$tag.', %');
        $query = $qb->distinct()->getQuery();
        $query = $em->getRepository("CoreProductBundle:Product")->setHint($query);
        if ($limit) {
            $query->setMaxResults($limit);
        }
        $entities = $query->getResult();
        $productIds = array();
        foreach ($entities as $e) {
            $productIds[] = $e->getId();
        }
        
        $media = array();
        $ordered = array();
        $stocks = array();
        $prices = array();
        if  (!empty($productIds)) {
            $media = $em->getRepository("CoreProductBundle:ProductMedia")->getProductsMediasArray($productIds);
            $stocks = $em->getRepository("CoreProductBundle:Stock")->getStocksArray($productIds);
            $prices = $em->getRepository("CoreProductBundle:Price")->getPricesArray($productIds);
            if (array_key_exists("shop", $minishop) && $minishop['shop']) {
                $ordered  = $em->getRepository("CoreShopBundle:OrderItem")->getProductsOrderCount($productIds);
            }
        }
        
        return $this->render('SiteProductBundle:Product:top.html.twig', array(
            'entities'      => $entities,
            'pricegroup'        => $priceGroup,
            'currency'      => $currency,
            'tag'           => $tag,
            'slug'          => GedmoUrlizer::urlize($tag),
            'media'         => $media,
            'stock'         => $stocks,
            'ordered'       => $ordered,
            'prices'        => $prices,
        ));
    }

    public function productMainMediaAction($product, $type = 'thumb', $link_path=null, $gallery = null)
    {
        $em = $this->getDoctrine()->getManager();
        $productEntity = $em->getRepository('CoreProductBundle:Product')->getProduct($product);
        $entity = ($productEntity) ? $productEntity->getMedia()->first() : null;
        if ($entity) {
            $entity = $entity->getMedia();
            if (!file_exists($entity->getAbsolutePath($type))) {
                $imageOptions = $this->container->getParameter('images');
                $entity->update($type, $imageOptions[$type]);
            }
        }

        $source = null;
        if ($entity) {

            $source = $entity->getWebPath($type);
        }

        return $this->render('CoreMediaBundle:Image:display.html.twig', array(
            'image'     => $entity,
            'source'    => $source,
            'link_path' => $link_path,
            'gallery'   => $gallery,
        ));
    }
}
