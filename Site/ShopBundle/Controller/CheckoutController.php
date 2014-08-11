<?php
namespace Site\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Site\ShopBundle\Entity\CouponItem;
use Core\ShopBundle\Entity\Address;
use Core\ShopBundle\Entity\Cart;
use Core\ShopBundle\Entity\Order;
use Core\ShopBundle\Entity\OrderItem;
use Core\UserBundle\Entity\User;
use Site\ShopBundle\Form\CartBaseAddressType;
use Site\ShopBundle\Form\CartUserAddressType;
use Site\ShopBundle\Form\CartPaymentShippingType;
use Site\ShopBundle\Form\CartOrderType;

/**
 * Description of CheckoutSiteController
 *
 * @author Birko
 */
class CheckoutController extends ShopController
{
    public function indexAction()
    {
        $this->testCart();
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->render('SiteShopBundle:Checkout:index.html.twig');
        } else {
            return $this->redirect($this->generateUrl('checkout_user'));
        }

    }

    public function userAction()
    {
        return $this->render('SiteShopBundle:Checkout:user.html.twig');
    }

    public function userFormAction()
    {
        $this->testCart();
        $cart = $this->getCart();
        $user = $this->getShopUser();
        if ($user === null) {
           throw $this->createNotFoundException('Unable to find User entity.');
        }
        $em = $this->getDoctrine()->getManager();
        $addresses = $em->getRepository('CoreShopBundle:Address')->getUserAddressQueryBuilder($user->getId())
            ->getQuery()
            ->getResult();
        if (!empty($addresses)) {
            $cart->setPaymentAddress(current($addresses));
            $cart->setShippingAddress(current($addresses));
        } else {
            throw $this->createNotFoundException('Unable to find User Address entity.');
        }

        $form = $this->createForm(new CartUserAddressType($user->getId(), $cart->isSameAddress()), $cart);

        return $this->render('SiteShopBundle:Checkout:userform.html.twig', array(
            'form'   => $form->createView(),
            'user' => $user,
        ));
    }

    public function userAddressAction()
    {
        $this->testCart();
        $cart = $this->getCart();
        $request = $this->getRequest();
        $user = $this->getShopUser();
        if ($user === null) {
           throw $this->createNotFoundException('Unable to find User entity.');
        }
        $em = $this->getDoctrine()->getManager();
        $addresses = $em->getRepository('CoreShopBundle:Address')->getUserAddressQueryBuilder($user->getId())
            ->getQuery()
            ->getResult();
        if (!empty($addresses)) {
            $cart->setPaymentAddress(current($addresses));
            $cart->setShippingAddress(current($addresses));
        } else {
            throw $this->createNotFoundException('Unable to find User Address entity.');
        }
        $form = $this->createForm(new CartUserAddressType($user->getId(), $cart->isSameAddress()), $cart);
        $form->bind($request);
        if ($cart->isSameAddress()) {
            $post = $request->get($form->getName());
            $post['shippingAddress'] = $post['paymentAddress'];
            $request->request->set($form->getName(), $post);
        }
        $form = $this->createForm(new CartUserAddressType($user->getId(), $cart->isSameAddress()), $cart);
        $form->bind($request);
        if ($form->isValid()) {
            $this->setCart($cart);

            return $this->redirect($this->generateUrl('checkout_paymentshipping'));
        }

        return $this->render('SiteShopBundle:Checkout:user.html.twig', array(
            'form'   => $form->createView(),
            'user' => $user,
        ));
    }

    public function guestAction()
    {
        $this->testCart();
        $cart = $this->getCart();
        $addressRequiredConfiguration = $this->container->getParameter("address.required");
        $form = $this->createForm(new CartBaseAddressType($cart->isSameAddress()), $cart, array('address' => array('required' => $addressRequiredConfiguration)));

        return $this->render('SiteShopBundle:Checkout:guestform.html.twig', array(
            'form'   => $form->createView(),
        ));
    }

    public function guestAddressAction()
    {
        $this->testCart();
        $cart = $this->getCart();
        $request = $this->getRequest();
        $addressRequiredConfiguration = $this->container->getParameter("address.required");
        $form = $this->createForm(new CartBaseAddressType($cart->isSameAddress()), $cart, array('address' => array('required' => $addressRequiredConfiguration)));
        $form->bind($request);
        $form->isValid();
        if ($cart->isSameAddress()) {
            $post = $request->get($form->getName());
            $post['shippingAddress'] = $post['paymentAddress'];
            unset($post['shippingAddress']['TIN']);
            unset($post['shippingAddress']['OIN']);
            unset($post['shippingAddress']['VATIN']);
            $request->request->set($form->getName(), $post);
        }
        $form2 = $this->createForm(new CartBaseAddressType($cart->isSameAddress()), $cart);
        $form2->bind($request);
        if ($form2->isValid()) {
            $this->setCart($cart);

            return $this->redirect($this->generateUrl('checkout_paymentshipping'));
        }

        return $this->render('SiteShopBundle:Checkout:guest.html.twig', array(
            'form'   => $form2->createView(),
        ));
    }

    public function paymentShippingAction()
    {
        $this->testCart();
        $cart = $this->getCart();
        $pricegroup = $this->getPriceGroup();
        $currency = $this->getCurrency();
        if ($cart->isSkipPayment() && $cart->isSkipShipping()) {
            return $this->redirect($this->generateUrl('checkout_confirm'));
        } else {
            $state = $cart->getPaymentAddress()->getState();
            $em = $this->getDoctrine()->getManager();
            $payments =  $em->getRepository('CoreShopBundle:Payment')->getPaymentQueryBuilder(true)
                ->getQuery()->getResult();
            if (!empty($payments) && !$cart->isSkipPayment()) {
                $cart->setPayment($payments[0]);
            }
            $shippings =  $em->getRepository('CoreShopBundle:Shipping')->getShippingQueryBuilder($state->getId(), true)
                ->getQuery()->getResult();
            if (!empty($shippings) && !$cart->isSkipShipping()) {
                $cart->setShipping($shippings[0]);
            }
            $form = $this->createForm(new CartPaymentShippingType(), $cart, array(
                'state' => $state->getId(),
                'payment' => !$cart->isSkipPayment(),
                'shipping' => !$cart->isSkipShipping(),
            ));

            return $this->render('SiteShopBundle:Checkout:paymentshipping.html.twig', array(
                'form'   => $form->createView(),
                'payment' => $payments,
                'shipping' => $shippings,
                'cart' => $cart,
                'currency' => $currency,
                'priceGroup' => $pricegroup,
            ));
        }
    }

    public function savePaymentShippingAction()
    {
        $this->testCart();
        $cart = $this->getCart();
        $pricegroup = $this->getPriceGroup();
        $currency = $this->getCurrency();
        $state = $cart->getShippingAddress()->getState();
        $form = $this->createForm(new CartPaymentShippingType(), $cart, array(
            'state' => $state->getId(),
            'payment' => !$cart->isSkipPayment(),
            'shipping' => !$cart->isSkipShipping(),
        ));
        $request = $this->getRequest();
        $form->bind($request);
        if ($form->isValid()) {
            $this->setCart($cart);

            return $this->redirect($this->generateUrl('checkout_confirm'));
        }
        $em = $this->getDoctrine()->getManager();
        $payments =  $em->getRepository('CoreShopBundle:Payment')->getPaymentQueryBuilder(true)
            ->getQuery()->getResult();
        $shippings =  $em->getRepository('CoreShopBundle:Shipping')->getShippingQueryBuilder($state->getId(), true)
            ->getQuery()->getResult();

        return $this->render('SiteShopBundle:Checkout:paymentshipping.html.twig', array(
            'form'   => $form->createView(),
            'cart' => $cart,
            'payment' => $payments,
            'shipping' => $shippings,
            'currency'=> $currency,
            'pricegroup'=> $pricegroup,
        ));

    }

    public function confirmAction()
    {
        $this->testCart();
        $cart = $this->getCart();
        $pricegroup = $this->getPriceGroup();
        $currency = $this->getCurrency();
        $form = $this->createForm(new CartOrderType(), $cart);
        if ($cart->isSameAddress()) {
            $cart->setShippingAddress($cart->getPaymentAddress());
        }

        return $this->render('SiteShopBundle:Checkout:confirm.html.twig', array(
            'cart'   => $cart,
            'form' => $form->createView(),
            'currency'=> $currency,
            'pricegroup'=> $pricegroup,
        ));
    }

    public function orderAction()
    {
        $this->testCart();
        $cart = $this->getCart();
        $pricegroup = $this->getPriceGroup();
        $currency = $this->getCurrency();
        $form = $this->createForm(new CartOrderType(), $cart);
        $form->bind($this->getRequest());
        $form->isValid();
        if (!$cart->getPaymentAddress()){
            return $this->redirect($this->generateUrl('cart'));
        }
        if ($cart->isSameAddress()) {
            $cart->setShippingAddress($cart->getPaymentAddress());
        }
        $user = $this->getShopUser();
        $sendEmail = null;
        if (!empty($user)) {
            $sendEmail = $user->getEmail();
        }
        // check if adress has an email fallback user email
        $pemail = ($cart->getShippingAddress()) ? $cart->getShippingAddress()->getEmail() : null;
        if (empty($pemail) && !empty($sendEmail)) {
            $cart->getShippingAddress()->setEmail($sendEmail);
        }
        if (!$cart->isSameAddress()) {
            $semail = $cart->getPaymentAddress()->getEmail();
            if (empty($semail) && !empty($sendEmail)) {
                $cart->getPaymentAddress()->setEmail($sendEmail);
            }
        }
        $em = $this->getDoctrine()->getManager();
        $order = new Order();
        $order->setInvoiceAddress($cart->getPaymentAddress());
        if ($cart->isSameAddress()) {
            $order->setDeliveryAddress($cart->getPaymentAddress());
        } else {
            $order->setDeliveryAddress($cart->getShippingAddress());
        }
        $order->setPrice($cart->getPrice());
        $order->setPriceVAT($cart->getPriceVAT());
        $order->setCurrency($currency);
        $order->setPriceGroup($pricegroup);
        $order->setComment($cart->getComment());
        if (!empty($user)) {
           $order->setUser($user);
        } elseif ($this->container->getParameter('site.shop.register_guest')) {
            $newUser = $em->getRepository('CoreUserBundle:User')->findOneByEmail($order->getInvoiceAddress()->getEmail());
            if (empty($newUser)) {
                $newUser = new User();
                //user settup
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($newUser);
                $newUser->setEnabled(true);
                $newUser->setLogin($order->getInvoiceAddress()->getEmail());
                $newUser->setEmail($order->getInvoiceAddress()->getEmail());
                $hash = md5($newUser->getLogin());
                $password = substr($hash,1,5);
                $passwordhash = $encoder->encodePassword($password, $newUser->getSalt());
                $newUser->setPassword($passwordhash);
                $addresses = array();
                $addresses[] = $order->getInvoiceAddress();
                if (!$cart->isSameAddress()) {
                    $addresses[] = $order->getDeliveryAddress();
                }
                $newUser = $em->getRepository('CoreUserBundle:User')->createUser($newUser);
                $em->getRepository('CoreShopBundle:Address')->createUser($newUser, $addresses);
                if ($newUser) {
                    $t = $this->get('translator')->trans('New registration - %subject%', array(
                        '%subject%' => $this->container->getParameter('site_title'),
                    ));
                    $emails = $this->container->getParameter('default.emails');
                    $message = \Swift_Message::newInstance()
                            ->setSubject($t)
                            ->setFrom($emails['default'], $this->container->getParameter('site_title'))
                            ->setTo(array($newUser->getEmail()))
                            ->setBody($this->renderView('SiteUserBundle:Email:new.html.twig', array(
                                'login' => $newUser->getLogin(),
                                'password' => $password,
                            )), 'text/html');
                    $this->get('swiftmailer.mailer.site_mailer')->send($message);
                }
            }
            $order->setUser($newUser);
        }

        //items
        foreach ($cart->getItems() as $item) {
            $orderItem = new OrderItem();
            $orderItem->setAmount($item->getAmount());
            $orderItem->setPrice($item->getPrice());
            $orderItem->setPriceVAT($item->getPriceVAT());
            $orderItem->setName($item->getName());
            $orderItem->setDescription($item->getDescription());
            $orderItem->setCurrency($currency);
            $orderItem->setPriceGroup($pricegroup);
            if ($item->getProductId() != null) {
                $productEntity = $em->getRepository('CoreProductBundle:Product')->find($item->getProductId());
                if ($productEntity !== null) {
                    $orderItem->setProduct($productEntity);
                    if($productEntity->getStock() && $productEntity->getStock()->getAmount() > 0) {
                        $productEntity->getStock()->setAmount($productEntity->getStock()->getAmount() - $item->getAmount());
                        $em->persist($productEntity->getStock());
                    }
                }
                // stock options
                $options = $item->getOptions();
                if (!empty($options)) {
                    $options = array();
                    foreach ($item->getOptions() as $option) {
                        if ($option) {
                            $options[] = "{$option->getName()->getName()}: {$option->getValue()->getValue()}";
                            $optionEntity = $em->getRepository('CoreProductBundle:ProductOption')->find($option->getId());
                            if ($optionEntity && $optionEntity->getAmount() > 0) {
                                $optionEntity->setAmount($optionEntity->getAmount() - $item->getAmount());
                                $em->persist($optionEntity);
                            }
                        }
                    }
                    $orderItem->setOptions(implode(', ', $options));
                }
            }
            if ($item instanceof CouponItem) {
                $couponEntity = $em->getRepository("CoreShopBundle:Coupon")->findOneByCode($item->getCode());
                if ($couponEntity) {
                    $couponEntity->setUsed(true);
                    $couponEntity->setActive(false);
                    $em->persist($couponEntity);
                }
            }
            $orderItem->setOrder($order);
            $order->addItem($orderItem);
        }
        //shipping
        if ($cart->getShipping() != null) {
            $orderItem = new OrderItem();
            $orderItem->setAmount(1);
            $orderItem->setCurrency($currency);
            $orderItem->setPriceGroup($pricegroup);
            $orderItem->setPrice($cart->getShipping()->calculatePrice($currency));
            $orderItem->setPriceVAT($cart->getShipping()->calculatePriceVAT($currency));
            $orderItem->setName($cart->getShipping()->getName());
            $orderItem->setDescription($cart->getShipping()->getDescription());
            $orderItem->setShipping($cart->getShipping());
            $order->setShipping($cart->getShipping());
            $orderItem->setOrder($order);
            $order->addItem($orderItem);
        }
        //payment
        if ($cart->getPayment() != null) {
            $orderItem = new OrderItem();
            $orderItem->setAmount(1);
            $orderItem->setCurrency($currency);
            $orderItem->setPriceGroup($pricegroup);
            $orderItem->setPrice($cart->getPayment()->calculatePrice($currency));
            $orderItem->setPriceVAT($cart->getPayment()->calculatePriceVAT($currency));
            $orderItem->setName($cart->getPayment()->getName());
            $orderItem->setPayment($cart->getPayment());
            $orderItem->setDescription($cart->getPayment()->getDescription());
            $order->setPayment($cart->getPayment());
            $orderItem->setOrder($order);
            $order->addItem($orderItem);
        }
        $order->setOptions(array(
            '_locale' => $this->getRequest()->get("_locale"),
        ));
        $em->persist($order);
        $em->flush();
        // Ordder has  "HasLifecycleCallbacks" to create order_number after insert
        $em->persist($order);
        $em->flush();

        $sendEmail= $cart->getPaymentAddress()->getEmail();
        //emails
        // TODO: Send emails
        $emails = $this->container->getParameter('default.emails');
        $ordernumber = $order->getOrderNumber();
        $title = (!empty($ordernumber)) ? $ordernumber : $order->getId();
        $t = $this->get('translator')->trans('%date% - New order No.: %title%', array(
            '%date%' => $order->getCreatedAt()->format('Y.m.d'),
            '%title%' => $title,
        ));
        $message = \Swift_Message::newInstance()
            ->setSubject($t)
            ->setFrom($emails['default'], $this->container->getParameter('site_title'))   //settings
            ->setTo(array($emails['order'], $sendEmail))
            ->setBody($this->renderView('SiteShopBundle:Email:order.html.twig', array(
                'order' => $order,
            )),"text/html")
            ->setContentType("text/html");
        $this->get('swiftmailer.mailer.site_mailer')->send($message);
        $cart->clearItems();
        $this->setCart($cart);
        // TODO: payment methods
        return $this->render('SiteShopBundle:Checkout:order.html.twig', array('order' => $order));
    }
}
