<?php

namespace Site\MarketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class MarketingSiteController extends Controller
{

    public function googleAnalyticsAction()
    {
        $config = $this->container->getParameter('marketing.google');
        
        return $this->render("SiteMarketingBundle:Marketing:googleanalytics.html.twig", array(
            'google' => $config,
        ));
    }
}
