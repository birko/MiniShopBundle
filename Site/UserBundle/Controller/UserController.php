<?php

namespace Site\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Core\UserBundle\Entity\User;
use Site\UserBundle\Form\NewUserType;
use Core\UserBundle\Form\ChangePasswordType;
use Core\UserBundle\Form\PasswordRecoveryType;
use Core\UserBundle\Form\SimpleChangePasswordType;

class UserController extends Controller
{
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('SiteUserBundle:User:login.html.twig', array(
        // last username entered by the user
        'last_username' => $session->get(SecurityContext::LAST_USERNAME),
        'error'         => $error,
        ));
    }

    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    public function logoutAction()
    {
        // The security layer will intercept this request
    }

    public function newAction()
    {
        $entity = new User();
        $minishop  = $this->container->getParameter('minishop');
        $full = $this->container->getParameter("site.user.fullregistration");
        $options = array();
        if ($minishop['shop'] && $full) {
            $addressRequiredConfiguration = $this->container->getParameter("address.required");
            $options['address'] = array('required' => $addressRequiredConfiguration);
        }
        $form  = $this->createForm(new NewUserType(), $entity, $options);
        if ($minishop['shop'] && $full) {
            $form->get('addresses')->setData(array(new \Core\ShopBundle\Entity\Address()));
        }

        return $this->render('SiteUserBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'full'  => $full,
        ));
    }

    public function createAction()
    {
        $entity  = new User();
        $request = $this->getRequest();
        $minishop  = $this->container->getParameter('minishop');
        $full = $this->container->getParameter("site.user.fullregistration");
        $options = array();
        if ($minishop['shop'] && $full) {
            $addressRequiredConfiguration = $this->container->getParameter("address.required");
            $options['address'] = array('required' => $addressRequiredConfiguration);
        }
        $form  = $this->createForm(new NewUserType(), $entity, $options);
        if ($minishop['shop'] && $full) {
            $form->get('addresses')->setData(array(new \Core\ShopBundle\Entity\Address()));
        }
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //user settup
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $password = $entity->getPassword();
            $passwordhash = $encoder->encodePassword($password, $entity->getSalt());
            $entity->setPassword($passwordhash);
            $login = $entity->getEmail();
            $entity->setLogin($login);
            $entity->setEnabled(true);
            //user save
            $entity = $em->getRepository('CoreUserBundle:User')->createUser($entity);
            if ($minishop['shop'] && $full) {
                $addresses =  $form->get('addresses')->getData();
                $em->getRepository('CoreShopBundle:Address')->createUser($entity, $addresses);
            }
            if ($entity) {
                $t = $this->get('translator')->trans('New registration - %subject%', array(
                       '%subject%' => $this->container->getParameter('site_title'),
                   ));
                //send register email
                $emails = $this->container->getParameter('default.emails');
                $message = \Swift_Message::newInstance()
                        ->setSubject($t)
                        ->setFrom($emails['default'], $this->container->getParameter('site_title'))
                        ->setTo(array($entity->getEmail()))
                        ->setBody($this->renderView('SiteUserBundle:Email:new.html.twig', array(
                            'login' => $entity->getLogin(),
                            'password' => $password,
                        )), 'text/html');
                $this->get('swiftmailer.mailer.site_mailer')->send($message);
                //login
                $session = $this->getRequest()->getSession();
                $cart = $session->get('cart');
                $token = new UsernamePasswordToken($entity, $entity->getPassword(), 'secured_area', $entity->getRoles());
                $this->get('security.context')->setToken($token);
                $redirect = $this->container->getParameter("site.user.registrationredirect");
                if($minishop['shop'] && $cart && !$cart->isEmpty()) {
                    $session->set('cart', $cart);
                }
                if(!empty($redirect)) {
                    return $this->redirect($this->generateUrl($redirect));
                } elseif ($minishop['shop'] && $cart && !$cart->isEmpty()) {  
                    return $this->redirect($this->generateUrl('cart'));
                } else {
                    return $this->redirect($this->generateUrl('category_homepage'));
                }
            }
        }

        return $this->render('SiteUserBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'full'  => $full,
        ));
    }

    public function indexAction()
    {
        $minishop  = $this->container->getParameter('minishop');
        
        return $this->render('SiteUserBundle:User:index.html.twig', array('minishop' => $minishop));
    }

    public function passwordAction()
    {
        if ($this->get('security.context')->getToken()) {
            $form = $this->createForm(new ChangePasswordType());
            $auth = $this->get('security.context')->getToken()->getUser();
            if ($auth) {
                $em = $this->getDoctrine()->getManager();
                $entity = $em->getRepository("CoreUserBundle:User")->find($auth->getId());

                return $this->render('SiteUserBundle:User:password.html.twig', array(
                    // last username entered by the user
                    'form' => $form->createView(),
                ));
            }
        }

        return $this->createNotFoundException();
    }

    public function changePasswordAction()
    {
        if ($this->get('security.context')->getToken()) {
            $auth = $this->get('security.context')->getToken()->getUser();

            $request = $this->getRequest();
            $formdata = (object) array('current_password' => '', 'new_password' => '');
            $form = $this->createForm(new ChangePasswordType(), $formdata);
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $entity = $em->getRepository("CoreUserBundle:User")->find($auth->getId());
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $oldpassword =  $formdata->current_password;
                $oldpassword = $encoder->encodePassword($oldpassword, $entity->getSalt());
                if ($entity->getPassword() == $oldpassword) {
                    $newpassword = $formdata->new_password;
                    $password = $encoder->encodePassword($newpassword, $entity->getSalt());
                    $entity->setPassword($password);
                    $em->persist($entity);
                    $em->flush();

                    return $this->redirect($this->generateUrl('SiteUserBundle_index'));
                }
            }

            return $this->render('SiteUserBundle:User:password.html.twig', array(
                // last username entered by the user
                'form' => $form->createView(),
            ));
        } else {
            return $this->createNotFoundException();
        }
    }

    public function passwordRecoveryAction()
    {
        $form = $this->createForm(new PasswordRecoveryType());

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                // check if user with email exists
                $data = $form->getData();
                $user = $this->getDoctrine()->getManager()->getRepository('CoreUserBundle:User')->findOneByEmail($data['email']);
                if (!$user) {
                    $t = $this->get('translator')->trans('User with supplied email not found.');
                    $this->get('session')->getFlashBag()->add('error', $t);
                } else {
                    // send email to user
                    $t = $this->get('translator')->trans('Password recovery');
                    $emails = $this->container->getParameter('default.emails');
                    $message = \Swift_Message::newInstance()
                            ->setSubject($t)
                            ->setFrom($emails['default'], $this->container->getParameter('site_title'))
                            ->setTo(array($user->getEmail()))
                            ->setBody($this->renderView('SiteUserBundle:Email:password_recovery.html.twig', array(
                                    'link' => $this->generateUrl('SiteUserBundle_password_recovery_do', array(
                                    'email' => urlencode(urlencode($user->getEmail())),
                                    'hash' => urlencode(md5($user->getEmail() . $user->getPassword())),
                                ), true),
                            )), 'text/html');
                    $this->get('swiftmailer.mailer.site_mailer')->send($message);
                    $t = $this->get('translator')->trans('Instructions for password recovery were sent to your email. Please follow instructions for password recovery.');
                    $this->get('session')->getFlashBag()->add('success', $t);
                }
            }
        }

        return $this->render('SiteUserBundle:User:password_recovery.html.twig', array(
                    'form' => $form->createView(),
       ));
    }

    public function passwordRecoveryDoAction()
    {
        $request = $this->getRequest();
        $email = $request->get('email');
        $hash = $request->get('hash');

        $user = $this->getDoctrine()->getManager()->getRepository('CoreUserBundle:User')->findOneByEmail(urldecode($email));
        // if user with supplied email is not found
        if (!$user) {
            throw $this->createNotFoundException();
        }

        // if generated hash does not equal with supplied hash
        $generatedHash = md5($user->getEmail() . $user->getPassword());
        if ($generatedHash !== $hash) {
            $this->createNotFoundException();
        }

        // login user and redirect him to edit form
        $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
        $this->container->get('security.context')->setToken($token);

        $form = $this->createForm(new SimpleChangePasswordType());

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            $data = $form->getData();

            if ($form->isValid()) {
                // change password
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $em = $this->getDoctrine()->getManager();
                $password = $encoder->encodePassword($data['new_password'], $user->getSalt());
                $user->setPassword($password);
                $em->persist($user);
                $em->flush();

                return $this->render('SiteUserBundle:User:change_password_success.html.twig');
            }
        }

        return $this->render('SiteUserBundle:User:change_password.html.twig', array(
            'form'  => $form->createView(),
            'email' => $email,
            'hash'  => $hash,
        ));
    }

    public function infoAction()
    {
        $entity = null;
        $auth = $this->get('security.context')->getToken()->getUser();
        if ($auth instanceof User) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository("CoreUserBundle:User")->find($auth->getId());
        }
        $minishop  = $this->container->getParameter('minishop');

        return $this->render('SiteUserBundle:User:info.html.twig', array(
            'entity' => $entity,
            'minishop' => $minishop
        ));
    }

    public function setLocaleAction()
    {
        $request = $this->getRequest();

        return $this->redirect($this->generateUrl('category_homepage'));
    }

    public function culturesAction()
    {
        $cultures = $this->container->getParameter('core.cultures');

        return $this->render('SiteUserBundle:User:cultures.html.twig', array(
            'cultures' => $cultures
        ));
    }
}
