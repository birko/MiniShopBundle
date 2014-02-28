<?php

namespace Core\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Core\AttributeBundle\Entity\AttributeName;
use Core\AttributeBundle\Entity\AttributeValue;

/**
 * Core\ProductBundle\Entity\ProductOption
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\ProductBundle\Entity\ProductOptionRepository")
 */
class ProductOption implements \Serializable
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
     * @ORM\ManyToOne(targetEntity="Core\AttributeBundle\Entity\AttributeName", cascade={"persist"})
     * @Gedmo\SortableGroup
     * @ORM\JoinColumn(name="attributename_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $name;
    /**
     * @ORM\ManyToOne(targetEntity="Core\AttributeBundle\Entity\AttributeValue", cascade={"persist"})
     * @ORM\JoinColumn(name="attributevalue_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $value;

     /**
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="Core\ProductBundle\Entity\Product", inversedBy="options")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $product;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;

    /**
     * @var decimal $amount
     *
     * @ORM\Column(name="amount", type="decimal", nullable = true)
     */
    private $amount;

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
     * Set name
     *
     * @param AttributeName $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return AttributeName
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param AttributeValue $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     *
     * @return AttributeValue
     */
    public function getValue()
    {
        return $this->value;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPosition()
    {
        return $this->position;
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
     * @param Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->name,
            $this->value,
            $this->position,
            $this->amount,
            $this->locale
        ));

    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->name,
            $this->value,
            $this->position,
            $this->amount,
            $this->locale
        ) = unserialize($serialized);
    }

    public function __toString()
    {
        return $this->getName()->getName() . ": " . $this->getValue()->getValue();
    }

    public function equals($entity)
    {
        return $this->getId() == $entity->getId() && $this->getAmount() == $entity->getAmount();
    }
}
