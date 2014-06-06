<?php

namespace Core\MediaBundle\Entity;

use Symfony\Component\Validator\Constraints;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Nws\MediaBundle\Entity\Image
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\MediaBundle\Entity\ImageRepository")
 */
class Image extends Media
{

    /**
     * @Constraints\Image(maxSize="6000000")
     */
    protected $file;

    private $options = array();

    /**
     * @var integer $width
     *
     * @ORM\Column(name="width", type="integer",  nullable = true)
     */
    private $width;

    /**
     * @var integer $height
     *
     * @ORM\Column(name="height", type="integer",  nullable = true)
     */
    private $height;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set width
     *
     * @param integer $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    public function getOptions()
    {
       return $this->options;
    }

    public function setOptions($options)
    {
        if (!empty($options)) {
            $this->options = $options;
        }
    }

    public function getType()
    {
        return "image";
    }

    protected function getUploadDir($dir = null)
    {
        return ($dir !== null) ? 'uploads/images/' . $dir: 'uploads/images/original';
    }

    public function removeUpload($removeTranslation = false)
    {
        $options = $this->getOptions();
        if (empty($options)) {
            throw new \Exception("No options specified for delete");
        }
        foreach ($options as $dir => $values) {
            if ($file = $this->getAbsolutePath($dir)) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
        parent::removeUpload($removeTranslation);
    }

    public function preUpload()
    {
        $file = $this->getFile();
        if (isset($file)) {
            $status  = parent::preUpload();
            if ($status) { //if is new image
                $im = ImageManipulation::createResource($file->getRealPath());
                if (ImageManipulation::checkResource($im)) {
                    $info = ImageManipulation::getResourceInfo($im);
                    if (isset($info['width'])) {
                        $this->setWidth($info['width']);
                    }
                    if (isset($info['height'])) {
                        $this->setHeight($info['height']);
                    }
                    unset($im);

                    return true;
                }
            }
        }

        return false;
    }

    public function upload()
    {
        parent::upload();
        $im = ImageManipulation::createResource($this->getAbsolutePath());
        if (ImageManipulation::checkResource($im)) {
            unset($im);
            $options = $this->getOptions();
            foreach ($options as $dir => $values) {
                $this->update($dir, $values);
            }
        } else {
            $afile = $this->getAbsolutePath();
            unset($afile);
            throw new \Exception("Unknown file format");
        }
    }

    public function update($dir, $values)
    {
        if (empty($values)) {
            throw new \Exception("No options specified for ". $dir);
        }
        $path = $this->getUploadRootDir($dir);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
            chmod($path, 0777);
        }

        $fileinfo = new \Symfony\Component\HttpFoundation\File\File($this->getAbsolutePath());
        $format = (isset($values["format"])) ? $values["format"] : $fileinfo->guessExtension();
        $quality = (isset($values["quality"])) ? $values["quality"] : 80;
        $method = (isset($values["method"])) ? $values["method"] : "resize";
        $enlarge = (isset($values["enlarge"])) ? $values["enlarge"] : false;
        $im = ImageManipulation::createResource($this->getAbsolutePath());
        switch ($method) {
            case "cut":
                $im = ImageManipulation::cutResource($im, $values["width"], $values["height"]);
                break;
            case "crop":
                $im = ImageManipulation::cropResource($im, $values["width"], $values["height"]);
                break;
            case "resize":
            default:
                $im = ImageManipulation::resizeResource($im, $values["width"], $values["height"], $enlarge);
                break;
        }

        if (isset($values["fill"]) && is_array($values["fill"])) {
            $im = ImageManipulation::fillResource($im, $values["width"], $values["height"], $values["fill"]);
        }

        if (isset($values["sepia"]) && !empty($values["sepia"])) {
            $im = ImageManipulation::sepiaResource($im);
        }

        if (isset($values["greyscale"]) && !empty($values["greyscale"])) {
            $im = ImageManipulation::greyscaleResource($im);
        }

        if (isset($values["watermark"]) && !empty($values["watermark"])) {
            $im = ImageManipulation::watermarkResource($im, $values["watermark"]);
        }
        if (isset($values["unsharp"]) && $values["unsharp"]) {
            $im = ImageManipulation::unsharpMaskResource($im, 1, 0, 0.05);
        }
        
        $im = ImageManipulation::saveResource($im, $this->getAbsolutePath($dir), $format, $quality);
        unset($im);
    }
}
