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
    
    public function googleEcommerceAction($order = null)
    {
        $config = $this->container->getParameter('marketing.google');
        
        return $this->render("SiteMarketingBundle:Marketing:googleecommerce.html.twig", array(
            'google' => $config,
            'order' => $order
        ));
    }
    
    public function facebookRemarketingAction()
    {
        $config = $this->container->getParameter('marketing.facebook');
        
        return $this->render("SiteMarketingBundle:Marketing:facebookremarketing.html.twig", array(
            'facebook' => $config,
        ));
    }
    
    public function facebookConversionAction($order = null)
    {
        $config = $this->container->getParameter('marketing.facebook');
        
        return $this->render("SiteMarketingBundle:Marketing:facebookconversion.html.twig", array(
            'facebook' => $config,
            'order' => $order
        ));
    }
}
