<?php

namespace Core\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Core\PriceBundle\Entity\AbstractPrice;

/**
 * Core\ShopBundle\Entity\Payment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\ShopBundle\Entity\PaymentRepository")
 * @Gedmo\TranslationEntity(class="Core\CommonBundle\Entity\Translation")
 */
class Payment  extends AbstractPrice implements \Serializable, Translatable
{
    /**
     * @var string $name
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var text $description
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", nullable = true)
     */
    protected $enabled = false;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    protected $locale;

    protected $translations;

    public function __construct()
    {
        $this->setEnabled(true);
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
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

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getTranslatableLocale()
    {
        return $this->locale;
    }

    /**
     * Get translations
     *
     * @return ArrayCollection
     */
    public function getTranslations()
    {
        if ($this->translations  === null) {
            $this->translations = new ArrayCollection();
        }

        return $this->translations;
    }

    public function setTranslations($translations)
    {
        $this->translations = $translations;
    }

    public function addTranslation($translation)
    {
        $this->getTranslations()->add($translation);
    }

    public function removeTranslation($translation)
    {
        $this->getTranslations()->removeElement($translation);
    }

    public function getTranslation($locale)
    {
        return $this->getTranslations()->filter(function ($entry) use ($locale) {
             return ($entry->getTranslatableLocale() == $locale);
         })->current();
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->price,
            $this->priceVAT,
            $this->name,
            $this->description,
            $this->vat,
            $this->enabled,
            $this->locale
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->price,
            $this->priceVAT,
            $this->name,
            $this->description,
            $this->vat,
            $this->enabled,
            $this->locale
        ) = unserialize($serialized);

    }

    public function __toString()
    {
        return $this->getName() . " " . number_format($this->getPriceVAT(), 2);
    }
}
