<?php

namespace Site\BannerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\BannerBundle\Entity\Banner;

/**
 * Banner controller.
 *
 */
class BannerController extends Controller
{
    /**
     * Lists all Banner entities.
     *
     */
    public function bannerAction($category = null, $type = 'banner')
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('CoreBannerBundle:Banner')->getBannersQuery($category);
        $entities = $query->getResult();

        return $this->render('SiteBannerBundle:Banner:banner.html.twig', array(
            'entities' => $entities,
            'type' => $type
        ));
    }
}
