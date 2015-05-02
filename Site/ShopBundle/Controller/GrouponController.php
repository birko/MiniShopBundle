<?php

namespace Site\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Site\ShopBundle\Entity\Cart;
use Site\ShopBundle\Form\CouponType;
use Site\ShopBundle\Entity\GrouponItem;

class GrouponController extends ShopController
{

    public function addAction($product)
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST') {
            $entity = (object) array('code'=>'');
            $form = $this->createForm(new CouponType(), $entity);
            $form->bind($request);
            $form->isValid();
            $cart = $this->getCart();
            $entities = $em->getRepository('CoreShopMarketingBundle:Groupon')->getProductGrouponsQueryBuilder($product, true)->getQuery()->getResult();
            if ($entities) {
                $coupon = current($entities);
                $product = $coupon->getProduct();
                $item = new GrouponItem();
                $item->setCode($entity->code);
                $item->setName($product->getTitle() . "({$entity->code})");
                $item->setProductId($product->getId());
                $item->setChangeAmount(true);
                $item->setAmount($coupon->getAmount());
                $item->setChangeAmount(false);
                if ($coupon->getPrice()) { // price coupon
                    $item->setPrice($coupon->getPrice());
                    $item->setPriceVAT($coupon->getPriceVAT());
                } else {  //percentage coupon
                    $price = $product->getPrices()->first();
                    if ($price) {
                        $item->setPrice($price->getPrice() *  (1 - $coupon->getDiscount()));
                        $item->setPriceVAT($price->getPriceVAT() * (1 - $coupon->getDiscount()));
                    } else {
                        $item->setPrice(0);
                        $item->setPriceVAT(0);
                    }
                }
                $item->setPrice($item->calculatePrice($cart->getCurrency()));
                $item->setPriceVAT($item->calculatePriceVAT($cart->getCurrency()));
                $cart->addItem($item);
                if ($coupon->getPayment()) {
                    $cart->setPayment($coupon->getPayment());
                    $cart->setSkipPayment(true);
                }
                if ($coupon->getShipping()) {
                    $cart->setShipping($coupon->getShipping());
                    $cart->setSkipShipping(true);
                }
                $this->setCart($cart);
            }
        }

        return $this->redirect($this->generateUrl('cart'));
    }

    public function formAction($product)
    {
        $form = $this->createForm(new CouponType());
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('CoreShopMarketingBundle:Groupon')->getProductGrouponsQueryBuilder($product, true)->getQuery()->getResult();

        return $this->render('SiteShopBundle:Groupon:form.html.twig', array(
            'form'   => $form->createView(),
            'entities' => $entities,
            'product' => $product,
        ));
    }
}
