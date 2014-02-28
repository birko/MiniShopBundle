<?php

namespace Core\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\MediaBundle\Entity\Image;

/**
 * Image controller.
 *
 */
class ImageController extends Controller
{
    public function displayAction($id, $dir, $link_path = null, $gallery = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreMediaBundle:Image')->find($id);

        return $this->displayEntityAction($entity, $dir, $link_path, $gallery);
    }

    public function displayEntityAction(Image $entity = null, $dir, $link_path = null, $gallery = null)
    {
        $webpath = null;
        if ($entity !== null) {
            $imageOptions = $this->container->getParameter('images');
            if (!file_exists($entity->getAbsolutePath($dir)) && $dir != "original") {
                $entity->update($dir, $imageOptions[$dir]);
            }
            if (!empty($link_path) && isset($imageOptions[$link_path]) && !file_exists($entity->getAbsolutePath($link_path))) {
                $entity->update($link_path, $imageOptions[$link_path]);
            }
            $webpath = $entity->getWebPath($dir);
        }

        return $this->render('CoreMediaBundle:Image:display.html.twig', array(
            'image'      => $entity,
            'source'    =>  $webpath,
            'link_path' => $link_path,
            'gallery'  => $gallery,

        ));
    }
}
