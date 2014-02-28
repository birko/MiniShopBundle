<?php

namespace Core\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\MediaBundle\Entity\Media;

/**
 * Core\ProductBundle\Entity\ProductCategory
 *
 * @ORM\Table(name="products_medias")
 * @ORM\Entity(repositoryClass="Core\ProductBundle\Entity\ProductMediaRepository")
 */

class ProductMedia
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="media")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Core\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $media;

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
     * Set media
     *
     * @param Core\MediaBundle\Entity\Media $media
     */
    public function setMedia(Media $media)
    {
        $this->media = $media;
    }

    /**
     * Get media
     *
     * @return Core\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
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
