<?php

namespace Core\CommonBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
/**
 * Description of Translate Entity
 *
 * @author Birko
 */
class TranslateEntity implements Translatable
{
    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    protected $locale;

    protected $translations;

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
}
