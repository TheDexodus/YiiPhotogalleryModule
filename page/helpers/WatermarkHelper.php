<?php

namespace app\modules\page\helpers;

use app\modules\page\models\Image;
use phpDocumentor\Reflection\Types\Resource_;

class WatermarkHelper
{
    const WATERMARK_DO_NOT_USE   = 'Do not use';
    const WATERMARK_LEFT_TOP     = 'Left Top';
    const WATERMARK_LEFT_BOTTOM  = 'Left Bottom';
    const WATERMARK_RIGHT_TOP    = 'Right Top';
    const WATERMARK_RIGHT_BOTTOM = 'Right Bottom';
    const LIST_WATERMARK         = [
        self::WATERMARK_DO_NOT_USE,
        self::WATERMARK_LEFT_TOP,
        self::WATERMARK_LEFT_BOTTOM,
        self::WATERMARK_RIGHT_TOP,
        self::WATERMARK_RIGHT_BOTTOM,
    ];

    /**
     * @param string $imagePath
     * @param string $extension
     * @param string $watermarkPosition
     */
    public static function addWaterMark(string $imagePath, string $extension, string $watermarkPosition)
    {
        $domain = \Yii::$app->params['watermark_text'];
        $color = \Yii::$app->params['watermark_color'];

        $image = self::getImage($imagePath, $extension);

        $x = 0; $y = 0;
        switch ($watermarkPosition) {
            case self::WATERMARK_LEFT_TOP:
                $x = 0;
                $y = 0;
                break;
            case self::WATERMARK_LEFT_BOTTOM:
                $x = 0;
                $y = imagesy($image) - imagefontheight(1);
                break;
            case self::WATERMARK_RIGHT_TOP:
                $x = imagesx($image) - imagefontwidth(1) * strlen($domain);
                $y = 0;
                break;
            case self::WATERMARK_RIGHT_BOTTOM:
                $x = imagesx($image) - imagefontwidth(1) * strlen($domain);
                $y = imagesy($image) - imagefontheight(1);
                break;
        }

        if ($watermarkPosition === self::WATERMARK_DO_NOT_USE) {
            return;
        }

        imagestring($image,1,$x, $y, $domain, $color);

        self::saveImage($image, $imagePath, $extension);
    }

    /**
     * @param string $imagePath
     * @param string $extension
     *
     * @return false|resource|null
     */
    private static function getImage(string $imagePath, string $extension)
    {
        switch ($extension) {
            case Image::EXTENSION_JPG:
            case Image::EXTENSION_JPEG:
                return imagecreatefromjpeg($imagePath);
            case Image::EXTENSION_PNG:
                return imagecreatefrompng($imagePath);
            case Image::EXTENSION_GIF:
                return imagecreatefromgif($imagePath);
        }

        return null;
    }

    /**
     * @param        $image
     * @param string $imagePath
     * @param string $extension
     */
    private static function saveImage($image, string $imagePath, string $extension): void
    {
        switch ($extension) {
            case Image::EXTENSION_JPG:
            case Image::EXTENSION_JPEG:
                imagejpeg($image, $imagePath);
                break;
            case Image::EXTENSION_PNG:
                imagepng($image, $imagePath);
                break;
            case Image::EXTENSION_GIF:
                imagegif($image, $imagePath);
                break;
        }
    }
}