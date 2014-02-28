<?php

namespace Site\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContentController extends Controller
{

    public function indexAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $content  = $em->getRepository("CoreContentBundle:Content")->getBySlug($slug);

        return $this->render('SiteContentBundle:Content:index.html.twig', array('content' => $content));
    }

    public function listAction($category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $querybuilder = $em->getRepository('CoreContentBundle:Content')->findContentByCategoryQueryBuilder($category)->orderBy('c.id', 'desc');
        $query = $em->getRepository('CoreContentBundle:Content')->setHint($querybuilder->getQuery());
        $paginator = $this->get('knp_paginator');
        $page = $this->getRequest()->get('cpage', 1);
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            6 /*limit per page*/
        );

        return $this->render('SiteContentBundle:Content:list.html.twig', array(
            'entities' => $pagination,
            'category' => $category
        ));
    }

    public function displayAction($category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $count = $em->getRepository('CoreContentBundle:Content')->findContentByCategoryQueryBuilder($category)
            ->select('count(c.id)')->getQuery()->getSingleScalarResult();
        if ($count >1) {
            return $this->listAction($category);
        } else {
            $query = $em->getRepository('CoreContentBundle:Content')->findContentByCategoryQueryBuilder($category)->getQuery();
            $content = $em->getRepository('CoreContentBundle:Content')->setHint($query)->getOneOrNullResult();

            return $this->render('SiteContentBundle:Content:content.html.twig', array(
                'content' => $content
            ));
        }
    }
}
