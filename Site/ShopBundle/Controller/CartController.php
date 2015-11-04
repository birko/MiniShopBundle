<?php

namespace Site\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Core\ProductBundle\Entity\Product;
use Site\ShopBundle\Entity\CartItem;
use Site\ShopBundle\Entity\Cart;
use Site\ShopBundle\Form\CartType;
use Site\ShopBundle\Form\CartItemAddType;
use Site\ShopBundle\Form\CartBaseType;
use Site\ShopBundle\Form\CartUserType;

/**
 * Cart controller.
 *
 */
class CartController extends ShopController
{
    public function indexAction()
    {
        $cart = $this->getCart();
        $this->testCart();
        $user = $this->getShopUser();
        $form = $this->createForm(new CartType(), $cart);
        $pricegroup = $this->getPriceGroup();
        $currency = $this->getCurrency();
        
        $request = $this->getRequest();
        $session = $request->getSession();
        $added = $session->get('added-entities', array());
        $session->set('added-entities', null);
        $session->remove('added-entities');

        return $this->render('SiteShopBundle:Cart:index.html.twig', array(
            'cart' => $cart,
            'form' => (!$cart->isEmpty()) ? $form->createView() : null,
            'user' => $user,
            'pricegroup' => $priceGroup,
            'currency' => $currency,
            'addedItems' => $added,
        ));
    }

    public function checkAction()
    {
        $this->testCart();
        $cart = $this->getCart();
        $user = $this->getShopUser();
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $form = $this->createForm(new CartBaseType(true), $cart);
        } else {
            if ($user === null) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }
            if ($user->getAddresses()->count() > 0) {
                $cart->setPaymentAddress($user->getAddresses()->first());
                $cart->setShippingAddress($user->getAddresses()->first());
            }
            $form = $this->createForm(new CartUserType($user->getId(), true), $cart);
        }
        $request = $this->getRequest();
        $form->bind($request);
        $form->isValid();
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $form2 = $this->createForm(new CartBaseType($cart->isSameAddress()), $cart);
        } else {
            $form2 = $this->createForm(new CartUserType($user->getId(), $cart->isSameAddress()), $cart);
        }
        if ($cart->isSameAddress()) {
            $post = $request->get($form2->getName());
            $post['shippingAddress'] = $post['paymentAddress'];
            if (!$user) {
                unset($post['shippingAddress']['TIN']);
                unset($post['shippingAddress']['OIN']);
                unset($post['shippingAddress']['VATIN']);
            }
            $request->request->set($form2->getName(), $post);
        }
        $form2->bind($request);
        if ($form2->isValid()) {
            $this->setCart($cart);

            return $this->redirect($this->generateUrl('checkout_order'));
        }

        $pricegroup = $this->getPriceGroup();
        $currency = $this->getCurrency();

        return $this->render('SiteShopBundle:Cart:index.html.twig', array(
            'form'   => $form2->createView(),
            'cart' => $cart,
            'pricegroup' => $priceGroup,
            'currency' => $currency,
        ));
    }

    public function addAction($product, $price, $amount = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $pricegroup = $this->getPriceGroup();
        $currency = $this->getCurrency();
        if (!($product instanceof Product)) {
            $product = $em->getRepository('CoreProductBundle:Product')->find($product);
        }
        if (!$price) {
            $price = $product->getMinimalPrice($currency, $pricegroup);
        }

        $entity = new CartItem();
        $entity->setAmount($amount);
        $entity->setName($product->getTitle());
        $entity->setProductId($product->getId());
        if ($price) {
            $entity->setPrice($price->getPrice());
            $entity->setPriceVAT($price->getPriceVAT());
        }
        $variations = $em->getRepository('CoreProductBundle:ProductVariation')->getProductVariationsCount($product->getId());
        $options = ($variations  == 0) ? $em->getRepository('CoreProductBundle:ProductOption')->getOptionsNamesByProduct($product->getId()) : array();
        $form = $this->createForm(new CartItemAddType(), $entity, array(
            'product'=>$product->getId(),
            'options' => $options,
            'requireOptions' => $this->container->getParameter('site.shop.require_options'),
            'variations' => $variations,
            'requireVariations' => $this->container->getParameter('site.shop.require_options'),
        ));

        return $this->render('SiteShopBundle:Cart:add.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'options' => $options,
            'variations' => $variations,
            'type' => ($price) ? $price->getType() : null,
        ));
    }

    public function addItemAction($product, $type = null)
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $pricegroup = $this->getPriceGroup();
        $currency = $this->getCurrency();
        if (!($product instanceof Product)) {
            $product = $em->getRepository('CoreProductBundle:Product')->find($product);
        }
        $price = $product->getMinimalPrice($currency, $pricegroup, $type);
        $entity = new CartItem();
        $entity->setAmount(1);
        $entity->setName($product->getTitle());
        $entity->setProductId($product->getId());
        if ($price) {
            $entity->setPrice($price->getPrice());
            $entity->setPriceVAT($price->getPriceVAT());
        }
        $variations = $em->getRepository('CoreProductBundle:ProductVariation')->getProductVariationsCount($product->getId());
        $options = ($variations  == 0) ? $em->getRepository('CoreProductBundle:ProductOption')->getOptionsNamesByProduct($product->getId()) : array();
        $form = $this->createForm(new CartItemAddType(), $entity, array(
            'product'=>$product->getId(),
            'options' => $options,
            'requireOptions' => $this->container->getParameter('site.shop.require_options'),
            'variations' => $variations,
            'requireVariations' => $this->container->getParameter('site.shop.require_options'),
        ));
        if ($request->getMethod() == 'POST') {
            $cart = $this->getCart();
            if (!$request->get('cart-add-small')) {
                $form->bind($request);
                if ($form->isValid()) {
                    $entity->setOptions($entity->getOptions()->toArray());
                }
            }
            $cart->addItem($entity);
            $this->setCart($cart);
            $session = $request->getSession();
            $session->set('added-entities', array($entity));
        }

        return $this->redirect($this->generateUrl('cart'));
    }

    public function removeItemAction($index)
    {
        $cart = $this->getCart();
        $cart->removeItem($index);
        $this->setCart($cart);

        return $this->redirect($this->generateUrl('cart'));
    }

    public function updateItemAction()
    {
        $request = $this->getRequest();
        $cart = $this->getCart();
        $form = $this->createForm(new CartType(), $cart);
        $em = $this->getDoctrine()->getManager();
        $pricegroup = $this->getPriceGroup();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                foreach ($cart->getItems() as $entity) {
                    if ($entity->getProductId() && $entity->isChangeAmount()) {
                    }
                }
                $this->setCart($cart);
            }
        }

        return $this->redirect($this->generateUrl('cart'));
    }

    public function clearAction()
    {
        $cart = $this->getCart();
        $cart->clearItems();
        $this->setCart($cart);

        return $this->redirect($this->generateUrl('cart'));
    }

    public function infoAction()
    {
        $cart = $this->getCart();

        $pricegroup = $this->getPriceGroup();
        $currency = $this->getCurrency();

        return $this->render('SiteShopBundle:Cart:info.html.twig', array(
            'cart' => $cart,
            'pricegroup' => $priceGroup,
            'currency' => $currency,
        ));
    }
}
