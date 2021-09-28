<?php

namespace AhmadMayahi\Vision\Utils;

use GdImage;

class Image
{
    protected GdImage $gdImage;

    public function __construct(string $inputFilePathname, private string $outputFilePathName)
    {
        $this->gdImage = imagecreatefromstring(file_get_contents($inputFilePathname));

        return $this;
    }

    public function getImage(): GdImage
    {
        return $this->gdImage;
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

    public function writeText(string $text, string $fontFile, int $color, int $fontSize, int $x, int $y)
    {
        $fontFile = dirname(__DIR__, 2) . '/fonts/' . $fontFile;

        imagettftext(
            $this->gdImage,
            $fontSize,
            0,
            $x,
            $y,
            $color,
            $fontFile,
            $text,
        );
    }

    public function save(): bool
    {
        return imagejpeg($this->gdImage, $this->outputFilePathName);
    }
}
