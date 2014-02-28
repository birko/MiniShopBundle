<?php

/**
 * Description of UserController
 *
 * @author birko
 */

namespace Core\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

use Core\UserBundle\Entity\User;
use Core\UserBundle\Form\UserType;

class UserController extends Controller
{
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('CoreUserBundle:User:login.html.twig', array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }

    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    public function logoutAction()
    {
        // The security layer will intercept this request
    }

    public function setLocaleAction()
    {
        $request = $this->getRequest();

        return $this->redirect($request->headers->get('referer'));
    }

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('CoreUserBundle:User')->createQueryBuilder('u')
        ->addOrderBy("u.id")
        ->getQuery();

        $page = $this->getRequest()->get("page", 1);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page /*page number*/,
            100 /*limit per page*/
        );

        return $this->render('CoreUserBundle:User:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createForm(new UserType(), $entity);

        return $this->render('CoreUserBundle:User:edit.html.twig', array(
        'entity'      => $entity,
        'edit_form'   => $editForm->createView(),
        ));
    }

    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $request = $this->getRequest();

        $editForm = $this->createForm(new UserType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_edit', array('id' => $id)));
        }

        return $this->render('CoreUserBundle:User:edit.html.twig', array(
        'entity'      => $entity,
        'edit_form'   => $editForm->createView(),
        ));
    }
}
