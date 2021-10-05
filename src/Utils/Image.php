<?php

namespace AhmadMayahi\Vision\Utils;

use GdImage;

class Image
{
    protected GdImage $gdImage;

    public function __construct(File $file)
    {
        $this->gdImage = imagecreatefromstring($file->getContents());

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

    public function drawRectangle(float $x1, float $y1, float $x2, float $y2, float $color): void
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

    public function toJpeg(string $outputFileName): bool
    {
        return imagejpeg($this->gdImage, $outputFileName);
    }

    public function toPng(string $outputFileName): bool
    {
        return imagepng($this->gdImage, $outputFileName);
    }

    public function toGif(string $outputFileName): bool
    {
        return imagegif($this->gdImage, $outputFileName);
    }

    public function toBmp(string $outputFileName): bool
    {
        return imagebmp($this->gdImage, $outputFileName);
    }
}
