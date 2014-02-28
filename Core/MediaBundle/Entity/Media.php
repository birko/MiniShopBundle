<?php

namespace Core\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Gedmo\Sluggable\Util\Urlizer as GedmoUrlizer;
use Core\CommonBundle\Entity\TranslateEntity;

/**
 * @ORM\Entity(repositoryClass="Core\MediaBundle\Entity\MediaRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"image" = "Image", "video" = "Video"})
 * @UniqueEntity("hash")
 * @UniqueEntity("source")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(class="Core\CommonBundle\Entity\Translation")
 */
abstract class Media extends TranslateEntity
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
     * @var string $title
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255, nullable = true)
     */
    protected $title;

    /**
     * @var string $slug
     * @Gedmo\Slug(fields={"title"})
     * @Gedmo\Translatable
     * @ORM\Column(name="slug", type="string", length=255, nullable = true)
     */
    protected $slug;

    /**
     * @var string $source
     * @Gedmo\Translatable
     * @ORM\Column(name="source", type="string", length=255, nullable = true)
     */
    private $source;

    /**
     * @var string $filename
     * @Gedmo\Translatable
     * @ORM\Column(name="filename", type="string", length=255, nullable = true)
     */
    private $filename;

    /**
     * @var string $hash
     *
     * @ORM\Column(name="hash", type="string", length=255, nullable = true)
     */
    private $hash;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var integer $usedCount
     *
     * @ORM\Column(name="used_count", type="integer")
     */
    private $usedCount;

    /**
     * @Constraints\File(maxSize="6000000")
     */
    protected $file;

    /**
     * @var text $description
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text",  nullable = true)
     */
    private $description;

    public function __construct()
    {
        $this->setUsedCount(0);
        $this->setCreatedAt(new \DateTime());
        $this->galleries = new ArrayCollection();
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
     * Set source
     *
     * @param  string $source
     * @return Media
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set filename
     *
     * @param  string $filename
     * @return Media
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set file
     *
     * @param  UploadedFile $file
     * @return this
     */
    public function setFile($file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set hash
     *
     * @param  string $hash
     * @return Media
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        $file = $this->getFile();
        if (empty($this->hash) && isset($file) && $file !== null) {
            return sha1_file($file->getRealPath());
        }

        return $this->hash;
    }

    /**
     * Set createdAt
     *
     * @param  datetime $createdAt
     * @return Media
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
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

    /**
     * Set usedCount
     *
     * @param integer $usedCount
     */
    public function setUsedCount($usedCount)
    {
        $this->usedCount = $usedCount;
    }

    /**
     * Get usedCount
     *
     * @return integer
     */
    public function getUsedCount()
    {
        return $this->usedCount;
    }

    /**
     * Add galery
     *
     * @param Gallery
     */
    public function addGallery($gallery)
    {
        $this->getGalleries()->add($gallery);
    }

    /**
     * Remove galery
     *
     * @param Gallery
     */
    public function removeGallery($gallery)
    {
         $this->getGalleries()->removeElement($gallery);
    }

    /**
     * Get galleries
     *
     * @return ArrayCollection
     */
    public function getGalleries()
    {
        return $this->galleries;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        $file = $this->getFile();
        $path = $this->getSource();
        $av = trim($path);
        if (empty($av)&& isset($file) && null !== $file) { //if is new file
            // do whatever you want to generate a unique name
            $this->setHash(trim(sha1_file($file->getRealPath())));
            $name = explode(".", $file->getClientOriginalName());
            $ext = array_pop($name);
            $filename = GedmoUrlizer::urlize(implode(".", $name));
            $this->setSource(uniqid().'.'.$filename. "." . $ext);
            $this->setFilename($file->getClientOriginalName());

            return  true;
        }

        return false;
    }

    public function getAbsolutePath($dir =  null)
    {
        $path = $this->getSource();
        $av = trim($path);

        return empty($av) ? null : $this->getUploadRootDir($dir).'/'.$av;
    }

    public function getWebPath($dir = null)
    {
        $path = $this->getSource();
        $av = trim($path);

        return empty($av) ? null : $this->getUploadDir($dir).'/'. $av;
    }

    protected function getUploadRootDir($dir = null)
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir($dir);
    }

    abstract protected function getUploadDir($dir = null);

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        $file = $this->getFile();
        if (null === $file) {
            return;
        }

        // you must throw an exception here if the file cannot be moved
        // so that the entity is not persisted to the database
        // which the UploadedFile move() method does
        $path = $this->getUploadRootDir();
        if (!file_exists($path)) {
            mkdir($path,0777, true);
            chmod($path, 0777);
        }

        $file->move($this->getUploadRootDir(), $this->getSource());

        unset($file);
        $this->setFile(null);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload($removeTranslation = false)
    {
        if (!$removeTranslation) {
            if ($this->getTranslations()) {
                foreach ($this->getTranslations() as $translation) {
                    $translation->removeUpload(true);
                }
            }
        }
        if ($file = $this->getAbsolutePath()) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
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
     * Set source
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

    abstract public function getType();
}
