<?php

namespace Core\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Neonus\Nws\ProductStockBundle\Entity\StockVariation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\ProductBundle\Entity\ProductVariationRepository")
 */
class ProductVariation implements \Serializable
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var integer $variation
     *
     * @ORM\Column(name="variation", type="integer")
     */
    private $variation;

    /**
     * @var decimal $amount
     *
     * @ORM\Column(name="amount", type="decimal", nullable = true)
     */
    private $amount;


    /**
     * @ORM\ManyToOne(targetEntity="Core\ProductBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
     protected $product;

    /**
     * @ORM\ManyToMany(targetEntity="ProductOption")
     * @ORM\JoinTable(name="product_option_variation",
     *      joinColumns={@ORM\JoinColumn(name="variation_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="option_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */

    protected $options;

    public function __construct()
    {
        $this->options = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set variation
     *
     * @param integer $variation
     */
    public function setVariation($variation)
    {
        $this->variation = $variation;
    }

    /**
     * Get variation
     *
     * @return integer
     */
    public function getVariation()
    {
        return $this->variation;
    }

    /**
     * Set amount
     *
     * @param decimal $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Get amount
     *
     * @return decimal
     */
    public function getAmount()
    {
        return $this->amount;
    }
    

    /**
     * Set product
     *
     * @param Core\ProductBundle\Entity\Product product
     */
    public function setProduct(\Core\ProductBundle\Entity\Product $product)
    {
        $this->product = $product;
    }

    /**
     * Get product
     *
     * @return \Core\ProductBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Get options
     *
     * @return ArrayCollection
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions();
        }

        return $this->options;
    }

    /**
     * set options
     *
     * @param ArrayCollection $options
     */
    public function setOptions($options = array())
    {
        return $this->options = ($options instanceof ArrayCollection) ? $options : new ArrayCollection($options);
    }

    /**
     * Add option
     *
     * @param ProductOption option
     */

    public function addOptions(ProductOption $option)
    {
        return $this->getOptions()->add($option);
    }

    /**
     * Remove option
     *
     * @param ProductOption option
     */

    public function removeOptions(ProductOption $option)
    {
        return $this->getOptions()->removeElement($option);
    }

    public function __toString()
    {
        $string = $this->getVariation() . ": ";
        $string .= implode(", ", $this->getOptions()->toArray());

        return $string;
    }

    public function toArray()
    {
        $array[] = $this->id;
        $array[] = $this->variation;
        $array[] = $this->getOptions()->toArray();
        $array[] = $this->amount;

        return $array;
    }

    public function fromArray($array)
    {
        $this->id = $array[0];
        $this->variation = $array[1];
        $this->setOptions($array[2]);
        $this->amount = $array[3];
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

    public function equals($entity)
    {
        if ($this->getOptions()->count() != $entity->getOptions()->count()) {
            return false;
        } else {
            foreach ($this->getOptions() as $key => $option) {
                $eoption = $entity->getOptions()->filter(function ($entry) use ($option) {
                    return ($entry && $option && $entry->equals($option));
                })->first();
                if (!$eoption) {
                    return false;
                }
            }
        }

        return ($this->getId() == $entity->getId()
            && $this->getVariation() == $entity->getVariation()
            && $this->getAmount() == $entity->getAmount()
        );
    }
}
