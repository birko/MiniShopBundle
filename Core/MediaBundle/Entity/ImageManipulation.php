<?php

namespace Core\MediaBundle\Entity;

class ImageManipulation
{
    public static function createResource($file = null)
    {
        if (extension_loaded('imagick')) {
            $im = new \Imagick($file);
            if ($file) {
                if ($im->getImageColorspace() == \Imagick::COLORSPACE_CMYK) {
                    /*$img->setImageColorspace(12);
                    $icc_rgb = file_get_contents(__dir__ . '/../Resources/' . 'sRGB_v4_ICC_preference.icc');
                    $im->profileImage('icc', $icc_rgb);
                    unset($icc_rgb);
                    $im->setImageColorspace(13);
                    $im->negateImage(false, \Imagick::CHANNEL_ALL);*/
                }

            }

            return $im;
        } elseif (function_exists('imagecreate')) {
            $info = getimagesize($file);
            switch ($info[2]) {
                /*gif*/case 1: return imagecreatefromgif($file); break;
                /*jpg*/case 2: return imagecreatefromjpeg($file); break;
                /*png*/case 3: return imagecreatefrompng($file); break;
                /*bmp*/case 6: return imagecreatefromwbmp($file); break;
            }
        }

        return null;
    }

    public static function saveResource($imageResource, $path = null, $format="jpeg", $quality = 80)
    {
        if ($imageResource) {
            if (extension_loaded('imagick')) {
                $imageResource->setImageCompressionQuality($quality);
                $imageResource->setImageFormat($format);
                if ($path) {
                    $imageResource->writeImage($path);
                } else {
                    echo $imageResource;
                }
            } elseif (function_exists('imagecreate')) {
                switch ($format) {
                    case 'gif': imagegif($imageResource, $path);break;
                    case 'jpeg': imagejpeg($imageResource, $path, $quality);break;
                    case 'png': $quality = round(9 * $quality / 100); imagepng ($imageResource, $path, $quality);break;
                    case 'wbmp': imagewbmp($imageResource, $path);break;
                    case 'bmp': imagewbmp($imageResource, $path);break;
                }
            }
        }

        return $imageResource;
    }

    public static function getResourceInfo($imageResource)
    {
        $result= array();
        if ($imageResource) {
            if (extension_loaded('imagick')) {
                $result['width']  = $imageResource->getImageWidth();
                $result['height'] = $imageResource->getImageHeight();
            } elseif (function_exists('imagecreate')) {
                $result['width']  = imagesx($imageResource);
                $result['height'] = imagesy($imageResource);
            }
        }

        return $result;
    }

    public static function checkResource($imageResource)
    {
        $save = false;
        if ($imageResource) {
            if (extension_loaded('imagick')) {
                switch ($imageResource->getImageType()) {
                    case IMAGETYPE_GIF: // gif
                    case IMAGETYPE_JPEG: // jpeg
                    case IMAGETYPE_JPEG2000: //jpeg2000
                    case IMAGETYPE_SWF: // swf nejake tif bralo ako flash
                    case IMAGETYPE_PSD: // gifka vracia ako PSD
                    case IMAGETYPE_BMP: //bmp
                    case IMAGETYPE_WBMP: //bmp
                    case IMAGETYPE_PNG: // png
                    case IMAGETYPE_TIFF_II: //tiff
                    case IMAGETYPE_TIFF_MM: //tiff
                        $save = true;
                        break;
                }
            } elseif (function_exists('imagecreate')) {
                $save = true;
            }
        }

        return $save;
    }

    public static function cutResource($imageResource, $width, $height)
    {
        if ($imageResource) {
            if (extension_loaded('imagick')) {
                $w = $imageResource->getImageWidth();
                $h = $imageResource->getImageHeight();
                if (($w / $h) > ($width / $height)) {
                    $y = 0;
                    $h = $imageResource->getImageHeight();
                    $w = $imageResource->getImageHeight() * $width / $height;
                    $x = abs($imageResource->getImageWidth() - $w) / 2;
                } else {
                    $x = 0;
                    $w = $imageResource->getImageWidth();
                    $h = $imageResource->getImageWidth() * $height / $width;
                    $y = abs($imageResource->getImageHeight() - $h) / 2;
                }
                $imageResource->cropImage($w, $h, $x, $y);
                $imageResource->scaleImage($width, $height);
                $imageResource->stripImage();
            } elseif (function_exists('imagecreate')) {
                $w = imagesx($imageResource);
                $h = imagesy($imageResource);
                if (($w / $h) > ($width / $height)) {
                    $y = 0;
                    $h = imagesy($imageResource);
                    $w = imagesy($imageResource) * $width / $height;
                    $x = abs(imagesx($imageResource) - $w) / 2;
                } else {
                    $x = 0;
                    $w = imagesx($imageResource);
                    $h = imagesx($imageResource) * $height / $width;
                    $y = abs(imagesy($imageResource) - $h) / 2;
                }

                $img2 = imagecreatetruecolor($width,$height);
                imagecopyresampled($img2, $imageResource, 0, 0, $x, $y, $width, $height, $w, $h);
                $imageResource = $img2;
            }
        }

        return $imageResource;
    }

    public static function resizeResource($imageResource, $width, $height, $enlarge = false)
    {
        if ($imageResource) {
            if (extension_loaded('imagick')) {
                $w = $imageResource->getImageWidth();
                $h = $imageResource->getImageHeight();
                if ((($imageResource->getImageWidth() >= $width) || ($imageResource->getImageHeight() >= $height)) || $enlarge) {
                    if (($w / $h) > ($width / $height)) {
                        $tempHeight = $h * $width / $w;
                        $imageResource->scaleImage($width, $tempHeight);
                    } else {
                        $tempWidth = $w * $height / $h;
                        $imageResource->scaleImage($tempWidth, $height);
                    }
                }
            } elseif (function_exists('imagecreate')) {
                $w = imagesx($imageResource);
                $h = imagesy($imageResource);
                if (((imagesx($imageResource) >= $width) || (imagesy($imageResource) >= $height)) || $enlarge) {
                    if (($w / $h) > ($width / $height)) {
                        $tempHeight = $h * $width / $w;
                        $img2 = imagecreatetruecolor($width,$tempHeight);
                        imagecopyresampled($img2, $imageResource, 0, 0, 0, 0, $width, $tempHeight, $w, $h);
                        $imageResource = $img2;
                    } else {
                        $tempWidth = $w * $height / $h;
                        $img2 = imagecreatetruecolor($tempWidth,$height);
                        imagecopyresampled($img2, $imageResource, 0, 0, 0, 0, $tempWidth, $height, $w, $h);
                        $imageResource = $img2;
                    }
                }
            }
        }

        return $imageResource;
    }

    public static function cropResource($imageResource, $width, $height)
    {
        if ($imageResource) {
            if (extension_loaded('imagick')) {
                if (($imageResource->getImageWidth() / $imageResource->getImageHeight()) > ($width / $height)) {
                    $y = 0;
                    $crop_height = $imageResource->getImageHeight();
                    $crop_width = $imageResource->getImageHeight() * $width / $height;
                    $x = abs($imageResource->getImageWidth() - $crop_width) / 2;
                } else {
                    $x = 0;
                    $crop_width = $imageResource->getImageWidth();
                    $crop_height = $imageResource->getImageWidth() * $height / $width;
                    $y = abs($imageResource->getImageHeight() - $crop_height) / 2;
                }
                $imageResource->cropImage($crop_width, $crop_height, $x, $y);
                $imageResource->scaleImage($width, $height);
            } elseif (function_exists('imagecreate')) {
                if ((imagesx($imageResource) / imagesy($imageResource)) > ($width / $height)) {
                    $y = 0;
                    $crop_height = imagesy($imageResource);
                    $crop_width = imagesy($imageResource) * $width / $height;
                    $x = abs(imagesx($imageResource) - $crop_width) / 2;
                } else {
                    $x = 0;
                    $crop_width = imagesx($imageResource);
                    $crop_height = imagesx($imageResource) * $height / $width;
                    $y = abs(imagesy($imageResource) - $crop_height) / 2;
                }
                $img2 = imagecreatetruecolor($width,$height);
                imagecopyresampled($img2, $imageResource, 0, 0, $x, $y, $width, $height, $crop_width, $crop_height);
                $imageResource = $img2;
            }
        }

        return  $imageResource;
    }

    public static function fillResource($imageResource, $width, $height, $color = array(0, 0, 0))
    {
        if ($imageResource) {
            if (extension_loaded('imagick')) {
                $im = self::createResource();
                $color_string = "rgb (" . $color[0] . "," . $color[1] . "," . $color[2] . ")";
                $im->newImage($width, $height, $color_string);
                $new_w = $imageResource->getImageWidth();
                $new_h = $imageResource->getImageHeight();
                $target_x = abs($width - $new_w) / 2;
                $target_y = abs($height - $new_h) / 2;
                $im->compositeImage($imageResource, \Imagick::COMPOSITE_DEFAULT, $target_x, $target_y);

                return $im;
            } elseif (function_exists('imagecreate')) {
                $img = imagecreatetruecolor($width, $height);
                $color = imagecolorallocate($img, $color[0], $color[1], $color[2]);
                imagefill($img, 0, 0, $color);
                $new_w = imagesx($imageResource);
                $new_h = imagesy($imageResource);
                $target_x = abs($width - $new_w) / 2;
                $target_y = abs($height - $new_h) / 2;
                imagecopy($img, $imageResource, $target_x, $target_y, 0, 0, $new_w, $new_h);

                return $img;
            }

            return $imageResource;
        }

        return null;
    }

    public static function watermarkResource($imageResource, $watermarkFile)
    {
        if (file_exists($watermarkFile) && $imageResource) {
            $dest_width = null;
            $dest_height = null;
            if (extension_loaded('imagick')) {
                $dest_width = $imageResource->getImageWidth();
                $dest_height = $imageResource->getImageHeight();
            } elseif (function_exists('imagecreate')) {
                $dest_width = imagesx($imageResource);
                $dest_height = imagesy($imageResource);
            }
            if ($dest_height && $dest_width) {
                $watermark = self::resizeResource(self::createResource($watermarkFile), $dest_width, $dest_height);
                if ($watermark) {
                    if (extension_loaded('imagick')) {
                        $watermark_width = $watermark->getImageWidth();
                        $watermark_height = $watermark->getImageHeight();
                        $target_x = abs($dest_width - $watermark_width) / 2;
                        $target_y = abs($dest_height - $watermark_height) / 2;
                        $imageResource->compositeImage($watermark, \Imagick::COMPOSITE_DEFAULT, $target_x, $target_y);
                    } elseif (function_exists('imagecreate')) {
                        $watermark_width = imagesx($watermark);
                        $watermark_height = imagesx($watermark);
                        $target_x = abs($dest_width - $watermark_width) / 2;
                        $target_y = abs($dest_height - $watermark_height) / 2;
                        imagecopy($imageResource, $watermark, $target_x, $target_y, 0, 0, $watermark_width, $watermark_height);
                    }
                }
            }
        }

        return $imageResource;
    }

    public static function unsharpMaskResource($imageResource, $amount, $radius, $threshold)
    {
        if ($imageResource) {
            if (extension_loaded('imagick')) {
                $imageResource->unsharpMaskImage($radius, 0.5, $amount, $threshold);
            }
            // code from http://vikjavev.no/computing/ump.php
            elseif (function_exists('imagecreate')) {
                if ($amount > 500)    $amount = 500;
                $amount = $amount * 0.016;
                if ($radius > 50)    $radius = 50;
                $radius = $radius * 2;
                if ($threshold > 255)    $threshold = 255;

                $radius = abs(round($radius));     // Only integers make sense.
                if ($radius == 0) {
                    return $imageResource; imagedestroy($imageResource); break;
                }
                $w = imagesx($imageResource);
                $h = imagesy($imageResource);
                $imgCanvas = imagecreatetruecolor($w, $h);
                $imgBlur = imagecreatetruecolor($w, $h);
                //gausian blur
                $matrix = array(
                   array( 1, 2, 1 ),
                   array( 2, 4, 2 ),
                   array( 1, 2, 1 )
                );
                imagecopy ($imgBlur, $imageResource, 0, 0, 0, 0, $w, $h);
                imageconvolution($imgBlur, $matrix, 16, 0);

                if ($threshold>0) {
                    // Calculate the difference between the blurred pixels and the original
                    // and set the pixels
                    for ($x = 0; $x < $w-1; $x++) { // each row
                        for ($y = 0; $y < $h; $y++) { // each pixel

                            $rgbOrig = ImageColorAt($imageResource, $x, $y);
                            $rOrig = (($rgbOrig >> 16) & 0xFF);
                            $gOrig = (($rgbOrig >> 8) & 0xFF);
                            $bOrig = ($rgbOrig & 0xFF);

                            $rgbBlur = ImageColorAt($imgBlur, $x, $y);

                            $rBlur = (($rgbBlur >> 16) & 0xFF);
                            $gBlur = (($rgbBlur >> 8) & 0xFF);
                            $bBlur = ($rgbBlur & 0xFF);

                            // When the masked pixels differ less from the original
                            // than the threshold specifies, they are set to their original value.
                            $rNew = (abs($rOrig - $rBlur) >= $threshold)
                                ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig))
                                : $rOrig;
                            $gNew = (abs($gOrig - $gBlur) >= $threshold)
                                ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig))
                                : $gOrig;
                            $bNew = (abs($bOrig - $bBlur) >= $threshold)
                                ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig))
                                : $bOrig;

                            if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) {
                                $pixCol = ImageColorAllocate($imageResource, $rNew, $gNew, $bNew);
                                ImageSetPixel($imageResource, $x, $y, $pixCol);
                            }
                        }
                    }
                } else {
                    for ($x = 0; $x < $w; $x++) { // each row
                        for ($y = 0; $y < $h; $y++) { // each pixel
                            $rgbOrig = ImageColorAt($imageResource, $x, $y);
                            $rOrig = (($rgbOrig >> 16) & 0xFF);
                            $gOrig = (($rgbOrig >> 8) & 0xFF);
                            $bOrig = ($rgbOrig & 0xFF);

                            $rgbBlur = ImageColorAt($imgBlur, $x, $y);

                            $rBlur = (($rgbBlur >> 16) & 0xFF);
                            $gBlur = (($rgbBlur >> 8) & 0xFF);
                            $bBlur = ($rgbBlur & 0xFF);

                            $rNew = ($amount * ($rOrig - $rBlur)) + $rOrig;
                            if ($rNew>255) {
                                $rNew=255;
                            } elseif ($rNew<0) {
                                $rNew=0;
                            }
                            $gNew = ($amount * ($gOrig - $gBlur)) + $gOrig;
                            if ($gNew>255) {
                                $gNew=255;
                            } elseif ($gNew<0) {
                                $gNew=0;
                            }
                            $bNew = ($amount * ($bOrig - $bBlur)) + $bOrig;
                            if ($bNew>255) {
                                $bNew=255;
                            } elseif ($bNew<0) {
                                $bNew=0;
                            }
                            $rgbNew = ($rNew << 16) + ($gNew <<8) + $bNew;
                            ImageSetPixel($imageResource, $x, $y, $rgbNew);
                        }
                    }
                }
                imagedestroy($imgCanvas);
                imagedestroy($imgBlur);
            }
        }

        return $imageResource;
    }

    public static function greyscaleResource($imageResource)
    {
        if ($imageResource) {
            if (extension_loaded('imagick')) {
                $imageResource->setImageColorspace(\Imagick::COLORSPACE_GRAY);
            } elseif (function_exists('imagecreate')) {
                imagefilter($imageResource, IMG_FILTER_GRAYSCALE);
            }
        }

        return $imageResource;
    }

    public static function sepiaResource($imageResource)
    {
        if ($imageResource) {
            if (extension_loaded('imagick')) {
                $imageResource->sepiaToneImage(80);
            } elseif (function_exists('imagecreate')) {
                imagefilter($imageResource, IMG_FILTER_GRAYSCALE);
                imagefilter($imageResource, IMG_FILTER_COLORIZE, 90, 60, 40);
            }
        }

        return $imageResource;
    }
}
