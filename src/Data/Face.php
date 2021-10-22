<?php

namespace AhmadMayahi\Vision\Data;

use AhmadMayahi\Vision\Support\DTO;

class FaceData extends DTO
{
    public function __construct(
        public string $anger,
        public string $joy,
        public string $surprise,
        public string $blurred,
        public string $headwear,
        public string $underExposes,
        public array $bounds,
        public float $detectionConfidence,
        public float $landmarking,
    ) {
    }
}
