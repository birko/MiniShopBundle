<?php

namespace Site\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of CartItem
 *
 * @author Birko
 */
class CartItem implements \Serializable
{

    protected $amount = 0;
    protected $productID = null;
    protected $options = null;
    protected $variations = null;
    protected $price = 0;
    protected $priceVAT = 0;
    protected $name;
    protected $description;
    protected $changeAmount = true;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function toArray()
    {
        return array(
            $this->amount,
            $this->productID,
            $this->price,
            $this->priceVAT,
            $this->name,
            $this->description,
            $this->getOptions()->toArray(),
            $this->variations,
            $this->changeAmount,
        );
    }

    public function fromArray($array)
    {
        $this->amount = $array[0];
        $this->productID = $array[1];
        $this->price = $array[2];
        $this->priceVAT = $array[3];
        $this->name = $array[4];
        $this->description = $array[5];
        $this->setOptions($array[6]);
        $this->variations = $array[7];
        $this->changeAmount = $array[8];
    }

    public function serialize()
    {
        return serialize($this->toArray());
    }

    public function unserialize($serialized)
    {
        $array = unserialize($serialized);
        $this->fromArray($array);
    }

    public function isChangeAmount()
    {
        return $this->changeAmount;
    }

    public function setChangeAmount($changeAmount)
    {
        $this->changeAmount = $changeAmount;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amout)
    {
        $this->amount = $amout;
    }

    public function addAmount($amount)
    {
        $this->setAmount($this->getAmount() + $amount);
    }

    public function setProductId($productid)
    {
        $this->productID = $productid;
    }
    public function getProductId()
    {
        return $this->productID;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPriceVAT($pricevat)
    {
        $this->priceVAT = $pricevat;
    }

    public function getPriceVAT()
    {
        return $this->priceVAT;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDescription($desciption)
    {
        $this->description = $desciption;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options = array())
    {
        if (!$this->options) {
            $this->options = new ArrayCollection();
        }
        if (!empty($options)) {
            $this->getOptions()->clear();
            $this->addOptions($options);
        }
    }

    public function addOption($option)
    {
        $this->getOptions()->add($option);
    }

    public function addOptions($options = array())
    {
        if (!empty($options)) {
            foreach ($options as $option) {
                $this->addOption($option);
            }
        }
    }

    public function getVariations()
    {
        return $this->variations;
    }

    public function setVariations($variations = array())
    {
        $this->variations = $variations;
    }

    public function addVariation($variation)
    {
        $this->variations[$variation->getId()] = $variation;
    }

    public function addVariations($variations = array())
    {
        if (!empty($variations)) {
            foreach ($variations as $variation) {
                $this->addVariation($variation);
            }
        }
    }

    public function compareData($entity)
    {
        if ($entity->getProductId() !== $this->getProductId()) {
            return false;
        }

        if ($this->getOptions()->count() == $entity->getOptions()->count()) {
            if ($this->getOptions()->count() > 0) {
                foreach ($entity->getOptions() as $key => $option) {
                    $opts = $this->getOptions();
                    if ( ($opts[$key] && $option && !$opts[$key]->equals($option)) || ($opts[$key] xor $option)) {
                        return false;
                    }
                }
            }
        } else {
            return false;
        }
        if (($this->getVariations() && $entity->getVariations() && !$this->getVariations()->equals($entity->getVariations())) || ($entity->getVariations() xor $this->getVariations())) {
            return false;
        }

        return true;
    }

    public function getPriceTotal()
    {
        return $this->getAmount()* $this->getPrice();
    }

    public function getPriceVATTotal()
    {
        return $this->getAmount()* $this->getPriceVAT();
    }
}
