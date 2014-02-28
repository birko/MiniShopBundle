<?php

namespace Core\MediaBundle\Entity;

class VideoType
{
    const FILE      = 0;
    const YOUTUBE   = 1;

    public static function getTypes()
    {
        return array(
             self::FILE        => "File",
             self::YOUTUBE     => "YouTube",
        );
    }
}
