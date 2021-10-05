<?php

namespace AhmadMayahi\Vision\Data;

class WebData
{
    public function __construct(
        private array $bestGuessLabels,
        private array $pagesWithMatchingImages,
        private array $fullMatchingImages,
        private array $partialMatchingImages,
        private array $visuallySimilarImages,
        private array $webEntities
    ) {
    }

    /**
     * @return LabelData[]
     */
    public function getBestGuessLabels(): array
    {
        return $this->bestGuessLabels;
    }

    /**
     * @return WebImageData[]
     */
    public function getPagesWithMatchingImages(): array
    {
        return $this->pagesWithMatchingImages;
    }

    /**
     * @return WebImageData[]
     */
    public function getFullMatchingImages(): array
    {
        return $this->fullMatchingImages;
    }

    /**
     * @return WebImageData[]
     */
    public function getPartialMatchingImages(): array
    {
        return $this->partialMatchingImages;
    }

    /**
     * @return WebImageData[]
     */
    public function getVisuallySimilarImages(): array
    {
        return $this->visuallySimilarImages;
    }

    /**
     * @return WebEntityData[]
     */
    public function getWebEntities(): array
    {
        return $this->webEntities;
    }
}
