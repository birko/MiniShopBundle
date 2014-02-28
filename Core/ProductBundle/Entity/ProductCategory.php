<?php

namespace Core\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Nws\ProductBundle\Entity\ProductCategory
 *
 * @ORM\Table(name="products_categorises")
 * @ORM\Entity(repositoryClass="Core\ProductBundle\Entity\ProductCategoryRepository")
 */

class ProductCategory
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="productCategories")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Core\CategoryBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $category;

    /**
     * @ORM\Column(name="position", type="integer", nullable = true)
     */
    private $position;

    /**
     * @var boolean $default
     * @ORM\Column(name="is_default", type="boolean", nullable = true)
     */
    private $default;

    public function __construct()
    {
        $this->setPosition(0);
    }

    public function setPosition($position = null)
    {
        $this->position = (!empty($position) && $position > 0) ? $position : 0;
    }

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

    /**
     * Set category
     *
     * @param Nws\CategoryBundle\Entity\Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return Nws\CategoryBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set default
     *
     * @param boolean $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * Is default
     *
     * @return boolean
     */
    public function isDefault()
    {
        return $this->default;
    }

}
