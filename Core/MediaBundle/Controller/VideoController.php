<?php

namespace Core\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\MediaBundle\Entity\Video;

/**
 * Image controller.
 *
 */
class VideoController extends Controller
{
    public function displayAction($id, $link_path = null, $gallery = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreMediaBundle:Video')->find($id);

        return $this->displayEntityAction($entity, $link_path, $gallery);
    }

    public function displayEntityAction(Video $entity = null, $link_path = null, $gallery = null)
    {
        $webpath = null;
        if ($entity !== null) {
            $webpath = $entity->getWebPath();
        }

        return $this->render('CoreMediaBundle:Video:display.html.twig', array(
            'video'     => $entity,
            'source'    => $webpath,
            'gallery'   => $gallery,
            'link_path' => $link_path,
        ));
    }
}
