<?php

namespace Site\VendorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class VendorController extends Controller
{
    public function indexAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $content  = $em->getRepository("CoreVendorBundle:Vendor")->getBySlug($slug);
        $page = $this->getRequest()->get("page", 1);

        return $this->render('SiteVendorBundle:Vendor:index.html.twig', array('entity' => $content, 'page' => $page));
    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities  = $em->getRepository("CoreVendorBundle:Vendor")->getVendorsQuery()->getResult();;

        return $this->render('SiteVendorBundle:Vendor:list.html.twig', array('entities' => $entities));
    }
}
