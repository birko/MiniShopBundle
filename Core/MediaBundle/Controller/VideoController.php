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
    public function displayAction($id, $gallery = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreMediaBundle:Video')->find($id);

        return $this->displayEntityAction($entity, $gallery);
    }

    public function displayEntityAction(Video $entity = null, $gallery = null)
    {
        $webpath = null;
        if ($entity !== null) {
            $webpath = $entity->getWebPath();
        }

        return $this->render('CoreMediaBundle:Video:display.html.twig', array(
            'video'     => $entity,
            'source'    => $webpath,
            'gallery'   => $gallery,
        ));
    }
}
