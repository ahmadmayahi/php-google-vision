<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Data;

use AhmadMayahi\Vision\Enums\Likelihood;
use AhmadMayahi\Vision\Support\AbstractConditionalLikelihood;

/**
 * @method bool isAngry($likelihood = Likelihood::VERY_LIKELY)
 * @method bool isJoyful($likelihood = Likelihood::VERY_LIKELY)
 * @method bool isSurprised($likelihood = Likelihood::VERY_LIKELY)
 * @method bool isBlurred($likelihood = Likelihood::VERY_LIKELY)
 * @method bool isHeadwear($likelihood = Likelihood::VERY_LIKELY)
 * @method bool isUnderExposed($likelihood = Likelihood::VERY_LIKELY)
 */
final class Face extends AbstractConditionalLikelihood
{
    public function __construct(
        public string $anger,
        public string $joy,
        public string $surprise,
        public string $blurred,
        public string $headwear,
        public string $underExposed,
        public array  $bounds,
        public float  $detectionConfidence,
        public float  $landmarking,
    ) {
    }

    protected function conditionals(): array
    {
        return [
            'angry' => 'anger',
            'joyful' => 'joy',
            'surprised' => 'surprise',
        ];
    }
}
