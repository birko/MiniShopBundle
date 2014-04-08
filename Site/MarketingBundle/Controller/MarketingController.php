<?php

namespace Site\MarketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class MarketingController extends Controller
{

    public function googleAnalyticsAction()
    {
        $config = $this->container->getParameter('marketing.google');
        
        return $this->render("SiteMarketingBundle:Marketing:googleanalytics.html.twig", array(
            'google' => $config,
        ));
    }
    
    public function facebookPixelAction($order = null)
    {
        $config = $this->container->getParameter('marketing.facebook');
        
        return $this->render("SiteMarketingBundle:Marketing:facebookpixel.html.twig", array(
            'facebook' => $config,
            'order' => $order
        ));
    }
}
