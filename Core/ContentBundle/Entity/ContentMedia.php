<?php

namespace Core\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\MediaBundle\Entity\Media;

/**
 * Core\ContentBundle\Entity\ContentMedia
 *
 * @ORM\Table(name="contents_medias")
 * @ORM\Entity(repositoryClass="Core\ContentBundle\Entity\ContentMediaRepository")
 */

class ContentMedia
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="media")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $content;

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
     * Set content
     *
     * @param Content
     */
    public function setContent(Content $content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
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
