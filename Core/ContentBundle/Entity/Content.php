<?php

namespace Core\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Core\CommonBundle\Entity\TranslateEntity;
use Core\CategoryBundle\Entity\Category;
use Core\MediaBundle\Entity\Media;

/**
 * Core\ContentBundle\Entity\Content
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\ContentBundle\Entity\ContentRepository")
 * @Gedmo\TranslationEntity(class="Core\CommonBundle\Entity\Translation")
 */
class Content extends TranslateEntity
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
     * @ORM\Column(name="title", type="string", length=255, nullable = true)
     */
    private $title;

    /**
     * @var string $slug
     * @Gedmo\Slug(fields={"title"})
     * @Gedmo\Translatable
     * @ORM\Column(name="slug", type="string", length=255, nullable = true)
     */
    private $slug;

    /**
     * @var string $shortDescription
     * @Gedmo\Translatable
     * @ORM\Column(name="shortDescription", type="text", nullable = true)
     */
    private $shortDescription;

    /**
     * @var string $longDescription
     * @Gedmo\Translatable
     * @ORM\Column(name="longDescription", type="text", nullable = true)
     */
    private $longDescription;

    /**
     * @ORM\ManyToOne(targetEntity="Core\CategoryBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="ContentMedia", mappedBy="content")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $media;

    public function __construct()
    {
        $this->media = new ArrayCollection();
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
     * @param string $shortDescription
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }

    /**
     * Get shortDescription
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set longDescription
     *
     * @param string $longDescription
     */
    public function setLongDescription($longDescription)
    {
        $this->longDescription = $longDescription;
    }

    /**
     * Get longDescription
     *
     * @return string
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * Set Category
     *
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add media
     *
     * @param Core\MediaBundle\Media
     */
    public function addMedia(Media $media)
    {
        $contentMedia = $this->getContentMedia($media->getId());
        if (empty($contentMedia)) {
            $media->setUsedCount($media->getUsedCount() + 1);
            $contentMedia = new ContentMedia();
            $contentMedia->setContent($this);
            $contentMedia->setMedia($media);
            $this->getMedia()->add($contentMedia);
        }

        return $contentMedia;
    }

    /**
     * Remove media
     *
     * @param Core\MediaBundle\Media
     */
    public function removeMedia(Media $media)
    {
        $contentMedia = $this->getContentMedia($media->getId());
        if ($contentMedia !== null) {
            $media->setUsedCount($media->getUsedCount() - 1);
            $this->getMedia()->removeElement($contentMedia);
        }

        return $contentMedia;
    }

    /**
     * Get ContentMedia
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

    public function getContentMedia($mediaID)
    {
        return $this->getMedia()->filter(function ($entry) use ($mediaID) {
            return ($entry->getMedia()->getId() == $mediaID);
        })->first();
    }
}
