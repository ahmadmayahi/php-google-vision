<?php

namespace AhmadMayahi\Vision\Data;

class ImagePropertiesData
{
    public function __construct(
        public float $pixelFraction,
        public float $red,
        public float $green,
        public float $blue,
    ) {
    }
}
