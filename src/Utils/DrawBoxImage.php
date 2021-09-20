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

    public function drawRectangle(int $x1, int $y1, int $x2, int $y2, int $color): void
    {
        imagerectangle($this->gdImage, $x1, $y1, $x2, $y2, $color);
    }

    public function saveImage(string $outputFile): bool
    {
        return imagejpeg($this->gdImage, $outputFile);
    }
}
