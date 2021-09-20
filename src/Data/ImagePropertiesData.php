<?php

namespace AhmadMayahi\GoogleVision\Data;

class ImagePropertiesData
{
    public function __construct(
        private float $pixelFraction,
        private float $red,
        private float $green,
        private float $blue,
    ) {
    }

    /**
     * @return float
     */
    public function getPixelFraction(): float
    {
        return $this->pixelFraction;
    }

    /**
     * @return float
     */
    public function getRed(): float
    {
        return $this->red;
    }

    /**
     * @return float
     */
    public function getGreen(): float
    {
        return $this->green;
    }

    /**
     * @return float
     */
    public function getBlue(): float
    {
        return $this->blue;
    }
}
