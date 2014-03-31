<?php

namespace Core\MediaBundle\Entity;

class VideoType
{
    const FILE      = 0;
    const YOUTUBE   = 1;
    const VIMEO   = 2;

    public static function getTypes()
    {
        return array(
            self::FILE        => "File",
            self::YOUTUBE     => "YouTube",
            self::VIMEO     => "Vimeo",
        );
    }
}
