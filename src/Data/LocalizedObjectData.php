<?php

namespace AhmadMayahi\Vision\Data;

/**
 * The Vision API can detect and extract multiple objects in an image with Object Localization.
 *
 * @see https://cloud.google.com/vision/docs/object-localizer
 */
class LocalizedObjectData
{
    public function __construct(
        private string $name,
        private string $mid,
        private string $languageCode,
        private float  $score,
        private array  $normalizedVertices,
    ) {
    }

    /**
     * Object name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * @return array
     */
    public function getNormalizedVertices(): array
    {
        return $this->normalizedVertices;
    }

    /**
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    /**
     * A machine-generated identifier (MID) corresponding to a label's Google Knowledge Graph entry.
     *
     * @see https://blog.google/products/search/introducing-knowledge-graph-things-not/
     *
     * @return string
     */
    public function getMid(): string
    {
        return $this->mid;
    }
}
