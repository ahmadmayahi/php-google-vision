<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Data;

final class CropHints
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
