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

        return $this->render('SiteShopBundle:Cart:index.html.twig', array(
            'cart' => $cart,
            'form' => (!$cart->isEmpty()) ? $form->createView() : null,
            'user' => $user,
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

        return $this->render('SiteShopBundle:Cart:index.html.twig', array(
            'form'   => $form2->createView(),
            'cart' => $cart,
        ));
    }

    public function addAction($product, $price, $amount = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $pricegroup = $this->getPriceGroup();
        if (!($product instanceof Product)) {
            $product = $em->getRepository('CoreProductBundle:Product')->find($product);
        }
        if (!$price) {
            $price = $product->getMinimalPrice($pricegroup);
        }

        $entity = new CartItem();
        $entity->setAmount($amount);
        $entity->setName($product->getTitle());
        $entity->setProductId($product->getId());
        $entity->setPrice($price->getPrice());
        $entity->setPriceVAT($price->getPriceVAT());
        $options = $em->getRepository('CoreProductBundle:ProductOption')->getOptionsNamesByProduct($product->getId());
        $form = $this->createForm(new CartItemAddType(), $entity, array(
            'product'=>$product->getId(),
            'options' => $options,
            'requireOptions' => $this->container->getParameter('site.shop.require_options'),
        ));

        return $this->render('SiteShopBundle:Cart:add.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'options' => $options,
        ));
    }

    public function addItemAction($product)
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $pricegroup = $this->getPriceGroup();
        if (!($product instanceof Product)) {
            $product = $em->getRepository('CoreProductBundle:Product')->find($product);
        }
        $price = $product->getMinimalPrice($pricegroup);
        $entity = new CartItem();
        $entity->setAmount(1);
        $entity->setName($product->getTitle());
        $entity->setProductId($product->getId());
        $entity->setPrice($price->getPrice());
        $entity->setPriceVAT($price->getPriceVAT());
        $options = $em->getRepository('CoreProductBundle:ProductOption')->getOptionsNamesByProduct($product->getId());
        $form = $this->createForm(new CartItemAddType(), $entity, array(
            'product'=>$product->getId(),
            'options' => $options,
            'requireOptions' => $this->container->getParameter('site.shop.require_options'),
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

        return $this->render('SiteShopBundle:Cart:info.html.twig', array(
            'cart' => $cart,
        ));
    }
}
