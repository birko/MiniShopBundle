<?php

namespace Core\MarketingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\PriceBundle\Entity\AbstractPrice;

/**
 * @ORM\MappedSuperclass
 */
abstract class Discount extends BaseDiscount
{

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    protected $active;

    public function __construct()
    {
        $this->setActive(true);
    }

    /**
     * Set active
     *
     * @param boolean active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    public function calculateDiscount(AbstractPrice $price)
    {
        if ($this->isActive()) {
            parent::calculateDiscount($price);
        }

        return $price;
    }
}
