<?php

namespace Site\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Site\ShopBundle\Entity\Cart;
use Site\ShopBundle\Form\CouponType;
use Site\ShopBundle\Entity\CouponItem;
/**
 * Address controller.
 *
 */
class CouponController extends ShopController
{

    public function addAction()
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST') {
            $entity = (object) array('code'=>'');
            $form = $this->createForm(new CouponType(), $entity);
            $form->bind($request);
            $form->isValid();
            $cart = $this->getCart();
            $coupon  = $em->getRepository("CoreShopBundle:Coupon")->findOneByCode($entity->code);

            if ($coupon) { //in system
                if ($coupon->isActive()) {
                    $count = $coupon->getProducts()->count();
                    if ($count > 0) { //product cupon
                        foreach ($coupon->getProducts() as $product) {
                            $item = new CouponItem();
                            $item->setCode($coupon->getCode());
                            $item->setName($product->getTitle(). "({$coupon->getCode()})");
                            $item->setProductId($product->getId());
                            $item->setChangeAmount(true);
                            $item->setAmount(1);
                            $item->setChangeAmount(false);
                            if ($coupon->getPrice()) { // price coupon
                                $item->setPrice($coupon->getPrice() / $count);
                                $item->setPriceVAT($coupon->getPriceVAT() / $count);
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
                        }
                    } else { //discount coupon
                        $item = new CouponItem();
                        $item->setCode($coupon->getCode());
                        $item->setName('Coupon: ' . "({$coupon->getCode()})");
                        $item->setChangeAmount(true);
                        $item->setAmount(1);
                        $item->setChangeAmount(false);
                        if ($coupon->getPrice()) { // price coupon
                            $item->setPrice($coupon->getPrice() * (-1));
                            $item->setPriceVAT($coupon->getPriceVAT() * (-1));
                        } else {  //percentage coupon
                            $item->setPrice($coupon->getDiscount() * $cart->getPrice() * (-1));
                            $item->setPriceVAT($coupon->getDiscount() * $cart->getPriceVAT() * (-1));
                        }
                        $item->setPrice($item->calculatePrice($cart->getCurrency()));
                        $item->setPriceVAT($item->calculatePriceVAT($cart->getCurrency()));
                        $cart->addItem($item);
                    }
                }
            } else {
                $item = new CouponItem();
                $item->setCode($entity->code);
                $item->setName('Coupon: ' . "({$entity->code})");
                $item->setChangeAmount(true);
                $item->setAmount(1);
                $item->setChangeAmount(false);
                $item->setPrice(0);
                $item->setPriceVAT(0);
                $cart->addItem($item);
            }
            $this->setCart($cart);
        }

        return $this->redirect($this->generateUrl('cart'));
    }

    public function formAction()
    {
        $form = $this->createForm(new CouponType());

        return $this->render('SiteShopBundle:Coupon:form.html.twig', array(
            'form'   => $form->createView(),
        ));
    }
}
