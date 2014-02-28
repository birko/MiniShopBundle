<?php

namespace Core\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Nws\MediaBundle\Entity\Video
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\MediaBundle\Entity\VideoRepository")
 */
class Video extends Media
{
    /**
     * @var integer $videoType
     *
     * @ORM\Column(name="video_type", type="integer",  nullable = true)
     */
    private $videoType;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set videoType
     *
     * @param integer $videoType
     */
    public function setVideoType($videoType)
    {
        $this->videoType = $videoType;
    }

    /**
     * Get videoType
     *
     * @return integer
     */
    public function getVideoType()
    {
        return $this->videoType;
    }

    public function getType()
    {
        return "video";
    }

    protected function getUploadDir($dir = null)
    {
        return  'uploads/video/';
    }

    public function preUpload()
    {
        $type = $this->getVideoType();
        $file = $this->getFile();
        if ($type == VideoType::YOUTUBE) {
            $source = $this->getSource();
            // found at http://stackoverflow.com/questions/6556559/youtube-api-extract-video-id
            $pattern =
                '%^# Match any youtube URL
                (?:https?://)?  # Optional scheme. Either http or https
                (?:www\.)?      # Optional www subdomain
                (?:             # Group host alternatives
                  youtu\.be/    # Either youtu.be,
                | youtube\.com  # or youtube.com
                  (?:           # Group path alternatives
                    /embed/     # Either /embed/
                  | /v/         # or /v/
                  | /watch\?v=  # or /watch\?v=
                  )             # End path alternatives.
                )               # End host alternatives.
                ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
                $%x';
            $result = preg_match($pattern, $source, $matches);
            if (false !== $result && count($matches) > 0) {
                $source =  $matches[1];
            }
            $this->setSource($source);
            $this->setFileName($source);
            $this->setHash(trim($source));

            return true;
        } elseif (isset($file)) {
            $status  = parent::preUpload();
            if ($status) { //if is new video

                return true;
            }
        }

        return false;
    }

    public function upload()
    {
        parent::upload();
    }

    public function removeUpload($removeTranslation = false)
    {
        $type = $this->getVideoType();
        if ($type != VideoType::YOUTUBE) {
            parent::removeUpload($removeTranslation);
        }
    }
}
