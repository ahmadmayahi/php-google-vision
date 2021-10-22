<?php

namespace AhmadMayahi\Vision\Data;

class WebData
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
