<?php

namespace AhmadMayahi\Vision\Data;

class CropHints
{
    /**
     * @param Vertex[] $bounds
     */
    public function __construct(
        public array $bounds,
        public float $confidence,
        public float $importanceFraction
    ) {
    }
}
