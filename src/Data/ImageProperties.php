<?php

namespace AhmadMayahi\Vision\Data;

class ImageProperties
{
    public function __construct(
        public float $pixelFraction,
        public float $red,
        public float $green,
        public float $blue,
    ) {
    }
}
