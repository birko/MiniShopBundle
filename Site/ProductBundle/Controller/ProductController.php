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
        $attributes = $em->getRepository("CoreProductBundle:Attribute")->getGroupedAttributesByProducts(
            array($product->getId()), 
            array(), 
            $this->getRequest()->get('_locale')
        );
        
        return $this->render('SiteProductBundle:Product:index.html.twig', 
            $this->container->get('site_product.productservice')->loadResources(
                array($product->getId()), 
                array(
                    'product'       => $product,
                    'pricegroup'    => $priceGroup,
                    'currency'      => $currency,
                    'attributes'    => $attributes,
                )
            )
        );
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

        return $this->render('SiteProductBundle:Product:list.html.twig',
            $this->container->get('site_product.productservice')->loadResources(
                $productIds,
                array(
                    'entities'      => $pagination,
                    'pricegroup'    => $priceGroup,
                    'currency'      => $currency,
                    'category'      =>$category,
                    'recursive'     => $this->container->getParameter("site.product.recursive"),
                )
            )
        );
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
        
        return $this->render('SiteProductBundle:Product:list.html.twig', 
            $this->container->get('site_product.productservice')->loadResources(
                $productIds,
                array(
                    'entities'      => $pagination,
                    'pricegroup'    => $priceGroup,
                    'currency'      => $currency,
                )
            )
        );
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

        return $this->render('SiteProductBundle:Product:search.html.twig',
            $this->container->get('site_product.productservice')->loadResources(
                $productIds,
                array(
                    'entities'      => $pagination,
                    'pricegroup'    => $priceGroup,
                    'currency'      => $currency,            
                )
            )
        );
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
        
        return $this->render('SiteProductBundle:Product:top.html.twig', 
            $this->container->get('site_product.productservice')->loadResources(
                $productIds,
                array(
                    'entities'      => $entities,
                    'pricegroup'    => $priceGroup,
                    'currency'      => $currency,
                )    
            )
       );
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
        
        return $this->render('SiteProductBundle:Product:top.html.twig',
            $this->container->get('site_product.productservice')->loadResources(
                $productIds,
                array(
                    'entities'      => $entities,
                    'pricegroup'    => $priceGroup,
                    'currency'      => $currency,
                    'tag'           => $tag,
                    'slug'          => GedmoUrlizer::urlize($tag),
                )
            )
        );
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
