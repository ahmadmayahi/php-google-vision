<?php

namespace AhmadMayahi\GoogleVision\Utils;

use GdImage;

class DrawBoxImage
{
    public GdImage $gdImage;

    public function __construct(string $outputFilePathname)
    {
        $this->gdImage = imagecreatefromstring(file_get_contents($outputFilePathname));

        return $this;
    }

    public function getWidth(): int
    {
        return imagesx($this->gdImage);
    }

    public function getHeight(): int
    {
        return imagesy($this->gdImage);
    }

    public function drawRectangle(int $x1, int $y1, int $x2, int $y2, int $color): void
    {
        imagerectangle($this->gdImage, $x1, $y1, $x2, $y2, $color);
    }

    public function writeText($text, $color, $fontSize, $x, $y)
    {
        imagettftext($this->gdImage, $fontSize, 0, $x, $y, $color, dirname(__DIR__, 2).'/tests/roboto.ttf', $text);
    }

    public function saveImage(string $outputFile): bool
    {
        return imagejpeg($this->gdImage, $outputFile);
    }
}
