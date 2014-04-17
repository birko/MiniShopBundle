<?php
namespace Site\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
/**
 * Description of Cart
 *
 * @author Birko
 */
class Cart implements \Serializable
{
    protected $shippingAddress = null;
    protected $paymentAddress = null;
    protected $sameAddress = true;
    protected $items =  null;
    protected $payment = null;
    protected $shipping = null;
    protected $comment = null;
    protected $skipPayment = false;
    protected $skipShipping = false;

    public function __construct()
    {
        $this->shippingAddress = null;
        $this->paymentAddress = null;
        $this->sameAddress = true;
        $this->items = new ArrayCollection();
    }

    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress($address = null)
    {
        $this->shippingAddress = $address;
    }

    public function getPaymentAddress()
    {
        return $this->paymentAddress;
    }

    public function setPaymentAddress($address = null)
    {
        $this->paymentAddress = $address;
    }

    public function isSameAddress()
    {
        return $this->sameAddress;
    }

    public function setSameAddress($sameAddress)
    {
        $this->sameAddress = $sameAddress;
    }

    public function isSkipPayment()
    {
        return $this->skipPayment;
    }

    public function setSkipPayment($skipPayment)
    {
        $this->skipPayment = $skipPayment;
    }

    public function isSkipShipping()
    {
        return $this->skipShipping;
    }

    public function setSkipShipping($skipShipping)
    {
        $this->skipShipping = $skipShipping;
    }

    public function getItems($type = null)
    {
        if ($type !== null) {
            return $this->getItems()->filter(function ($entry) use ($type) {
                return $entry->getType() == $type;
            });
        } else {
            return $this->items;
        }
    }

    public function setItems($items = array())
    {
        $this->items = ($items instanceof ArrayCollection)? $items : new ArrayCollection($items);
    }

    public function addItem(CartItem $item, $index = null)
    {
        if ($item->getAmount() > 0) {
            $cartItem = $this->findItem($item);
            if ($cartItem && $cartItem->isChangeAmount()) {
                $cartItem->addAmount($item->getAmount());
                $cartItem->setPrice($item->getPrice());
                $cartItem->setPriceVAT($item->getPriceVAT());
            } else {
                $this->getItems()->add($item);
            }
        }
    }

    public function getItem($index)
    {
        $count = $this->getItems()->count();
        if (!$this->isEmpty() && $this->getItems()->containsKey($index)) {
            return  $this->getItems()->get($index);
        }

        return null;
    }

    public function getItemsCount()
    {
        $count = 0;
        if (!$this->isEmpty()) {
            $items = $this->getItems();
            foreach ($items as $item) {
                $count += $item->getAmount();
            }
        }

        return $count;

    }

    public function addItems($items = array(), $index = null)
    {
        if (!empty($items)) {
            foreach ($items as $item) {
                $this->addItem($item);
            }
        }
    }

    public function findItem(CartItem $itemData)
    {
        if (!$this->isEmpty()) {
            $items = $this->getItems();
            foreach ($items as $key => $item) {
                if ($item->compareData($itemData)) {
                    return $item;
                }
            }
        }

        return false;
    }

    public function isEmpty()
    {
        return $this->getItems()->isEmpty();
    }

    public function removeItem($index)
    {
        if (!$this->isEmpty()) {
            if ($this->getItems()->containsKey($index)) {
                $this->getItems()->remove($index);
            }
        }
    }

    public function getPayment()
    {
        return $this->payment;
    }

    public function setPayment($payment = null)
    {
        $this->payment = $payment;
    }

    public function getShipping()
    {
        return $this->shipping;
    }

    public function setShipping($shipping = null)
    {
        $this->shipping = $shipping;
    }

    public function getPrice()
    {
        $price = 0;
        if (!$this->isEmpty()) {
            foreach ($this->getItems() as $item) {
                $price += $item->getPriceTotal();
            }
        }
        if (!empty($this->payment)) {
            $price += $this->payment->getPrice();
        }
        if (!empty($this->shipping)) {
            $price += $this->shipping->getPrice();
        }

        return $price;
    }

    public function getPriceVAT()
    {
        $price = 0;
        if (!$this->isEmpty()) {
            foreach ($this->getItems() as $item) {
                $price += $item->getPriceVATTotal();
            }
        }
        if (!empty($this->payment)) {
            $price += $this->payment->getPriceVAT();
        }
        if (!empty($this->shipping)) {
            $price += $this->shipping->getPriceVAT();
        }

        return $price;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment = null)
    {
        $this->comment = $comment;
    }

    public function clearItems()
    {
        $this->setPaymentAddress();
        $this->setShippingAddress();
        $this->setPayment();
        $this->setShipping();
        $this->setComment();
        $this->setSameAddress(true);
        $this->setSkipPayment(false);
        $this->setSkipShipping(false);
        $this->setItems();
    }

    public function serialize()
    {
        return serialize(array(
            $this->shippingAddress,
            $this->paymentAddress,
            $this->sameAddress,
            $this->skipPayment,
            $this->skipShipping,
            $this->getItems()->toArray(),
            $this->payment,
            $this->shipping,
            $this->comment,
        ));
    }
    public function unserialize($serialized)
    {
        $items = array();
        list(
            $this->shippingAddress,
            $this->paymentAddress,
            $this->sameAddress,
            $this->skipPayment,
            $this->skipShipping,
            $items,
            $this->payment,
            $this->shipping,
            $this->comment,
        ) = unserialize($serialized);
        $this->setItems($items);
    }
}
