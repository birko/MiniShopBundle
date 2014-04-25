<?php

namespace Site\NewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Core\NewsletterBundle\Form\BaseNewsletterEmailType;
use Core\NewsletterBundle\Entity\NewsletterEmail;

class NewsletterController extends Controller
{
    public function formAction($target = null)
    {
        $entity = new NewsletterEmail();
        $form   = $this->createForm(new BaseNewsletterEmailType(), $entity);

        return $this->render('SiteNewsletterBundle:Newsletter:form.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'target' => $target,
        ));
    }

    public function subscribeAction()
    {
        $entity = new NewsletterEmail();
        $form   = $this->createForm(new BaseNewsletterEmailType(), $entity);
        $request = $this->getRequest();
        $result = false;
        if ($request->getMethod() == "POST") {
            $form->bind($request);
            $form->isValid();
            $email = filter_var($entity->getEmail(), FILTER_VALIDATE_EMAIL);
            if ($email !== false) {
                $sendy = $this->container->getParameter('newsletter.sendy');
                if (!empty($sendy['api_key'])) {
                    $sendy = new \SendyPHP\SendyPHP($sendy);
                    $status = $sendy->subscribe(array( "email" => $email ));
                    $result = $status['status'];
                } else {
                    $em = $this->getDoctrine()->getManager();
                    $entity2 = $em->getRepository('CoreNewsletterBundle:NewsletterEmail')->getEmail($email);
                    if (!$entity2) {
                        $entity->setEnabled(true);
                        $em->persist($entity);
                        $result = true;
                    } else {
                        $entity2->setEnabled(true);
                        $em->persist($entity2);
                        $result = true;
                    }
                    $em->flush();
                }
            }
        }
        $target= $request->get('_target', null);
        if ($target) {
            return $this->redirect($target);
        } else {
            return $this->render('SiteNewsletterBundle:Newsletter:subscribe.html.twig', array(
                'result' => $result,
            ));
        }
    }

    public function unsubscribeAction()
    {
        $entity = new NewsletterEmail();
        $form   = $this->createForm(new BaseNewsletterEmailType(), $entity);
        $request = $this->getRequest();
        $result = false;
        if ($request->getMethod() == "POST") {
            $form->bind($request);
            $form->isValid();
            $em = $this->getDoctrine()->getManager();
            $email = filter_var($entity->getEmail(), FILTER_VALIDATE_EMAIL);
            if ($email !== false) {
                if (!empty($sendy['api_key'])) {
                    $sendy = new \SendyPHP\SendyPHP($sendy);
                    $status = $sendy->unsubscribe($email);
                    $result = $status['status'];
                } else {
                    $entity2 = $em->getRepository('CoreNewsletterBundle:NewsletterEmail')->getEmail($email);
                    if ($entity2) {
                        $entity2->setEnabled(false);
                        $em->persist($entity2);
                        $em->flush();
                        $result = true;
                    }
                }
            }
        }

        return $this->render('SiteNewsletterBundle:Newsletter:unsubscribe.html.twig', array(
            'result' => $result,
            'form'  => $form->createView()
        ));
    }
}
