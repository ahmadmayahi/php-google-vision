<?php

namespace AhmadMayahi\Vision\Support;

use AhmadMayahi\Vision\Exceptions\FileException;
use Exception;
use GdImage;

class Image
{
    protected GdImage $gdImage;

    public function __construct(File $file)
    {
        $contents = $file->getContents();

        if (! $contents) {
            throw new FileException('Invalid file!');
        }

        $this->gdImage = imagecreatefromstring($contents);

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

    public function cropImage($x, $y, $width, $height)
    {
        $crop = imagecrop($this->gdImage, ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);

        if (false === $crop) {
            throw new Exception('Could not crop the image');
        }

        $this->gdImage = $crop;
    }

    public function drawPolygon(array $points, $numberOfPoints, $color)
    {
        imagepolygon($this->gdImage, $points, $numberOfPoints, $color);
    }

    public function writeText(string $text, string $fontFile, int $color, int $fontSize, int $x, int $y)
    {
        imagettftext(
            $this->gdImage,
            $fontSize,
            0,
            $x,
            $y,
            $color,
            $this->getFontPath($fontFile),
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

    private function getFontPath(string $font): string
    {
        return dirname(__DIR__, 2) . '/fonts/' . $font;
    }
}
