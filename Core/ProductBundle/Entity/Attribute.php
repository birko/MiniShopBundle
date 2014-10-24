<?php

namespace Core\ProductBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Core\AttributeBundle\Entity\AttributeName;
use Core\AttributeBundle\Entity\AttributeValue;

/**
 * Core\ProductBundle\Entity\Attribute
 *
 * @ORM\Table(name="product_attribute", indexes={
        @ORM\Index(name="attribute_namevalue_idx", columns={"attributename_id", "attributevalue_id"}),
        @ORM\Index(name="attribute_productnamevalue_idx", columns={"product_id", "attributename_id", "attributevalue_id"})
   })
 * @ORM\Entity(repositoryClass="Core\ProductBundle\Entity\AttributeRepository")
 */
class Attribute
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Core\AttributeBundle\Entity\AttributeName", cascade={"persist"})
     * @ORM\JoinColumn(name="attributename_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $name;
    /**
     * @ORM\ManyToOne(targetEntity="Core\AttributeBundle\Entity\AttributeValue", cascade={"persist"})
     * @ORM\JoinColumn(name="attributevalue_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $value;
    /**
     * @var string group
     * @Gedmo\SortableGroup
     * @ORM\Column(name="agroup", type="string", nullable=true)
     */
    protected $group;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    protected $position;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="attributes")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
     protected $product;

    public function __construct()
    {
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
     * Set name
     *
     * @param AttributeName $name
     */
    public function setName(AttributeName $name)
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
    public function setValue(AttributeValue $value)
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

    /**
     * Set group
     *
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set position
     *
     * @param integer $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set product
     *
     * @param Product product
     */
    public function setProduct(Product $product)
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
}
