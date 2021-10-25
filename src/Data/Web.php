<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Data;

final class Web
{
    public function __construct(
        public array $bestGuessLabels,
        public array $pagesWithMatchingImages,
        public array $fullMatchingImages,
        public array $partialMatchingImages,
        public array $visuallySimilarImages,
        public array $webEntities
    ) {
    }
}
