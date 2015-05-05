<?php

namespace Site\MarketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\MarketingBundle\Entity\Message;
use Site\MarketingBundle\Form\ContactType;
use Site\MarketingBundle\Form\ContactMultiType;
use Site\MarketingBundle\Form\ContactClaimType;

class ContactController extends Controller
{

    public function contactAction()
    {
        $minishop  = $this->container->getParameter('minishop');
        return $this->render("SiteMarketingBundle:Contact:contact.html.twig", array(
            'minishop' => $minishop,
        ));
    }

    public function contactFormAction()
    {
        $minishop  = $this->container->getParameter('minishop');
        $verificationCode = (string) $this->container->getParameter('contact.verification_code');
        $form = $this->createForm(new ContactType());

        return $this->render("SiteMarketingBundle:Contact:contactForm.html.twig", array(
            'form' => $form->createView(),
            'verification_code' => $verificationCode,
            'minishop' => $minishop,
        ));
    }

    public function sendContactFormAction()
    {
        $verificationCode = (string) $this->container->getParameter('contact.verification_code');
        $request = $this->getRequest();
        $form =$this->createForm(new ContactType());
        if ($request->getMethod() == "POST") {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if ($verificationCode !== $data['verification_code']) {
                    $t = $this->get('translator')->trans('Verification code does not match. You must enter %code%', array('%code%' => $verificationCode));
                    $form->addError(new \Symfony\Component\Form\FormError($t));
                }

                if ($form->isValid()) {
                // send email
                    $t = $this->get('translator')->trans('Contact %subject%', array('%subject%' => $request->getHost()));
                    $emails = $this->container->getParameter('default.emails');
                    $send = array($emails['contact']);
                    if ($data['copy']) {
                        $send[] = $data['email'];
                    }
                    $message = \Swift_Message::newInstance()
                            ->setSubject($t)
                            ->setFrom($emails['default'], $this->container->getParameter('site_title'))
                            ->setTo($send)
                            ->setBody($this->renderView('SiteMarketingBundle:Email:message.html.twig', array(
                                'data'    => $data,
                                'type'   =>  'contact',
                                )), 'text/html')
                            ->setContentType("text/html");
                    $this->get('swiftmailer.mailer.site_mailer')->send($message);

                    $em = $this->getDoctrine()->getManager();
                    $msg = new Message();
                    $msg->setType('contact');
                    $msg->setTitle($t);
                    unset($data['verification_code']);
                    unset($data['_token']);
                    unset($data['copy']);
                    $msg->setMessage($data);
                    $em->persist($msg);
                    $em->flush();

                    return $this->render('SiteMarketingBundle:Contact:send.html.twig');
                }
            }
        }

        return $this->render("SiteMarketingBundle:Contact:contactForm.html.twig", array(
            'form' => $form->createView(),
            'verification_code' => $verificationCode,
        ));
    }

     public function contactMultiFormAction()
    {
        $verificationCode = (string) $this->container->getParameter('contact.verification_code');
        $form =$this->createForm(new ContactMultiType());

        return $this->render("SiteMarketingBundle:Contact:contactMultiForm.html.twig", array(
            'form' => $form->createView(),
            'verification_code' => $verificationCode,
        ));
    }

    public function sendContactMultiFormAction()
    {
        $verificationCode = (string) $this->container->getParameter('contact.verification_code');
        $request = $this->getRequest();
        $form =$this->createForm(new ContactMultiType());
        if ($request->getMethod() == "POST") {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if ($verificationCode !== $data['verification_code']) {
                    $t = $this->get('translator')->trans('Verification code does not match. You must enter %code%', array('%code%' => $verificationCode));
                    $form->addError(new \Symfony\Component\Form\FormError($t));
                }

                if ($form->isValid()) {
                // send email
                    $emails = $this->container->getParameter('default.emails');
                    $send = array($emails['contact']);
                    if ($data['copy']) {
                        $send[] =$data['email'];
                    }
                    $t = $this->get('translator')->trans('Contact %type% %subject%', array('%subject%' => $request->getHost(), '%type%' => $data['type']));
                    $message = \Swift_Message::newInstance()
                            ->setSubject($t)
                            ->setFrom($emails['default'], $this->container->getParameter('site_title'))
                            ->setTo($send)
                            ->setBody($this->renderView('SiteMarketingBundle:Email:message.html.twig', array(
                                'data'    => $data,
                                'type'   =>  'contactmulti',
                                )), 'text/html')
                            ->setContentType("text/html");
                    $this->get('swiftmailer.mailer.site_mailer')->send($message);

                    $em = $this->getDoctrine()->getManager();
                    $msg = new Message();
                    $msg->setType('contactmulti');
                    $msg->setTitle($t);
                    unset($data['verification_code']);
                    unset($data['_token']);
                    unset($data['copy']);
                    $minishop  = $this->container->getParameter('minishop');
                    if ((!empty($data['orderNumber'])) && isset($minishop['shop']) && $minishop['shop']) {
                        $order = $em->getRepository('CoreShopBundle:Order')->findOneBy(array('order_number' => $data['orderNumber']));
                        if (!$order) {
                            $order = $em->getRepository('CoreShopBundle:Order')->find($data['orderNumber']);
                        }
                        if ($order) {
                            $data['orderId'] = $order->getId();
                        }
                    }
                    $msg->setMessage($data);
                    $em->persist($msg);
                    $em->flush();

                    return $this->render('SiteMarketingBundle:Contact:send.html.twig');
                }
            }
        }

        return $this->render("SiteMarketingBundle:Contact:contactMultiForm.html.twig", array(
            'form' => $form->createView(),
            'verification_code' => $verificationCode,
        ));
    }

    public function contactClaimFormAction()
    {
        $verificationCode = (string) $this->container->getParameter('contact.verification_code');
        $form =$this->createForm(new ContactClaimType());

        return $this->render("SiteMarketingBundle:Contact:contactClaimForm.html.twig", array(
            'form' => $form->createView(),
            'verification_code' => $verificationCode,
        ));
    }

    public function sendContactClaimFormAction()
    {
        $verificationCode = (string) $this->container->getParameter('contact.verification_code');
        $request = $this->getRequest();
        $form =$this->createForm(new ContactClaimType());
        if ($request->getMethod() == "POST") {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if ($verificationCode !== $data['verification_code']) {
                    $t = $this->get('translator')->trans('Verification code does not match. You must enter %code%', array('%code%' => $verificationCode));
                    $form->addError(new \Symfony\Component\Form\FormError($t));
                }

                if ($form->isValid()) {
                // send email
                    $t = $this->get('translator')->trans('Claim order no.:%order% %subject%', array('%subject%' => $request->getHost(), '%order%' => $data['orderNumber']));
                    $emails = $this->container->getParameter('default.emails');
                    $send = array($emails['contact']);
                    if ($data['copy'] && isset($data['email'])) {
                        $send[] = $data['email'];
                    }
                    $message = \Swift_Message::newInstance()
                            ->setSubject($t)
                            ->setFrom($emails['default'], $this->container->getParameter('site_title'))
                            ->setTo($send)
                            ->setBody($this->renderView('SiteMarketingBundle:Email:message.html.twig', array(
                                'data'    => $data,
                                'type'   =>  'claim',
                                )), 'text/html')
                                ->setContentType("text/html");
                    $this->get('swiftmailer.mailer.site_mailer')->send($message);

                    $em = $this->getDoctrine()->getManager();
                    $msg = new Message();
                    $msg->setType('claim');
                    $msg->setTitle($t);
                    unset($data['verification_code']);
                    unset($data['_token']);
                    unset($data['copy']);
                    $minishop  = $this->container->getParameter('minishop');
                    if ((!empty($data['orderNumber'])) && isset($minishop['shop']) && $minishop['shop']) {
                        $order = $em->getRepository('CoreShopBundle:Order')->findOneBy(array('order_number' => $data['orderNumber']));
                        if (!$order) {
                            $order = $em->getRepository('CoreShopBundle:Order')->find($data['orderNumber']);
                        }
                        if ($order) {
                            $data['orderId'] = $order->getId();
                        }
                    }
                    $msg->setMessage($data);
                    $em->persist($msg);
                    $em->flush();

                    return $this->render('SiteMarketingBundle:Contact:send.html.twig');
                }
            }
        }

        return $this->render("SiteMarketingBundle:Contact:contactClaimForm.html.twig", array(
            'form' => $form->createView(),
            'verification_code' => $verificationCode,
        ));
    }
}
