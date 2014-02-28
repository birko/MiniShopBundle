<?php

namespace Core\CategoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Core\CommonBundle\Entity\TranslateEntity;

/**
 * Core\CategoryBundle\Entity\Category
 * @Gedmo\Tree()
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\CategoryBundle\Entity\CategoryRepository")
 * @Gedmo\TranslationEntity(class="Core\CommonBundle\Entity\Translation")
 */
class Category extends TranslateEntity
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
     * @var integer $menu
     *
     * @ORM\Column(name="menu", type="integer")
     */
    private $menu;

    /**
     * @var boolean $home
     *
     * @ORM\Column(name="home", type="boolean", nullable = true)
     */
    private $home;

    /**
     * @var boolean $external
     *
     * @ORM\Column(name="external", type="boolean", nullable = true)
     */
    private $external;

    /**
     * @var boolean $anabled
     *
     * @ORM\Column(name="enabled", type="boolean", nullable = true)
     */
    private $enabled;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     */
    private $children;

     /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    public function __construct()
    {
        $this->setHome(false);
        $this->setEnabled(true);
        $this->setMenu(0);
        $this->children = new ArrayCollection();
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
     * Set menu
     *
     * @param integer $menu
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
    }

    /**
     * Get menu
     *
     * @return integer
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set home
     *
     * @param boolean $home
     */
    public function setHome($home)
    {
        $this->home = $home;
    }

    /**
     * Get home
     *
     * @return boolean
     */
    public function isHome()
    {
        return $this->home;
    }

    /**
     * Set external
     *
     * @param boolean $external
     */
    public function setExternal($external)
    {
        $this->external = $external;
    }

    /**
     * Get external
     *
     * @return boolean
     */
    public function isExternal()
    {
        return $this->external;
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
     * Set parent category
     *
     * @param Category $parent
     */
    public function setParent(Category $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return Category
     */
    public function getParent()
    {
        return $this->parent;
    }

     /**
     * Get children
     *
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function getToOption($separator = "-")
    {
        $pad = "";
        for ($i = 0; $i<  $this->getLevel(); $i++) {
            $pad .= $separator;
        }

        return $pad . $this->getTitle();
    }

    public function getLevel()
    {
        return $this->lvl;
    }

    public function getLeft()
    {
        return $this->lft;
    }

    public function getRight()
    {
        return $this->rgt;
    }
}
