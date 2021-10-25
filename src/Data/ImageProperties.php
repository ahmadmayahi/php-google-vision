<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Data;

final class ImageProperties
{
    public function __construct(
        public float $pixelFraction,
        public float $red,
        public float $green,
        public float $blue,
    ) {
    }
}
