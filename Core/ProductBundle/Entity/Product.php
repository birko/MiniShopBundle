<?php

namespace Core\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Core\CommonBundle\Entity\TranslateEntity;
use Core\CategoryBundle\Entity\Category;
use Core\MediaBundle\Entity\Media;
use Core\UserBundle\Entity\PriceGroup;
use Core\PriceBundle\Entity\Currency;

/**
 * Core\ProductBundle\Entity\Product
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\ProductBundle\Entity\ProductRepository")
 * @Gedmo\TranslationEntity(class="Core\CommonBundle\Entity\Translation")
 */
class Product extends TranslateEntity
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
     * @var string $title
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string $slug
     * @Gedmo\Slug(fields={"title"})
     * @Gedmo\Translatable
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @var text $shortDescription
     * @Gedmo\Translatable
     * @ORM\Column(name="shortDescription", type="text", nullable=true)
     */
    private $shortDescription;

    /**
     * @var text $longDescription
     * @Gedmo\Translatable
     * @ORM\Column(name="longDescription", type="text", nullable=true)
     */
    private $longDescription;

     /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="ProductCategory", mappedBy="product", cascade={"persist", "remove"})
     * @ORM\OrderBy({"default" = "DESC", "position" = "ASC"})
     */

     protected $productCategories;
    /**
     * @ORM\OneToMany(targetEntity="Core\ProductBundle\Entity\Price", mappedBy="product")
     * @ORM\OrderBy({ "priceAmount" = "ASC",  "priceVAT" = "ASC"})
     */
    private $prices;

     /**
     * @ORM\ManyToOne(targetEntity="Core\VendorBundle\Entity\Vendor", inversedBy="products")
     * @ORM\JoinColumn(name="vendor_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $vendor;

     /**
     * @ORM\OneToMany(targetEntity="Core\ProductBundle\Entity\ProductOption", mappedBy="product")
     * @ORM\OrderBy({ "position" = "ASC"})
     */
    private $options;

    /**
     * @ORM\OneToMany(targetEntity="ProductMedia", mappedBy="product")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $media;

    /**
     * @ORM\OneToMany(targetEntity="Attribute", mappedBy="product")
     */
    private $attributes;

    /**
     * @ORM\OneToOne(targetEntity="Stock", mappedBy="product")
     **/
    private $stock;

    /**
     * @var boolean $anabled
     *
     * @ORM\Column(name="enabled", type="boolean", nullable = true)
     */
    private $enabled;

    /**
     * @var string $tags
     * @ORM\Column(name="tags", type="string", length=255, nullable = true)
     */
    private $tags;

    public function __construct()
    {
        $this->setEnabled(true);
        $this->setCreatedAt(new \DateTime());
        $this->productCategories = new ArrayCollection();
        $this->prices = new ArrayCollection();
        $this->options = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->attributes = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set shortDescription
     *
     * @param text $shortDescription
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }

    /**
     * Get shortDescription
     *
     * @return text
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set longDescription
     *
     * @param text $longDescription
     */
    public function setLongDescription($longDescription)
    {
        $this->longDescription = $longDescription;
    }

    /**
     * Get longDescription
     *
     * @return text
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * Add category
     *
     * @param Core\CategoryBundle\Category
     */
    public function addCategory(Category $category)
    {
        $productCategory = $this->getProductCategory($category->getId());
        if (empty($productCategory)) {
            $productCategory = new ProductCategory();
            $productCategory->setProduct($this);
            $productCategory->setCategory($category);
            $this->getProductCategories()->add($productCategory);
        }

        return $productCategory;
    }

    /**
     * Remove category
     *
     * @param Core\CategoryBundle\Category
     */
    public function removeCategory(Category $category)
    {
        $productCategory = $this->getProductCategory($category->getId());
        if ($productCategory !== null) {
            $this->getProductCategories()->removeElement($productCategory);
        }

        return $productCategory;
    }
    
    public function getCategories()
    {
        $list = new ArrayCollection();
        foreach ($this->getProductCategories() as $productCategory)
        {
            $list->add($productCategory->getCategory());
        }
        
        return $list;
    }
    
    public function setCategories($categories)
    {
        $list = $this->getCategories();
        foreach ($categories as $category) {
            $list = $list->filter(function($entry) use ($category) {
                return $entry->getId() != $category->getId();
            });
            $this->addCategory($category);
        }
        foreach ($list as $category) {
            $this->removeCategory($category);
        }
        
        return $this;
    }


    /**
     * Get ProductCategories
     *
     * @return ArrayCollection
     */
    public function getProductCategories()
    {
        return $this->productCategories;
    }

    public function getProductCategory($categoryID)
    {
        return $this->getProductCategories()->filter(function ($entry) use ($categoryID) {
            return ($entry->getCategory()->getId() == $categoryID);
        })->first();
    }

    /**
     * Get prices
     *
     * @return ArrayCollection
     */
    public function getPrices($type = null)
    {
        if ($type) {
            return $this->getPrices()->filter(
                function ($entry) use ($type) {
                    return ($entry->getType() == $type);
                }
            );
        } else {
            return $this->prices;
        }
    }

    /**
     * Get options
     *
     * @return ArrayCollection
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set Vendor
     *
     * @param Vendor $vendor
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * Get Vendor
     *
     * @return Vendor
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Add media
     *
     * @param Core\MediaBundle\Media
     */
    public function addMedia(Media $media)
    {
        $productMedia = $this->getProductMedia($media->getId());
        if (empty($productMedia)) {
            $media->setUsedCount($media->getUsedCount() + 1);
            $productMedia = new ProductMedia();
            $productMedia->setProduct($this);
            $productMedia->setMedia($media);
            $this->getMedia()->add($productMedia);
        }

        return $productMedia;
    }

    /**
     * Remove media
     *
     * @param Core\MediaBundle\Media
     */
    public function removeMedia(Media $media)
    {
        $productMedia = $this->getProductMedia($media->getId());
        if ($productMedia !== null) {
            $media->setUsedCount($media->getUsedCount() - 1);
            $this->getMedia()->removeElement($productMedia);
        }

        return $productMedia;
    }

    /**
     * Get ProductMedia
     *
     * @return ArrayCollection
     */
    public function getMedia($type = null)
    {
        if ($type !== null) {
            return $this->getMedia()->filter(function ($entry) use ($type) {
                return ($entry->getMedia()->getType() == $type);
            });
        } else {
            return $this->media;
        }
    }

    public function getProductMedia($mediaID)
    {
        return $this->getMedia()->filter(function ($entry) use ($mediaID) {
            return ($entry->getMedia()->getId() == $mediaID);
        })->first();
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    public function getPricesByCurrency(Currency $currency = null, $type = null) 
    {
        if ($currency !== null) {
            return $this->getPrices($type)->filter(
                function ($entry) use ($currency) {
                    return ($entry->getCurrency()->getId() == $currency->getId());
                }
            );
        } else {
            return $this->getPrices($type);
        }
    }

    public function getPricesByPriceGroup(PriceGroup $priceGroup = null, $type = null)
    {
        if ($priceGroup !== null) {
            return $this->getPrices($type)->filter(
                function ($entry) use ($priceGroup) {
                    return ($entry->getPriceGroup()->getId() == $priceGroup->getId());
                }
            );
        } else {
            return $this->getPrices($type);
        }
    }
    
    public function getPricesByCurrencyAndPriceGroup(Currency $currency = null, PriceGroup $priceGroup = null, $type = null)
    {
        $prices = $this->getPricesByCurrency($currency, $type);
        if ($priceGroup !== null) {
            return $prices->filter(
                function ($entry) use ($priceGroup) {
                    return ($entry->getPriceGroup()->getId() == $priceGroup->getId());
                }
            );
        } else {
             return $prices;
        }
    }

    public function getMinimalPrice(Currency $currency = null, PriceGroup $priceGroup = null, $type = null)
    {
        if ($this->getPrices()!== null && $this->getPrices()->count() > 0) {
            $minprice = $this->getPricesByCurrencyAndPriceGroup($currency, $priceGroup, $type)->first();
            if ($minprice === null) {
                $minprice = $this->getPrices($type)->first();
            }
            if ($minprice) {
                $price = new Price();
                $price->setPriceGroup($priceGroup);
                $price->setCurrency($currency);
                $price->setType($minprice->getType());
                $price->setPriceAmount($minprice->getPriceAmount());
                $price->setVAT($minprice->getVat());
                $price->setPrice($minprice->getPrice());
                $price->setPriceVAT($minprice->getPriceVAT());
                
                if ($currency) {
                    $price->setPrice($price->getPrice() * $currency->getRate());
                    $price->setPriceVAT($price->getPriceVAT() * $currency->getRate());
                }
                    
                if ($priceGroup) {
                    $price->setPrice($price->getPrice() * $priceGroup->getRate());
                    $price->setPriceVAT($price->getPriceVAT() * $priceGroup->getRate());
                }
                
                if ($priceGroup || $currency) {
                    $price->recalculate();
                }
                
                $price->setProduct($this);
                
                return $price;
            }
        }
        
        return null;
    }

     /**
     * Get attributes
     *
     * @return ArrayCollection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name)
    {
        return $this->getAttributes()->filter(function ($entry) use ($name) {
            return ($entry->getName() == $name);
        })->current();
    }

    public function getGroupedAttributes()
    {
        $result = array();
        $attributes = $this->getAttributes();
        if (!empty($attributes)) {
            foreach ($this->getAttributes() as $attribute) {
                $result[$attribute->getName()->getId()][] = $attribute;
            }
        }

        return $result;
    }

    /**
     * Set stock
     *
     * @param Stock $stock
     */
    public function setStock(Stock $stock = null)
    {
        $this->stock = $stock;
    }

    /**
     * Get Stock
     *
     * @return Stock
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set tags
     *
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        if (!empty($tags)) {
            $this->tags = implode(', ', $tags) .  ', ';
        } else {
            $this->tags = null;
        }
    }

    /**
     * Get tags
     *
     * @return mixed
     */
    public function getTags()
    {
        $tags = array();
        if (!empty($this->tags)) {
            $tags = explode(', ', $this->tags);
            $end = trim(end($tags));
            if (empty($end)) {
                unset($tags[count($tags) - 1]);
            }
        }

        return $tags;
    }
}
