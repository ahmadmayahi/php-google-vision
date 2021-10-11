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
        public string $name,
        public string $mid,
        public string $languageCode,
        public float  $score,
        public array  $normalizedVertices,
    ) {
    }
}
