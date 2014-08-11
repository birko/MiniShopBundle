<?php

namespace Site\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Site\ShopBundle\Entity\Cart;
use Core\PriceBundle\Entity\Currency;
use Core\ShopBundle\Controller\BaseOrderController;

class ShopController extends BaseOrderController
{
    protected function getCart()
    {
        $session = $this->getRequest()->getSession();
        $cart = $session->get('cart', new Cart());
        if (empty($cart)) {
            $cart = new Cart();
        }
        $cart->setCurrency($this->getCurrency());
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($cart->getPaymentAddress() != null) {
            if ($cart->getPaymentAddress()->getId() != null) {
                $cart->setPaymentAddress($em->merge($cart->getPaymentAddress()));
            }

            if ($cart->getPaymentAddress()->getState() && $cart->getPaymentAddress()->getState()->getId()!= null) {
                $cart->getPaymentAddress()->setState($em->merge($cart->getPaymentAddress()->getState()));
            }
        }
        if ($cart->getShippingAddress() != null) {
            if ($cart->getShippingAddress()->getId() != null) {
                $cart->setShippingAddress($em->merge($cart->getShippingAddress()));
            }

            if ($cart->getShippingAddress()->getState() && $cart->getShippingAddress()->getState()->getId()!= null) {
                $cart->getShippingAddress()->setState($em->merge($cart->getShippingAddress()->getState()));
            }
        }
        if ($cart->getPayment() != null) {
            $cart->setPayment($em->merge($cart->getPayment()));
        }
        if ($cart->getShipping() != null) {
            $cart->setShipping($em->merge($cart->getShipping()));
        }

        return $cart;

    }

    protected function setCart(Cart $cart)
    {
        $session = $this->getRequest()->getSession();
        $session->set('cart', $cart);
    }

    protected function testCart()
    {
        $cart = $this->getCart();
        if ($cart->isEmpty()) {
            return $this->redirect($this->generateUrl('cart'));
        }
    }

    protected function getShopUser()
    {
        $user = null;
        if ($this->get('security.context')->getToken()) {
            $auth = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
                $user = $em->getRepository('CoreUserBundle:User')->find($auth->getId());
            }
        }

        return $user;
    }

    protected function getPriceGroup()
    {
        $pricegroup = null;
        $user = $this->getShopUser();
        $em = $this->getDoctrine()->getManager();
        if ($user !== null) {
            $pricegroup  = $user->getPriceGroup();
        }
        if ($pricegroup === null) {
           $pricegroup  =  $em->getRepository('CoreUserBundle:PriceGroup')->findOneByDefault(true);
        } else {
            $pricegroup = $em->merge($pricegroup);
        }
        if ($pricegroup === null) {
            $pricegroup  =  $em->getRepository('CoreUserBundle:PriceGroup')->createQueryBuilder("pg")
            ->orderBy('pg.id')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
        }

        return $pricegroup;
    }
    
    protected function getCurrency()
    {
        $currency = null;
        $em = $this->getDoctrine()->getManager();
        //session
        $session = $this->getRequest()->getSession();
        $currency = $session->get('currency');
        //defaults
        if ($currency === null) {
           $currency = $em->getRepository('CorePriceBundle:Currency')->findOneByDefault(true);
        } else {
           $currency = $em->merge($currency);
        }
        if ($currency === null) {
            $currency =  $em->getRepository('CorePriceBundle:Currency')->createQueryBuilder("c")
            ->orderBy('c.id')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
        }
        $this->setCurrency($currency);
        
        return $currency;
    }
    
    protected function setCurrency(Currency $currency)
    {
        $session = $this->getRequest()->getSession();
        $session->set('currency', $currency);
    }
}
