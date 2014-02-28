<?php

namespace Core\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction()
    {
        return $this->render('CoreCommonBundle:Default:index.html.twig');
    }

    public function sidebarAction()
    {
        $minishop  = $this->container->getParameter('minishop');

        return $this->render('CoreCommonBundle:Default:sidebar.html.twig', array(
            'minishop' => $minishop,
        ));
    }
}
