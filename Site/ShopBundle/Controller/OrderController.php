<?php

namespace Site\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OrderController extends ShopController
{
    /* Lists all Order entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getShopUser();
        if ($user === null) {
           throw $this->createNotFoundException('Unable to find User entity.');
        }

        $query = $em->getRepository('CoreShopBundle:Order')
                ->createQueryBuilder('o')
                ->andWhere('o.user = :uid')
                ->orderBy('o.createdAt', 'desc')
                ->setParameter('uid', $user->getId())
                ->getQuery();
        $page = $this->getRequest()->get("page", 1);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            200/*limit per page*/
        );

        return $this->render('SiteShopBundle:Order:index.html.twig', array(
            'entities' => $pagination
        ));
    }

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        $user = $this->getShopUser();
        if ($user === null) {
           throw $this->createNotFoundException('Unable to find User entity.');
        }

        if ($user->getId() != $entity->getUser()->getId()) {
            throw new AccessDeniedException();
        }

        return $this->render('SiteShopBundle:Order:show.html.twig', array(
            'entity'      => $entity,
        ));
    }
}
