<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Data;

use AhmadMayahi\Vision\Enums\Likelihood;
use AhmadMayahi\Vision\Support\AbstractConditionalLikelihood;

/**
 * @method bool isAdult($likelihood = Likelihood::VERY_LIKELY)
 * @method bool isMedical($likelihood = Likelihood::VERY_LIKELY)
 * @method bool isSpoof($likelihood = Likelihood::VERY_LIKELY)
 * @method bool isViolence($likelihood = Likelihood::VERY_LIKELY)
 * @method bool isRacy($likelihood = Likelihood::VERY_LIKELY)
 *
 * @see https://cloud.google.com/vision/docs/reference/rpc/google.cloud.vision.v1#safesearchannotation
 */
final class SafeSearch extends AbstractConditionalLikelihood
{
    public function __construct(
        public string $adult,
        public string $medical,
        public string $spoof,
        public string $violence,
        public string $racy
    ) {
    }

    protected function conditionals(): array
    {
        return [];
    }
}
